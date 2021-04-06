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

\Likee\Site\Helper::addBodyClass('page--list');

\Bitrix\Main\Loader::includeModule('likee.site');
$APPLICATION->SetTitle('Результат поиска');
$sSearch = htmlentities(trim($_REQUEST['q']));

$corrector = new \Likee\Site\Text\LangCorrect();
$sSearch = $corrector->parse($sSearch, \Likee\Site\Text\LangCorrect::KEYBOARD_LAYOUT);

if (strlen($sSearch) > 0) {
    $GLOBALS[$arParams['FILTER_NAME']][] = [
        'LOGIC' => 'OR',
        ['NAME' => '%' . $sSearch . '%'],
        ['PROPERTY_ARTICLE' => $sSearch]
    ];
    $APPLICATION->SetTitle('Товары по запросу &laquo;' . $sSearch . '&raquo;');
} else {
    $arParams['USE_FILTER'] = 'N';
}

$arCurSection = \Likee\Site\Helpers\IBlock::getSectionByVariables($arParams['IBLOCK_ID'], $arResult['VARIABLES']);

if (!$arCurSection) {
    $arParams['USE_FILTER'] = 'N';
}
?>

<? if ($arParams['USE_FILTER'] == 'Y'): ?>
    <? $APPLICATION->IncludeComponent(
        "likee:catalog.smart.filter",
        "",
        array(
            'CITY_CODE' => $arParams['CITY_CODE'],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "SECTION_ID" => $arCurSection['ID'],
            "FILTER_NAME" => $arParams["FILTER_NAME"],
            "PRICE_CODE" => $arParams["PRICE_CODE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "SAVE_IN_SESSION" => "N",
            "FILTER_VIEW_MODE" => '',
            "XML_EXPORT" => "Y",
            "SECTION_TITLE" => "NAME",
            "SECTION_DESCRIPTION" => "DESCRIPTION",
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
            "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            "SEF_MODE" => $arParams["SEF_MODE"],
            "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
            "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
            'SEARCH' => strlen($sSearch) > 0 ? $sSearch : 'Y' // для кеша
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    ); ?>
<? endif; ?>

<? if (empty($arCurSection)): ?>
    <div class="container" style="padding: 20px 0;">
        <div class="column-8 pre-1">
            <? ShowError('Раздел не найден'); ?>
        </div>
    </div>
    <? return; ?>
<? endif; ?>

<? if (!empty($arCurSection['PICTURE'])): ?>
    <section style="background-image: url('<?= $arCurSection['PICTURE']['SRC']; ?>')" class="hero phone--hidden">
        <div class="container">
            <div class="column-10">
                <div class="hero__content">
                    <h1><?= $arCurSection['NAME']; ?></h1>
                    <a href="#products-list" class="hero-scroll-down"><i class="icon icon-arrow-bottom"></i></a></div>
            </div>
        </div>
    </section>
<? endif; ?>

<div id="products-list"></div>

<? if (empty($sSearch)): ?>
    <div class="container" style="padding: 20px 0;">
        <div class="column-8 pre-1">
            <? ShowError('Не указан запрос для поиска'); ?>
        </div>
    </div>
    <? return; ?>
<? endif; ?>

<div class="container search-description">
    <div class="column-2 pre-1 column-md-2">
        <p><span>Результат поиска:&nbsp;</span><b><?= $sSearch; ?></b></p>
    </div>

    <div class="column-2 column-md-2">
        <p><span>Найдено:&nbsp;</span><b><? $APPLICATION->ShowProperty('SEARCH_RESULT_COUNT'); ?></b></p>
    </div>
</div>

<? $APPLICATION->IncludeComponent(
    'bitrix:menu',
    'search',
    array(
        'COMPONENT_TEMPLATE' => '.default',
        'ROOT_MENU_TYPE' => 'search',
        'MENU_CACHE_TYPE' => 'N',
        'MENU_CACHE_TIME' => $arParams['CACHE_TIME'],
        'MENU_CACHE_USE_GROUPS' => $arParams['CACHE_GROUPS'],
        'MENU_CACHE_GET_VARS' => array(),
        'MAX_LEVEL' => '3',
        'CHILD_MENU_TYPE' => 'left',
        'USE_EXT' => 'N',
        'DELAY' => 'N',
        'ALLOW_MULTI_SELECT' => 'N'
    )
); ?>

<div class="js-catalog-section">
    <?
    $bAjaxFilter = isset($_REQUEST['set_filter']) && \Likee\Site\Helper::isAjax();
    $bAjaxLoadMore = isset($_REQUEST['load_more']) && empty($_REQUEST['action']);
    ?>

    <? if ($bAjaxFilter || $bAjaxLoadMore) {
        $APPLICATION->RestartBuffer();
    } ?>

    <? $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        'search',
        array(
            "CITY_CODE" => $arParams['CITY_CODE'],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIONS_IBLOCK_ID" => $arParams["ACTIONS_IBLOCK_ID"],
            "BANNERS_BLOCK_ID" => $arParams["BANNERS_BLOCK_ID"],
            "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
            "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
            "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
            "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
            "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME" => $arParams["FILTER_NAME"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_FILTER" => isset($_REQUEST['set_filter']) ? 'N' : 'Y',
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "SET_TITLE" => $arParams["SET_TITLE"],
            "MESSAGE_404" => $arParams["MESSAGE_404"],
            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
            "SHOW_404" => $arParams["SHOW_404"],
            "FILE_404" => $arParams["FILE_404"],
            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
            "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
            "PRICE_CODE" => $arParams["PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

            "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

            'LABEL_PROP' => $arParams['LABEL_PROP'],
            'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
            'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
            'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
            'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
            'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
            'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

            'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
            "ADD_SECTIONS_CHAIN" => "N",
            'ADD_TO_BASKET_ACTION' => '',
            'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
            'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
            'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
            'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    ); ?>
    <? if ($bAjaxFilter || $bAjaxLoadMore) {
        $APPLICATION->FinalActions();
        exit;
    } ?>
</div>