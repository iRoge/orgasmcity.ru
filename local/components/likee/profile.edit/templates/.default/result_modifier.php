<?php
use Bitrix\Sale\Location\LocationTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arRegions = $arCities = $arRegionsData = [];

$rsRegions = LocationTable::getList([
    'filter' => [
        'NAME.LANGUAGE_ID' => LANGUAGE_ID,
        [
            'LOGIC' => 'OR',
            ['TYPE.CODE' => 'REGION'],
            ['CODE' => '0000073738'] //Москва
        ]
    ],
    'select' => ['ID', 'NAME_RU' => 'NAME.NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN'],
    'order' => ['NAME_RU']
]);

while ($arRegion = $rsRegions->fetch()) {
    $arRegions[$arRegion['ID']] = $arRegion['NAME_RU'];
    $arRegionsData[$arRegion['ID']] = $arRegion;
}

$sRegion = isset($_REQUEST['region_name']) ? htmlentities(trim($_REQUEST['region_name'])) : $arResult['PROFILE']['FIELDS']['REGION']['VALUE'];

if (!empty($sRegion) && in_array($sRegion, $arRegions)) {
    $iParentId = array_search($sRegion, $arRegions);
    $arParent = $arRegionsData[$iParentId];

    $rsCities = LocationTable::getList([
        'filter' => [
            'NAME.LANGUAGE_ID' => LANGUAGE_ID,
            'TYPE.CODE' => 'CITY',
            '>LEFT_MARGIN' => $arParent['LEFT_MARGIN'],
            '<RIGHT_MARGIN' => $arParent['RIGHT_MARGIN']
        ],
        'select' => ['ID', 'NAME_RU' => 'NAME.NAME'],
        'order' => ['NAME_RU']
    ]);

    while ($arCity = $rsCities->fetch()) {
        $arCities[$arCity['ID']] = $arCity['NAME_RU'];
    }

    if ($sRegion == 'Москва') {
        $arCities = [];
    }
}

if (\Likee\Site\Helper::isAjax() && $_REQUEST['action'] == 'load_cities') {
    $APPLICATION->RestartBuffer();
    echo json_encode(['CITIES' => $arCities]);
    $APPLICATION->FinalActions();
    exit;
}

$arResult['REGIONS'] = $arRegions;
$arResult['CITIES'] = $arCities;