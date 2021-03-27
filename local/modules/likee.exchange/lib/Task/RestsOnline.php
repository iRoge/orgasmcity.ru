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
use Likee\Exchange\Task;
use Likee\Location\Location;

/**
 * Класс для работы с импортом остатков.
 *
 * @package Likee\Exchange\Task
 */
class RestsOnline extends Task
{
    /**
     * @public string элемент xml
     */
    public $node = 'rests';

    /**
     * @public string xml для импорта
     */
    public $xml = 'restsim.xml';

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

        $this->log("====================");
        $this->log("Размерные остатки ИМ");
        $this->log("====================");

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

            if (!$rest['size_id']) {
                throw new ExchangeException(
                    "У остатка $rest[product_id] не указано поле size_id",
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
            $iExist = false;

            $iExist = $this->offers[$iProduct][$rest['size_id']]['ID'];

            if (!$iExist) {
                return;
                throw new ExchangeException(
                    "Товар $rest[product_id] с размером $rest[size_id] не найден",
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            $symbol = mb_substr($rest['quantity'], 0, 1);

            $this->rests[] = [
                'XML_ID' => $rest['product_id'],
                'STORE_ID' => $this->stores[$rest['store_id']],
                'SIZE_ID' => $rest['size_id'],
                'OFFER_ID' => $iExist,
                'OFFER_XML_ID' => $this->offers[$iProduct][$rest['size_id']]['XML_ID'],
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
                '!PROPERTY_SIZE' => false,
            ],
            false,
            false,
            [
                'ID',
                'XML_ID',
                'PROPERTY_SIZE',
                'PROPERTY_CML2_LINK',
            ]
        );

        $arOffersIds = [];
        while ($arElement = $rsElements->Fetch()) {
            $this->offers[$arElement['PROPERTY_CML2_LINK_VALUE']][$arElement['PROPERTY_SIZE_VALUE']] = $arElement;
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

    /**
     * Заполняет свойства STORES у торговых предложений - привязка к складам
     */
    protected function attachStoresToOffers()
    {
        foreach ($this->offers as $iProduct => $arOffers) {
            foreach ($this->offers[$iProduct] as $arOffer) {
                $arStores = [];
                $amount = 0;

                $rsStock = \CCatalogStoreProduct::GetList([], [
                    'PRODUCT_ID' => $arOffer['ID'],
                    '>AMOUNT' => 0,
                ], false, false, ['ID', 'STORE_ID', 'AMOUNT']);

                while ($arStock = $rsStock->Fetch()) {
                    $arStores[] = $arStock['STORE_ID'];
                    $amount += $arStock['AMOUNT'];
                }

                \CIBlockElement::SetPropertyValuesEx(
                    $arOffer['ID'],
                    $this->config['OFFERS_IBLOCK_ID'],
                    ['STORES' => $arStores]
                );

                \CCatalogProduct::Update($arOffer['ID'], ['QUANTITY' => $amount]);
            }
        }
    }

    /**
     * Деактивирует разделы, в которых нет аквтиных товаров
     *
     * Заполняет пользовательское поле UF_CITY_LINK
     *
     * @param integer $iblockId Id инфоблока
     * @param integer $offersIblockID Id инфоблока торговых предложений
     * @return bool True в случае успешного выполнения
     */
    public static function updateSectionsActivity($iblockId = null, $offersIblockID = null)
    {
        global $USER_FIELD_MANAGER, $CACHE_MANAGER;

        if (!Loader::includeModule('iblock') || !Loader::includeModule('likee.location')) {
            return false;
        }

        $arConfig = Config::get();

        if (is_null($iblockId)) {
            $iblockId = $arConfig['IBLOCK_ID'];
        }

        if (is_null($offersIblockID)) {
            $offersIblockID = $arConfig['OFFERS_IBLOCK_ID'];
        }

        $sEntityID = 'IBLOCK_' . $iblockId . '_SECTION';
        $iblockId = intval($iblockId);

        $arMaxDepthSection = \CIBlockSection::GetList(array('depth_level' => 'desc'), array(
            'IBLOCK_ID' => $iblockId,
        ), false, array('DEPTH_LEVEL'))->Fetch();

        $maxLevel = intval($arMaxDepthSection['DEPTH_LEVEL']);

        $arLocations = Location::all();
        $arLocationIDS = [];

        foreach ($arLocations as $arLocation) {
            if (!empty($arLocation['STORES'])) {
                $arLocationIDS[$arLocation['ID']] = array_column($arLocation['STORES'], 'ID');
            }
        }

        //города, которые менежер добавил вручную, не привязаны к складу
        $arAdditionalLocations = Location::getAdditionalCities();
        foreach ($arAdditionalLocations as $arAdditionalLocation) {
            if (!array_key_exists($arAdditionalLocation['ID'], $arLocationIDS)) {
                $arLocationIDS[$arAdditionalLocation['ID']] = $arAdditionalLocation['STORES'];
            }
        }


        //получаем склады, которые доступны для всех городов
        $rsOnlineStores = \CCatalogStore::GetList(
            ['ID' => 'ASC'],
            [
                'ACTIVE' => 'Y',
                'UF_ONLINE' => 1
            ],
            false,
            false,
            ['ID']
        );

        $arOnlineStores = [];
        while ($arStore = $rsOnlineStores->Fetch()) {
            $arOnlineStores[] = $arStore['ID'];
        }

        foreach ($arLocationIDS as $iLocationID => $arStoresIDS) {
            $arStoresIDS = array_unique(array_filter($arStoresIDS));

            $arStoresIDS = array_unique(array_merge($arStoresIDS, $arOnlineStores));

            if ($arStoresIDS) {
                $arLocationIDS[$iLocationID] = $arStoresIDS;
            } else {
                unset($arLocationIDS[$iLocationID]);
            }
        }

        while ($maxLevel > 0) {
            $rsSections = \CIBlockSection::GetList(
                array('sort' => 'asc'),
                array(
                    'IBLOCK_ID' => $iblockId,
                    'DEPTH_LEVEL' => $maxLevel,
                    'ACTIVE' => 'Y',
                ),
                false,
                array('ID', 'NAME', 'DEPTH_LEVEL')
            );

            while ($arSection = $rsSections->Fetch()) {
                $arCityLink = [];

                $rsSubsections = \CIBlockSection::GetList(array('id' => 'asc'), array(
                    'IBLOCK_ID' => $iblockId,
                    'SECTION_ID' => $arSection['ID'],
                ), false, array('ID', 'UF_CITY_LINK'));

                while ($arSubsection = $rsSubsections->GetNext()) {
                    if (is_array($arSubsection['UF_CITY_LINK'])) {
                        $arCityLink = array_merge($arCityLink, $arSubsection['UF_CITY_LINK']);
                    }
                }

                foreach ($arLocationIDS as $iLocationID => $arStoresIDS) {
                    if (empty($arStoresIDS)) {
                        continue;
                    }

                    $rsSubItems = \CIBlockElement::GetList(array('id' => 'asc'), array(
                        'IBLOCK_ID' => $iblockId,
                        'ACTIVE' => 'Y',
                        'SECTION_ID' => $arSection['ID'],
                        'INCLUDE_SUBSECTIONS' => 'N',
                        '>PROPERTY_MINIMUM_PRICE' => 0,
                        'CATALOG_AVAILABLE' => 'Y',
                        '!DETAIL_PICTURE' => false
                    ), false, false, array('ID'));

                    while ($arSubItem = $rsSubItems->Fetch()) {
                        $rsOffers = \CIBlockElement::GetList(array('id' => 'asc'), array(
                            'IBLOCK_ID' => $offersIblockID,
                            'ACTIVE' => 'Y',
                            '=PROPERTY_CML2_LINK' => $arSubItem['ID'],
                            'PROPERTY_STORES' => $arStoresIDS,
                            'CATALOG_AVAILABLE' => 'Y',
                            '>CATALOG_QUANTITY' => 0
                        ), false, array('nTopCount' => 1), array('ID'));

                        if ($rsOffers->SelectedRowsCount() > 0) {
                            $arCityLink[] = $iLocationID;
                            break;
                        }
                    }
                }

                $USER_FIELD_MANAGER->Update($sEntityID, $arSection['ID'], array(
                    'UF_CITY_LINK' => array_unique($arCityLink)
                ));
            }

            $maxLevel--;
        }

        $CACHE_MANAGER->ClearByTag('bitrix:menu');

        return true;
    }
}
