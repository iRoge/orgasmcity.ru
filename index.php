<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Город Оргазма. Секс-шоп №1 в России. Анонимно. Более 30 тысяч товаров");
$APPLICATION->SetTitle("Главная страница");
\Likee\Site\Helper::addBodyClass('page--index');
global $LOCATION;
?>

<div class="front-blocks">
    <?
    if (!Functions::checkMobileDevice()) {
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home",
            [
                "COMPONENT_TEMPLATE" => "home",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "21",
                "COUNT" => "20",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "AUTOPLAY_CODE" => "home_slider_autoplay_1",
                "LOCATION" => $LOCATION->getName(),
                'BANNER_TYPE' => 'Главный баннер'
            ],
            false
        );
        $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "home_actions",
            [
                "DISPLAY_DATE" => "N",
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "Y",
                "AJAX_MODE" => "Y",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "22",
                "NEWS_COUNT" => "10",
                "SORT_BY1" => "SORT",
                "SORT_ORDER1" => "ASC",
                "SORT_BY2" => "ID",
                "SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "arrFilterBanners",
                "FIELD_CODE" => [
                    0 => "",
                    1 => "ID",
                    2 => "",
                ],
                "PROPERTY_CODE" => [
                    0 => "",
                    1 => "DESCRIPTION",
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
                ],
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
                "FILE_404" => "",
                'BANNER_TYPE' => 'Маленький слайдер под главным',
            ],
            false
        );
    } else {
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "28",
                "COUNT" => "20",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob1",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_1",
                "LOCATION" => $LOCATION->getName(),
                'BANNER_TYPE' => 'Мобильный слайдер 1',
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "29",
                "COUNT" => "20",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob2",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_2",
                "LOCATION" => $LOCATION->getName(),
                'BANNER_TYPE' => 'Мобильный слайдер 2',
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "30",
                "COUNT" => "20",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob3",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_3",
                "LOCATION" => $LOCATION->getName(),
                'BANNER_TYPE' => 'Мобильный слайдер 3',
            ),
            false
        );
        $APPLICATION->IncludeComponent(
            "likee:slider",
            "home-mob",
            array(
                "COMPONENT_TEMPLATE" => "home-mob",
                "IBLOCK_TYPE" => "CONTENT",
                "IBLOCK_ID" => "31",
                "COUNT" => "20",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "604800",
                "CUSTOM_NUMBER" => "-mob4",
                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_4",
                "LOCATION" => $LOCATION->getName(),
                'BANNER_TYPE' => 'Мобильный слайдер 4',
            ),
            false
        );
    } ?>
    <div class="clearfix"></div>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:news.list',
        'home_advantages',
        [
            'IBLOCK_TYPE' => 'CONTENT',
            'IBLOCK_ID' => 'advantages',
            'PROPERTY_CODE' => [
                0 => 'IMG',
            ],
            'SORT_BY1' => 'sort',
            'SORT_ORDER1' => 'asc',
            'SET_TITLE' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000000',
            'CACHE_FILTER' => 'N',
            'CACHE_GROUPS' => 'N',
            'IS_MOBILE' => Functions::checkMobileDevice()
        ]
    );
    ?>
    <?php
    $APPLICATION->IncludeComponent(
        'orgasmcity:recommended',
        'home',
        []
    );
    ?>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
