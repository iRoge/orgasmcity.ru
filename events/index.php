<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Пресс-центр");

$chain = [];
$url = explode('/', $_SERVER["REQUEST_URI"])[1];
$cacheID = $unitID = 'nav_chain_' . $url;
$cache = \Bitrix\Main\Application::getInstance()->getManagedCache();

if ($cache->read(360000, $cacheID)) {
    $chain = $cache->get($cacheID);
} else {
    $chain[] = [
        'title' => 'События',
        'url' => '/events/'
    ];
    if ($url == 'actions') {
        $chain[] = [
            'title' => 'Акции',
            'url' => '/actions/'
        ];
    } elseif ($url == 'news') {
         $chain[] = [
             'title' => 'Новости',
             'url' => '/news/'
         ];
    }
    $cache->set($cacheID, $chain);
}

foreach ($chain as $item) {
        $APPLICATION->AddChainItem($item['title'], $item['url']);
}
?>

<?
$APPLICATION->IncludeComponent(
    "bitrix:news",
    "events",
    array(
        "IBLOCK_TYPE" => "CONTENT",
        "IBLOCK_ID" => "64",
        "TEMPLATE_THEME" => "site",
        "NEWS_COUNT" => "10",
        "USE_SEARCH" => "N",
        "USE_RSS" => "Y",
        "NUM_NEWS" => "20",
        "NUM_DAYS" => "180",
        "YANDEX" => "N",
        "USE_RATING" => "N",
        "USE_CATEGORIES" => "N",
        "USE_REVIEW" => "N",
        "USE_FILTER" => "Y",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "CHECK_DATES" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_SHADOW" => "Y",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "DISPLAY_PANEL" => "Y",
        "SET_TITLE" => "N",
        "SET_STATUS_404" => "Y",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_ELEMENT_CHAIN" => "Y",
        "USE_PERMISSIONS" => "N",
        "PREVIEW_TRUNCATE_LEN" => "",
        "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "LIST_FIELD_CODE" => array(
            0 => "DETAIL_PAGE_URL",
            1 => "DATE_ACTIVE_TO",
        ),
        "LIST_PROPERTY_CODE" => array(
            0 => "PROPERTY_ACTIVE_DIRECT_LINK",
            1 => "PROPERTY_ACTIVE_PHOTO_LINK",
            2 => "PROPERTY_NEWS_OR_ACTION",
            3 => "PROPERTY_PICTURE_LOCATION",
            4 => "PROPERTY_TEXT",
            5 => "PROPERTY_DIRECT_LINK",
            6 => "PROPERTY_PHOTO_LINK",
        ),
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "DISPLAY_NAME" => "Y",
        "META_KEYWORDS" => "-",
        "META_DESCRIPTION" => "-",
        "BROWSER_TITLE" => "-",
        "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "DETAIL_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "DETAIL_PROPERTY_CODE" => array(
            0 => "GALLERY",
            1 => "",
        ),
        "DETAIL_DISPLAY_TOP_PAGER" => "N",
        "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
        "DETAIL_PAGER_TITLE" => "Страница",
        "DETAIL_PAGER_TEMPLATE" => "arrows",
        "DETAIL_PAGER_SHOW_ALL" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Новости",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "show_more",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
        "PAGER_SHOW_ALL" => "Y",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "SLIDER_PROPERTY" => "PICS_NEWS",
        "COMPONENT_TEMPLATE" => ".default",
        "SET_LAST_MODIFIED" => "N",
        "USE_SHARE" => "N",
        "DETAIL_SET_CANONICAL_URL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/",
        "SEF_URL_TEMPLATES" => array(
            "events" => "events/",
            "news" => "news/",
            "actions" => "actions/",
            "section" => "#SECTION_CODE#/",
            "detail" => "events/#ELEMENT_CODE#/"
        )
    ),
    false
);

?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
