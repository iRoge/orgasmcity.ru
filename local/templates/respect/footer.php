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
        <div class="row ones">
            <div class="col-md-6 col-sm-8">
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
                    <h4>RESPECT</h4>
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
            <div class="col-md-6 col-sm-4 right-footer-div">
                <div class="col-md-5 col-md-offset-0 col-sm-9 col-sm-offset-3 footer-div">
                    <div class="col-sm-12 col-xs-6 num1">
                        <h3><a href="tel:88005555292">8 800 555-52-92</a></h3>
                        <p>Единая справочная</p>
                    </div>
                    <div class="col-sm-12 col-xs-6 num2">
                        <h3><a href="tel:<?=SUPPORT_PHONE?>"><?=SUPPORT_PHONE?></a></h3>
                        <p>Интернет-магазин</p>
                    </div>
                    <div class="col-xs-12 social">
                        <? $APPLICATION->IncludeComponent(
                            "likee:social",
                            "footer",
                            array(
                                "FACEBOOK_LINK" => "https://www.facebook.com/RESPECTSHOES",
                                "INSTAGRAM_LINK" => "https://www.instagram.com/respectshoes/",
                                "VK_LINK" => "https://vk.com/respectshoess",
                                "TELEGRAM_LINK" => "https://telegram.me/RespectShoesBot",
                                "PINTEREST_LINK" => "https://www.pinterest.ru/Respect_shoes/",
                                "YOUTUBE_LINK" => "https://www.youtube.com/channel/UCtImAFnNl_WCVasjjcXK4qg",
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
        <div class="col-sm-7 hidden-sm mailsender">
            <h4>Подпишитесь на рассылку</h4>
            <? $APPLICATION->IncludeComponent(
                'qsoft:subscribe',
                'footerm',
                array(),
                false
            ); ?>
        </div>
        <div class="col-sm-12 bottom-mob">
            <p class="copy-shop-mob">
                <?= date('Y'); ?> ©Интернет-магазин секс товаров "Город Оргазма". Все права защищены.
            </p>
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

<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/options.php"), false);?>
<script>
(function () {
    let regForm   = $('#reg-form-popup'),
        authForm  = $('#auth-form'),
        regInput  = $('#vkl20'),
        authInput = $('#vkl10');

    $('.reg').click(function() {
        regInput.not(':checked').prop("checked", true);
        regForm.show();
    });
    $('.ent').click(function() {
        authInput.not(':checked').prop("checked", true);
        authForm.show();
    });
    $('.cls-mail-div, .podlozhka').click(function() {
        regForm.hide();
        authForm.hide();
    })
    regInput.click(function() {
        authForm.hide();
        regForm.show();
    });
    authInput.click(function() {
        authForm.show();
        regForm.hide();
    });

})();
</script>
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
); ?>

    <!-- <style>
        @media (min-width: 767px) {
            .pod-auth p:nth-of-type(1) {
                display: inline-block;
            }
    
            .pod-auth p:nth-of-type(2) {
                display: none;
            }
        }
    
        @media (max-width: 767px) {
            .pod-auth p:nth-of-type(1) {
                display: none;
            }
    
            .pod-auth p:nth-of-type(2) {
                display: inline-block;
            }
        }
    </style>
    <div class="pod-auth">
        <p style="margin-top: 25px; float: left; color: #4e4e4e; font: 16px 'firaregular';">Используйте для входа
            социальные сети</p>
        <p style="margin-top: 25px; float: left; color: #4e4e4e; font: 16px 'firaregular';">Вход через соц. сети</p>
        <a href="#"><img src="<?= SITE_TEMPLATE_PATH ?>/img/vk.png"
                         style="margin-top: 15px;margin-left: 20px!important; margin-right: 15px;"/></a>
        <a href="#"><img src="<?= SITE_TEMPLATE_PATH ?>/img/fb.png" style="margin-top: 15px"/></a>
    </div> -->
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

<? if ($_COOKIE['seller_id']) {
    $APPLICATION->IncludeComponent(
        "rdevs:sellers",
        "",
        array(
            "CACHE_TIME" => "3600000",
            "CACHE_TYPE" => "A",
        )
    );
}?>
</body>
</html>
