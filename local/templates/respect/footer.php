<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if (in_array($GLOBALS["PAGE"][1], array('personal', 'refund'))) : ?>
    </div>
<? endif; ?>
<? if ($bContentContainer) : ?>
    </div></div></div>
<? elseif ('N' != $APPLICATION->GetProperty('MAIN_WR', 'N')) : ?>
    </div></div>
<? endif; ?>

<? if (!CSite::InDir(SITE_DIR . 'index.php') && !CSite::InDir(SITE_DIR . 'cart')) : ?>
    <? $APPLICATION->ShowViewContent('under_instagram'); ?>


<? endif; ?>

<div class="footer col-xs-12">
    <div class="main">
        <div class="row ones" style="padding: 0 15px;">
            <div class="col-xs-12 footer-border">
                <div class="col-md-5 col-sm-8">
                    <div class="col-xs-6 footer-div">
                        <h4>ПОКУПАТЕЛЯМ</h4>
                        <? $APPLICATION->IncludeComponent(
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
                    <div class="col-xs-6 footer-div">
                        <h4>Город Огразма</h4>
                        <? $APPLICATION->IncludeComponent(
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
                    <div style="clear: both"></div>
                </div>
                <div class="col-md-7 col-sm-4 right-footer-div">
                    <div class="col-md-5 col-md-offset-0 col-sm-9 col-sm-offset-3 footer-div">
                        <div class="col-sm-12 col-xs-6 num2">
                            <p>Обратная связь<a href="tel:<?=SUPPORT_PHONE?>"><?=SUPPORT_PHONE?></a></p>
                            <p><a href="mailto:support@orgasmcity.ru">support@orgasmcity.ru</a></p>
                            <p>г. Москва ул. Автозаводская д.16 к.2 стр.8 "Поставщик счастья"</p>
                        </div>
                        <div class="col-xs-12 social">
                            <? $APPLICATION->IncludeComponent(
                                "likee:social",
                                "footer",
                                array(
//                                    "FACEBOOK_LINK" => "",
                                    "INSTAGRAM_LINK" => "https://www.instagram.com/orgasmcity.ru/",
                                    "VK_LINK" => "https://vk.com/club205704529",
                                    "TELEGRAM_LINK" => "https://t.me/Orgasmcity",
//                                    "PINTEREST_LINK" => "",
//                                    "YOUTUBE_LINK" => "",
                                    "COMPONENT_TEMPLATE" => "footer",
                                ),
                                false
                            ); ?>
                        </div>
                    </div>
                    <div class="col-md-7 hidden-xs mailsender">
                        <h4>Подпишитесь на рассылку</h4>
                        <? $APPLICATION->IncludeComponent(
                            'qsoft:subscribe',
                            'footer',
                            array(),
                            false
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-7 hidden-sm mailsender">
                <h4>Подпишитесь на рассылку</h4>
                <? $APPLICATION->IncludeComponent(
                    'qsoft:subscribe',
                    'footerm',
                    array(),
                    false
                ); ?>
            </div>
            <div class="col-xs-12 col-sm-12 bottom-mob">
                <p class="copy-shop-mob">
                    <?= date('Y'); ?> ©Интернет-магазин секс товаров "Город Оргазма". Все права защищены.
                </p>
            </div>
        </div>
    </div>
</div>


<div class="col-xs-12 bottom">
    <div class="main">
        <p class="copy-shop col-xs-8">
            <?= date('Y'); ?> ©Интернет-магазин секс товаров "Город Оргазма". Все права защищены.
        </p>
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

<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/options.php"), false);?>
<div class="auth-div-full">
    <div class="cls-mail-div"></div>
    <div class="popup-title">
        <input type="radio" name="odin2" id="vkl10"/>
        <label for="vkl10" class="in-auth"><span>Вход</span></label>

        <input type="radio" name="odin2" id="vkl20"/>
        <label for="vkl20" class="in-auth"><span>Регистрация</span></label>
    </div>
    <? $APPLICATION->IncludeComponent("bitrix:system.auth.form", "modal", array(), false); ?>
    <? $APPLICATION->IncludeComponent(
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
        <?
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
                    "AJAX_MODE" => "Y",
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
