<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange\Task;

use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Catalog\GroupAccessTable;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\ElementTable;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;

/**
 * Класс для работы с импортом цен.
 *
 * @package Likee\Exchange\Task
 */
class Prices extends Task
{
    /**
     * @public string элемент xml
     */
    public $node = 'prices';
    /**
     * @public string xml для импорта
     */
    public $xml = 'prices.xml';
    /**
     * @public array Группы
     */
    public $groups;
    /**
     * @public array Цены
     */
    public $prices;
    /**
     * @public array Предложения
     */
    public $offers;
    /**
     * @public array Товары
     */
    public $products;
    /**
     * @public array Товары
     */
    public $pr;
    /**
     * @public array Предложения
     */
    public $off = [];
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


        $this->log("====");
        $this->log("Цены");
        $this->log("====");

        $this->log("События OnBeforeImport");
        foreach (GetModuleEvents('likee.exchange', 'OnBeforeImport', true) as $arEvent) {
            $this->log("Событие ".$arEvent["TO_NAME"]);
            ExecuteModuleEventEx($arEvent, ['TASK' => 'prices']);
        }
        $this->log("Конец событий OnBeforeImport");

        $this->log("Загрузка из БД...");
        $this->load();
        $this->log("Чтение файла...");
        $this->read();
        $this->log("Прочитано ".count($this->products)." товаров");

        if (count($this->products) == 0) {
            $this->log("Файл пустой или отсутствует\n");
            throw new ExchangeException(
                'Файл пустой или отсутствует',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        $this->log("Запись в БД...");
        $this->apply();

        $this->log("События OnAfterImport");
        foreach (GetModuleEvents('likee.exchange', 'OnAfterImport', true) as $arEvent) {
            $this->log("Событие ".$arEvent["TO_NAME"]);
            ExecuteModuleEventEx($arEvent, ['TASK' => 'prices']);
        }
        $this->log("Конец событий OnAfterImport");

        $this->log("Начало архивации файла");
        $this->arhivate();
        $this->log("Конец архивации файла");

        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно',
        ]);

