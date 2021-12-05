<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("keywords_inner", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("title", "Город Оргазма. Секс шоп в России с доставкой на дом. Анонимно. Более 16 тысяч товаров");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Город Оргазма - это лучший секс-шоп в Москве с ассортиментом более 16 тысяч товаров для взрослых. Продажа интим товаров с бесплатной и АНОНИМНОЙ доставкой на дом. Огромный выбор продуктов: вибраторы, фаллоимитаторы, вибропули, мастурбаторы, секс-куклы, вакуумные помпы и многое другое");
$APPLICATION->SetTitle("Главная страница");
\Likee\Site\Helper::addBodyClass('page--index');
global $LOCATION;
global $DEVICE;
?>
<div class="front-blocks">
    <div class="main" style="margin-bottom: 20px">
        <h1 class="main-zagolovok">
            Город Оргазма - анонимный онлайн секс шоп (sex shop)
        </h1>
    </div>
    <?php
    if (!$DEVICE->isMobile()) {
        ?>
        <div class="main" style="display: flex">
        <?php
        $APPLICATION->IncludeComponent(
            'orgasmcity:catalogs.line',
            'main',
            [
                'FILTERS' => [
                    'IBLOCK_ID' => IBLOCK_CATALOG,
                    "ACTIVE" => "Y",
                    "XML_ID" => [1263, 1272, 1288, 1304, 1306, 1312, 1329, 1330, 1343, 1344, 1345, 1346]
                ],
                'SHOW_BACKGROUND' => false,
                'SHOW_SLIDER' => false,
                'ICONS_TYPE' => 'DEFAULT'
            ]
        );
        ?>
        </div>
        <?php
        $APPLICATION->IncludeComponent(
            'orgasmcity:mini.banners',
            'default',
            [
                'FILTERS' => [
                    'IBLOCK_ID' => IBLOCK_MINI_BANNERS,
                    "ACTIVE" => "Y",
                ],
            ]
        );
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
                'BANNER_TYPE' => 'Главный баннер'
            ],
            false
        );
        $APPLICATION->IncludeComponent(
            "orgasmcity:actions.banners",
            "",
            [],
            false
        );
    } else {
//        $APPLICATION->IncludeComponent(
//            "likee:slider",
//            "home-mob",
//            array(
//                "COMPONENT_TEMPLATE" => "home-mob",
//                "IBLOCK_TYPE" => "CONTENT",
//                "IBLOCK_ID" => "28",
//                "COUNT" => "20",
//                "CACHE_TYPE" => "A",
//                "CACHE_TIME" => "604800",
//                "CUSTOM_NUMBER" => "-mob1",
//                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_1",
//                "LOCATION" => $LOCATION->getName(),
//                'BANNER_TYPE' => 'Мобильный слайдер 1',
//            ),
//            false
//        );

        $APPLICATION->IncludeComponent(
            "orgasmcity:actions.banners",
            "",
            [],
            false
        );

//        $APPLICATION->IncludeComponent(
//            "likee:slider",
//            "home-mob",
//            array(
//                "COMPONENT_TEMPLATE" => "home-mob",
//                "IBLOCK_TYPE" => "CONTENT",
//                "IBLOCK_ID" => "29",
//                "COUNT" => "20",
//                "CACHE_TYPE" => "A",
//                "CACHE_TIME" => "604800",
//                "CUSTOM_NUMBER" => "-mob2",
//                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_2",
//                "LOCATION" => $LOCATION->getName(),
//                'BANNER_TYPE' => 'Мобильный слайдер 2',
//            ),
//            false
//        );
//        $APPLICATION->IncludeComponent(
//            "likee:slider",
//            "home-mob",
//            array(
//                "COMPONENT_TEMPLATE" => "home-mob",
//                "IBLOCK_TYPE" => "CONTENT",
//                "IBLOCK_ID" => "30",
//                "COUNT" => "20",
//                "CACHE_TYPE" => "A",
//                "CACHE_TIME" => "604800",
//                "CUSTOM_NUMBER" => "-mob3",
//                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_3",
//                "LOCATION" => $LOCATION->getName(),
//                'BANNER_TYPE' => 'Мобильный слайдер 3',
//            ),
//            false
//        );
//        $APPLICATION->IncludeComponent(
//            "likee:slider",
//            "home-mob",
//            array(
//                "COMPONENT_TEMPLATE" => "home-mob",
//                "IBLOCK_TYPE" => "CONTENT",
//                "IBLOCK_ID" => "31",
//                "COUNT" => "20",
//                "CACHE_TYPE" => "A",
//                "CACHE_TIME" => "604800",
//                "CUSTOM_NUMBER" => "-mob4",
//                "AUTOPLAY_CODE" => "home_slider_mobile_autoplay_4",
//                "LOCATION" => $LOCATION->getName(),
//                'BANNER_TYPE' => 'Мобильный слайдер 4',
//            ),
//            false
//        );
    }

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
            'CACHE_TIME' => '604800',
            'CACHE_FILTER' => 'N',
            'CACHE_GROUPS' => 'N',
        ]
    );
    ?>
    <?php
    $APPLICATION->IncludeComponent(
        'orgasmcity:products.line',
        'default',
        [
            'TITLE' => 'Выбор клиентов',
            'FILTERS' => [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "=PROPERTY_BESTSELLER_VALUE" => "1",
                "PRICE_FROM" => 500,
            ],
        ]
    );
    ?>
    <div class="default-section">
        <h2 class="default-header">Как мы работаем</h2>
        <div class="how-we-work-wrapper main">
            <img width="100%" class="lazy-img" data-src="<?=SITE_TEMPLATE_PATH . ($DEVICE->isMobile() ? '/img/howWorkMobile.webp' : '/img/howWork.webp')?>" alt="Как мы работаем">
        </div>
    </div>
    <?php
    $APPLICATION->IncludeComponent(
        "orgasmcity:brands.list",
        "",
        [],
        false
    );
    ?>
    <?php if (!$DEVICE->isMobile()) { ?>
        <div class="order-help-section">
            <div class="order-help-wrapper main">
                <img width="100%" class="lazy-img" data-src="<?=SITE_TEMPLATE_PATH?>/img/clientHelpBlock.webp" alt="Не знаете что выбрать?">
            </div>
        </div>
    <?php } ?>
    <?php $APPLICATION->IncludeComponent(
        "orgasmcity:feedback.list",
        "main",
        [
            'FILTERS' => [
                'IBLOCK_ID' => IBLOCK_FEEDBACK,
                'ACTIVE' => 'Y',
                'PROPERTY_PRODUCT_ID' => false
            ],
        ],
        false
    );
    ?>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>