<? use Bitrix\Main\Page\Asset;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @const SITE_ID */
/** @const SITE_TEMPLATE_PATH */
?>
<!doctype html>
<html lang="<?= LANGUAGE_ID; ?>" style="background-color: #fff5f7;">
<head>
    <meta charset="UTF-8">
    <title><? $APPLICATION->ShowTitle(); ?></title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg" type="image/svg+xml">

    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="mailru-verification" content="d9b83fb9577bb7bd"/>
    <meta name="mailru-verification" content="2e2a6b2c8292fc5b"/>
    <meta name="yandex-verification" content="9150aa1aa386cc50"/>
    <meta name="yandex-verification" content="fbad8d6555dc89a3"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?
    \Bitrix\Main\Loader::includeModule('likee.site');
    \Bitrix\Main\Loader::includeModule('likee.location');
    global $LOCATION;
    global $DEVICE;

    $APPLICATION->ShowHead();
    $bMainPage = $APPLICATION->GetCurDir() == '/';
    //    CJSCore::Init(['ajax']);

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/style.min.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/application.min.css?up=1');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/bootstrap.min.css');
    if (!($DEVICE->isTablet() || $DEVICE->isMobile())) {
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick.min.css');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick-theme.min.css');
    }
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/fixes.min.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/js/select2/select2.min.css");
    //    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/swiper.css');

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-3.3.1.min.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/bootstrap.min.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.zoom.min.js');

    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.ellipsis.min.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/index.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/microplugin.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/sifter.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/selectize.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/wNumb.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/nouislider.js');

    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.ellipsis.min.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.inputmask.bundle.min.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/messages_ru.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/slick.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/tooltipster.bundle.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/bowser.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/clipboard.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/tinycolor.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.inview.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/swiper.js');

    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/ajax.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/tabs.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/subscribe.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/shop-list.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/vacancy.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/dropdown.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/map.js');
    //    \Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/product.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/cart-item.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/toggle.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/product-gallery.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/animate-scroll.js');

    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/toggle.js');

    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/product.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/cart.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/shop.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/order-pickup.js');
    //    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/bonuses.js');

    // Получаем количество избранных товаров
    $favoritesCount = 0;
    if ($USER->IsAuthorized()) {
        $arUser = $USER->GetByID($USER->GetID())->Fetch();
        $favoritesCount = count($arUser['UF_FAVORITES']);
    } else {
        if (isset($_COOKIE['favorites'])) {
            $favoritesCount = count(unserialize($_COOKIE['favorites']));
        }
    }
    ?>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/global/popup.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/global/to-top.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/global/cart.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/counter.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/clearable.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/phone.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/jquery.maskedinput.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/size.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/inputs/sku.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/global/application.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/global/show-more.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/custom.min.js' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/script.min.js?up=1' ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/lib/lazy.min.js' ?>" defer></script>
    <?php if (!($DEVICE->isTablet() || $DEVICE->isMobile())) { ?>
        <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/slick.min.js' ?>" defer></script>
    <?php } ?>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . "/js/select2/select2.min.js" ?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . "/js/select2/select2.ru.min.js" ?>" defer></script>
