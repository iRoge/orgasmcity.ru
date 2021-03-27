<?php
/**
 * Project: respect
 * Date: 12.01.19
 *
 * @author: Boltov Ignat
 */

namespace Likee\Exchange\Task;

use Bitrix\Catalog\Product\Sku;
use Bitrix\Catalog\StoreTable;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\StoreProductTable;
use Likee\Exchange\Config;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;
use Likee\Location\Location;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

/**
 * Класс для работы с импортом остатков.
 *
 * @package Likee\Exchange\Task
 */
class CustomImport extends Task
{
    /**
     * @var string элемент xml
     */
    var $node = 'rests';

    /**
     * @var string xml для импорта
     */
    var $xml = 'rests.xml';
    var $xml2 = 'rests2.xml';
    var $xmlimport = 'import.xml';
    var $xmloffers = 'offers.xml';
    var $xmlreferences = 'references.xml';
    var $xmlstores = 'stores.xml';
    /**
     * @var array Остатки
     */
    var $rests;

    /**
     * @var array Склады
     */
    var $stores;

    /**
     * @var array Изменения
     */
    var $changes;

    /**
     * @var array Элементы
     */
    var $elements;

    /**
     * @var array Количество
     */
    var $quantity;

    /**
     * @var array Торговые предложения
     */
    var $offers = [];

