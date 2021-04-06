<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

foreach ($arResult as &$arItem) {
    $arItem['LINK'] = str_replace('/catalog/', '/catalog/search/', $arItem['LINK']);
    if (isset($_REQUEST['q']))
        $arItem['LINK'] .= '?q=' . htmlentities(trim($_REQUEST['q']));
}
unset($arItem);

if (\Bitrix\Main\Loader::includeModule('likee.site'))
    $arResult = \Likee\Site\Helpers\Menu::menuTreeBuild($arResult);