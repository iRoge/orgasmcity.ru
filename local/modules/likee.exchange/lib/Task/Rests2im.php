<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Exchange\Task;

use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\Product\Sku;
use Bitrix\Catalog\StoreTable;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\StoreProductTable;
use Likee\Exchange\Config;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task\Rests;
use Likee\Location\Location;

/**
 * Класс для работы с импортом остатков.
 *
 * @package Likee\Exchange\Task
 */
class Rests2im extends Rests
{
    /**
     * @public string элемент xml
     */
    public $node = 'rests';

    /**
     * @public string xml для импорта
     */
    public $xml = 'rests2im.xml';

    /**
     * @public array Остатки
     */
    public $rests;

    /**
     * @public array Склады
     */
    public $stores;

    /**
     * @public array Изменения
     */
    public $changes;

    /**
     * @public array Элементы
     */
    public $elements;

    /**
     * @public array Количество
     */
    public $quantity;

    /**
     * @public array Торговые предложения
     */
    public $offers = [];

    /**
     * @public bool Только изменения
     */
    public $only_changes = false;

    /**
     * @public array Дополнительные сообщения результата импорта
     */
    public $log = [];

    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {
        if (!$this->config['IBLOCK_ID']) {
            throw new ExchangeException(
                'В настройках не указан инфоблок товаров',
                ExchangeException::$ERR_NO_PRODUCTS_IBLOCK
            );
        }

        if (!$this->config['OFFERS_IBLOCK_ID']) {
            throw new ExchangeException(
                'В настройках не указан инфоблок предложений',
                ExchangeException::$ERR_NO_OFFERS_IBLOCK
            );
        }

        $this->log("=======================");
        $this->log("Безразмерные остатки ИМ");
        $this->log("=======================");

        $this->log("События OnBeforeImport");
        foreach (GetModuleEvents('likee.exchange', 'OnBeforeImport', true) as $arEvent) {
            $this->log("Событие ".$arEvent["TO_NAME"]);
            ExecuteModuleEventEx($arEvent, ['TASK' => 'rests']);
        }
        $this->log("Конец событий OnBeforeImport");

        $this->log("Загрузка из БД...");
        $this->load();
        $this->log("Чтение файла...");
        $this->read();
        $this->log("Прочитано ".count($this->rests)." остатков");
        $this->log("Запись в БД...");
        $this->apply();
        if (defined("IMPORT_TYPE") && IMPORT_TYPE == "FULL") {
            $this->log("Обновление свойства 'склады' у ТП...");
            $this->attachStoresToOffers();
        }

        $this->log("События OnAfterImport");
        foreach (GetModuleEvents('likee.exchange', 'OnAfterImport', true) as $arEvent) {
            $this->log("Событие ".$arEvent["TO_NAME"]);
            ExecuteModuleEventEx($arEvent, ['TASK' => 'rests']);
        }
        $this->log("Конец событий OnAfterImport");

        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно' . ($this->log ? "\n\n".implode("\n", $this->log) : ''),
        ]);

        return $this->result;
    }

    /**
     * Читает xml файл
     *
     * @throws ExchangeException
     */
    private function read()
    {
        $this->reader->setExpandedNodes([
            'rests'
        ]);

        $this->reader->on('only_changes', function ($reader, $xml) {
            $xml = Helper::xml2array(simplexml_load_string($xml));
            $this->only_changes = $xml[0] == 1;
        });

        $this->reader->on('rest', function ($reader, $xml) {
            $rest = Helper::xml2array(simplexml_load_string($xml));

            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла остаток с ID ".$rest['product_id']);
            }
            if (!$rest['product_id']) {
                throw new ExchangeException(
                    'У остатка не указано поле product_id',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            if (!$rest['store_id']) {
                throw new ExchangeException(
                    'У остатка не указано поле store_id',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            if (! empty($rest['size_id'])) {
                throw new ExchangeException(
                    "У остатка $rest[product_id] указано поле size_id",
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }
                
            if (!is_numeric($rest['quantity'])) {
                throw new ExchangeException(
                    'Некорректно указано поле quantity',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            if (!$this->elements[$rest['product_id']]) {
                $this->log[] = "Товар с кодом $rest[product_id] не найден";
                return;
            }

            if (!$this->stores[$rest['store_id']]) {
                $this->log[] = "Склад с кодом $rest[store_id] не найден";
                return;
            }

            $iProduct = $this->elements[$rest['product_id']]['ID'];
            $iExist = $this->offers[$iProduct]['ID'] ?: false;

            if (!$iExist) {
                return;
            }

            $symbol = mb_substr($rest['quantity'], 0, 1);

            $this->rests[] = [
                'XML_ID' => $rest['product_id'],
                'STORE_ID' => $this->stores[$rest['store_id']],
                'OFFER_ID' => $iExist,
                'OFFER_XML_ID' => $this->offers[$iProduct]['XML_ID'],
                'QUANTITY' => in_array($symbol, ['+', '-']) ? mb_substr($rest['quantity'], 1) : $rest['quantity'],
                'SYMBOL' => in_array($symbol, ['+', '-']) ? $symbol : false
            ];
        });

        $this->reader->read();
    }

    /**
     * Загружает остатки
     */
    private function load()
    {
        $this->log("Загружаем товары");
        //загрузка товаров
        $rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['IBLOCK_ID']
            ],
            false,
            false,
            [
                'ID',
                'XML_ID'
            ]
        );

        while ($arElement = $rsElements->Fetch()) {
            $this->elements[$arElement['XML_ID']] = $arElement;
        }
        $this->log("Загружено ".count($this->elements)." товаров");

        $this->log("Загружаем предложения");

        //загрузка предложений
        $rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                'PROPERTY_SIZE' => false
            ],
            false,
            false,
            [
                'ID',
                'XML_ID',
                'PROPERTY_CML2_LINK',
            ]
        );

        $arOffersIds = [];
        while ($arElement = $rsElements->Fetch()) {
            $this->offers[$arElement['PROPERTY_CML2_LINK_VALUE']] = $arElement;
            $arOffersIds[$arElement['ID']] = $arElement['ID'];
        }
        $this->log("Загружено ".count($this->offers)." предложений");

        $this->log("Загружаем магазины");
        //загрузка магазинов
        $rsStores = StoreTable::getList([
            'filter' => [
                'ID' => 209
            ]
        ]);
        while ($arStore = $rsStores->fetch()) {
            $this->stores[$arStore['XML_ID']] = $arStore['ID'];
        }
        $this->log("Загружено ".count($this->stores)." магазинов");

        $this->log("Загружаем остатки");
        //загрузка остатков
        $rsQuantities = StoreProductTable::getList([
            'filter' => [
                'STORE_ID' => array_values($this->stores)
            ]
        ]);
        $i = 0;
        while ($arQuantity = $rsQuantities->fetch()) {
            if (!$arOffersIds[$arQuantity['PRODUCT_ID']]) {
                continue;
            }
            $i++;
            $this->quantity[$arQuantity['PRODUCT_ID']][$arQuantity['STORE_ID']] = $arQuantity;
        }
        $this->log("Загружено ".$i." остатков");
    }

    /**
     * Применяет изменения в базе
     *
     * @throws ExchangeException
     */
    private function apply()
    {
        foreach ($this->rests as $rest) {
            $arRow = $this->quantity[$rest['OFFER_ID']][$rest['STORE_ID']];
            
            if ($arRow) {
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log("Обновляем в БД остаток у ТП с ID ".$rest['OFFER_ID']." на складе с ID ".$rest['STORE_ID']);
                }
                $iCurAmount = $arRow['AMOUNT'];

                switch ($rest['SYMBOL']) {
                    case '-':
                        $arRow['AMOUNT'] -= $rest['QUANTITY'];
                        break;
                    case '+':
                        $arRow['AMOUNT'] += $rest['QUANTITY'];
                        break;
                    default:
                        $arRow['AMOUNT'] = $rest['QUANTITY'];
                        break;
                }
                
                
                $this->changes[$rest['OFFER_ID']][$rest['STORE_ID']] = [
                    'ID' => $rest['OFFER_ID'],
                    'XML_ID' => $rest['OFFER_XML_ID'],
                ];

                if ($iCurAmount != $arRow['AMOUNT']) {
                    $rs = StoreProductTable::update(
                        $arRow['ID'],
                        [
                            'AMOUNT' => $arRow['AMOUNT']
                        ]
                    );

                    if (!$rs->isSuccess()) {
                        throw new ExchangeException(
                            reset($rs->getErrorMessages()),
                            ExchangeException::$ERR_CREATE_UPDATE
                        );
                    }

                    foreach (GetModuleEvents('likee.exchange', 'OnAfterRestUpdate', true) as $arEvent) {
                        ExecuteModuleEventEx($arEvent, [
                                'TASK' => 'offers',
                                'PRODUCT_XML_ID' => $rest['OFFER_XML_ID'],
                                'REST' => [
                                    'AMOUNT' => $arRow['AMOUNT'],
                                    'PRODUCT_ID' => $rest['OFFER_ID'],
                                    'STORE_ID' => $rest['STORE_ID']
                                ]
                            ]);
                    }
                }
            } else {
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log("Добавляем в БД остаток у ТП с ID ".$rest['OFFER_ID']." на складе с ID ".$rest['STORE_ID']);
                }
                $arRow['AMOUNT'] = 0;
                switch ($rest['SYMBOL']) {
                    case '-':
                        $arRow['AMOUNT'] -= $rest['QUANTITY'];
                        break;
                    case '+':
                        $arRow['AMOUNT'] += $rest['QUANTITY'];
                        break;
                    default:
                        $arRow['AMOUNT'] = $rest['QUANTITY'];
                        break;
                }

                $rs = StoreProductTable::add(
                    [
                        'AMOUNT' => $arRow['AMOUNT'],
                        'PRODUCT_ID' => $rest['OFFER_ID'],
                        'STORE_ID' => $rest['STORE_ID']
                    ]
                );

                $this->changes[$rest['OFFER_ID']][$rest['STORE_ID']] = [
                    'ID' => $rest['OFFER_ID'],
                    'XML_ID' => $rest['OFFER_XML_ID'],
                ];

                if (!$rs->isSuccess()) {
                    throw new ExchangeException(
                        reset($rs->getErrorMessages()),
                        ExchangeException::$ERR_CREATE_UPDATE
                    );
                }

                foreach (GetModuleEvents('likee.exchange', 'OnAfterRestAdd', true) as $arEvent) {
                    ExecuteModuleEventEx($arEvent, [
                            'TASK' => 'offers',
                            'PRODUCT_XML_ID' => $rest['OFFER_XML_ID'],
                            'REST' => [
                                'AMOUNT' => $arRow['AMOUNT'],
                                'PRODUCT_ID' => $rest['OFFER_ID'],
                                'STORE_ID' => $rest['STORE_ID']
                            ]
                        ]);
                }
            }
        }

        if (!$this->only_changes) {
            $this->log("Удаляем остатки, которые отсутствовали в файле");
            $i = 0;
            foreach ($this->quantity as $productId => $arStoresList) {
                foreach ($arStoresList as $storeId => $arBaseTable) {
                    if (empty($this->changes[$productId][$storeId])) {
                        if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                            $this->log("Удаляем остаток у предложения с ID ".$productId." на складе с ID ".$storeId);
                        }
                        $i++;
                        StoreProductTable::delete($arBaseTable['ID']);
                    }
                }
            }
            $this->log("Удалено ".$i." остатков");
        }

        if (defined("IMPORT_TYPE") && IMPORT_TYPE == "FULL") {
            $this->log("Обновляем доступность в каталоге");
            $this->log("Необходимо обновить доступность у ".count($this->changes)." предложений");
            $obElement = new \CIBlockElement();

            foreach ($this->changes as $productId => $arStoresList) {
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log("Обновляем доступность предложения с ID ".$productId);
                }
                $rsQuantity = StoreProductTable::getList([
                    'filter' => [
                        'PRODUCT_ID' => $productId
                    ]
                ]);

                $iTotal = 0;

                while ($arQuantity = $rsQuantity->fetch()) {
                    $iTotal += $arQuantity['AMOUNT'];
                }
                \CCatalogProduct::Add([
                    'ID' => $productId,
                    'QUANTITY' => $iTotal,
                    'TYPE' => ProductTable::TYPE_OFFER
                ]);
                Sku::updateAvailable($productId, $this->config['OFFERS_IBLOCK_ID']);
            }
        }
        
        foreach (GetModuleEvents('likee.exchange', 'OnAfterRestUpdateAvailable', true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, [
                    'TASK' => 'offers',
                    'CHANGES' => $this->changes
                ]);
        }
    }
}
