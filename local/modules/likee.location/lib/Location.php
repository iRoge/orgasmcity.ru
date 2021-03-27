<?php
/**
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Location;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location\LocationTable;

class Location
{
    public static $_all;

    const IBLOCK_CITY_ID = 21; //инфоблок, в котором указываются дополнительные города, которые доступны для выбора пользователя

    public static function getCurrent()
    {
        Loader::includeModule('catalog');
        Loader::includeModule('sale');

        $arAll = self::all();

        /** Сначала проверяем, был ли выбран город вручную */
        $iCookieLocationID = intval($_COOKIE['CURRENT_LOCATION_ID']);
        if ($iCookieLocationID > 0 && array_key_exists($iCookieLocationID, $arAll)) {
            return $arAll[$iCookieLocationID];
        }

        /**  Пытаемся определить город присутствия по ip */
        $oRemoteAddress = new RemoteAddress();
        $oRemoteAddress->setUseProxy(true);
        $oRemoteAddress->setTrustedProxies(array('127.0.0.1'));
        $sCurrentIP = $oRemoteAddress->getIpAddress();

        if (strlen($sCurrentIP) <= 0) {
            return self::getDefault();
        }

        $oGeoIP = new GeoIp();
        $arFindLocation = $oGeoIP->getLocation($sCurrentIP);

        if (strlen($arFindLocation['city']) > 0) {
            foreach ($arAll as $arLocation) {
                if ($arLocation['CITY_NAME'] == $arFindLocation['city']) {
                    return $arLocation;
                }
            }
        }

        /** Если мы не в городе присутствия, ищем ближайший город */
        if (!empty($arFindLocation['lat']) && !empty($arFindLocation['lng'])) {
            $byDist = [];
            foreach ($arAll as $arLocation) {
                $dist = intval(self::distance($arLocation['LAT'], $arLocation['LON'], $arFindLocation['lat'], $arFindLocation['lng']));
                $byDist[$dist] = $arLocation;
            }
            ksort($byDist);
            return reset($byDist);
        }


        return self::getDefault();
    }

    public static function setCurrent($ID)
    {
        $ID = intval($ID);

        $arLocations = self::all();

        if ($ID > 0 && in_array($ID, array_column($arLocations, 'ID'))) {
            setcookie('CURRENT_LOCATION_ID', $ID, time() + 86400 * 30, '/');
            return true;
        }

        return false;
    }

    public static function getDefault()
    {
        $arLocations = self::all();

        $sDefaultLocationCode = Option::get('sale', 'location');

        $arLocation = reset(array_filter($arLocations, function ($arLoc) use ($sDefaultLocationCode) {
            return $arLoc['CODE'] == $sDefaultLocationCode;
        }));

        return $arLocation ?: false;
    }

    public static function all()
    {
        if (is_null(self::$_all)) {
            self::$_all = [];

            $obCache = Application::getCache();
            if ($obCache->initCache(3600, 'stores_list', '/likee/locations/')) {
                $arResult = $obCache->getVars();
            } else {
                Loader::includeModule('catalog');
                Loader::includeModule('sale');
                Loader::includeModule('iblock');

                Application::getInstance()->getTaggedCache()->startTagCache('/likee/locations/');

                $rsStores = \CCatalogStore::GetList(
                    ['UF_CITY' => 'asc'],
                    [
                        'ACTIVE' => 'Y',
                        '!UF_CITY' => false
                    ],
                    false,
                    false,
                    ['ID', 'XML_ID', 'TITLE', 'UF_CITY', 'GPS_N', 'GPS_S', 'SORT']
                );

                $arResult = [];
                $arLocations = [];

                while ($arStore = $rsStores->Fetch()) {
                    $arLocations[$arStore['UF_CITY']][] = $arStore;
                }

                $rsAdditionalLocs = \CIBlockElement::GetList(
                    ['ID' => 'ASC'],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => self::IBLOCK_CITY_ID
                    ],
                    false,
                    false,
                    ['IBLOCK_ID', 'NAME'] // IBLOCK_ID нужен для тегированного кеша
                );

                while ($arAdditionalLoc = $rsAdditionalLocs->Fetch()) {
                    // проверка нужна, чтобы не затирать склады
                    if (!array_key_exists($arAdditionalLoc['NAME'], $arLocations)) {
                        $arLocations[$arAdditionalLoc['NAME']] = [];
                    }
                }

                $arLocationsNames = array_keys($arLocations);

                $rsSaleLocations = LocationTable::getList([
                    'order' => [
                        'SORT' => 'asc',
                        'NAME.NAME' => 'asc',
                    ],
                    'filter' => [
                        'TYPE.CODE' => 'CITY',
                        'NAME.NAME' => $arLocationsNames
                    ],
                    'select' => [
                        'ID', 'CODE', 'CITY_NAME' => 'NAME.NAME', 'LAT' => 'LATITUDE', 'LON' => 'LONGITUDE'
                    ]
                ]);

                $citiesArr = [];
                while ($arSaleLocation = $rsSaleLocations->fetch()) {
                    $arSaleLocation['STORES'] = $arLocations[$arSaleLocation['CITY_NAME']];

                    if (empty($arSaleLocation['LAT']) || empty($arSaleLocation['LON'])) {
                        foreach ($arSaleLocation['STORES'] as $arStore) {
                            if (!empty($arStore['GPS_N']) && !empty($arStore['GPS_S'])) {
                                $arSaleLocation['LAT'] = $arStore['GPS_N'];
                                $arSaleLocation['LON'] = $arStore['GPS_S'];
                                break;
                            }
                        }
                    }

                    if (!$citiesArr[$arSaleLocation['CITY_NAME']]) {
                        $arResult[$arSaleLocation['ID']] = $arSaleLocation;
                        $citiesArr[$arSaleLocation['CITY_NAME']] = $arSaleLocation['CITY_NAME'];
                    }
                }

                Application::getInstance()->getTaggedCache()->endTagCache();

                if ($obCache->startDataCache()) {
                    $obCache->endDataCache($arResult);
                }
            }

            self::$_all = $arResult;
        }

        return self::$_all;
    }

    /**
     * Города, которые менежер добавил вручную, не привязаны к складу
     *
     * @return array
     */
    public static function getAdditionalCities()
    {
        $arResult = [];

        $rsMoreLocs = \CIBlockElement::GetList(
            ['ID' => 'ASC'],
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => self::IBLOCK_CITY_ID
            ],
            false,
            false,
            ['IBLOCK_ID', 'NAME']
        );

        $arLocationsNames = [];

        while ($arMoreLoc = $rsMoreLocs->Fetch()) {
            $arLocationsNames[$arMoreLoc['NAME']] = true;
        }

        $rsSaleLocations = LocationTable::getList([
            'order' => [
                'SORT' => 'asc',
                'NAME.NAME' => 'asc',
            ],
            'filter' => [
                'TYPE.CODE' => 'CITY',
                'NAME.NAME' => array_keys($arLocationsNames)
            ],
            'select' => [
                'ID', 'CODE', 'CITY_NAME' => 'NAME.NAME', 'LAT' => 'LATITUDE', 'LON' => 'LONGITUDE'
            ]
        ]);

        while ($arSaleLocation = $rsSaleLocations->fetch()) {
            $arSaleLocation['STORES'] = [];
            $arResult[$arSaleLocation['ID']] = $arSaleLocation;
        }

        return $arResult;
    }

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param string $unit
     * @return float
     *
     */
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit = 'm')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        $result = $miles;

        if ($unit == 'K') {
            $result = ($miles * 1.609344);
        } elseif ($unit == 'm') {
            $result = ($miles * 1.609344) * 1000;
        } elseif ($unit == 'N') {
            $result = ($miles * 0.8684);
        }

        return $result > 0 ? $result : 0;
    }

    public static function updateLocationsGeoData()
    {
        $rsLocs = LocationTable::getList([
            'filter' => [
                'TYPE.CODE' => 'CITY',
                'NAME.LANGUAGE_ID' => LANGUAGE_ID
            ],
            'select' => [
                '*',
                'CITY_NAME' => 'NAME.NAME'
            ],
        ]);

        while ($arLoc = $rsLocs->fetch()) {
            $arData = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?format=json&geocode=Россия, ' . $arLoc['CITY_NAME']), true);

            if (!$arData) {
                continue;
            }

            $arCity = reset($arData['response']['GeoObjectCollection']['featureMember']);
            if (!$arCity) {
                continue;
            }


            $sGeo = $arCity['GeoObject']['Point']['pos'];

            if (!$sGeo) {
                continue;
            }

            $arGeo = explode(' ', $sGeo);

            if (!is_array($arGeo) || count($arGeo) != 2) {
                continue;
            }

            LocationTable::update($arLoc['ID'], [
                'LONGITUDE' => $arGeo[0],
                'LATITUDE' => $arGeo[1]
            ]);
        }
    }
}
