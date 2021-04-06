<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

$iCount = intval($arResult['RECORD_COUNT']);

$APPLICATION->SetPageProperty(
    'SEARCH_RESULT_COUNT',
    $iCount . ' ' . \Likee\Site\Helper::strMorph($iCount, 'позиций', 'позиции', 'позиций')
);