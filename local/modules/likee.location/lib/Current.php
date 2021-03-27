<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Location;

use Bitrix\Main\Loader;

class Current
{
    const DEFAULT_LOCATION_NAME = 'Москва';

    public static function getLocation()
    {
        $arSessionLocation = $_SESSION['CURRENT_LOCATION'];
        if (!$arSessionLocation) {
            $arSessionLocation = [
                'ID' => ''
            ];
        }
    }


    private static $_stores;

    public static function getStores()
    {
        if (is_null(self::$_stores)) {
            Loader::includeModule('catalog');

            $sCurrentCity = 'Москва';

            $rsStores = \CCatalogStore::GetList([], [
                'UF_CITY' => $sCurrentCity,
            ]);

            $arResult = [];

            while ($arStore = $rsStores->Fetch()) {
                $arResult[] = $arStore;
            }

            self::$_stores = $arResult;
        }

        return self::$_stores;
    }
}