        return $this->result;
    }
    /**
     * Загружает цены
     */
    private function load()
    {
        $this->log("Загрузка товаров");
        //загрузка предложений
        $rsElements = ElementTable::getList([
            'select' => ['ID', 'XML_ID'],
            'filter' => [
                'IBLOCK_ID' => $this->config['IBLOCK_ID']
            ]
        ]);
        while ($arElement = $rsElements->fetch()) {
            $this->pr[$arElement['XML_ID']] = $arElement['ID'];
        }
        $this->log("Загружено ".count($this->pr)." товаров");

        $this->log("Загрузка предложений");
        //загрузка предложений
        $rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID']
            ],
            false,
            false,
            [
                'ID',
                'XML_ID',
                'PROPERTY_CML2_LINK'
            ]
        );
        while ($arElement = $rsElements->fetch()) {
            $this->off[$arElement['PROPERTY_CML2_LINK_VALUE']][] = $arElement;
        }
        $this->log("Загружено ".count($this->off)." предложений");

        $this->log("Загрузка цен");
        //загрузка цен
        $rsGroups = GroupTable::getList();
        while ($arGroup = $rsGroups->fetch()) {
            $this->groups[$arGroup['XML_ID']] = $arGroup['ID'];
        }
        $this->log("Загружено ".count($this->groups)." цен");
    }
    /**
     * Читает xml файл
     *
     * @throws ExchangeException
     */
    private function read()
    {
        $this->reader->setExpandedNodes([
            'prices',
            'offers',
        ]);

        $this->reader->on('price', function ($reader, $xml) {
            $price = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла цену с ID ".$price['id']);
            }
            if (!$price['id']) {
                throw new ExchangeException(
                    'У цены не указано поле id',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            if (!$price['name']) {
                throw new ExchangeException(
                    'У цены не указано поле name',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            $this->prices[$price['id']] = [
                'XML_ID' => $price['id'],
                'NAME' => $price['name']
            ];
        });

        $this->reader->on('offer', function ($reader, $xml) {
            $offer = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла товар с ID ".$offer['id']);
            }

            if (!$offer['prices']['price'][0]) {
                $offer['prices']['price'] = [
                    $offer['prices']['price']
                ];
            }

            $arPrices = [];
            foreach ($offer['prices']['price'] as $price) {
                if (!$price['id']) {
                    throw new ExchangeException(
                        'У цены не указано поле id',
                        ExchangeException::$ERR_EMPTY_FIELD
                    );
                }
                if (!$this->prices[$price['id']]) {
                    throw new ExchangeException(
                        "Цена с кодом $price[id] не найдена",
                        ExchangeException::$ERR_EMPTY_FIELD
                    );
                }
                $arPrices[] = [
                    'XML_ID' => $price['id'],
                    'VALUE' => intval(str_replace(' ', '', $price['value']))
                ];
            }

            $arProductProperties = [];
            if (isset($offer['PriceSegmentID'])) {
                $arProductProperties['PriceSegmentID'] = $offer['PriceSegmentID'];
            }
            if (isset($offer['MaxDiscBP'])) {
                $arProductProperties['MaxDiscBP'] = $offer['MaxDiscBP'];
            }

            $this->products[] = [
                'ID' => $offer['id'],
                'PRICES' => $arPrices,
                'PROPERTIES' => $arProductProperties
            ];
        });

        $this->reader->read();
    }
    /**
     * Применяет изменения в базе
     *
     * @throws ExchangeException
     */
    private function apply()
    {

        foreach ($this->prices as $price) {
            if (!$this->groups[$price['XML_ID']]) {
                $rs = GroupTable::add($price);
            } else {
                $rs = GroupTable::update($this->groups[$price['XML_ID']], $price);
            }
            if (!$rs->isSuccess()) {
                throw new ExchangeException(
                    reset($rs->getErrorMessages()),
                    ExchangeException::$ERR_CREATE_UPDATE
                );
            } else {
                if (!$this->groups[$price['XML_ID']]) {
                    GroupAccessTable::add(
                        [
                            'CATALOG_GROUP_ID' => $rs->getId(),
                            'GROUP_ID' => 2,
                            'ACCESS' => GroupAccessTable::ACCESS_VIEW
                        ]
                    );
                    GroupAccessTable::add(
                        [
                            'CATALOG_GROUP_ID' => $rs->getId(),
                            'GROUP_ID' => 2,
                            'ACCESS' => GroupAccessTable::ACCESS_BUY
                        ]
                    );
                }
            }

            $this->groups[$price['XML_ID']] = $rs->getId();
        }

        $obElement = new \CIBlockElement();

        foreach ($this->products as $product) {
            foreach ($this->off[$this->pr[$product['ID']]] as $off) {
                foreach ($product['PRICES'] as $price) {
                    $arPrice = PriceTable::getRow(
                        [
                            'filter' => [
                                'CATALOG_GROUP_ID' => $this->groups[$price['XML_ID']],
                                'PRODUCT_ID' => $off['ID']
                            ]
                        ]
                    );

                    if ($arPrice) {
                        if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                            $this->log("Обновляем в БД цену с ID ".$arPrice['ID']." для продукта ".$off['ID']);
                        }
                        $rs = PriceTable::update($arPrice['ID'], ['PRICE' => $price['VALUE']]);
                        /*foreach (GetModuleEvents('likee.exchange', 'OnAfterPriceUpdate', true) as $arEvent) {
                            ExecuteModuleEventEx($arEvent, [
                                    'TASK' => 'price',
                                    'PRODUCT_XML_ID' => $off['XML_ID'],
                                    'PRICE' => [
                                        'CATALOG_GROUP_ID' => $this->groups[$price['XML_ID']],
                                        'PRODUCT_ID' => $off['ID'],
                                        'PRICE' => $price['VALUE'],
                                        'PRICE_SCALE' => $price['VALUE'],
                                        'CURRENCY' => 'RUB',
                                    ]
                                ]);
                        }*/
                    } else {
                        if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                            $this->log("Добавляем в БД цену для продукта ".$off['ID']);
                        }
                        $rs = PriceTable::add([
                            'CATALOG_GROUP_ID' => $this->groups[$price['XML_ID']],
                            'PRODUCT_ID' => $off['ID'],
                            'PRICE' => $price['VALUE'],
                            'PRICE_SCALE' => $price['VALUE'],
                            'CURRENCY' => 'RUB',
                        ]);

                        /*foreach (GetModuleEvents('likee.exchange', 'OnAfterPriceAdd', true) as $arEvent) {
                            ExecuteModuleEventEx($arEvent, [
                                    'TASK' => 'price',
                                    'PRODUCT_XML_ID' => $off['XML_ID'],
                                    'PRICE' => [
                                        'CATALOG_GROUP_ID' => $this->groups[$price['XML_ID']],
                                        'PRODUCT_ID' => $off['ID'],
                                        'PRICE' => $price['VALUE'],
                                        'PRICE_SCALE' => $price['VALUE'],
                                        'CURRENCY' => 'RUB',
                                    ]
                                ]);
                        }*/
                    }

                    if (!$rs->isSuccess()) {
                        throw new ExchangeException(
                            reset($rs->getErrorMessages()),
                            ExchangeException::$ERR_CREATE_UPDATE
                        );
                    }
                }
            }
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Обновляем в БД какие-то свойства");
            }
            if ($this->pr[$product['ID']] && $product['PROPERTIES']) {
                $properties = [];

                foreach ($product['PROPERTIES'] as $propertyKey => $propertyValue) {
                    if ($this->addIblockPriceProperty($propertyKey, $propertyValue)) {
                        $properties[mb_strtoupper($propertyKey)] = [
                            'VALUE' => $propertyValue
                        ];
                    }
                }

                if ($properties) {
                    $obElement->SetPropertyValuesEx($this->pr[$product['ID']], $this->config['IBLOCK_ID'], $properties);
                }
            }
        }
    }

    private function addIblockPriceProperty($sName, $sValue)
    {
        static $arClasses = [];
        static $arClassesValues = [];

        if (!array_key_exists($sName, $arClasses)) {
            \Bitrix\Main\Loader::includeModule('sprint.migration');

            $helper = new \Sprint\Migration\HelperManager();

            $hlblockId = $helper->Hlblock()->getHlblockId($sName);

            if (!$hlblockId) {
                $hlblockId = $helper->Hlblock()->addHlblock([
                    'NAME' => $sName,
                    'TABLE_NAME' => 'b_1c_dict_' . mb_strtolower($sName)
                ]);

                $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_XML_ID', [
                    'USER_TYPE_ID' => 'string',
                    'EDIT_FORM_LABEL' => [
                        'ru' => 'XML_ID',
                    ],
                    'LIST_COLUMN_LABEL' => [
                        'ru' => 'XML_ID'
                    ],
                ]);

                $helper->Iblock()->addPropertyIfNotExists($this->config['IBLOCK_ID'], [
                    'NAME' => $sName,
                    'CODE' => mb_strtoupper($sName),
                    'PROPERTY_TYPE' => 'S',
                    'USER_TYPE' => 'directory',
                    'MULTIPLE' => 'N',
                    'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_'.mb_strtolower($sName)),
                ]);
            }

            $arClasses[$sName] = $hlblockId;

            $block = HL::getById($arClasses[$sName])->fetch();
            $class = HL::compileEntity($block)->getDataClass();

            $rsPropValues = $class::getList();
            while ($arPropValue = $rsPropValues->fetch()) {
                $arClassesValues[$sName][$arPropValue['UF_XML_ID']] = $arPropValue['ID'];
            }
            unset($arPropValue, $rsPropValues);
        }

        if (!array_key_exists($sValue, $arClassesValues[$sName])) {
            $arClassesValues[$sName][$sValue] = false;

            $block = HL::getById($arClasses[$sName])->fetch();
            $class = HL::compileEntity($block)->getDataClass();

            $result = $class::add([
                'UF_XML_ID' => $sValue
            ]);
            if ($result->isSuccess()) {
                $arClassesValues[$sName][$sValue] = $result->getId();
            }
        }

        return $arClassesValues[$sName][$sValue];
    }
}
