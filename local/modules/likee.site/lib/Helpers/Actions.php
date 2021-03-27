<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Likee\Location\Location;

/**
 * Класс для работы с акциями
 *
 * @package Likee\Site\Helpers
 */
class Actions
{
    public static function getLocationFiltration()
    {
        if (!Loader::includeModule('iblock') || !Loader::includeModule('likee.location'))
            return [];

        $arShopsIblock = IblockTable::getRow(['filter' => ['CODE' => 'SHOPSLIST']]);
        $arCurrentLocation = \Likee\Location\Location::getCurrent();

        if (!$arShopsIblock || !$arCurrentLocation)
            return [];
        
        $arActionsFilter = [];
        $arLocationShops = [];

        $res = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $arShopsIblock['ID'],
                'ACTIVE' => 'Y',
                'PROPERTY_SHOP_CITY.NAME' => $arCurrentLocation['CITY_NAME'],
            ],
            false, 
            false,
            [
                'IBLOCK_ID',
                'ID'
            ]
        );
        while($ob = $res->GetNext(false, false)){
            $arLocationShops[] = $ob['ID'];
        }
        
        if (! $arLocationShops) {
            $arActionsFilter['PROPERTY_SHOPS'] = false;
        } else {
            $arActionsFilter[] = [
                'LOGIC' => 'OR',
                ['PROPERTY_SHOPS' => false],
                ['PROPERTY_SHOPS' => $arLocationShops]
            ];
        }

        return $arActionsFilter;
    }
}