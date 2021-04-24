<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @const SITE_ID */
/** @const SITE_TEMPLATE_PATH */
?>
<!doctype html>
<html lang="<?= LANGUAGE_ID; ?>" style="background-color: #fff5f7">
<head>
    <meta charset="UTF-8">
    <title><? $APPLICATION->ShowTitle(); ?></title>

    <link rel="icon" href="/favicon.ico" type="image/svg+xml">
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?
    \Bitrix\Main\Loader::includeModule('likee.site');
    \Bitrix\Main\Loader::includeModule('likee.location');

    global $LOCATION;

    $APPLICATION->ShowHead();
    $bMainPage = $APPLICATION->GetCurDir() == '/';
    CJSCore::Init(['ajax']);

    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/style.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/application.css?up=1');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/lib/fancybox/jquery.fancybox.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/bootstrap.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick-theme.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/fixes.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/swiper.css');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-3.3.1.min.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/bootstrap.min.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/slick.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.zoom.min.js');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/underscore.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.ellipsis.min.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/index.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/microplugin.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/sifter.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/selectize.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/wNumb.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/nouislider.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.inputmask.bundle.min.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.validate.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.zoom.min.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/messages_ru.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/slick.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/tooltipster.bundle.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/bowser.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.datetimepicker.full.min.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/clipboard.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/tinycolor.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.inview.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/swiper.js');

