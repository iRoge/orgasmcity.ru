<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true || !isset($arGroupSection)) die();

try {
    $catalogBlocksIblockId = \Likee\Site\Helpers\IBlock::getIBlockId('BLOCK_BANNERS');

    ob_start();

    $GLOBALS['arCatalogBannersFilter']['PROPERTY_BLOCK_GROPUS'] = $arGroupSection['ID'];
    
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "catalog_banners",
        array(
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "CONTENT",
            "IBLOCK_ID" => $catalogBlocksIblockId,
            "NEWS_COUNT" => "100",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "NAME",
            "SORT_ORDER2" => "ASC",
            "FILTER_NAME" => "arCatalogBannersFilter",
            "FIELD_CODE" => array(
                0 => "",
            ),
            "PROPERTY_CODE" => array(
                0 => "",
                1 => ["LINK"],
                2 => ["BLOCK_SIZE"],
            ),
            "CHECK_DATES" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "604800",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N"
        ),
        false
    );
    
    $APPLICATION->AddViewContent('under_instagram', ob_get_contents());
    ob_end_clean();
} catch (\Exception $e) {}