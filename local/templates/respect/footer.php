<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?php if (in_array($GLOBALS["PAGE"][1], array('personal', 'refund'))) : ?>
    </div>
<?php endif; ?>
<?php if ($bContentContainer) : ?>
    </div></div></div>
<?php elseif ('N' != $APPLICATION->GetProperty('MAIN_WR', 'N')) : ?>
    </div></div>
<?php endif; ?>
<?php $APPLICATION->IncludeComponent(
    'qsoft:subscribe',
    'footer',
    array(),
    false
); ?>
<div class="footer col-xs-12">
    <div class="main footer-wrapper">
        <div class="footer-block footer-social-block col-lg-3 col-md-3 col-sm-3">
            <?php $APPLICATION->IncludeComponent(
                "likee:social",
                "footer",
                [
                    "INSTAGRAM" => "https://www.instagram.com/orgasmcity.ru/",
                    "VK.COM" => "https://vk.com/club205704529",
                    "TELEGRAM" => "https://t.me/Orgasmcity",
                    "WhatsApp" => "https://wa.me/79998526016",
//                    "Spotify" => "#",
                    "COMPONENT_TEMPLATE" => "footer",
                ],
                false
            ); ?>
            <img height="56px" width="68px" src="<?=SITE_TEMPLATE_PATH?>/img/designedBy.webp" alt="Designed by Grape.ov">
        </div>
        <div class="footer-block footer-information-block col-lg-2 col-md-2 col-sm-2">
            <h3>Информация</h3>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:menu',
                'footer',
                array(
                    'COMPONENT_TEMPLATE' => '.default',
                    'ROOT_MENU_TYPE' => 'footer_1',
                    'MENU_CACHE_TYPE' => 'Y',
                    'MENU_CACHE_TIME' => '604800',
                    'MENU_CACHE_USE_GROUPS' => 'Y',
                    'MENU_CACHE_GET_VARS' => array(),
                    'MAX_LEVEL' => '1',
                    'CHILD_MENU_TYPE' => 'left',
                    'USE_EXT' => 'N',
                    'DELAY' => 'N',
                    'ALLOW_MULTI_SELECT' => 'N'
                )
            ); ?>
        </div>
        <div class="footer-block col-lg-2 col-md-2 col-sm-2">
            <h3>Поддержка</h3>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:menu',
                'footer',
                array(
                    'COMPONENT_TEMPLATE' => '.default',
                    'ROOT_MENU_TYPE' => 'footer_2',
                    'MENU_CACHE_TYPE' => 'Y',
                    'MENU_CACHE_TIME' => '604800',
                    'MENU_CACHE_USE_GROUPS' => 'Y',
                    'MENU_CACHE_GET_VARS' => array(),
                    'MAX_LEVEL' => '1',
                    'CHILD_MENU_TYPE' => 'left',
                    'USE_EXT' => 'N',
                    'DELAY' => 'N',
                    'ALLOW_MULTI_SELECT' => 'N'
                )
            ); ?>
        </div>
        <div class="footer-block col-lg-2 col-md-2 col-sm-2">
            <h3>Дополнительно</h3>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:menu',
                'footer',
                array(
                    'COMPONENT_TEMPLATE' => '.default',
                    'ROOT_MENU_TYPE' => 'footer_3',
                    'MENU_CACHE_TYPE' => 'Y',
                    'MENU_CACHE_TIME' => '604800',
                    'MENU_CACHE_USE_GROUPS' => 'Y',
                    'MENU_CACHE_GET_VARS' => array(),
                    'MAX_LEVEL' => '1',
                    'CHILD_MENU_TYPE' => 'left',
                    'USE_EXT' => 'N',
                    'DELAY' => 'N',
                    'ALLOW_MULTI_SELECT' => 'N'
                )
            ); ?>
        </div>
        <div class="footer-block footer-right-block col-lg-3 col-md-3 col-sm-3">
            <h3>Контактные данные</h3>
            <div class="footer-element right-footer-element"><a href="mailto:support@orgasmcity.ru">support@orgasmcity.ru</a></div>
            <div class="footer-element right-footer-element"><a style="width: 100%;" href="tel:<?=SUPPORT_PHONE?>"><?=SUPPORT_PHONE?></a><br><span>Круглосуточно</span></div>
            <div class="footer-element right-footer-element" style="height: auto; padding-bottom: 5px"><span>г. Москва ул. Автозаводская д.16 к.2 стр.8 "Поставщик счастья"</span></div>
            <div class="footer-element right-footer-element"><span>Сайт только для взрослых</span><img height="100%" width="100%" style="margin-left: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/18plus.svg" alt="18+"></div>
        </div>
    </div>
    <div class="rights-line">
        <?=date('Y');?> ©Интернет-магазин "Город Оргазма". Все права защищены.
    </div>