//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/ajax.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/popup.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/tabs.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/subscribe.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/shop-list.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/vacancy.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/dropdown.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/to-top.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/cart.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/map.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/product.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/cart-item.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/toggle.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/product-gallery.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/animate-scroll.js');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/toggle.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/counter.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/clearable.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/phone.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/datetime.js');
    !$bFranchise ? \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/jquery.maskedinput.min.js') : '' ;
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/size.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputs/sku.js');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/_default.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/index.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/product.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/cart.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/shop.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/order-pickup.js');
//    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/bonuses.js');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/application.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/show-more.js');

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/global/device.min.js');
    !$bFranchise ? \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/fancybox/jquery.fancybox.js') : '' ;

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/custom.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/script.js?up=1');

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
    <script type="text/javascript" data-skip-moving="true" >
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
    <script data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH . '/js/device.js' ?>"></script>
    <script data-skip-moving="true">
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
    <div class="main top-border">
        <div class="col-md-8 col-sm-9 col-xs-12 info">
            <? $APPLICATION->IncludeComponent(
                'qsoft:geolocation',
                '.default',
                array(
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 31536000,
                )
            ); ?>
            <?php $phone = SUPPORT_PHONE?>
            <div class="col-xs-4 pull-right phone-xs">
                <p class="header-container">
                    <img class="header-mail-icon mail mail2" src="<?= SITE_TEMPLATE_PATH; ?>/img/envelope.png"/>
                    <a class="header-call-icon" href="tel:+<?=str_replace([' ', '(', ')', '-', '+'], '', $phone)?>"></a>
                </p>

            </div>
            <div class="col-sm-offset-1 col-sm-3 hidden-xs phone-top">
                <p class="phone-top-second">
                    <span>Интернет-магазин: </span>
                    <span><a class="phone-top-link" href="tel:<?=$phone?>"><?=$phone?></a></span>
                </p>
                    <? /*<span class="order-info__btn">Статус заказ</span></p>
                    <div class="order-info">
                        <div class="order-info__modal">
                            <div class="order-info__title">Для того, чтобы узнать статус заказа<br /> введите номер заказа и ваш номер телефона</div>
                            <div class="order-info__row">
                                <input class="order-info__col order-info__input" type="text" name="order_number" placeholder="Введите номер заказа">
                                <input class="order-info__col order-info__input" type="text" name="order_phone" placeholder="Введите номер телефона">
                            </div>

                            <div class="order-info__row order-info__captcha">
                                <?
                                include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
                                $cpt = new CCaptcha();
                                $captchaPass = COption::GetOptionString("main", "captcha_password", "");
                                if (strlen($captchaPass) <= 0)
                                {
                                    $captchaPass = randString(10);
                                    COption::SetOptionString("main", "captcha_password", $captchaPass);
                                }
                                $cpt->SetCodeCrypt($captchaPass);
                                ?>
                                <input class="static_input" type="hidden" name="captcha_code" value="<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">

                                <img class="order-info__col order-info_captcha-img" src="/bitrix/tools/captcha.php?captcha_code=<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">
                                <input class="static_input order-info__col order-info__input inputtext" placeholder="Введите текст с картинки" type="text" size="10" name="captcha_word">
                            </div>
                            <div class="button button--primary button--outline button--xl button--block order-info__submit">Узнать статус заказа</div>

                            <div class="order-info__result"></div>
                        </div>
                    </div>*/ ?>

            </div>

            <div class="col-sm-5 hidden-xs phone-top-mob">
                <p>Телефон: <span><a class="phone-top-link" href="tel:<?=$phone?>"><?=$phone?></a></span></p>
            </div>
        </div>
        <div class="col-md-4 col-sm-3 col-xs-12 right-block-top">
            <div class="col-md-6 col-sm-6 hidden-xs auth">
                <?php
                    $APPLICATION->ShowViewContent("AUTH_HEAD_BLOCK");
                ?>
            </div>
            <div class="col-md-2 col-sm-2 hidden-xs mail mail2">
                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/envelope.png"/>
            </div>
            <div class="col-md-2 col-sm-2 hidden-xs cart heart">
                <a class="favorites_header" href="/catalog/favorites/">
                    <p class="count count--heart in-full"><?= $_COOKIE['favorites_count'] ?? '0'?></p>
                    <img src="<?= SITE_TEMPLATE_PATH; ?>/img/transparent-heart.png"/>
                </a>
            </div>
            <div class="col-md-2 col-sm-2 hidden-xs cart">
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
            <div class="main clearfix">
                <div class="col-md-1 col-sm-1 col-xs-2 logo-div">
                    <a href="/"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/logo.png" class="logo header__logotype"/></a>
                    <a href="/"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/logo.png" class="logo-small"/></a>
                </div>
                <div class="col-md-9 menu-div">
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
                </div>
                <div class="col-md-2 col-sm-10 col-xs-10 pull-right search-div header__search" style="padding-left: 0px">
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
                        <img src="<?= SITE_TEMPLATE_PATH; ?>/img/search.png" style="margin-top: 18px;"/>
                    </div>
                    <div class="hidden-sm col-xs-2 mail auth2">
                        <? if (!$USER->IsAuthorized()) : ?>
                            <img class="ent" src="<?= SITE_TEMPLATE_PATH; ?>/img/man.png" style="margin-top: 16px;"/>
                        <? else : ?>
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/man.png" style="margin-top: 16px;"/>
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
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/transparent-heart.png"/>
                        </a>
                    </div>

                    <div class="hidden-sm col-xs-2 cart">
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

                    <div class="blue-menu"></div>
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
$bShowTitle = !CSite::InDir(SITE_DIR . 'index.php')
    && !CSite::InDir(SITE_DIR . 'new/index.php')
    && !CSite::InDir(SITE_DIR . 'shops/index.php')
    && !(defined('HIDE_TITLE') && HIDE_TITLE === true);

$bContentContainer = $bShowTitle && !CSite::InDir(SITE_DIR . 'personal/') && !CSite::InDir(SITE_DIR . 'refund/') && !CSite::InDir(SITE_DIR . 'actions/') && !CSite::InDir(SITE_DIR . 'shops/');
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
<div class="<?= !$bFranchise ? 'col-xs-12 after-st padding-o' : '' ?>" style="<?= !$bFranchise ? 'margin-top: -20px;' : '' ?>">
    <div class="<?= !$bFranchise ? 'main' : '' ?>">
        <div class="<?= !$bFranchise ? 'col-md-8 col-md-offset-2' : '' ?>">
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
        $menuTemplate = 'horizontal-personal';?>
        <div class="after-lk-in col-md-8 col-md-offset-2">
        <?php
        break;

    default:
        $menuTemplate = 'horizontal';
        break;
}

if (isset($menuTemplate)) : ?>
    <div class="<?= ($menuTemplate == 'horizontal-personal') ? 'desktop-sl' : '' ?>">
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
    </div>
<?php endif; ?>
