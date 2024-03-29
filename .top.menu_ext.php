<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
/** @global CMain $APPLICATION */
/** @global array $aMenuLinks */

\Bitrix\Main\Loader::includeModule('likee.location');
$arLocation = \Likee\Location\Location::getCurrent();

$aMenuLinksExt = $APPLICATION->IncludeComponent(
    'likee:menu.sections',
    '',
    array(
        'IBLOCK_TYPE' => '',
        'IBLOCK_ID' => IBLOCK_CATALOG,
        'IBLOCK_OFFERS_ID' => IBLOCK_OFFERS,
        'DEPTH_LEVEL' => 4,
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '604800',
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);

foreach ($aMenuLinks as $iKey => $arLink) {
    if (!empty($arLink[3]) && isset($arLink[3]['BUTTON']) && $arLink[3]['BUTTON'] == 'Y') {
        unset($aMenuLinks[$iKey]);
        $aMenuLinks[] = $arLink;
    }
}

$aMenuLinks = array_values($aMenuLinks);