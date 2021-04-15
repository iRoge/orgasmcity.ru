<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

\Bitrix\Main\Loader::includeModule('likee.site');

//проверка, что раздел верный
$sCacheKey = md5(serialize($arResult));
$sCacheDir = 'likee\catalog\section_code_path';
$obCache = \Bitrix\Main\Application::getCache();

$bCurSectionPath = false;

if ($obCache->initCache('section_code_path_' . $sCacheKey, $arParams['CACHE_TIME'])) {
    $arVars = $obCache->getVars();
    $bCurSectionPath = $arVars['CUR'];
} elseif ($obCache->startDataCache()) {
    \Bitrix\Main\Application::getInstance()->getTaggedCache()->startTagCache($sCacheDir);
    $bCurSectionPath = \Likee\Site\Helpers\IBlock::checkSectionCodePath($arResult['VARIABLES'], $arParams['IBLOCK_ID'], true);
    \Bitrix\Main\Application::getInstance()->getTaggedCache()->endTagCache();

    $obCache->endDataCache(['CUR' => $bCurSectionPath]);
}

if (!$bCurSectionPath) {
    \Bitrix\Iblock\Component\Tools::process404(
        '',
        $arParams['SET_STATUS_404'] === 'Y',
        $arParams['SET_STATUS_404'] === 'Y',
        $arParams['SHOW_404'] === 'Y',
        $arParams['FILE_404']
    );
    return;
}

?>
<? $APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "",
    array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => "0"
    )
); ?>

<?$iElementID = $APPLICATION->IncludeComponent(
    "qsoft:catalog.element",
    ".default",
    array(
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
        "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
    ),
    $component
); ?>

<?
$showMoreCount = 4;
$showAll = 'N';
if ($_POST['SHOW_MORE'] == 'Y') {
    $showMoreCount = 32;
    $showAll = 'Y';
}
?>

<? if (COption::GetOptionInt("likee", "watch_history", 1)) {
    $APPLICATION->IncludeComponent(
        'bitrix:sale.viewed.product',
        'product',
        array(
            'VIEWED_COUNT' => $showMoreCount,
            'MAX_VIEWED_COUNT' => 32,
            'SHOW_ALL' => $showAll,
            'VIEWED_NAME' => 'Y',
            'VIEWED_IMAGE' => 'Y',
            'VIEWED_PRICE' => 'Y',
            'VIEWED_CURRENCY' => 'default',
            'VIEWED_CANBUY' => 'Y',
            'VIEWED_CANBASKET' => 'Y',
            'VIEWED_IMG_HEIGHT' => '150',
            'VIEWED_IMG_WIDTH' => '150',
            'BASKET_URL' => '/cart/',
            'ACTION_VARIABLE' => 'action',
            'PRODUCT_ID_VARIABLE' => 'id',
            'SET_TITLE' => 'N',
            'AJAX_MODE' => 'Y',
            'AJAX_OPTION_SHADOW' => 'N',
            'AJAX_OPTION_JUMP' => 'N',
            'AJAX_OPTION_ADDITIONAL' => 'of3h4',
            'AVIABLE_STORES' => \Likee\Site\Helpers\Catalog::getStoresForFiltration()
        )
    );
}?>


<?
if (!empty($GLOBALS['ELEMENT_BREADCRUMB'])) {
    //$APPLICATION->AddChainItem($GLOBALS['ELEMENT_BREADCRUMB']); СЕО сказали убрать для элементов.
}