<!--    <script type="text/javascript" data-skip-moving="true" src="--><?//= SITE_TEMPLATE_PATH . '/lib/jquery.validate.min.js' ?><!--" defer></script>-->
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/lib/underscore.min.js' ?>" defer></script>
    <!--    <script type="text/javascript" data-skip-moving="true" src="-->
    <? //=SITE_TEMPLATE_PATH . '/lib/jquery.datetimepicker.full.min.js'?><!--" defer></script>-->
    <!--    <script type="text/javascript" data-skip-moving="true" src="-->
    <? //=SITE_TEMPLATE_PATH . '/js/inputs/datetime.js'?><!--" defer></script>-->
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/pages/_default.min.js' ?>"
            defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/pages/index.min.js' ?>"
            defer></script>


    <script type="text/javascript" data-skip-moving="true" async defer>
        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function setAttr(prmName, val) {
            var res = '';
            var d = location.href.split("#")[0].split("?");
            var base = d[0];
            var query = d[1];
            if (query) {
                var params = query.split("&");
                for (var i = 0; i < params.length; i++) {
                    var keyval = params[i].split("=");
                    if (keyval[0] != prmName) {
                        res += params[i] + '&';
                    }
                }
            }
            res += prmName + '=' + val;
            return base + '?' + res;
        }
    </script>
    <script type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/device.js' ?>"></script>
    <script type="text/javascript" data-skip-moving="true">
        let phpDeviceType = '<?= $GLOBALS['device_type'] ?>';
        let targetDeviceType = '';
        if (device.tablet() === true) {
            targetDeviceType = 'tablet';
        } else if (device.mobile() === true) {
            targetDeviceType = 'mobile';
        } else {
            targetDeviceType = 'pc';
        }
        setCookie('device_type', targetDeviceType);
        if (phpDeviceType !== targetDeviceType) {
            document.location.reload();
        }
    </script>

    <!-- Jivosite -->
        <script type="text/javascript" src="//code-ya.jivosite.com/widget/IanrVwAEsl" async defer></script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" async defer>
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(82799680, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: false,
            ecommerce: "metrikaData"
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/82799680" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script type="text/javascript" src="https://www.googletagmanager.com/gtag/js?id=UA-202524127-1" async defer></script>
    <script type="text/javascript" async>
        window.metrikaData = window.metrikaData || [];
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-202524127-1');
    </script>

    <? $GLOBALS["PAGE"] = explode("/", $APPLICATION->GetCurPage()); ?>
</head>

<?
$bShowRegister = !empty($_POST['REGISTER']) && is_array($_POST['REGISTER']) && !empty($_REQUEST['HEADER_FORM']);
$bShowAuth = isset($_POST['AUTH_FORM']) && $_POST['AUTH_FORM'] == 'Y' && !empty($_REQUEST['HEADER_FORM']);

if (!$USER->IsAuthorized() && ($bShowAuth || $bShowRegister)) {
    \Qsoft\Helpers\SiteHelper::addBodyClass('body--auth');
}
?>
<body class="<? $APPLICATION->ShowProperty('BODY_CLASS'); ?>">
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="body-settings">
    <div class="podlozhka"></div>
    <?php
    if ($DEVICE->isTablet() || $DEVICE->isMobile()) {
        $APPLICATION->IncludeComponent(
            'bitrix:menu',
            'mobile',
            array(
                'COMPONENT_TEMPLATE' => 'mobile',
                'ROOT_MENU_TYPE' => 'top',
                'MENU_CACHE_TYPE' => 'N',
                'MENU_CACHE_TIME' => '604800',
                'MENU_CACHE_USE_GROUPS' => 'Y',
                'MENU_CACHE_GET_VARS' => array(),
                'MAX_LEVEL' => '3',
                'CHILD_MENU_TYPE' => '',
                'USE_EXT' => 'Y',
                'DELAY' => 'N',
                'ALLOW_MULTI_SELECT' => 'N',
                'MAIN_MENU' => 'Y'
            )
        );
    }
    ?>
    <?php
    $APPLICATION->ShowViewContent('geolocation_popup');
    ?>

    <div class="top col-xs-12 tolltips">
        <div class="main" style="display: flex;align-items: center;height: 100%;">
            <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12 info">
                <? $APPLICATION->IncludeComponent(
                    'qsoft:geolocation',
                    '.default',
                    array(
                        'CACHE_TYPE' => 'A',
                        'CACHE_TIME' => 31536000,
                    )
                ); ?>
                <?php $phone = SUPPORT_PHONE ?>
                <div class="col-xs-3 phone-xs">
                    <p class="header-container">
                        <img class="header-mail-icon mail mail2" src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/support.svg"
                             alt="mail"/>
                        <a class="header-call-icon"
                           href="tel:+<?= str_replace([' ', '(', ')', '-', '+'], '', $phone) ?>"></a>
                    </p>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-3 hidden-xs info">
                <div class="phone-top">
                    <?php if (!$DEVICE->isMobile() || $DEVICE->isTablet()) { ?>
                        <img class="phone-icon" src="<?= SITE_TEMPLATE_PATH ?>/img/svg/phone.svg"/>
                    <?php } ?>
                    <p class="phone-top-first">
                        <span>Интернет-магазин: </span>
                        <span><a class="phone-top-link" href="tel:<?= $phone ?>"><?= $phone ?></a></span>
                    </p>
                    <p class="hidden-sm phone-top-second">
                        <span>Режим работы справочной: </span>
                        <span>с 12.00 - 18.00 СБ, ВС - выходной</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-5 hidden-xs right-block-top">
                <div class="col-md-3 col-sm-3 hidden-xs auth<?= $USER->IsAuthorized() ? '' : ' ent' ?>">
                    <?php
                    $APPLICATION->ShowViewContent("AUTH_HEAD_BLOCK");
                    ?>
                </div>
                <div class="col-md-3 col-sm-3 hidden-xs mail mail2">
                    <img class="header-icon" src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/support.svg"/>
                    <span>Поддержка</span>
                </div>
                <div class="col-md-3 col-sm-3 hidden-xs cart heart">
                    <a class="favorites_header" href="/catalog/favorites/">
                        <p class="count count--heart in-full"><?= $_COOKIE['favorites_count'] ?? '0' ?></p>
                        <img class="header-icon" src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/heart.svg"/>
                        <span>Избранное</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-3 hidden-xs cart">
                    <? $APPLICATION->IncludeComponent(
                        "likee:basket.small",
                        "",
                        array(
                            "PATH_TO_BASKET" => "/cart/",
                            "PATH_TO_ORDER" => "/order/",
                            "SHOW_DELAY" => "Y",
                            "SHOW_NOTAVAIL" => "Y",
                            "SHOW_SUBSCRIBE" => "Y",
                            'CACHE_TYPE' => 'N'
                        )
                    ); ?>

                </div>
            </div>
        </div>
        <div class="header-border-wrapper main">
            <div class="header-border"></div>
        </div>
    </div>
    <div class="menu-spacer">
        <div class="poisk-div">
            <? $APPLICATION->IncludeComponent(
                "bitrix:search.form",
                "mob",
                array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "PAGE" => "#SITE_DIR#catalog/search/"
                ),
                false
            ); ?>
        </div>
        <div class="menu-wrap">
            <div class="menu col-xs-12">
                <div class="main clearfix" style="display: flex; align-items: center">
                    <div class="col-md-2 col-sm-2 col-xs-3 logo-div">
                        <a href="/">
                            <svg class="logo" version="1.2" baseProfile="tiny-ps" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 81" width="100%" height="100%">
                                <title>logo_new-svg</title>
                                <defs>
                                    <linearGradient id="grd1" gradientUnits="userSpaceOnUse"  x1="0" y1="36.364" x2="45.128" y2="36.364">
                                        <stop offset="0" stop-color="#667eea"  />
                                        <stop offset="1" stop-color="#764ba2"  />
                                    </linearGradient>
                                    <linearGradient id="grd2" gradientUnits="userSpaceOnUse"  x1="42.179" y1="37.091" x2="87.307" y2="37.091">
                                        <stop offset="0" stop-color="#6a11cb"  />
                                        <stop offset="1" stop-color="#2575fc"  />
                                    </linearGradient>
                                    <linearGradient id="grd3" gradientUnits="userSpaceOnUse"  x1="32.026" y1="48.727" x2="69.65" y2="48.727">
                                        <stop offset="0" stop-color="#a18cd1"  />
                                        <stop offset="1" stop-color="#fbc2eb"  />
                                    </linearGradient>
                                    <linearGradient id="grd4" gradientUnits="userSpaceOnUse"  x1="67.19" y1="49.353" x2="104.814" y2="49.353">
                                        <stop offset="0" stop-color="#fbc2eb"  />
                                        <stop offset="1" stop-color="#a6c1ee"  />
                                    </linearGradient>
                                </defs>
                                <style>
                                    tspan { white-space:pre }
                                    .shp0 { fill: url(#grd1) }
                                    .shp1 { fill: url(#grd2) }
                                    .shp2 { fill: url(#grd3) }
                                    .shp3 { fill: url(#grd4) }
                                    .shp4 { fill: #000000 }
                                </style>
                                <path id="Layer" class="shp0" d="M45.13 21.82C45.13 33.87 45.13 50.18 45.13 72.73C19.65 53.09 0 33.87 0 21.82C0 9.77 10.1 0 22.56 0C35.03 0 45.13 9.77 45.13 21.82Z" />
                                <path id="Layer" class="shp1" d="M87.31 23.27C87.31 35.32 56.77 63.27 45.09 72.73C45.09 64 42.18 35.32 42.18 23.27C42.18 11.22 52.28 1.45 64.74 1.45C77.2 1.45 87.31 11.22 87.31 23.27Z" />
                                <path id="Layer" class="shp2" d="M69.65 36.22C69.65 46.58 69.65 60.61 69.65 80C48.41 63.11 32.03 46.58 32.03 36.22C32.03 25.86 40.45 17.45 50.84 17.45C61.23 17.45 69.65 25.86 69.65 36.22Z" />
                                <path id="Layer" class="shp3" d="M104.81 37.47C104.81 47.83 79.36 71.87 69.62 80C69.62 72.49 67.19 47.83 67.19 37.47C67.19 27.11 75.61 18.71 86 18.71C96.39 18.71 104.81 27.11 104.81 37.47Z" />
                                <g id="Layer">
                                    <path id="Layer" fill-rule="evenodd" class="shp4" d="M63.74 39.62L63.54 39.66C63.45 39.35 63.43 39.02 63.48 38.66C63.53 38.3 63.61 37.94 63.72 37.58C63.83 37.21 64.01 36.85 64.28 36.52C64.56 36.17 64.88 35.87 65.24 35.6C65.6 35.33 66.06 35.12 66.62 34.96C67.18 34.8 67.79 34.72 68.46 34.72C70.5 34.72 72.16 35.4 73.44 36.76C74.73 38.11 75.38 39.78 75.38 41.78C75.38 43.99 74.62 45.8 73.1 47.2C71.58 48.59 69.65 49.28 67.32 49.28C64.99 49.28 63.05 48.59 61.5 47.2C59.97 45.81 59.2 44.01 59.2 41.8C59.2 40.37 59.53 39.11 60.2 38C60.87 36.89 61.82 36.03 63.06 35.4L63.18 35.58C62.31 36.15 61.69 36.98 61.3 38.06C60.93 39.14 60.74 40.29 60.74 41.5C60.74 43.75 61.35 45.51 62.58 46.78C63.81 48.05 65.37 48.68 67.28 48.68C69.21 48.68 70.79 48.05 72 46.78C73.23 45.5 73.84 43.82 73.84 41.74C73.84 39.94 73.35 38.42 72.38 37.18C71.42 35.94 70.06 35.32 68.3 35.32C67.51 35.32 66.8 35.46 66.16 35.74C65.52 36.01 65.02 36.36 64.66 36.8C64.3 37.23 64.03 37.69 63.86 38.18C63.7 38.67 63.66 39.15 63.74 39.62ZM77.17 35L82.57 35C83.74 35 84.68 35.33 85.39 36C86.11 36.65 86.47 37.53 86.47 38.62C86.47 39.7 86.14 40.63 85.49 41.42C84.84 42.19 84.03 42.58 83.07 42.58L88.31 48.28C88.83 48.83 89.24 49.25 89.55 49.56C89.87 49.88 90.28 50.25 90.77 50.66C91.28 51.09 91.72 51.4 92.11 51.6C92.5 51.81 92.94 51.98 93.43 52.1C93.94 52.23 94.44 52.27 94.93 52.2L94.93 52.4C94.4 52.55 93.88 52.62 93.39 52.62C92.9 52.63 92.42 52.58 91.97 52.46C91.52 52.34 91.08 52.18 90.67 51.98C90.27 51.79 89.87 51.54 89.47 51.22C89.07 50.91 88.7 50.59 88.35 50.26C88 49.93 87.64 49.54 87.25 49.1L81.87 43.12C81.32 42.51 80.92 42.19 80.67 42.16L80.67 41.96C81.31 41.96 81.66 41.96 81.73 41.96C81.97 41.95 82.18 41.93 82.35 41.9C82.52 41.87 82.76 41.82 83.05 41.74C83.34 41.65 83.59 41.53 83.79 41.4C84 41.25 84.21 41.05 84.41 40.8C84.61 40.53 84.76 40.23 84.85 39.88C84.94 39.53 84.98 39.13 84.97 38.68C84.94 37.77 84.72 37.04 84.29 36.48C83.88 35.92 83.28 35.63 82.49 35.62L80.01 35.62L80.01 47.54C80.01 47.87 80.13 48.17 80.37 48.42C80.62 48.67 80.93 48.8 81.29 48.8L81.53 48.8L81.55 49L77.17 49L77.17 48.8L77.43 48.8C77.76 48.8 78.06 48.68 78.31 48.44C78.56 48.2 78.7 47.91 78.71 47.56L78.71 36.46C78.7 36.11 78.56 35.82 78.31 35.58C78.07 35.33 77.78 35.2 77.43 35.2L77.17 35.2L77.17 35ZM104.11 42.5L103.81 42.5C103.47 42.5 103.22 42.58 103.05 42.74C102.87 42.89 102.79 43.11 102.81 43.4L102.81 46.7C102.26 47.42 101.5 48.03 100.53 48.54C99.55 49.03 98.38 49.28 97 49.28C94.87 49.27 93.14 48.59 91.81 47.26C90.48 45.93 89.83 44.17 89.83 42C89.83 39.83 90.48 38.07 91.81 36.74C93.14 35.39 94.87 34.72 97 34.72C98.84 34.72 100.47 35.17 101.86 36.08L102.27 38.9L102.08 38.9C101.79 37.75 101.2 36.87 100.31 36.26C99.42 35.63 98.33 35.32 97 35.32C95.31 35.32 93.94 35.93 92.89 37.16C91.84 38.39 91.33 40 91.33 42C91.33 44 91.84 45.61 92.89 46.84C93.94 48.05 95.31 48.67 97 48.68C97.87 48.68 98.69 48.53 99.47 48.22C100.25 47.91 100.94 47.49 101.53 46.94L101.53 43.4C101.53 43.11 101.43 42.89 101.25 42.74C101.07 42.58 100.83 42.5 100.5 42.5L100.19 42.5L100.19 42.3L104.11 42.3L104.11 42.5ZM123.38 52.82L123.4 53.02C122.76 53.23 122.14 53.34 121.52 53.34C120.91 53.34 120.3 53.18 119.68 52.86C119.07 52.55 118.49 52.12 117.94 51.56C117.41 51.01 116.89 50.29 116.38 49.38C115.89 48.49 115.44 47.45 115.04 46.26L114.26 43.98L108.78 43.98L107.4 47.54C107.26 47.91 107.27 48.22 107.44 48.46C107.63 48.69 107.87 48.8 108.16 48.8L108.32 48.8L108.32 49L104.3 49L104.3 48.8L104.48 48.8C104.8 48.8 105.12 48.71 105.42 48.52C105.73 48.32 105.98 48.03 106.18 47.64L111.14 35.76C110.12 34.29 108.71 33.88 106.92 34.52L106.86 34.32C107.37 34.11 107.85 33.96 108.3 33.88C108.77 33.8 109.18 33.77 109.52 33.78C109.88 33.79 110.23 33.88 110.56 34.04C110.9 34.19 111.18 34.35 111.42 34.52C111.66 34.68 111.9 34.92 112.12 35.24C112.36 35.56 112.56 35.84 112.7 36.08C112.86 36.32 113.03 36.64 113.2 37.04C113.38 37.44 113.51 37.77 113.6 38.02C113.7 38.26 113.81 38.58 113.94 38.98L116.38 45.9C116.65 46.62 116.92 47.3 117.2 47.94C117.5 48.57 117.86 49.23 118.3 49.92C118.74 50.61 119.2 51.19 119.68 51.64C120.16 52.11 120.72 52.45 121.36 52.68C122.02 52.92 122.69 52.97 123.38 52.82ZM109 43.38L114.06 43.38L112.48 38.72C112.18 37.83 111.9 37.13 111.64 36.62L109 43.38ZM122.14 39.46L126.08 43.1C126.75 43.71 127.08 44.53 127.08 45.54C127.08 46.66 126.74 47.57 126.06 48.26C125.38 48.94 124.48 49.28 123.34 49.28C122.76 49.28 122.13 49.19 121.46 49C120.81 48.83 120.3 48.55 119.94 48.18C119.88 47.89 119.86 47.46 119.9 46.9C119.94 46.34 120.04 45.84 120.18 45.4L120.36 45.4C120.26 46.41 120.48 47.23 121.04 47.84C121.6 48.45 122.34 48.73 123.24 48.68C124 48.64 124.64 48.38 125.14 47.9C125.66 47.41 125.92 46.77 125.92 46C125.92 45.24 125.66 44.62 125.14 44.14L121.3 40.58C120.58 39.91 120.22 39.09 120.22 38.1C120.22 37.19 120.54 36.41 121.16 35.74C121.8 35.06 122.66 34.72 123.74 34.72C124.25 34.72 124.82 34.79 125.46 34.92L126.24 34.92L126.24 37.38L126.06 37.38C126.06 36.75 125.84 36.25 125.4 35.88C124.98 35.51 124.44 35.32 123.8 35.32C123.08 35.32 122.49 35.55 122.02 36C121.57 36.45 121.34 36.99 121.34 37.62C121.34 38.35 121.61 38.97 122.14 39.46ZM143.23 34.72L145.09 46.56C145.37 48.43 145.99 49.85 146.93 50.84C147.89 51.84 149.07 52.31 150.47 52.24L150.47 52.44C149.49 52.59 148.58 52.53 147.75 52.28C146.93 52.04 146.24 51.65 145.69 51.1C145.15 50.55 144.7 49.91 144.35 49.16C144.01 48.43 143.77 47.62 143.65 46.74L142.43 37.84L138.27 47.24C137.9 48.05 137.67 48.73 137.59 49.28L137.39 49.28L132.21 38.02L131.33 47.96C131.31 48.19 131.38 48.39 131.55 48.56C131.73 48.73 131.91 48.82 132.09 48.82L132.41 48.82L132.41 49L128.53 49L128.53 48.8L128.85 48.8C129.21 48.8 129.53 48.69 129.79 48.48C130.07 48.25 130.25 47.96 130.31 47.6L131.57 36.68C130.72 34.99 129.74 33.41 128.64 32.95C128.93 32.99 129.2 33.05 129.45 33.12C129.81 33.23 130.15 33.39 130.47 33.6C130.79 33.8 131.07 34.01 131.31 34.24C131.55 34.45 131.77 34.69 131.97 34.96C132.19 35.23 132.36 35.47 132.49 35.7C132.63 35.91 132.75 36.13 132.85 36.34L137.67 46.78L143.05 34.72L143.23 34.72ZM126.89 33.24L126.89 33.04C127.37 32.92 127.83 32.88 128.25 32.92C128.38 32.93 128.5 33.37 128.61 33.45C128.07 33.23 127.5 33.16 126.89 33.24ZM128.61 33.45C128.62 33.45 128.63 33.46 128.64 33.46C128.63 33.46 128.62 33.46 128.61 33.45ZM113.72 61.5L113.92 61.5C114.01 64.03 113.42 65.97 112.14 67.3C110.87 68.62 109.11 69.28 106.86 69.28C105.34 69.28 103.97 68.96 102.74 68.32C101.53 67.68 100.58 66.81 99.9 65.7C99.23 64.59 98.9 63.35 98.9 61.98C98.9 60.62 99.23 59.39 99.9 58.28C100.57 57.17 101.49 56.31 102.66 55.68C103.83 55.04 105.14 54.72 106.58 54.72C107.49 54.72 108.41 54.85 109.34 55.1C110.29 55.34 111.07 55.67 111.7 56.08L112.1 58.9L111.92 58.9C111.64 57.78 110.99 56.91 109.98 56.28C108.97 55.64 107.84 55.32 106.6 55.32C104.83 55.32 103.35 55.95 102.16 57.22C100.99 58.47 100.4 60.05 100.4 61.94C100.4 63.86 101.01 65.47 102.22 66.76C103.45 68.04 105.03 68.68 106.96 68.68C108.97 68.68 110.6 68.04 111.84 66.76C113.09 65.48 113.72 63.73 113.72 61.5ZM120.01 68.8L120.01 69L115.63 69L115.63 68.8L115.89 68.8C116.23 68.8 116.53 68.68 116.77 68.44C117.02 68.19 117.15 67.89 117.17 67.54L117.17 56.44C117.15 56.09 117.02 55.8 116.77 55.56C116.53 55.32 116.23 55.2 115.89 55.2L115.63 55.2L115.63 55L120.01 55L120.01 55.2L119.75 55.2C119.4 55.2 119.1 55.32 118.85 55.56C118.61 55.8 118.48 56.09 118.47 56.44L118.47 67.54C118.47 67.89 118.59 68.19 118.85 68.44C119.1 68.68 119.4 68.8 119.75 68.8L120.01 68.8ZM123.98 54.98L130.74 54.98C131.16 54.98 131.61 54.94 132.1 54.86C132.61 54.78 132.96 54.7 133.16 54.62L133.16 57.14L132.96 57.12L132.96 56.78C132.96 56.46 132.85 56.19 132.62 55.96C132.4 55.72 132.13 55.59 131.82 55.58L128.02 55.58L128.02 67.56C128.04 67.91 128.16 68.2 128.4 68.44C128.66 68.68 128.96 68.8 129.3 68.8L129.56 68.8L129.56 69L125.18 69L125.18 68.8L125.44 68.8C125.79 68.8 126.08 68.68 126.32 68.44C126.58 68.2 126.71 67.91 126.72 67.58L126.72 55.58L122.92 55.58C122.6 55.59 122.33 55.72 122.1 55.96C121.89 56.19 121.78 56.46 121.78 56.78L121.78 57.12L121.58 57.14L121.58 54.62C121.78 54.7 122.13 54.78 122.62 54.86C123.12 54.94 123.57 54.98 123.98 54.98ZM132.75 54.06L132.75 53.86C133.24 53.83 133.71 53.87 134.17 53.96C134.62 54.05 135.02 54.18 135.37 54.34C135.73 54.49 136.07 54.71 136.41 55C136.75 55.28 137.05 55.55 137.29 55.8C137.53 56.04 137.77 56.36 138.01 56.76C138.26 57.16 138.46 57.5 138.61 57.78C138.77 58.06 138.94 58.43 139.13 58.88C139.31 59.33 139.45 59.68 139.53 59.92C139.62 60.15 139.75 60.47 139.91 60.9C140 61.17 140.07 61.36 140.11 61.48C140.15 61.39 140.2 61.25 140.27 61.06C140.44 60.59 140.57 60.23 140.67 59.98C140.77 59.73 140.92 59.37 141.11 58.9C141.31 58.43 141.48 58.07 141.63 57.8C141.77 57.53 141.96 57.2 142.19 56.8C142.43 56.39 142.65 56.06 142.87 55.82C143.08 55.58 143.33 55.32 143.63 55.04C143.93 54.76 144.23 54.55 144.53 54.4C144.83 54.24 145.17 54.11 145.55 54.02C145.92 53.91 146.31 53.86 146.73 53.86L146.73 54.06C145.98 54.06 145.21 54.45 144.43 55.24C143.65 56.03 142.99 56.98 142.43 58.1C141.88 59.21 141.43 60.36 141.07 61.56C140.72 62.75 140.55 63.76 140.55 64.6L140.55 67.54C140.55 67.87 140.67 68.17 140.91 68.42C141.16 68.67 141.47 68.8 141.83 68.8L142.07 68.8L142.07 69L137.71 69L137.71 68.8L137.97 68.8C138.3 68.8 138.59 68.68 138.85 68.44C139.1 68.2 139.23 67.91 139.25 67.56L139.25 64.58C139.25 63.69 139.06 62.64 138.69 61.44C138.31 60.24 137.83 59.09 137.25 58C136.66 56.89 135.96 55.96 135.15 55.2C134.33 54.44 133.53 54.06 132.75 54.06ZM146.46 68.82C146.28 68.99 146.07 69.08 145.82 69.08C145.56 69.08 145.35 68.99 145.18 68.82C145 68.65 144.92 68.43 144.92 68.18C144.92 67.93 145 67.71 145.18 67.54C145.35 67.37 145.56 67.28 145.82 67.28C146.07 67.28 146.28 67.37 146.46 67.54C146.63 67.71 146.72 67.93 146.72 68.18C146.72 68.43 146.63 68.65 146.46 68.82ZM148.79 55L154.19 55C155.36 55 156.3 55.33 157.01 56C157.73 56.65 158.09 57.53 158.09 58.62C158.09 59.7 157.76 60.63 157.11 61.42C156.46 62.19 155.65 62.58 154.69 62.58L159.93 68.28C160.45 68.83 160.86 69.25 161.17 69.56C161.49 69.88 161.9 70.25 162.39 70.66C162.9 71.09 163.34 71.4 163.73 71.6C164.12 71.81 164.56 71.98 165.05 72.1C165.56 72.23 166.06 72.27 166.55 72.2L166.55 72.4C166.02 72.55 165.5 72.62 165.01 72.62C164.52 72.63 164.04 72.58 163.59 72.46C163.14 72.34 162.7 72.18 162.29 71.98C161.89 71.79 161.49 71.54 161.09 71.22C160.69 70.91 160.32 70.59 159.97 70.26C159.62 69.93 159.26 69.54 158.87 69.1L153.49 63.12C152.94 62.51 152.54 62.19 152.29 62.16L152.29 61.96C152.93 61.96 153.28 61.96 153.35 61.96C153.59 61.95 153.8 61.93 153.97 61.9C154.14 61.87 154.38 61.82 154.67 61.74C154.96 61.65 155.21 61.53 155.41 61.4C155.62 61.25 155.83 61.05 156.03 60.8C156.23 60.53 156.38 60.23 156.47 59.88C156.56 59.53 156.6 59.13 156.59 58.68C156.56 57.77 156.34 57.04 155.91 56.48C155.5 55.92 154.9 55.63 154.11 55.62L151.63 55.62L151.63 67.54C151.63 67.87 151.75 68.17 151.99 68.42C152.24 68.67 152.55 68.8 152.91 68.8L153.15 68.8L153.17 69L148.79 69L148.79 68.8L149.05 68.8C149.38 68.8 149.68 68.68 149.93 68.44C150.18 68.2 150.32 67.91 150.33 67.56L150.33 56.46C150.32 56.11 150.18 55.82 149.93 55.58C149.69 55.33 149.4 55.2 149.05 55.2L148.79 55.2L148.79 55ZM171.46 55L175.16 55L175.16 55.2L174.84 55.2C174.56 55.2 174.31 55.29 174.1 55.48C173.9 55.65 173.79 55.87 173.78 56.14L173.78 64.06C173.78 65.62 173.29 66.88 172.3 67.84C171.31 68.8 170.01 69.28 168.38 69.28C166.73 69.28 165.39 68.81 164.36 67.86C163.35 66.9 162.84 65.64 162.84 64.08L162.84 56.2C162.84 55.91 162.73 55.67 162.52 55.48C162.32 55.29 162.07 55.2 161.78 55.2L161.46 55.2L161.46 55L165.52 55L165.52 55.2L165.2 55.2C164.91 55.2 164.66 55.29 164.46 55.48C164.26 55.65 164.15 55.88 164.14 56.16L164.14 63.8C164.14 65.25 164.53 66.43 165.3 67.32C166.09 68.21 167.11 68.66 168.38 68.66C169.73 68.66 170.81 68.24 171.62 67.4C172.43 66.55 172.84 65.43 172.84 64.06L172.84 56.16C172.83 55.88 172.72 55.65 172.52 55.48C172.32 55.29 172.07 55.2 171.78 55.2L171.46 55.2L171.46 55Z" />
                                </g>
                            </svg>
                        </a>
                    </div>
                    <div class="col-md-8 menu-div">
                        <?php
                        if (!($DEVICE->isTablet() || $DEVICE->isMobile())) {
                            $APPLICATION->IncludeComponent(
                                'bitrix:menu',
                                'top',
                                array(
                                    'COMPONENT_TEMPLATE' => '.default',
                                    'ROOT_MENU_TYPE' => 'top',
                                    'MENU_CACHE_TYPE' => 'N',
                                    'MENU_CACHE_TIME' => '604800',
                                    'MENU_CACHE_USE_GROUPS' => 'Y',
                                    'MENU_CACHE_GET_VARS' => [],
                                    'MAX_LEVEL' => '3',
                                    'CHILD_MENU_TYPE' => '',
                                    'USE_EXT' => 'Y',
                                    'DELAY' => 'N',
                                    'ALLOW_MULTI_SELECT' => 'N',
                                    'MAIN_MENU' => 'Y'
                                )
                            );
                        }
                        ?>
                    </div>
                    <div class="col-md-2 col-sm-10 col-xs-9 search-div header__search" style="padding-left: 0">
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:search.form",
                            "top",
                            array(
                                "COMPONENT_TEMPLATE" => ".default",
                                "PAGE" => "#SITE_DIR#catalog/search/"
                            ),
                            false
                        ); ?>
                        <div class="hidden-sm col-xs-2 mail touch-for-poisk">
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/search-colored.svg" width="20px"
                                 height="20px"/>
                        </div>
                        <div class="hidden-sm col-xs-2 mail auth2<?= $USER->IsAuthorized() ? '' : ' ent' ?>">
                            <? if (!$USER->IsAuthorized()) : ?>
                                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/cabinet.svg" width="20px" height="20px"/>
                            <? else : ?>
                                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/cabinet.svg" width="20px" height="20px"/>
                                <div class="auth-div menu_mob_fly" style="margin-top: 10px!important;">
                                    <a href="/personal/orders/">История заказов</a><br/>
                                    <a href="/personal/bonuses/">Бонусы</a><br/>
                                    <a href="/personal/">Личные данные</a><br/>
                                    <a href="/personal/subscribe/">Управление рассылкой</a><br/>
                                    <a href="<?= $APPLICATION->GetCurPage() ?>?logout=yes"
                                       id="logout-btn">Выйти</a><br/>
                                </div>
                            <? endif; ?>
                        </div>
                        <div class="hidden-sm col-xs-2 cart heart">
                            <a class="favorites_header" href="/catalog/favorites/">
                                <p class="count count--heart"><?= $favoritesCount ?></p>
                                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/heart.svg" width="20px" height="20px"/>
                            </a>
                        </div>
                        <div class="hidden-sm col-xs-2 cart">
                            <? $APPLICATION->IncludeComponent(
                                "likee:basket.small",
                                "mobile",
                                array(
                                    "PATH_TO_BASKET" => "/cart/",
                                    "PATH_TO_ORDER" => "/order/",
                                    "SHOW_DELAY" => "Y",
                                    "SHOW_NOTAVAIL" => "Y",
                                    "SHOW_SUBSCRIBE" => "Y",
                                    'CACHE_TYPE' => 'N'
                                )
                            ); ?>
                        </div>
                        <div class="blue-menu hidden-lg hidden-md col-xs-2">
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/burger.svg" alt="Burger" width="20px"
                                 height="20px">
                            <span>Каталог</span>
                        </div>
                        <div class="cls-blue-menu">
                            <svg width="65%" height="65%" viewBox="0 0 22 22" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <line x1="1.93934" y1="20.4462" x2="20.4461" y2="1.93948" stroke="white" stroke-width="3"/>
                                <line x1="2.06066" y1="1.93934" x2="20.5674" y2="20.4461" stroke="white" stroke-width="3"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (($DEVICE->isTablet() || $DEVICE->isMobile()) && $bMainPage) {
        $APPLICATION->IncludeComponent(
            'bitrix:menu',
            'mobile_menu_top',
            array(
                'COMPONENT_TEMPLATE' => '.default',
                'ROOT_MENU_TYPE' => 'mobile_menu_top',
                'MENU_CACHE_TYPE' => 'N',
                'MENU_CACHE_TIME' => '604800',
                'MENU_CACHE_USE_GROUPS' => 'Y',
                'MENU_CACHE_GET_VARS' => array(),
                'MAX_LEVEL' => '3',
                'CHILD_MENU_TYPE' => '',
                'USE_EXT' => 'Y',
                'DELAY' => 'N',
                'ALLOW_MULTI_SELECT' => 'N',
                'MAIN_MENU' => 'Y'
            )
        );
    }
    ?>
    <?php
    $bShowTitle = !CSite::InDir(SITE_DIR . 'faq/')
        && !CSite::InDir(SITE_DIR . 'index.php')
        && !(defined('HIDE_TITLE') && HIDE_TITLE === true)
        && !CSite::InDir(SITE_DIR . 'company_about/')
        && !CSite::InDir(SITE_DIR . 'company_anonymity/')
        && !CSite::InDir(SITE_DIR . 'company_bonus/')
        && !CSite::InDir(SITE_DIR . 'company_contacts/')
        && !CSite::InDir(SITE_DIR . 'company_delivery/')
        && !CSite::InDir(SITE_DIR . 'company_price_garanty/')
        && !CSite::InDir(SITE_DIR . 'company_repayment/')
        && !CSite::InDir(SITE_DIR . 'company_payment/');

    $bContentContainer = $bShowTitle
        && !CSite::InDir(SITE_DIR . 'personal/')
        && !CSite::InDir(SITE_DIR . 'brands/')
        && !CSite::InDir(SITE_DIR . 'refund/')
        && !CSite::InDir(SITE_DIR . 'company_about/')
        && !CSite::InDir(SITE_DIR . 'company_anonymity/')
        && !CSite::InDir(SITE_DIR . 'company_bonus/')
        && !CSite::InDir(SITE_DIR . 'company_contacts/')
        && !CSite::InDir(SITE_DIR . 'company_delivery/')
        && !CSite::InDir(SITE_DIR . 'company_price_garanty/')
        && !CSite::InDir(SITE_DIR . 'company_repayment/')
        && !CSite::InDir(SITE_DIR . 'company_payment/')
        && !CSite::InDir(SITE_DIR . 'order-success/');

    ?>

    <? if ($bShowTitle) : ?>
    <div class="col-xs-12 <? $APPLICATION->ShowProperty('TITLE_CLASS', ''); ?>">
        <div class="main">
            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                )
            ); ?>
            <h1 class="zagolovok"><? $APPLICATION->ShowTitle(false); ?></h1>
        </div>
    </div>

    <? if ($bContentContainer) :
    ?>
    <div class="col-xs-12 after-st padding-o" style="margin-top: -20px;">
        <div class="main">
            <div class="col-md-8 col-md-offset-2">
                <? elseif ('N' != $APPLICATION->GetProperty('MAIN_WR', 'N')) : ?>
                <div class="col-xs-12 after-<?= $APPLICATION->GetProperty('MAIN_WR', ''); ?>">
                    <div class="main">
                        <? endif; ?>
                        <? endif; ?>

                        <?php
                        switch ($GLOBALS['PAGE'][1]) {
                        case 'company_repayment':
                            break;

                        case 'personal':
                        if ($USER->IsAuthorized()) {
                            $menuTemplate = 'horizontal-personal';
                        }
                        ?>
                        <div class="after-lk-in main">
                            <?php
                            break;

                            default:
                                $menuTemplate = 'horizontal';
                                break;
                            }

                            if (isset($menuTemplate)) : ?>
                                <?php
                                $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $menuTemplate,
                                    [
                                        'COMPONENT_TEMPLATE' => '.default',
                                        'ROOT_MENU_TYPE' => 'left',
                                        'MENU_CACHE_TYPE' => 'Y',
                                        'MENU_CACHE_TIME' => '604800',
                                        'MENU_CACHE_USE_GROUPS' => 'Y',
                                        'MENU_CACHE_GET_VARS' => array(),
                                        'MAX_LEVEL' => '1',
                                        'CHILD_MENU_TYPE' => 'left',
                                        'USE_EXT' => 'N',
                                        'DELAY' => 'N',
                                        'ALLOW_MULTI_SELECT' => 'N'
                                    ]
                                );
                                ?>
                            <?php endif; ?>
