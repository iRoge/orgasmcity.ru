<?php

namespace Likee\Site\Helpers;

use Bitrix\Catalog\StoreProductTable;
use Bitrix\Main\Loader;
use Likee\Location\Location;
use Likee\Exchange\Reserve;

/**
 * Класс для работы с каталогоми. Содержит методы для фильтрации и валидации.
 *
 * @package Likee\Site\Helpers
 */
class Catalog
{
    /**
     * ID инфоблока каталога
     */
    const IBLOCK_CATALOG_ID = 16;
    /**
     * ID инфоблока торговых предложений
     */
    const IBLOCK_OFFERS_ID = 17;
    /**
     * ID свойства SKU
     */
    const PROPERTY_SKU_ID = 202;
    /**
     *  ID свойства STORES
     */
    const PROPERTY_STORES_ID = 262;

    /**
     * Возвращает магазины для фильтрации
     *
     * @return array Магазины
     */
    public static function getStoresForFiltration()
    {
        if (!Loader::includeModule('likee.location')) {
            return [];
        }

        $arLocation = Location::getCurrent();
        $arStores = array_column($arLocation['STORES'], 'ID');

        return array_unique(array_merge($arStores, self::getOnlineStores()));
    }

    /**
     * Возвращает активные онлайн магазины
     *
     * @return array|null Магазины
     */
    public static function getOnlineStores()
    {
        static $arOnlineStores = null;

        if (!is_null($arOnlineStores)) {
            return $arOnlineStores;
        }

        $arOnlineStores = [];
        $arLocationOnlineStores = self::getLocationOnlineStores();
        
        if (is_array($arLocationOnlineStores)) {
            $arOnlineStores = $arLocationOnlineStores;
        } elseif (Loader::includeModule('catalog')) {
            $rsStores = \CCatalogStore::GetList(
                ['ID' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'UF_ONLINE' => 1
                ],
                false,
                false,
                ['ID']
            );

            while ($arStore = $rsStores->Fetch()) {
                $arOnlineStores[] = $arStore['ID'];
            }
        }

        return $arOnlineStores;
    }
    
    public static function getLocationOnlineStores()
    {
        static $arLocationOnlineStores = null;

        if (!is_null($arLocationOnlineStores)) {
            return $arLocationOnlineStores;
        }

        $arLocationOnlineStores = false;
        
        if (Loader::includeModule('iblock') && Loader::includeModule('likee.location')) {
            $currentLocation = \Likee\Location\Location::getCurrent();
            
            $rsStores = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => 39,
                    'NAME' => $currentLocation['CITY_NAME']
                ],
                false,
                false,
                ['ID', 'PROPERTY_STORE']
            );
            while ($store = $rsStores->Fetch()) {
                if (false === $arLocationOnlineStores) {
                    $arLocationOnlineStores = [];
                }
                if (intval($store['PROPERTY_STORE_VALUE'])) {
                    $arLocationOnlineStores[] = $store['PROPERTY_STORE_VALUE'];
                }
            }
            unset($rsStores, $currentLocation);
        }

        return $arLocationOnlineStores;
    }

    public static function getLocationOnlineStoresDelivery()
    {
        static $arLocationOnlineStores = null;

        if (!is_null($arLocationOnlineStores)) {
            return $arLocationOnlineStores;
        }

        $arLocationOnlineStores = false;

        if (Loader::includeModule('iblock')) {
            $currentLocation = \Likee\Location\Location::getCurrent();
            $currentLocationAll = \Likee\Location\Location::all();

            $rsStores = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => 39,
                ],
                false,
                false,
                ['ID', 'PROPERTY_STORE','NAME','PROPERTY_REGION']
            );
            //Список складов и их ID с которых возможна доставка
