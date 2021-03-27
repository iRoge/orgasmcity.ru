<?php
/**
 * User: Azovcev Artem
 * Date: 07.12.16
 * Time: 15:53
 */

namespace Likee\Site\Events;

use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\Product\Sku;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\StoreProductTable;

/**
 * Класс для обработки событий импорта 1с. Обновляет информацию о товарах после импорта.
 *
 * @package Likee\Site\Events
 */
class Import
{
    /**
     *  Id блока товаров
     */
    const REAL_IBLOCK_ID = 16;

    /**
     * Id торгового предложения
     */
    const REAL_OFFERS_IBLOCK_ID = 19;

    /**
     * Торговые предложения
     */
    static $offers;

    /**
     * Товары
     */
    static $products;

    /**
     *  Xml товара
     */
    static $product2xml;

    /**
     * @var array Количество товара
     */
    static $quantity = [];

    /**
     * Обнавляет информацию о товарах
     *
     * @param string $sTask Имя задачи
     */
    public static function updateSections($sTask)
    {

        if ($sTask != 'import')
            return;

        if (!Loader::includeModule('likee.exchange'))
            return;

        $arConfig['IBLOCK_ID'] = self::REAL_IBLOCK_ID;

        $rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $arConfig['IBLOCK_ID']
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_RHODEPRODUCT',
                'PROPERTY_VID',
                'PROPERTY_TYPEPRODUCT',
            ]
        );

        /**
         * Возвращает секцию
         *
         * @param $iBlockID
         * @param bool $iSection
         * @return array
         */
        function getSections($iBlockID, $iSection = false)
        {
            $rsSections = \CIBlockSection::GetList(
                [],
                [
                    'IBLOCK_ID' => $iBlockID,
                    'SECTION_ID' => $iSection
                ],
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'EXTERNAL_ID',
                ]
            );
            $arSections = [];
            while ($arSection = $rsSections->Fetch()) {
                $arSections[$arSection['EXTERNAL_ID']] = [
                    'ID' => $arSection['ID'],
                    'CHILDS' => getSections($iBlockID, $arSection['ID'])
                ];
            }
            return $arSections;
        }

        /**
         * Создает секцию
         *
         * @param $arData
         * @return int
         */
        function createSection($arData)
        {
            if ($arData['NAME'] == 'Женская обувь' || $arData['NAME'] == 'Мужская обувь')
                return 0;

            //запрет на создание раздела в корне
            if (empty($arData['IBLOCK_SECTION_ID']))
                return 0;

            $obSection = new \CIBlockSection();
            $ID = $obSection->Add([
                'ACTIVE' => 'Y',
                'IBLOCK_SECTION_ID' => $arData['IBLOCK_SECTION_ID'],
                'IBLOCK_ID' => $arData['IBLOCK_ID'],
                'NAME' => ucfirst($arData['NAME']),
                'EXTERNAL_ID' => $arData['EXTERNAL_ID'],
                'CODE' => \CUtil::translit($arData['NAME'], 'ru')
            ]);

            return $ID;
        }

        $arSections = getSections($arConfig['IBLOCK_ID']);

        $Rhodeproduct = HL::getRow([
            'filter' => [
                'NAME' => 'Rhodeproduct'
            ]
        ]);
        $Vid = HL::getRow([
            'filter' => [
                'NAME' => 'Vid'
            ]
        ]);
        $Typeproduct = HL::getRow([
            'filter' => [
                'NAME' => 'Typeproduct'
            ]
        ]);

        $rsRhodeproduct = HL::compileEntity($Rhodeproduct)->getDataClass();
        $rsVid = HL::compileEntity($Vid)->getDataClass();
        $rsTypeproduct = HL::compileEntity($Typeproduct)->getDataClass();

        $arRhodeproducts = $arVids = $arTypeproducts = [];

        $rsRhodeproduct = $rsRhodeproduct::getList();
        $rsVid = $rsVid::getList();
        $rsTypeproduct = $rsTypeproduct::getList();

        while ($arRhodeproduct = $rsRhodeproduct->fetch())
            $arRhodeproducts[$arRhodeproduct['UF_XML_ID']] = $arRhodeproduct['UF_NAME'];

        while ($arVid = $rsVid->fetch())
            $arVids[$arVid['UF_XML_ID']] = $arVid['UF_NAME'];

        while ($arTypeproduct = $rsTypeproduct->fetch())
            $arTypeproducts[$arTypeproduct['UF_XML_ID']] = $arTypeproduct['UF_NAME'];


        $obElement = new \CIBlockElement();
        while ($arElement = $rsElements->Fetch()) {
            $sRhodeproduct = $arElement['PROPERTY_RHODEPRODUCT_VALUE']; //род изделия 000000199
            $sVid = $arElement['PROPERTY_VID_VALUE'];
            $sTypeproduct = $arElement['PROPERTY_TYPEPRODUCT_VALUE']; //Вид изделия МО0011272

            $arRhodeSection = &$arSections[$sRhodeproduct];

            if ($arRhodeSection) {
                $arVidSection = &$arRhodeSection['CHILDS'][$sVid];

                if ($arVidSection) {
                    $arTypeSection = &$arVidSection['CHILDS'][$sTypeproduct];

                    if ($arTypeSection) {
                        $iType = $arTypeSection['ID'];
                    } else {
                        $iType = createSection([
                            'IBLOCK_SECTION_ID' => $arVidSection['ID'],
                            'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                            'NAME' => $arTypeproducts[$sTypeproduct],
                            'EXTERNAL_ID' => $sTypeproduct
                        ]);

                        $arVidSection['CHILDS'][$sTypeproduct] = [
                            'ID' => $iType,
                            'CHILDS' => []
                        ];
                    }

                    $obElement->Update($arElement['ID'], ['IBLOCK_SECTION_ID' => $iType]);
                } else {
                    $iVid = createSection([
                        'IBLOCK_SECTION_ID' => $arRhodeSection['ID'],
                        'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                        'NAME' => $arVids[$sVid],
                        'EXTERNAL_ID' => $sVid
                    ]);
                    $arRhodeproducts['CHILDS'][$sVid] = [
                        'ID' => $iVid,
                        'CHILDS' => []
                    ];
                    $iType = createSection([
                        'IBLOCK_SECTION_ID' => $iVid,
                        'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                        'NAME' => $arTypeproducts[$sTypeproduct],
                        'EXTERNAL_ID' => $sTypeproduct
                    ]);
                    $arRhodeproducts['CHILDS'][$sVid]['CHILDS'][$sTypeproduct] = [
                        'ID' => $iType,
                        'CHILDS' => []
                    ];

                    $obElement->Update($arElement['ID'], ['IBLOCK_SECTION_ID' => $iType]);
                }
            } else {
                $iRhode = createSection([
                    'IBLOCK_SECTION_ID' => false,
                    'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                    'NAME' => $arRhodeproducts[$sRhodeproduct],
                    'EXTERNAL_ID' => $sRhodeproduct
                ]);

                $arSections[$sRhodeproduct] = [
                    'ID' => $iRhode,
                    'CHILDS' => []
                ];

                $iVid = createSection([
                    'IBLOCK_SECTION_ID' => $iRhode,
                    'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                    'NAME' => $arVids[$sVid],
                    'EXTERNAL_ID' => $sVid
                ]);

                $arSections[$sRhodeproduct]['CHILDS'][$sVid] = [
                    'ID' => $iVid,
                    'CHILDS' => []
                ];

                $iType = createSection([
                    'IBLOCK_SECTION_ID' => $iVid,
                    'IBLOCK_ID' => $arConfig['IBLOCK_ID'],
                    'NAME' => $arTypeproducts[$sTypeproduct],
                    'EXTERNAL_ID' => $sTypeproduct
                ]);

                $arSections['CHILDS'][$sVid]['CHILDS'][$sTypeproduct] = [
                    'ID' => $iType,
                    'CHILDS' => []
                ];

                $obElement->Update($arElement['ID'], ['IBLOCK_SECTION_ID' => $iType]);
            }
        }
    }

    /**
     * Обновление полей товара
     *
     * @param string $sTask Имя задачи
     * @param array $arProduct Товар
     */
    public static function productAddUpdate($sTask, $arProduct)
    {
        if ($sTask == 'offers') {
            if (!self::$offers) {
                $rsElements = ElementTable::getList([
                    'select' => ['ID', 'XML_ID'],
                    'filter' => [
                        'IBLOCK_ID' => self::REAL_OFFERS_IBLOCK_ID
                    ]
                ]);
                while ($arElement = $rsElements->fetch()) {
                    self::$offers[$arElement['XML_ID']] = $arElement['ID'];
                }
            }
            if (!self::$product2xml) {
                $rsElements = \CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => self::REAL_IBLOCK_ID
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'IBLOCK_ID',
                        'PROPERTY_REAL_XML_ID'
                    ]
                );
                while ($p = $rsElements->Fetch()) {
                    if (!is_array($p['PROPERTY_REAL_XML_ID_VALUE']))
                        $p['PROPERTY_REAL_XML_ID_VALUE'] = [
                            $p['PROPERTY_REAL_XML_ID_VALUE']
                        ];

                    foreach ($p['PROPERTY_REAL_XML_ID_VALUE'] as $xml) {
                        self::$product2xml[$xml] = $p['ID'];
                    }
                }
            }

            if (!self::$product2xml[$arProduct['PARENT_ID']]) return;

            $arProduct['IBLOCK_ID'] = self::REAL_OFFERS_IBLOCK_ID;
            $arProduct['PROPERTY_VALUES']['CML2_LINK'] = self::$product2xml[$arProduct['PARENT_ID']];
            $arProduct['PROPERTY_VALUES']['REAL_PARENT_XML_ID'] = $arProduct['PARENT_ID'];

            $obElement = new \CIBlockElement();
            if (self::$offers[$product['XML_ID']]) {
                $obElement->Update(self::$offers[$product['XML_ID']], $arProduct);
            } else {
                $obElement->Add($arProduct);
            }
        } elseif ($sTask == 'import') {
            if (!self::$products) {
                $rsElements = \CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => self::REAL_IBLOCK_ID
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'NAME',
                        'IBLOCK_ID',
                        'PROPERTY_LINE',
                        'PROPERTY_SHOE',
                        'PROPERTY_MODEL',
                        'PROPERTY_MANUFACTURER',
                        'PROPERTY_REAL_XML_ID'
                    ]
                );
                while ($p = $rsElements->Fetch()) {
                    $md5 = md5(
                        $p['PROPERTY_LINE_VALUE'] . $p['PROPERTY_SHOE_VALUE'] .
                        $p['PROPERTY_MODEL_VALUE'] . $p['PROPERTY_MANUFACTURER_VALUE']
                    );
                    if (self::$products[$md5]) {
                        self::$products[$md5]['PROPERTY_REAL_XML_ID_VALUE'] = array_merge(self::$products[$md5]['PROPERTY_REAL_XML_ID_VALUE'], $p['PROPERTY_REAL_XML_ID_VALUE']);
                        self::$products[$md5]['PROPERTY_COLORSFILTER_VALUE'] = array_merge(self::$products[$md5]['PROPERTY_COLORSFILTER_VALUE'], $p['PROPERTY_COLORSFILTER_VALUE']);
                    } else
                        self::$products[$md5] = $p;
                }
            }

            $arProduct['IBLOCK_ID'] = self::REAL_IBLOCK_ID;
            $xml = $arProduct['XML_ID'];

            if (!$arProduct['PROPERTY_VALUES']['LINE'] || !$arProduct['PROPERTY_VALUES']['SHOE'] || !$arProduct['PROPERTY_VALUES']['MODEL'] || !$arProduct['PROPERTY_VALUES']['MANUFACTURER']) return;

            $arProduct['XML_ID'] = md5(
                $arProduct['PROPERTY_VALUES']['LINE'] . $arProduct['PROPERTY_VALUES']['SHOE'] .
                $arProduct['PROPERTY_VALUES']['MODEL'] . $arProduct['PROPERTY_VALUES']['MANUFACTURER']
            );

            $arName = explode(' ', $arProduct['NAME']);
            array_pop($arName);
            $arProduct['NAME'] = implode(' ', $arName);

            $obElement = new \CIBlockElement();
            if (self::$products[$arProduct['XML_ID']]) {
                $arXML = array_flip(self::$products[$arProduct['XML_ID']]['PROPERTY_REAL_XML_ID_VALUE']);
                $arXML[$xml] = $xml;
                $arProduct['PROPERTY_VALUES']['REAL_XML_ID'] = array_flip($arXML);
                $arXML = array_flip(self::$products[$arProduct['XML_ID']]['PROPERTY_COLORSFILTER_VALUE']);
                foreach ($arProduct['PROPERTY_VALUES']['COLORSFILTER'] as $colorFilter) {
                    $arXML[$colorFilter] = $colorFilter;
                }
                $arProduct['PROPERTY_VALUES']['COLORSFILTER'] = array_values(array_flip($arXML));

                $arXML = array_flip(self::$products[$arProduct['XML_ID']]['PROPERTY_SIZERANGE_VALUE']);
                foreach ($arProduct['PROPERTY_VALUES']['SIZERANGE'] as $sizeRange) {
                    $arXML[$sizeRange] = $sizeRange;
                }
                $arProduct['PROPERTY_VALUES']['SIZERANGE'] = array_values(array_flip($arXML));

                $obElement->Update(self::$products[$arProduct['XML_ID']]['ID'], $arProduct);
            } else {
                $arProduct['PROPERTY_VALUES']['COLORSFILTER'] = [$arProduct['PROPERTY_VALUES']['COLORSFILTER']];
                $ID = $obElement->Add($arProduct);
                self::$products[$arProduct['XML_ID']] = [
                    'ID' => $ID,
                    'PROPERTY_REAL_XML_ID_VALUE' => [$xml],
                    'PROPERTY_COLORSFILTER_VALUE' => [$arProduct['PROPERTY_VALUES']['COLORSFILTER']],
                    'PROPERTY_SIZERANGE_VALUE' => [$arProduct['PROPERTY_VALUES']['SIZERANGE']],
                ];
            }

        }
    }

    /**
     * Обновление свойств товара
     *
     * @param string $sTask Имя задачи
     * @param array $arProperty Свойства товара
     */
    public static function propertyAddUpdate($sTask, $arProperty)
    {
        if ($sTask == 'import') {
            $arProperty['IBLOCK_ID'] = self::REAL_IBLOCK_ID;
        } elseif ($sTask == 'offers') {
            $arProperty['IBLOCK_ID'] = self::REAL_OFFERS_IBLOCK_ID;
        }

        $obProp = new \CIBlockProperty();
        $rsProp = $obProp->GetList(
            [],
            [
                'CODE' => $arProperty['CODE'],
                'IBLOCK_ID' => $arProperty['IBLOCK_ID']
            ]
        );
        if ($arProp = $rsProp->Fetch()) {
            $obProp->Update($arProp['ID'], $arProperty);
        } else {
            $obProp->Add($arProperty);
        }
    }

    /**
     * Добавляет поля к инфоблоку
     *
     * @param string $sTask Имя задачи
     */
    public static function createFields($sTask)
    {

        if ($sTask == 'import') {
            $arProperty = [
                'PROPERTY_TYPE' => 'S',
                'MULTIPLE' => 'Y',
                'NAME' => 'Реальный XML_ID',
                'CODE' => 'REAL_XML_ID',
                'IBLOCK_ID' => self::REAL_IBLOCK_ID
            ];

            $obProp = new \CIBlockProperty();
            $rsProp = $obProp->GetList(
                [],
                [
                    'CODE' => $arProperty['CODE'],
                    'IBLOCK_ID' => $arProperty['IBLOCK_ID'],
                ]
            );

            if ($arProp = $rsProp->Fetch()) {
                $obProp->Update($arProp['ID'], $arProperty);
            } else {
                $obProp->Add($arProperty);
            }
        } elseif ($sTask == 'offers') {
            $arProperty = [
                'PROPERTY_TYPE' => 'S',
                'NAME' => 'Реальный родительский XML_ID',
                'CODE' => 'REAL_PARENT_XML_ID',
                'IBLOCK_ID' => self::REAL_OFFERS_IBLOCK_ID
            ];

            $obProp = new \CIBlockProperty();
            $rsProp = $obProp->GetList(
                [],
                [
                    'CODE' => $arProperty['CODE'],
                    'IBLOCK_ID' => $arProperty['IBLOCK_ID'],
                ]
            );

            if ($arProp = $rsProp->Fetch()) {
                $obProp->Update($arProp['ID'], $arProperty);
            } else {
                $obProp->Add($arProperty);
            }
        }
    }

    /**
     *  Обновление цены торгового предложения
     *
     * @param string $sTask Имя задачи
     * @param integer $xmlId XmlId торгового предложения
     * @param array $price Цена
     */
    public static function priceAddUpdate($sTask, $xmlId, $price)
    {

        if (!self::$offers) {
            $rsElements = ElementTable::getList([
                'select' => ['ID', 'XML_ID'],
                'filter' => [
                    'IBLOCK_ID' => self::REAL_OFFERS_IBLOCK_ID
                ]
            ]);
            while ($arElement = $rsElements->fetch()) {
                self::$offers[$arElement['XML_ID']] = $arElement['ID'];
            }
        }

        $arPrice = PriceTable::getRow(
            [
                'filter' => [
                    'CATALOG_GROUP_ID' => $price['CATALOG_GROUP_ID'],
                    'PRODUCT_ID' => self::$offers[$xmlId]
                ]
            ]
        );

        if ($arPrice) {
            $rs = PriceTable::update(self::$offers[$xmlId], ['PRICE' => $price['PRICE']]);
        } else {
            $rs = PriceTable::add([
                'CATALOG_GROUP_ID' => $price['CATALOG_GROUP_ID'],
                'PRODUCT_ID' => self::$offers[$xmlId],
                'PRICE' => $price['PRICE'],
                'PRICE_SCALE' => $price['PRICE_SCALE'],
                'CURRENCY' => $price['CURRENCY'],
            ]);
        }
    }

    /**
     * Обновлении остатков
     *
     * @param string $sTask Имя задачи
     * @param integer $xmlId XmlId торгового предложения
     * @param array $rest Остаток
     */
    public static function restAddUpdate($sTask, $xmlId, $rest)
    {

        if (!self::$quantity) {
            //загрузка остатков
            $rsQuantities = StoreProductTable::getList();
            while ($arQuantity = $rsQuantities->fetch()) {
                self::$quantity[$arQuantity['PRODUCT_ID']][$arQuantity['STORE_ID']] = $arQuantity;
            }
        }

        if (!self::$offers) {
            $rsElements = ElementTable::getList([
                'select' => ['ID', 'XML_ID'],
                'filter' => [
                    'IBLOCK_ID' => self::REAL_OFFERS_IBLOCK_ID
                ]
            ]);
            while ($arElement = $rsElements->fetch()) {
                self::$offers[$arElement['XML_ID']] = $arElement['ID'];
            }
        }

        $arRow = self::$quantity[self::$offers[$xmlId]][$rest['STORE_ID']];

        if (!self::$offers[$xmlId])
            return;

        if ($arRow) {
            $rs = StoreProductTable::update(
                $arRow['ID'],
                [
                    'AMOUNT' => $arRow['AMOUNT']
                ]
            );
        } else {
            $rs = StoreProductTable::add(
                [
                    'AMOUNT' => $rest['AMOUNT'],
                    'PRODUCT_ID' => self::$offers[$xmlId],
                    'STORE_ID' => $rest['STORE_ID']
                ]
            );
        }
    }

    /**
     * Обновляет доступность товара
     *
     * @param string $sTask Имя задачи
     * @param array $arChanges Изменения
     */
    public static function restUpdateAvailable($sTask, $arChanges)
    {

        if (!self::$offers) {
            $rsElements = ElementTable::getList([
                'select' => ['ID', 'XML_ID'],
                'filter' => [
                    'IBLOCK_ID' => self::REAL_OFFERS_IBLOCK_ID
                ]
            ]);
            while ($arElement = $rsElements->fetch()) {
                self::$offers[$arElement['XML_ID']] = $arElement['ID'];
            }
        }

        foreach ($arChanges as $arChange) {
            $rsQuantity = StoreProductTable::getList([
                'filter' => [
                    'PRODUCT_ID' => self::$offers[$arChange['XML_ID']]
                ]
            ]);

            $iTotal = 0;

            while ($arQuantity = $rsQuantity->fetch()) {
                $iTotal += $arQuantity['AMOUNT'];
            }
            \CCatalogProduct::Add([
                'ID' => self::$offers[$arChange['XML_ID']],
                'QUANTITY' => $iTotal,
                'TYPE' => 1
            ]);

            Sku::updateAvailable(self::$offers[$arChange['XML_ID']], self::REAL_OFFERS_IBLOCK_ID);
        }
    }
}