    /**
     * @var bool Только изменения
     */
    var $only_changes = false;

    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {
        /*if (!$this->config['IBLOCK_ID'])
            throw new ExchangeException(
                'В настройках не указан инфоблок товаров',
                ExchangeException::$ERR_NO_PRODUCTS_IBLOCK
            );

        if (!$this->config['OFFERS_IBLOCK_ID'])
            throw new ExchangeException(
                'В настройках не указан инфоблок предложений',
                ExchangeException::$ERR_NO_OFFERS_IBLOCK
            );

		foreach (GetModuleEvents('likee.exchange', 'OnBeforeImport', true) as $arEvent)
ExecuteModuleEventEx($arEvent, ['TASK' => 'rests']);*/

        echo '<br/>';
        echo '+++++++++++++++++++++start+++++++++++++++++++++<br/>';
        echo '<pre>';

        $this->ibapplystores();
        /*
		$this->ibapplyrests();
		$this->ibapplyimport();
			$this->ibapplyoffers();
		$this->ibapplyreferences();
        $this->ibapplyrests2();

		*/

        /*foreach (GetModuleEvents('likee.exchange', 'OnAfterImport', true) as $arEvent)
ExecuteModuleEventEx($arEvent, ['TASK' => 'rests']);*/

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно',
        ]);

        return $this->result;
    }


    private function ibapplyrests()
    {
        echo '<br/>';
        echo '+++++++++++++++++++rests++++++++++++++++++++++++<br/>';
        echo '<pre>';

        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xml;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }

        while ($reader->read()) {
            if ($reader->name == 'rest' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $rest = Helper::xml2array(simplexml_load_string($xml));

                $product_id = $this->returnIdOfferIdByXmlId($rest['product_id']);
                $store_id = $this->returnIdStoreByXmlId($rest['store_id']);
                if ($store_id = 'no store') {
                    echo 'Не найдено такого слада со внешним кодом ' . $rest['store_id'] . '<br>';
                    continue;
                }
                echo 'product_id ' . $product_id . '<br>';
                echo 'store_id ' . $rest['store_id'] . '<br>';
                echo 'AMOUNT ' . $rest['quantity'] . '<br>';

                $arFields = Array(
                    "PRODUCT_ID" => $product_id,
                    "STORE_ID" => $store_id,
                    "AMOUNT" => $rest['quantity'],
                );

                $rsStore = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $product_id, 'STORE_ID' => $rest['store_id']), false, false, array());
                if ($arStore = $rsStore->Fetch()) {
                    $idrow = $arStore['ID'];
                }

                if (empty($idrow)) {

                    $idnom = CCatalogStoreProduct::Add($arFields);
                    echo 'ADD' . $idnom . '<br>';
                } else {
                    \CCatalogStoreProduct::Update($idrow, $arFields);
                }

            }
        }
        $reader->close();

        echo '</pre>';
        echo '<br/>+++++++++++++++++++++++++++++++++++++++++++<br/>';
    }

    public function ibapplyimport()
    {
        echo '<br/>';
        echo '++++++++++++++++++import++++++++++++++++++++++++<br/>';
        echo '<pre>';

        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xmlimport;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }

        $obElement = new \CIBlockElement();
        $obProp = new \CIBlockProperty();

        while ($reader->read()) {
            if ($reader->name == 'product' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $product = Helper::xml2array(simplexml_load_string($xml));
//                pre($product);
                $properties = [];

                foreach ($product['properties']['property'] as $property) {
                    $name = $this->getHighloadBlockName($property['id']);

                    $properties[strtoupper($name)] = $property['value'];

                    $arPictures = [];
                    if ($product['pictures']['picture']) {
                        foreach ($product['pictures']['picture'] as $picture) {
                            $path = ($_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . $picture);
                            if (is_file($path)) {
                                $arPictures[] = \CFile::MakeFileArray($path);
                            }
                        }
                    }

                    if (count($arPictures)) {
                        $arDetailPicture = array_shift($arPictures);

                        if (count($arPictures)) {
                            $tmp = $arDetailPicture;
                            $arDetailPicture = array_shift($arPictures);
                            array_unshift($arPictures, $tmp);
                        }

                        $properties['MORE_PHOTO'] = $arPictures;
                    } else {
                        $properties['MORE_PHOTO'] = ['del' => 'Y'];
                        $arDetailPicture = ['del' => 'Y'];
                    }

                    $properties['ARTICLE'] = $product['article'];
                    $rezProduct = [
                        'XML_ID' => $product['id'],
                        'DETAIL_PICTURE' => $arDetailPicture,
                        'NAME' => $product['name'],
                        'DETAIL_TEXT' => $product['descriptions']['full_description'],
                        'PREVIEW_TEXT' => $product['descriptions']['short_description'],
                        'PROPERTIES' => $properties
                    ];
                }

                $idIblock = $this->returnIdByXmlId($product['id'], $this->config['IBLOCK_ID']);
//                pre($idIblock);
                $obElement->SetPropertyValuesEx(
                    $idIblock,
                    $this->config['IBLOCK_ID'],
                    $rezProduct['PROPERTIES']
                );

                $obElement->Update($idIblock,
                    array(
                        'PREVIEW_TEXT' => $rezProduct['PREVIEW_TEXT'],
                        'DETAIL_PICTURE' => $rezProduct['DETAIL_PICTURE'],
                        'DETAIL_TEXT' => $rezProduct['DETAIL_TEXT'],
                        'ACTIVE' => 'Y',
                        'NAME' => $rezProduct['NAME'],
                        'CODE' => \CUtil::translit($product['article'], 'ru'),
                    )
                );

            }
        }
        $reader->close();

        echo '</pre>';
        echo '<br/>+++++++++++++++++++++++++++++++++++++++++++<br/>';
    }

    public function ibapplyoffers()
    {
        echo '<br/>';
        echo '++++++++++++++++++++++offers+++++++++++++++++++++<br/>';
        echo '<pre>';
        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xmloffers;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }

        $obElement = new \CIBlockElement();
        $obProp = new \CIBlockProperty();

        while ($reader->read()) {
            if ($reader->name == 'offer' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $offer = Helper::xml2array(simplexml_load_string($xml));
                if (!$offer['id']) {
                    $arErrors[] = [
                        'У предложения не указано поле id',
                        ExchangeException::$ERR_EMPTY_FIELD
                    ];
                    return;
                }
                if (!$offer['parent_id']) {
                    $arErrors[] = [
                        "У предложения $offer[id] не указано поле parent_id",
                        ExchangeException::$ERR_EMPTY_FIELD
                    ];
                    return;
                }
                if (!$offer['name']) {
                    $arErrors[] = [
                        'У предложения не указано поле name',
                        ExchangeException::$ERR_EMPTY_FIELD
                    ];
                    return;
                }

                if (!$this->elements[$offer['parent_id']]) {
                    $arErrors[] = [
                        "У предложения $offer[id] не найден родительский товар",
                        ExchangeException::$ERR_EMPTY_FIELD
                    ];
//                    return;
                }
                $properties = [];

                foreach ($offer['properties']['property'] as $property) {
                    $name = $this->getHighloadBlockName($property['id']);

                    if ($properties[$name]['TYPE'] == 'reference') {
                        if (!array_key_exists($property['value'], $this->properties[$name]['VALUES'])) {
                            $arErrors[] = [
                                "Значение $property[value] отсутствует в справочнике $name",
                                ExchangeException::$ERR_NOT_EXIST
                            ];
                            continue;
                        }
                        $property['value'] = $property['value'];
                    }

                    if ($properties[$name]['TYPE'] == 'list') {
                        $obProp = new \CIBlockProperty();
                        $arValues = $arPropertyEnum = [];
                        $arProp = $obProp->GetList(
                            [],
                            [
                                'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                                'CODE' => $name
                            ]
                        )->Fetch();

                        $rsEnum = $obProp->GetPropertyEnum($arProp['ID']);
                        while ($arEnum = $rsEnum->Fetch()) {
                            $arPropertyEnum[$arEnum['ID']] = [
                                'SORT' => $arEnum['SORT'],
                                'VALUE' => $arEnum['VALUE']
                            ];
                            $arValues[] = $arEnum['VALUE'];
                        }

                        if (!in_array($property['value'], $arValues)) {
                            $arPropertyEnum[] = [
                                'SORT' => 100,
                                'VALUE' => $property['value']
                            ];
                            $obProp->UpdateEnum($arProp['ID'], $arPropertyEnum);
                        }

                        $rsEnum = $obProp->GetPropertyEnum($arProp['ID']);
                        while ($arEnum = $rsEnum->Fetch()) {
                            $arValues[$arEnum['VALUE']] = $arEnum['ID'];
                        }

                        $property['value'] = $arValues[$property['value']];

                    }

                    if ($properties[$name]['MULTIPLE']) {
                        if (!$property['values']['value'][0])
                            $property['values']['value'] = [
                                $property['values']['value']
                            ];

                        $values = [];

                        foreach ($property['values']['value'] as $value) {
                            $values[] = $value;
                        }

                        $properties[strtoupper($name)] = $values;
                    } else {
                        $properties[strtoupper($name)] = $property['value'];
                    }
                }

                $properties['CML2_LINK'] = $this->returnIdByXmlId($offer['parent_id'], 16);

                $properties['ARTICLE'] = $offer['article'];
                $product = [
                    'XML_ID' => $offer['id'],
                    'PARENT_ID' => $offer['parent_id'],
                    'NAME' => $offer['name'],
                    'DETAIL_TEXT' => $offer['descriptions']['full_description'],
                    'PREVIEW_TEXT' => $offer['descriptions']['short_description'],
                    'PROPERTIES' => $properties
                ];
                $idofferibc = $this->returnIdByXmlId($product['XML_ID'], 17);
                $obElement->SetPropertyValuesEx(
                    $idofferibc,
                    $this->config['OFFERS_IBLOCK_ID'],
                    $product['PROPERTIES']
                );

                $obElement->Update($idofferibc,
                    [
                        'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                        'PREVIEW_TEXT' => $product['PREVIEW_TEXT'],
                        'PARENT_ID' => $product['PARENT_ID'],
                        'DETAIL_TEXT' => $product['DETAIL_TEXT'],
                        'NAME' => $product['NAME'],
                        'ACTIVE' => 'Y',
                        'CODE' => \CUtil::translit($product['NAME'], 'ru'),
                    ]
                );
            }
        }
        echo '</pre>';
        echo '<br/>+++++++++++++++++++end++++++++++++++++++++++++<br/>';
        $reader->close();


    }

    public function ibapplyreferences()
    {
        echo '<br/>';
        echo '++++++++++++++++++++++references+++++++++++++++++++++<br/>';
        echo '<pre>';

        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xmlreferences;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }

        global $APPLICATION, $DB;

        $obField = new \CUserTypeEntity();

        while ($reader->read()) {
            if ($reader->name == 'reference' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $reference = Helper::xml2array(simplexml_load_string($xml));
                $properties = [];
                foreach ($reference['properties']['property'] as $property) {
                    if (empty($property['id']))
                        throw new ExchangeException(
                            "Отсутствует поле id словаря $reference[id]",
                            ExchangeException::$ERR_EMPTY_FIELD
                        );

                    if (strtolower($property['id']) == 'code') {
                        $properties[strtolower($property['id'])] = [
                            'ID' => 'UF_XML_ID',
                            'TYPE' => $property['type'],
                            'NAME' => $property['name'],
                        ];
                    } else {
                        $properties[strtolower($property['id'])] = [
                            'ID' => 'UF_' . strtoupper($property['id']),
                            'TYPE' => $property['type'],
                            'NAME' => $property['name'],
                        ];
                    }
                }

                if (empty($properties['code']))
                    throw new ExchangeException("Отсутствует поле code у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);

                if (!$reference['elements']['element'][0])
                    $reference['elements']['element'] = [
                        $reference['elements']['element']
                    ];

                $elements = [];
                foreach ($reference['elements']['element'] as $element) {
                    $data = [];
                    foreach ($element['property'] as $property) {
                        if (empty($property['id']))
                            throw new ExchangeException("Отсутствует поле id у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);


                        if (strtoupper($property['id']) == 'CODE')
                            $data['UF_XML_ID'] = $property['value'];
                        else
                            $data['UF_' . strtoupper($property['id'])] = $property['value'];
                    }
                    $elements[] = $data;
                }

                $dictionary = [
                    'ID' => $this->getHighloadBlockName($reference['id']),
                    'TABLE_NAME' => 'b_1c_dict_' . strtolower($reference['id']),
                    'PROPERTIES' => $properties,
                    'ELEMENTS' => $elements
                ];

                $block = HL::getRow([
                    'filter' => [
                        'NAME' => $dictionary['ID']
                    ]
                ]);
                if ($block) {
                    $HLBLOCK_ID = $block['ID'];
                } else {
                    $res = HL::add([
                        'NAME' => $dictionary['ID'],
                        'TABLE_NAME' => $dictionary['TABLE_NAME']
                    ]);
                    $HLBLOCK_ID = $res->getId();
                }

                $block = HL::getById($HLBLOCK_ID)->fetch();
                foreach ($dictionary['PROPERTIES'] as $field) {
                    $aUserFields = [
                        'ENTITY_ID' => 'HLBLOCK_' . $HLBLOCK_ID,
                        'FIELD_NAME' => $field['ID'],
                        'USER_TYPE_ID' => $field['TYPE'],
                        'EDIT_FORM_LABEL' => [
                            'ru' => $field['NAME'],
                        ],
                        'LIST_COLUMN_LABEL' => [
                            'ru' => $field['NAME'],
                        ],
                        'LIST_FILTER_LABEL' => [
                            'ru' => $field['NAME'],
                        ],
                        'ERROR_MESSAGE' => [
                            'ru' => $field['NAME'],
                        ]
                    ];

                    $arField = $obField->GetList([], [
                        'ENTITY_ID' => 'HLBLOCK_' . $HLBLOCK_ID,
                        'FIELD_NAME' => $field['ID'],
                    ])->Fetch();

                    if ($arField)
                        $obField->Update($arField['ID'], $aUserFields);
                    else
                        $ID = $obField->Add($aUserFields);

                    if ($ex = $APPLICATION->GetException())
                        throw new ExchangeException($ex->GetString(), ExchangeException::$ERR_CREATE_UPDATE);

                }

                $class = HL::compileEntity($block)->getDataClass();
                foreach ($dictionary['ELEMENTS'] as $data) {
                    if (count($data)) {
                        $row = $class::getRow([
                            'filter' => [
                                'UF_XML_ID' => $data['UF_XML_ID']
                            ]
                        ]);
                        if ($row) {
                            $class::update($row['ID'], $data);
                        } else {
                            $class::add($data);
                        }
                    }
                }
            }
        }

        $reader->close();

        echo '</pre>';
        echo '<br/>+++++++++++++++++++++++++++++++++++++++++++<br/>';
    }

    public function ibapplyrests2()
    {
        echo '<br/>';
        echo '++++++++++++++++++++++rests2+++++++++++++++++++++<br/>';
        echo '<pre>';

        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xml2;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }

        while ($reader->read()) {
            if ($reader->name == 'rest' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $rest = Helper::xml2array(simplexml_load_string($xml));

                $product_id = returnIdOfferIdByXmlId($rest['product_id']);

                $arFields = Array(
                    "PRODUCT_ID" => $product_id,
                    "STORE_ID" => $rest['store_id'],
                    "AMOUNT" => $rest['quantity'],
                );

                $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $product_id, 'STORE_ID' => $rest['store_id']), false, false, array());
                if ($arStore = $rsStore->Fetch()) {
                    $idrow = $arStore['ID'];
                }

                CCatalogStoreProduct::Update($idrow, $arFields);
            }
        }
        $reader->close();

        echo '</pre>';
        echo '<br/>+++++++++++++++++++++++++++++++++++++++++++<br/>';
    }

    public function ibapplystores()
    {
        echo '<br/>';
        echo '++++++++++++++++++++++stores+++++++++++++++++++++<br/>';
        echo '<pre>';

        $reader = new \XMLReader();
        $url = $_SERVER['DOCUMENT_ROOT'] . $this->config[PATH] . $this->xmlstores;
        if (!$reader->open($url)) {
            die("Failed to open '$url'");
        }
        Loader::includeModule('highloadblock');
        Loader::includeModule('catalog');

        while ($reader->read()) {
            if ($reader->name == 'rest' && $reader->nodeType == 1) {
                $xml = $reader->readOuterXml();
                $rest = Helper::xml2array(simplexml_load_string($xml));

                $arAddress = [];
                if ($store['address']['city'])
                    $arAddress[] = $store['address']['city'];
                if ($store['address']['street'])
                    $arAddress[] = $store['address']['street'];
                if ($store['address']['home'])
                    $arAddress[] = $store['address']['home'];

                if (count($arAddress))
                    $address = implode(', ', $arAddress);
                else
                    $address = 'Отсутствует';

                $arSubways = $arPhones = [];
                $block = HL::getRow([
                    'filter' => [
                        'NAME' => 'Metro'
                    ]
                ]);

                $arValues = [];
                $class = HL::compileEntity($block)->getDataClass();
                $rsValues = $class::getList();
                while ($arValue = $rsValues->fetch()) {
                    $arValues[$arValue['UF_XML_ID']] = $arValue;
                }

                if (!$store['address']['subways']['subway'][0])
                    $store['address']['subways']['subway'] = [
                        $store['address']['subways']['subway']
                    ];

                foreach ($store['address']['subways']['subway'] as $subway) {
                    if (!$arValues[$subway['id']])
                        continue;
                    $arSubways[] = $arValues[$subway['id']]['ID'];
                }

                if (!is_array($store['phones']['phone']))
                    $store['phones']['phone'] = [
                        $store['phones']['phone']
                    ];

                foreach ($store['phones']['phone'] as $phone) {
                    $arPhones[] = $phone;
                }

                $arPictures = [];
                if ($store['pictures']['picture']) {
                    foreach ($store['pictures']['picture'] as $picture) {
                        $path = ($_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . $picture);
                        if (is_file($path)) {
                            $arPictures[$picture] = \CFile::MakeFileArray($path);
                        }
                    }
                }
                ksort($arPictures, SORT_STRING);
                $arPictures = array_values($arPictures);

                $arScheme = [];
                if ($store['address']['scheme']) {
                    $path = ($_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . $store['address']['scheme']);
                    if (is_file($path)) {
                        $arScheme = \CFile::MakeFileArray($path);
                    }
                }


                $store['worktime'] = '';

                foreach ($store['modes'] as $mode) {
                    $store['worktime'] = (string)$mode;

                }

                if (count($arPictures)) {
                    $arDetailPicture = array_shift($arPictures);
                }

                $store = [
                    'XML_ID' => $store['id'],
                    'TITLE' => $store['name'],
                    'GPS_N' => $store['coordinates']['latitude'],
                    'GPS_S' => $store['coordinates']['longitude'],
                    'ADDRESS' => $address,
                    'SCHEDULE' => $store['worktime'],
                    'UF_METRO' => $arSubways,
                    'UF_CITY' => $store['address']['city'],
                    'UF_STREET' => $store['address']['street'],
                    'UF_HOME' => $store['address']['home'],
                    'UF_STATUS' => $store['status'],
                    'UF_PHONES' => $arPhones,
                    'UF_PICTURES' => $arPictures,
                    'IMAGE_ID' => $arDetailPicture,
                    'UF_DRIVING' => $arScheme,

                ];

                $ID = returnIdStoreByXmlId($store['XML_ID']);
                if ($ID) {
                    $arStore = StoreTable::getRowById($ID);
                    $iImage = false;
                    if ($store['IMAGE_ID']) {
                        \CFile::Delete($store['IMAGE_ID']);
                        if ($store['IMAGE_ID']) {
                            $iImage = \CFile::SaveFile($store['IMAGE_ID'], 'stores');
                        }
                    }
                    $store['IMAGE_ID'] = $iImage;

                    //unset ($store['ADDRESS']);
                    unset ($store['UF_CITY']);

                    $rs = StoreTable::update($ID, $store);


                } else
                    $rs = StoreTable::add($store);
            }
        }
        $reader->close();

        echo '</pre>';
        echo '<br/>+++++++++++++++++++++++++++++++++++++++++++<br/>';
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

    public static function returnIdOfferIdByXmlId($offersIblockID)
    {

        \CModule::IncludeModule('iblock');

        $arFindElFilter = array(
            'IBLOCK_ID' => 16,
            'XML_ID' => $offersIblockID,
        );

        $el = new \CIBlockElement;

        $rsFindEl = $el->GetList(
            array(),
            $arFindElFilter,
            false,
            false,
            array()
        );

        while ($findEl = $rsFindEl->Fetch()) {
            return $findEl['ID'];
        }

    }

    public static function returnIdStoreByXmlId($XMLID)
    {
        \CModule::IncludeModule('catalog');
        $arFilter = Array('XML_ID' => $XMLID);
        $arSelectFields = Array("ID");
        $res = \CCatalogStore::GetList(Array(), $arFilter, false, false, $arSelectFields);
        if ($arRes = $res->GetNext()) return $arRes['ID'];
        return 'no store';
    }

    public static function returnIdByXmlId($offersIblockID, $IblockID)
    {

        \CModule::IncludeModule('iblock');

        $arFindElFilter = array(
            'IBLOCK_ID' => $IblockID,
            'XML_ID' => $offersIblockID,
        );

        $el = new \CIBlockElement;

        $rsFindEl = $el->GetList(
            array(),
            $arFindElFilter,
            false,
            false,
            array()
        );

        while ($findEl = $rsFindEl->Fetch()) {
            return $findEl['ID'];
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

        if (!Loader::includeModule('iblock') || !Loader::includeModule('likee.location'))
            return false;

        $arConfig = Config::get();

        if (is_null($iblockId))
            $iblockId = $arConfig['IBLOCK_ID'];

        if (is_null($offersIblockID))
            $offersIblockID = $arConfig['OFFERS_IBLOCK_ID'];

        $sEntityID = 'IBLOCK_' . $iblockId . '_SECTION';
        $iblockId = intval($iblockId);

        $arMaxDepthSection = \CIBlockSection::GetList(Array('depth_level' => 'desc'), Array(
            'IBLOCK_ID' => $iblockId,
        ), false, Array('DEPTH_LEVEL'))->Fetch();

        $maxLevel = intval($arMaxDepthSection['DEPTH_LEVEL']);

        $arLocations = Location::all();
        $arLocationIDS = [];

        foreach ($arLocations as $arLocation) {
            if (!empty($arLocation['STORES']))
                $arLocationIDS[$arLocation['ID']] = array_column($arLocation['STORES'], 'ID');
        }

        //города, которые менежер добавил вручную, не привязаны к складу
        $arAdditionalLocations = Location::getAdditionalCities();
        foreach ($arAdditionalLocations as $arAdditionalLocation) {
            if (!array_key_exists($arAdditionalLocation['ID'], $arLocationIDS))
                $arLocationIDS[$arAdditionalLocation['ID']] = $arAdditionalLocation['STORES'];
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
                    if (empty($arStoresIDS)) continue;

                    $rsSubItems = \CIBlockElement::GetList(Array('id' => 'asc'), Array(
                        'IBLOCK_ID' => $iblockId,
                        'ACTIVE' => 'Y',
                        'SECTION_ID' => $arSection['ID'],
                        'INCLUDE_SUBSECTIONS' => 'N',
                        '>PROPERTY_MINIMUM_PRICE' => 0,
                        'CATALOG_AVAILABLE' => 'Y',
                        '!DETAIL_PICTURE' => false
                    ), false, false, Array('ID'));

                    while ($arSubItem = $rsSubItems->Fetch()) {
                        $rsOffers = \CIBlockElement::GetList(Array('id' => 'asc'), Array(
                            'IBLOCK_ID' => $offersIblockID,
                            'ACTIVE' => 'Y',
                            '=PROPERTY_CML2_LINK' => $arSubItem['ID'],
                            'PROPERTY_STORES' => $arStoresIDS,
                            'CATALOG_AVAILABLE' => 'Y',
                            '>CATALOG_QUANTITY' => 0
                        ), false, array('nTopCount' => 1), Array('ID'));

                        if ($rsOffers->SelectedRowsCount() > 0) {
                            $arCityLink[] = $iLocationID;
                            break;
                        }
                    }
                }

                $USER_FIELD_MANAGER->Update($sEntityID, $arSection['ID'], Array(
                    'UF_CITY_LINK' => array_unique($arCityLink)
                ));
            }

            $maxLevel--;
        }

        $CACHE_MANAGER->ClearByTag('bitrix:menu');

        return true;
    }
}