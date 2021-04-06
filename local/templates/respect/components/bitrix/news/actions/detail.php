<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$APPLICATION->SetTitle('Акция');
?>

<? $ElementID = $APPLICATION->IncludeComponent(
    "bitrix:news.detail",
    "actions",
    Array(
        "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
        "DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
        "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
        "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "META_KEYWORDS" => $arParams["META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
        "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "SHOW_404" => $arParams["SHOW_404"],
        "FILE_404" => $arParams["FILE_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
        "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
        "ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
        "DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
        "PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
        "CHECK_DATES" => $arParams["CHECK_DATES"],
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
        "USE_SHARE" => $arParams["USE_SHARE"],
        "SHARE_HIDE" => $arParams["SHARE_HIDE"],
        "SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
        "SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
        "SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
        "SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
        "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : '')
    ),
    $component
); ?>

<?
\Bitrix\Main\Loader::includeModule('likee.location');

$arLocation = \Likee\Location\Location::getCurrent();

$arStores = array_column($arLocation['STORES'], 'ID');

if (!empty($arStores)) {
    $GLOBALS['f']['OFFERS']['PROPERTY_STORES'] = $arStores;
}

if (!isset($_REQUEST['set_filter']))
    $GLOBALS['f']['>PROPERTY_MINIMUM_PRICE'] = 0;
?>

<? $APPLICATION->IncludeComponent(
    "bitrix:catalog.recommended.products",
    ".default",
    array(
        "ID" => $ElementID,
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "LINE_ELEMENT_COUNT" => '4',
        "TEMPLATE_THEME" => "",
        "PROPERTY_LINK" => "PRODUCTS",
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "BASKET_URL" => "",
        "ACTION_VARIABLE" => "",
        "PRODUCT_ID_VARIABLE" => "",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "ADD_PROPERTIES_TO_BASKET" => "N",
        "PRODUCT_PROPS_VARIABLE" => '',
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PAGE_ELEMENT_COUNT" => 12,
        "SHOW_OLD_PRICE" => "N",
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "PRICE_CODE" => $arParams['PRICE_CODE'],
        "SHOW_PRICE_COUNT" => "",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PRICE_VAT_INCLUDE" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "SHOW_NAME" => "Y",
        "SHOW_IMAGE" => "Y",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "",
        "MESS_BTN_SUBSCRIBE" => "Уведомить о поступлении",
        "HIDE_NOT_AVAILABLE" => "Y",
        "CONVERT_CURRENCY" => "N",
        "CURRENCY_ID" => '',
        "COMPONENT_TEMPLATE" => ".default",
        "CODE" => "",
        "OFFERS_PROPERTY_LINK" => "PRODUCTS",
        "DETAIL_URL" => "",
        "FILTER_NAME" => "f",
        "SHOW_PRODUCTS_" . $arParams["CATALOG_IBLOCK_ID"] => "Y",
        "PROPERTY_CODE_" . $arParams["CATALOG_IBLOCK_ID"] => array(),
        "CART_PROPERTIES_" . $arParams["CATALOG_IBLOCK_ID"] => array(),
        "ADDITIONAL_PICT_PROP_" . $arParams["CATALOG_IBLOCK_ID"] => "",
        "LABEL_PROP_" . $arParams["CATALOG_IBLOCK_ID"] => ""
    )
); ?>

<? if (!empty($arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'])): ?>
    <div class="container show-more">
        <div class="column-2 column-center column-md-2">
            <a href="<?= $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news']; ?>"
               class="button button--primary button--block">
                К списку акций
            </a>
        </div>
    </div>
<? endif; ?>