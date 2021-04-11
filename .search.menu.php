<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
/** @global CMain $APPLICATION */
/** @global array $aMenuLinks */


\Bitrix\Main\Loader::includeModule('likee.location');

$arLocation = \Likee\Location\Location::getCurrent();

$aMenuLinks = $APPLICATION->IncludeComponent(
    'likee:menu.sections',
    '',
    array(
        'IBLOCK_TYPE' => '',
        'IBLOCK_ID' => IBLOCK_CATALOG,
        'IBLOCK_OFFERS_ID' => IBLOCK_OFFERS,
        'DEPTH_LEVEL' => 3,
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '604800',
        'CITY_ID' => $arLocation['ID']
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);