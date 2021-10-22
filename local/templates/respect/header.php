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
    <meta name="mailru-verification" content="d9b83fb9577bb7bd" />
    <meta name="mailru-verification" content="2e2a6b2c8292fc5b" />
    <meta name="yandex-verification" content="9150aa1aa386cc50" />
    <meta name="yandex-verification" content="fbad8d6555dc89a3" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?
    \Bitrix\Main\Loader::includeModule('likee.site');
    \Bitrix\Main\Loader::includeModule('likee.location');
    global $LOCATION;

    $APPLICATION->ShowHead();
    $bMainPage = $APPLICATION->GetCurDir() == '/';
//    CJSCore::Init(['ajax']);

    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/style.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/application.css?up=1');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/bootstrap.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/slick.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/slick-theme.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/fixes.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/select2/select2.min.css");
    $APPLICATION->ShowCSS(true, false);
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
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/global/popup.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/global/to-top.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/global/cart.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/counter.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/clearable.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/phone.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/jquery.maskedinput.min.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/size.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/inputs/sku.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/global/application.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/global/show-more.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/custom.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/script.js?up=1'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/lib/underscore.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/lib/lazy.min.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/slick.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . "/js/select2/select2.min.js"?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . "/js/select2/select2.ru.min.js"?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/lib/jquery.validate.js'?>" defer></script>
<!--    <script type="text/javascript" data-skip-moving="true" src="--><?//=SITE_TEMPLATE_PATH . '/lib/jquery.datetimepicker.full.min.js'?><!--" defer></script>-->
<!--    <script type="text/javascript" data-skip-moving="true" src="--><?//=SITE_TEMPLATE_PATH . '/js/inputs/datetime.js'?><!--" defer></script>-->
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/pages/_default.js'?>" defer></script>
    <script type="text/javascript" data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH . '/js/pages/index.js'?>" defer></script>


    <script type="text/javascript" data-skip-moving="true" async>
        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        function setCookie(name,value,days) {
            let expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
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
    <script type="text/javascript" data-skip-moving="true" async>
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
    <script type="text/javascript" src="//code-ya.jivosite.com/widget/IanrVwAEsl" async></script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" async>
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(82799680, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor: true,
            ecommerce:"metrikaData"
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/82799680" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script type="text/javascript" src="https://www.googletagmanager.com/gtag/js?id=UA-202524127-1" async></script>
    <script type="text/javascript" async>
        window.metrikaData = window.metrikaData || [];
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
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
<?
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
            <?php $phone = SUPPORT_PHONE?>
            <div class="col-xs-3 phone-xs">
                <p class="header-container">
                    <img class="header-mail-icon mail mail2" src="<?=SITE_TEMPLATE_PATH; ?>/img/svg/support.svg" alt="mail"/>
                    <a class="header-call-icon" href="tel:+<?=str_replace([' ', '(', ')', '-', '+'], '', $phone)?>"></a>
                </p>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-3 hidden-xs info">
            <div class="phone-top">
                <img class="phone-icon" src="<?= SITE_TEMPLATE_PATH ?>/img/svg/phone.svg"/>
                <p class="phone-top-first">
                    <span>Интернет-магазин: </span>
                    <span><a class="phone-top-link" href="tel:<?=$phone?>"><?=$phone?></a></span>
                </p>
                <p class="hidden-sm phone-top-second">
                    <span>Режим работы справочной: </span>
                    <span>с 12.00 - 18.00 СБ, ВС - выходной</span>
                </p>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-5 hidden-xs right-block-top">
            <div class="col-md-3 col-sm-3 hidden-xs auth<?=$USER->IsAuthorized() ? '' : ' ent'?>">
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
                    <p class="count count--heart in-full"><?= $_COOKIE['favorites_count'] ?? '0'?></p>
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
    <div class="header-border-wrapper main"><div class="header-border"></div></div>
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
                    <a href="/"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/logo_new.svg" class="logo header__logotype"/></a>
                    <a href="/"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/logo_new.svg" class="logo-small"/></a>
                </div>
                <div class="col-md-8 menu-div">
                    <?
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
                        <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/search-colored.svg" width="20px" height="20px"/>
                    </div>
                    <div class="hidden-sm col-xs-2 mail auth2<?=$USER->IsAuthorized() ? '' : ' ent'?>">
                        <? if (!$USER->IsAuthorized()) : ?>
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/cabinet.svg" width="20px" height="20px"/>
                        <? else : ?>
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/cabinet.svg" width="20px" height="20px"/>
                            <div class="auth-div menu_mob_fly" style="margin-top: 10px!important;">
                                <a href="/personal/orders/">История заказов</a><br />
                                <a href="/personal/bonuses/">Бонусы</a><br />
                                <a href="/personal/">Личные данные</a><br />
                                <a href="/personal/subscribe/">Управление рассылкой</a><br />
                                <a href="<?= $APPLICATION->GetCurPage() ?>?logout=yes" id="logout-btn">Выйти</a><br />
                            </div>
                        <? endif; ?>
                    </div>
                    <div class="hidden-sm col-xs-2 cart heart">
                        <a class="favorites_header" href="/catalog/favorites/">
                            <p class="count count--heart"><?=$favoritesCount?></p>
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
                        <img src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/burger.svg" alt="Burger" width="20px" height="20px">
                        <span>Каталог</span>
                    </div>
                    <div class="cls-blue-menu"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?
if (in_array($GLOBALS['device_type'], ['mobile', 'tablet']) && $bMainPage) {
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
}?>
<?
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