</div>

<?php
$waPhone = COption::GetOptionString('respect', 'whatsapp_phone');
$waProlog = COption::GetOptionString('respect', 'whatsapp_text');
$waAllowShow = COption::GetOptionString('respect', 'whatsapp_allowShow');
?>

<?php if ($waAllowShow) : ?>
<a href="<?= "https://wa.me/$waPhone?text=$waProlog" ?>" class="whatsap-call-btn js-whatsapp" aria-label="Напишите нам в WhatSap"></a>

<?php endif; ?>

<?php $APPLICATION->IncludeComponent(
    'qsoft:subscribe',
    'popupBanner',
    array(),
    false
); ?>

<?php $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/options.php"), false);?>
<div class="auth-div-full">
    <div class="cls-mail-div">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="1.93934" y1="20.4462" x2="20.4461" y2="1.93942" stroke="black" stroke-width="3"/>
            <line x1="2.06066" y1="1.93934" x2="20.5674" y2="20.4461" stroke="black" stroke-width="3"/>
        </svg>
    </div>
    <div class="popup-title">
        <input type="radio" name="odin2" id="vkl10"/>
        <label for="vkl10" class="in-auth"><span>Вход</span></label>

        <input type="radio" name="odin2" id="vkl20"/>
        <label for="vkl20" class="in-auth"><span>Регистрация</span></label>
    </div>
    <?php $APPLICATION->IncludeComponent("bitrix:system.auth.form", "modal", array(), false); ?>
    <?php $APPLICATION->IncludeComponent(
        "bitrix:main.register",
        "registr-popup",
        array(
        "AUTH" => "Y",
        "REQUIRED_FIELDS" => array(
            0 => "NAME",
            1 => "PERSONAL_PHONE",
        ),
        "SET_TITLE" => "N",
        "SHOW_FIELDS" => array(
            0 => "EMAIL",
            1 => "NAME",
            2 => "SECOND_NAME",
            3 => "LAST_NAME",
            4 => "PERSONAL_GENDER",
            5 => "PERSONAL_BIRTHDAY",
            6 => "PERSONAL_PHONE",
        ),
        "SUCCESS_PAGE" => "",
        "USER_PROPERTY" => array(
        ),
        "USE_BACKURL" => "Y",
        "COMPONENT_TEMPLATE" => "registr-popup",
        "USER_PROPERTY_NAME" => "",
        "POPUP_FORM" => "Y",
        ),
        false
);
?>
</div>

<div class="mail-div">
    <div class="popup--feedback popup--overflow">
        <?php
        $feedbackFormId = COption::GetOptionInt('respect.feedback', "feedback_form_id");
        if ($feedbackFormId) {
            $APPLICATION->IncludeComponent(
                "bitrix:form.result.new",
                "feedback",
                array(
                    "CACHE_TIME" => "3600000",
                    "CACHE_TYPE" => "A",
                    "CHAIN_ITEM_LINK" => "",
                    "CHAIN_ITEM_TEXT" => "",
                    "EDIT_URL" => "",
                    "IGNORE_CUSTOM_TEMPLATE" => "N",
                    "LIST_URL" => "",
                    "SEF_MODE" => "N",
                    "SUCCESS_URL" => "",
                    "USE_EXTENDED_ERRORS" => "N",
                    "VARIABLE_ALIASES" => array(
                        "RESULT_ID" => "RESULT_ID",
                        "WEB_FORM_ID" => "WEB_FORM_ID"
                    ),
                    "WEB_FORM_ID" => $feedbackFormId,
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_SHADOW" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N"
                )
            );
        }
        ?>
    </div>
</div>
</div>
</body>
</html>

<?php exit; ?>
