<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Брендовая женская и мужская обувь: продажа модной обуви в интернет-магазине Respect");
$APPLICATION->SetTitle("Главная страница");
\Likee\Site\Helper::addBodyClass('page--index');
global $arrFilterBanners;
$arrFilterBanners = array(
    [
        'LOGIC' => 'OR',
        ['PROPERTY_LOCATION' => false],
        ['PROPERTY_LOCATION' => ''],
        ['=PROPERTY_LOCATION' => $LOCATION->getName()],
        ['=PROPERTY_LOCATION' => $LOCATION->getRegion()],
        ['=PROPERTY_LOCATION' => $LOCATION->getCountry()],
    ]
);
?>

<div class="front-blocks">
    <?
    if (!Functions::checkMobileDevice()) {
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home",
            array(
                "COMPONENT_TEMPLATE" => "home",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "7",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "AUTOPLAY_CODE" => "home_slider_autoplay_1",
                "LOCATION" => $LOCATION->getName()
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "home_actions",
            array(
                "DISPLAY_DATE" => "N",
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "Y",
                "AJAX_MODE" => "Y",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "20",
                "NEWS_COUNT" => "10",
                "SORT_BY1" => "SORT",
                "SORT_ORDER1" => "ASC",
                "SORT_BY2" => "ID",
                "SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "arrFilterBanners",
                "FIELD_CODE" => array(
                    0 => "",
                    1 => ["ID"],
                    2 => "",
                ),
                "PROPERTY_CODE" => array(
                    0 => "",
                    1 => ["DESCRIPTION"],
                    2 => "VIDEO_FILE_MP4",
                    3 => "VIDEO_FILE_WEBM",
                    4 => "VIDEO_FILE_OGV",
                    5 => "VIDEO_FILE",
                    6 => "VIDEO_PREVIEW",
                    7 => "AUTOPLAY",
                    8 => "VIDEO_GAG_LINK",
                    9 => "VIDEO_GAG",
                    10 => "MOBILE_LINK",
                    11 => "MOBILE_IMAGE",
                    12 => "",
                ),
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "PREVIEW_TRUNCATE_LEN" => "",
                "ACTIVE_DATE_FORMAT" => "m.Y",
                "SET_TITLE" => "N",
                "SET_BROWSER_TITLE" => "Y",
                "SET_META_KEYWORDS" => "Y",
                "SET_META_DESCRIPTION" => "Y",
                "SET_LAST_MODIFIED" => "Y",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                "ADD_SECTIONS_CHAIN" => "Y",
                "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
                "PARENT_SECTION" => "",
                "PARENT_SECTION_CODE" => "",
                "INCLUDE_SUBSECTIONS" => "Y",
                "CACHE_TYPE" => "Y",
                "CACHE_TIME" => "604800",
                "CACHE_FILTER" => "Y",
                "CACHE_GROUPS" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Новости",
                "PAGER_SHOW_ALWAYS" => "Y",
                "PAGER_TEMPLATE" => "",
                "PAGER_DESC_NUMBERING" => "Y",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "Y",
                "PAGER_BASE_LINK_ENABLE" => "Y",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
                "PAGER_BASE_LINK" => "",
                "PAGER_PARAMS_NAME" => "arrPager",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "COMPONENT_TEMPLATE" => ".default",
                "FILE_404" => ""
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home",
            array(
                "COMPONENT_TEMPLATE" => "home",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "37",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "2",
                "AUTOPLAY_CODE" => "home_slider_autoplay_2",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
    }
    if (Functions::checkMobileDevice()) {
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "40",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob1",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_1",
                "LOCATION" => $LOCATION->getName()
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "44",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob2",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_2",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "47",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob3",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_3",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "48",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob4",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_4",
                "LOCATION" => $LOCATION->getName()
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "49",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob5",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_5",
                "LOCATION" => $LOCATION->getName()
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "50",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob6",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_6",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "51",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob7",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_7",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "52",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob8",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_8",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "53",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob9",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_9",
                "LOCATION" => $LOCATION->getName(),
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "54",
                "COUNT" => "10",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob10",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_10",
                "LOCATION" => $LOCATION->getName()
            ),
            false
        );
    }?>
</div>

<?/*div class="home-bg">
<div class="container container--no-padding in-view">
    <div class="column-10">
        <? $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            ".default",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => SITE_DIR . "includes/banner_respect_yourself_xl.php",
                "COMPONENT_TEMPLATE" => ".default",
                "EDIT_TEMPLATE" => "standard.php"
            ),
            false
        ); ?>
    </div>
    </div>


</div*/
?>
<?/*h1 class="center-header">Продажа брендовой обуви</h1*/
?>
<!--<div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfd4c97a5250230f3bfa0" data-stock-id="--><?//=$LOCATION->getUserShowcase()?><!--"></div>-->
<!--<div class="js-retail-rocket-recommendation2" data-retailrocket-markup-block="5ebcfd5a97a52821e059f022" data-stock-id="--><?//=$LOCATION->getUserShowcase()?><!--"></div>-->
<?
if (!Functions::checkMobileDevice()) {
    $APPLICATION->IncludeComponent(
        "likee:bestsellers",
        ".default",
        array(
            "COMPONENT_TEMPLATE" => ".default",
            "PRODUCTS_IBLOCK_TYPE" => "1c_catalog",
            "PRODUCTS_IBLOCK_ID" => IBLOCK_CATALOG,
            "PRICE_CODE" => array(0 => "Цена"),
            "ACTIONS_IBLOCK_TYPE" => "CONTENT",
            "ACTIONS_IBLOCK_ID" => "8",
            "INSATGRAM_LINK" => 'http://instagram.com/respectshoes',
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => "604800"
        ),
        false
    );
    if (COption::GetOptionString("likee", "instashopping_token", false)) {
        $APPLICATION->IncludeComponent(
            'likee:instagram.list',
            '',
            array(
                'COUNT' => 4
            ),
            false
        );
    }
}
?>
<?/*div class="container col-xs-12 container--no-padding phone--hidden in-view">
    <div class="column-10">
        <div class="content content--index">
            <div class="container">
                <div class="column-6 pre-2">
                    <h2>@RespectShoes</h2>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        ".default",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "includes/history.php",
                            "COMPONENT_TEMPLATE" => ".default",
                            "EDIT_TEMPLATE" => "standard.php"
                        ),
                        false
                    ); ?>
                </div>
            </div>
            <a href="<?= SITE_DIR; ?>company_about/" class="button button--third button--xxl">Наша история</a>
        </div>
    </div>
</div*/
?>

<?/* $APPLICATION->IncludeComponent(
"bitrix:news.list",
"home",
array(
    "DISPLAY_DATE" => "Y",
    "DISPLAY_NAME" => "Y",
    "DISPLAY_PICTURE" => "Y",
    "DISPLAY_PREVIEW_TEXT" => "Y",
    "AJAX_MODE" => "Y",
    "IBLOCK_TYPE" => "CONTENT",
    "IBLOCK_ID" => "6",
    "NEWS_COUNT" => "2",
    "SORT_BY1" => "ACTIVE_FROM",
    "SORT_ORDER1" => "DESC",
    "SORT_BY2" => "SORT",
    "SORT_ORDER2" => "ASC",
    "FILTER_NAME" => "",
    "FIELD_CODE" => array(
        0 => "",
        1 => ["ID"],
        2 => "",
    ),
    "PROPERTY_CODE" => array(
        0 => "",
        1 => ["DESCRIPTION"],
        2 => "",
    ),
    "CHECK_DATES" => "Y",
    "DETAIL_URL" => "",
    "PREVIEW_TRUNCATE_LEN" => "",
    "ACTIVE_DATE_FORMAT" => "m.Y",
    "SET_TITLE" => "N",
    "SET_BROWSER_TITLE" => "Y",
    "SET_META_KEYWORDS" => "Y",
    "SET_META_DESCRIPTION" => "Y",
    "SET_LAST_MODIFIED" => "Y",
    "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
    "ADD_SECTIONS_CHAIN" => "Y",
    "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
    "PARENT_SECTION" => "",
    "PARENT_SECTION_CODE" => "",
    "INCLUDE_SUBSECTIONS" => "Y",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "604800",
    "CACHE_FILTER" => "Y",
    "CACHE_GROUPS" => "Y",
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "N",
    "PAGER_TITLE" => "Новости",
    "PAGER_SHOW_ALWAYS" => "Y",
    "PAGER_TEMPLATE" => "",
    "PAGER_DESC_NUMBERING" => "Y",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "Y",
    "PAGER_BASE_LINK_ENABLE" => "Y",
    "SET_STATUS_404" => "N",
    "SHOW_404" => "N",
    "MESSAGE_404" => "",
    "PAGER_BASE_LINK" => "",
    "PAGER_PARAMS_NAME" => "arrPager",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_ADDITIONAL" => "",
    "COMPONENT_TEMPLATE" => ".default",
    "FILE_404" => ""
),
false
); */
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