//            foreach ($currentLocationAll as $shop){
//                foreach ($shop['STORES'] as $store){
//                    $curStoreId[$store['ID']]=$store['TITLE'];
//                }
//            }
            while ($store = $rsStores->Fetch()) {
                if (false === $arLocationOnlineStores) {
                    $arLocationOnlineStores = [];
                }
                if (intval($store['PROPERTY_STORE_VALUE'])) {
                    $arLocationOnlineStores[$store['NAME']][] = $store['PROPERTY_STORE_VALUE'];
                }
            }
            unset($rsStores, $currentLocation);
        }
        return $arLocationOnlineStores;
    }

    public static function getLocationOnlineStoresDeliveryRegion()
    {
        static $arLocationOnlineStores = null;

        if (!is_null($arLocationOnlineStores)) {
            return $arLocationOnlineStores;
        }

        $arLocationOnlineStores = false;

        if (Loader::includeModule('iblock')) {
            $currentLocation = \Likee\Location\Location::getCurrent();
            $currentLocationAll = \Likee\Location\Location::all();

            $rsStores = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => 39,
                ],
                false,
                false,
                ['ID', 'PROPERTY_STORE','NAME','PROPERTY_REGION']
            );
            while ($store = $rsStores->Fetch()) {
                if (false === $arLocationOnlineStores) {
                    $arLocationOnlineStores = [];
                }
                if ($store['PROPERTY_REGION_VALUE']) {
                    $arLocationOnlineStoresRegion[$store['PROPERTY_REGION_VALUE']][$store['NAME']][] = $store['PROPERTY_STORE_VALUE'];
                }
            }
            unset($rsStores, $currentLocation);
        }
        return $arLocationOnlineStoresRegion;
    }

    public static function getDeliveryStores()
    {
        static $arDeliveryStores = null;

        if (!is_null($arDeliveryStores)) {
            return $arDeliveryStores;
        }

        $arDeliveryStores = [];
        if (Loader::includeModule('catalog')) {
            $rsStores = \CCatalogStore::GetList(
                ['ID' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'ISSUING_CENTER' => 'Y'
                ],
                false,
                false,
                ['ID']
            );

            while ($arStore = $rsStores->Fetch()) {
                $arDeliveryStores[] = $arStore['ID'];
            }
        }

        return $arDeliveryStores;
    }

    /**
     * Возвращает стандартный фильтр или дополняет имеющийся
     *
     * @param array $arFilter Фильтр
     * @return array Стандартный/дополненный фильтр
     */
    public static function getDefaultFilter($arFilter = [])
    {
        $arStores = static::getStoresForFiltration();

        $arDefaultFilter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => self::IBLOCK_CATALOG_ID,
            '!DETAIL_PICTURE' => false,
            'CATALOG_AVAILABLE' => 'Y',
            '>PROPERTY_MINIMUM_PRICE' => 0,
            'OFFERS' => [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => self::IBLOCK_OFFERS_ID,
                'CATALOG_AVAILABLE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'PROPERTY_STORES' => $arStores
            ]
        ];

        if ($arFilter && is_array($arFilter)) {
            $arDefaultFilter = array_merge($arDefaultFilter, $arFilter);
            if (array_key_exists('OFFERS', $arFilter) && is_array($arFilter['OFFERS'])) {
                $arDefaultFilter['OFFERS'] = array_column($arDefaultFilter['OFFERS'], $arFilter['OFFERS']);
            }
        }

        $arDefaultFilter['=ID'] = \CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arDefaultFilter['OFFERS']);
        unset($arDefaultFilter['OFFERS']);

        return $arDefaultFilter;
    }

    /**
     * Проверяет на возможность резервирования продукта
     *
     * Помимо битриксовой таблицы со складами, используется проверка на зарезервированный товар в отдельной таблице.
     * ВНИМАНИЕ! В список аргументов может передаваться массив магазинов. Если этот массив передан, то идет дополнительное ограничение по ID
     * @param int $iProductID - Id продукта
     * @param null|array $arAdditionalFilter
     * @return bool
     * @link http://redmine.crealink.ru/issues/7591
     */
    public static function productCanBeReserved($iProductID, $arAdditionalFilter = null)
    {
        $iProductID = intval($iProductID);

        if (!Loader::includeModule('likee.exchange')) {
            return false;
        }

        if ($iProductID <= 0) {
            return false;
        }

        $arOnlineStores = self::getOnlineStores();
        $arDeliveryStores = self::getDeliveryStores();

        $arStoresFilter = [
            'PRODUCT_ID' => $iProductID,
            '>AMOUNT' => 0
        ];

        $arProductStores = [];

        $rsAmount = StoreProductTable::getList([
            'filter' => $arStoresFilter,
            'select' => ['STORE_ID', 'AMOUNT']
        ]);

        while ($arAmount = $rsAmount->fetch()) {
            $reservedAmount = Reserve::getItemReservedCount($iProductID, $arAmount['STORE_ID']);
            //Количество должно быть положительным плюс, если передан дополнительный фильтр, быть в этом фильтре.
            if ($arAmount['AMOUNT'] - $reservedAmount > 0 && (!empty($arAdditionalFilter) && in_array($arAmount['STORE_ID'], $arAdditionalFilter))) {
                $arProductStores[] = $arAmount['STORE_ID'];
            }
        }


        //Проверка, не выкуплены ли случайно все остатки, если это онлайн магазин
        $allBought = false;
        $bought = Reserve::getItemBoughtCount($iProductID);
        $current = 0;
        $rsAmountOnlineStores = StoreProductTable::getList([
            'filter' => [
                'STORE_ID' => $arOnlineStores,
                'PRODUCT_ID' => $iProductID,
                '>AMOUNT' => 0
            ],
            'select' => ['STORE_ID', 'AMOUNT']
        ]);

        while ($arAmountOnlineStores = $rsAmountOnlineStores->fetch()) {
            $current += $arAmountOnlineStores['AMOUNT'];
        }

        if ($bought >= $current) {
            $allBought = true;
        }

        //Если все остатки выкуплены, удаляем из списка все онлайн магазины
        if ($allBought) {
            $arProductStores = array_diff($arProductStores, $arOnlineStores);
        }


        $arLocation = Location::getCurrent();


        $arLocationStores = array_column($arLocation['STORES'], 'ID');
        $arProductStores = array_intersect($arProductStores, $arLocationStores);


        //товар есть в наличии в этом городе, и есть склад, для резервирования
        $arProductStores = array_intersect($arProductStores, $arDeliveryStores);

        return !empty($arProductStores) && count($arProductStores) > 0;
    }


    /*
    * Метод аналогичен верхнему, но идет проверка по отдельному складу.
    */
    public static function productCanBeReservedByShop($iProductID, $iStoreID = null)
    {
        $iProductID = (int)$iProductID;
        $iStoreID = (int)$iStoreID;

        if (!Loader::includeModule('likee.exchange')) {
            return false;
        }

        if ($iProductID <= 0) {
            return false;
        }


        $arOnlineStores = self::getOnlineStores();
        $arDeliveryStores = self::getDeliveryStores();

        //Проверка, не выкуплены ли случайно все остатки, если это онлайн магазин
        if (in_array($iStoreID, $arOnlineStores)) {
            $bought = Reserve::getItemBoughtCount($iProductID);
            $current = 0;
            $rsAmountOnlineStores = StoreProductTable::getList([
                'filter' => [
                    'STORE_ID' => $arOnlineStores,
                    'PRODUCT_ID' => $iProductID,
                    '>AMOUNT' => 0
                ],
                'select' => ['STORE_ID', 'AMOUNT']
            ]);

            while ($arAmountOnlineStores = $rsAmountOnlineStores->fetch()) {
                $current += $arAmountOnlineStores['AMOUNT'];
            }

            if ($bought >= $current) {
                return false;
            }
        }


        $arProductStores = [];
        $rsAmount = StoreProductTable::getList([
            'filter' => [
                'STORE_ID' => $iStoreID,
                'PRODUCT_ID' => $iProductID,
                '>AMOUNT' => 0
            ],
            'select' => ['STORE_ID', 'AMOUNT']
        ]);

        while ($arAmount = $rsAmount->fetch()) {
            $reservedAmount = Reserve::getItemReservedCount($iProductID, $arAmount['STORE_ID']);
            $buyedAmount = Reserve::getItemBoughtCount($iProductID, $arAmount['STORE_ID']);
            if ($arAmount['AMOUNT'] - $reservedAmount - $buyedAmount > 0) {
                $arProductStores[] = $arAmount['STORE_ID'];
            }
        }

        $arLocation = Location::getCurrent();
        $arLocationStores = array_column($arLocation['STORES'], 'ID');

        $arProductStores = array_intersect($arProductStores, $arLocationStores);
        $arProductStores = array_intersect($arProductStores, $arDeliveryStores);


        return !empty($arProductStores) && count($arProductStores) > 0;
    }


    /**
     * Проверка на возможность покупки товара
     * В рамках этого проекта, купить можно товар который есть в онлайн магазине
     * Для остальных доступно только резервирование
     * Помимо битриксовой таблицы со складами, используется проверка на зарезервированный товар в отдельной таблице.
     * ВНИМАНИЕ! В список аргументов может передаваться массив магазинов. Если этот массив передан, то идет проверка по ним и проверка на зарезервированный товар НЕ РАБОТАЕТ
     *
     * @param int $iProductID
     * @param null|array $arProductStores
     * @return bool
     * @link http://redmine.crealink.ru/issues/7591
     */
    public static function productCanBuy($iProductID, $arProductStores = null)
    {
        $iProductID = intval($iProductID);
        $arOnlineStores = self::getOnlineStores();

        if (!Loader::includeModule('likee.exchange')) {
            return false;
        }

        if ($iProductID <= 0) {
            return false;
        }


        $arProductStores = [];

        $rsAmount = StoreProductTable::getList([
            'filter' => [
                'PRODUCT_ID' => $iProductID,
                '>AMOUNT' => 0
            ],
            'select' => ['STORE_ID', 'AMOUNT']
        ]);

        $itemQuantity = 0;
        while ($arAmount = $rsAmount->fetch()) {
            if (in_array($arAmount['STORE_ID'], $arOnlineStores)) {
                $alreadyReservedItems = Reserve::getItemReservedCount($iProductID, $arAmount['STORE_ID']);
                $quantity = $arAmount['AMOUNT'] - $alreadyReservedItems;
                $itemQuantity += $quantity;
                $arProductStores[] = $arAmount['STORE_ID'];
            }
        }

        $reservedAmount = Reserve::getItemBoughtCount($iProductID);

        if ($itemQuantity <= $reservedAmount) {
            return false;
        }


        //товар есть в онлайн магазине
        return !empty($arProductStores) && count(array_intersect($arOnlineStores, $arProductStores)) > 0;
    }


    /*
    * Проверка на доступные к покупке товары (по наличию в онлайн магазинах)
    *
    */
    public static function getProductAvailableCount($iProductID)
    {

        $iProductID = intval($iProductID);
        $arOnlineStores = self::getOnlineStores();

        if (!Loader::includeModule('likee.exchange')) {
            return false;
        }

        if ($iProductID <= 0) {
            return false;
        }


        $rsAmount = StoreProductTable::getList([
            'filter' => [
                'PRODUCT_ID' => $iProductID,
                '>AMOUNT' => 0
            ],
            'select' => ['STORE_ID', 'AMOUNT']
        ]);

        $itemQuantity = 0;
        while ($arAmount = $rsAmount->fetch()) {
            if (in_array($arAmount['STORE_ID'], $arOnlineStores)) {
                $itemQuantity += $arAmount['AMOUNT'];
            }
        }

        $reservedAmount = Reserve::getItemBoughtCount($iProductID);


        $result = $itemQuantity - $reservedAmount;

        if ($result <= 0) {
            $result = 0;
        }


        return $result;
    }

    /**
     * @param array $arOffers
     * @return array
     */
    public static function getMinPriceByOffers($arOffers = [])
    {
        $arPrices = [];

        foreach ($arOffers as $arOffer) {
            if (!empty($arOffer['MIN_PRICE'])) {
                $arPrices[$arOffer['MIN_PRICE']['DISCOUNT_VALUE']] = $arOffer['MIN_PRICE'];
            }
        }

        ksort($arPrices);
        return reset($arPrices);
    }

    public static function getProductLabels($TABLE_NAME, $XML_ID)
    {
        static $labes = array();

        if (!isset($labes[$TABLE_NAME])) {
            $labes[$TABLE_NAME] = array();

            $obEntity = \Likee\Site\Helpers\HL::getEntityClassByTableName($TABLE_NAME);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList(array(
                'select' => array('*')
            ));
            while ($arItem = $rsData->Fetch()) {
                if (empty($arItem['UF_ICON'])) {
                    continue;
                }

                $arLabel = [];
                $arLabel['NAME'] = $arItem['UF_NAME'];
                $arLabel['PAGE_URL'] = '/catalog/'.$arItem['UF_CODE'].'/';

                $arFile = \CFile::GetByID($arItem['UF_ICON'])->Fetch();
                $arLabel['SRC'] = \CFile::GetFileSRC($arFile);

                $labes[$TABLE_NAME][$arItem['UF_XML_ID']] = $arLabel;
            }
        }

        return isset($labes[$TABLE_NAME][$XML_ID]) ? $labes[$TABLE_NAME][$XML_ID] : false;
    }
    
    /**
     * Возвращает активный список складов для фильтрации
     */
    public static function getSelectedStoresForFiltration($fStoresFlag = null)
    {
        $arOnlineStores = self::getOnlineStores();
        $arStoresForFiltration = self::getStoresForFiltration();

        switch ($fStoresFlag == null ? self::getSelectedStoresFlag() : $fStoresFlag) {
            case 'O':
                $arStoresForFiltration = $arOnlineStores;
                break;
            case 'R':
                $arStoresForFiltration = array_diff($arStoresForFiltration, $arOnlineStores);
                break;
        }

        return $arStoresForFiltration;
    }

    /**
     * Возвращает активный флаг отбора складов
     */
    public static function getSelectedStoresFlag()
    {
        if (!isset($_SESSION['LIKEE_STORE_FLAG'])) {
            $_SESSION['LIKEE_STORE_FLAG'] = 'A';
        }
        if (isset($_REQUEST['f_stores']) && in_array($_REQUEST['f_stores'], ['A', 'O', 'R'])) {
            $_SESSION['LIKEE_STORE_FLAG'] = $_REQUEST['f_stores'];
        }

        return $_SESSION['LIKEE_STORE_FLAG'];
    }

    public static function getDiscountPercent($arPrice)
    {
        if ($arPrice['VALUE'] > $arPrice['DISCOUNT_VALUE']) {
            $iProductPrice = (int) $arPrice['DISCOUNT_VALUE'];
            $iProductOldPrice = (int) $arPrice['VALUE'];

            return floor(($iProductOldPrice-$iProductPrice)*100/$iProductOldPrice);
        }

        return 0;
    }

    public static function getProductPrice($iProductID, $iCatalogGroupId)
    {
        static $arCatalogGroupsNames = [
            7 => 'price',
            8 => 'price1'
        ];

        $priceParams = \Likee\Site\Helpers\Price::getProductPriceParamsByOfferId($iProductID);
        if ($priceParams && isset($arCatalogGroupsNames[$iCatalogGroupId])) {
            return [
                'CATALOG_GROUP_ID' => $iCatalogGroupId,
                'PRICE' => $priceParams[$arCatalogGroupsNames[$iCatalogGroupId]],
                'CURRENCY' => 'RUB'
            ];
        } elseif (Loader::includeModule('catalog')) {
            $rsPrice = \CPrice::GetList(
                [],
                [
                    "PRODUCT_ID" => $iProductID,
                    'CATALOG_GROUP_ID' => $iCatalogGroupId
                ]
            );
    
            if ($arPrice = $rsPrice->Fetch()) {
                return $arPrice;
            }
        }
        
        return false;
    }

    public static function checkElementResult(&$element)
    {
        $priceParams = \Likee\Site\Helpers\Price::getProductPriceParams($element['ID']);
        if ($priceParams) {
            $element['PROPERTIES']['PRICESEGMENTID']['VALUE'] = $priceParams['price_segment_id'];
            $element['PROPERTIES']['MAXDISCBP']['VALUE'] = $priceParams['max_disc_bp'];

            if (!empty($priceParams['price_original'])) {
                foreach ($element['OFFERS'] as &$arOffer) {
                    if ($arOffer['CATALOG_AVAILABLE'] == 'Y' && $arOffer['CAN_BUY']) {
                        $arOffer['MIN_PRICE']['VALUE'] = $priceParams['price_original'];
                        $arOffer['MIN_PRICE']['PRINT_VALUE'] = CurrencyFormat($arOffer['MIN_PRICE']['VALUE'], $arOffer['MIN_PRICE']['CURRENCY']);
                        $arOffer['MIN_PRICE']['DISCOUNT_PCT'] = $priceParams['max_disc_bp'];
                        break;
                    }
                }
            }
        }
        unset($priceParams);
    }
}
