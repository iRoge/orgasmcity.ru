<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

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
                <div class="col-xs-4 footer-div">
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
                <div class="col-xs-4 footer-div">
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
                <div class="col-xs-4 footer-div">
                    <h4>СОТРУДНИЧЕСТВО</h4>
                    <? $APPLICATION->IncludeComponent(
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
                <div style="clear: both"></div>
            </div>
            <div class="col-md-6 col-sm-4 right-footer-div">
                <div class="col-md-5 col-md-offset-0 col-sm-9 col-sm-offset-3 footer-div">
                    <div class="col-sm-12">
                        <h3><?=SUPPORT_PHONE?></h3>
                        <p>Интернет-магазин</p>
                        <?php $arWork = explode("/", COption::GetOptionString('likee', 'work_time_tablet', '')); ?>
                        <p class="worktime-title-footer"><?=$arWork[0]?><br><?=$arWork[1]?></p>
                    </div>
                    <div class="col-xs-12 social">
                        <? $APPLICATION->IncludeComponent(
                            "likee:social",
                            "footer",
                            array(
                                "FACEBOOK_LINK" => "https://www.facebook.com/respectbelarus/",
                                "INSTAGRAM_LINK" => "https://www.instagram.com/respect_belarus/",
                                "VK_LINK" => "https://vk.com/respect.belarus",
                                "YOUTUBE_LINK" => "https://www.youtube.com/channel/UCZhItgoSohm81b1IP744Qdg",
                                "OK_LINK" => "https://ok.ru/group/59329262977086",
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
        <!--        <div class="col-sm-12 bottom-mob">-->
        <!--            <p class="copy-shop-mob">-->
        <!--                2012---><?//= date('Y'); ?><!-- ©Интернет-магазин обуви и аксессуаров Respect. Все права защищены.-->
        <!--            </p>-->
        <!--            <p class="copy-razrab-mob">-->
        <!--                <a href="https://qsoft.ru/" target="_blank">Поддержка и развитие в QSOFT</a>-->
        <!--            </p>-->
        <!--        </div>-->
        <div class="col-sm-12 bottom-mob bottom-mob_context_footer">
            <ul class="payment-partners-list footer__payment-partners">
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_visa">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/visa.png" alt="VISA" width="91" height="29">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_visa-verified">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/visa-verified.png" alt="Verified by VISA" width="107" height="54">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_mastercard">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/mastercard.png" alt="Mastercard Secure Code" width="248" height="56">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_belcart">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/belcart.png" alt="БЕЛКАРТ" width="56" height="62">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_belcart-net-pass">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/belcart-net-pass.png" alt="БЕЛКАРТ ИнтернетПароль" width="230" height="54">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_bepaid">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/bepaid.png" alt="bePaid" width="117" height="29">
                    </span>
                </li>
                <li class="payment-partners-list__item">
                    <span class="payment-partner__partner payment-partners-list__partner_mt-bank">
                        <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/mt-bank.png" alt="МТБанк" width="221" height="72">
                    </span>
                </li>
            </ul>
            <div class="bottom-mob__inner">
                <p class="seller-info">
                    Интернет-магазин «Респект», ООО «Эрикана»<br>
                    Юридический адрес: 220005, Республика Беларусь, г. Минск, пр-т Независимости, 58в, пом.104<br>
                    Свидетельство о государственной регистрации №191310345 от 01.09.2010 г. выдано Мингорисполкомом<br>
                    Дата регистрации в торговом реестре Республики Беларусь - 19.08.2019 г.<br>
                    Режим работы: с 9.00 - 18.00 СБ, ВС - выходной<br>
                </p>
            </div>
        </div>
    </div>
</div>


<div class="col-xs-12 bottom">

    <!--    <div class="main">-->
    <!--        <p class="copy-shop col-xs-8">-->
    <!--            2012---><?//= date('Y'); ?><!-- ©Интернет-магазин обуви и аксессуаров Respect. Все права защищены.-->
    <!--        </p>-->
    <!--        <p class="copy-razrab col-xs-4">-->
    <!--            <a href="https://qsoft.ru/" target="_blank">Поддержка и развитие в <img src="/local/templates/respect/images/qsoft_gray.png"></a>-->
    <!--        </p>-->
    <!--    </div>-->
    <div class="main main_context_footer">
        <ul class="payment-partners-list footer__payment-partners">
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_visa">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/visa.png" alt="VISA" width="91" height="29">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_visa-verified">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/visa-verified.png" alt="Verified by VISA" width="107" height="54">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_mastercard">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/mastercard.png" alt="Mastercard Secure Code" width="248" height="56">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_belcart">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/belcart.png" alt="БЕЛКАРТ" width="56" height="62">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_belcart-net-pass">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/belcart-net-pass.png" alt="БЕЛКАРТ ИнтернетПароль" width="230" height="54">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_bepaid">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/bepaid.png" alt="bePaid" width="117" height="29">
                </span>
            </li>
            <li class="payment-partners-list__item">
                <span class="payment-partner__partner payment-partners-list__partner_mt-bank">
                    <img class="payment-partners-list__logo" src="/local/templates/respect/images/payment-partners/mt-bank.png" alt="МТБанк" width="221" height="72">
                </span>
            </li>
        </ul>
        <div class="main__inner">
            <p class="seller-info">
                Интернет-магазин «Респект», ООО «Эрикана»<br>
                Юридический адрес: 220005, Республика Беларусь, г. Минск, пр-т Независимости, 58в, пом.104<br>
                Свидетельство о государственной регистрации №191310345 от 01.09.2010 г. выдано Мингорисполкомом<br>
                Дата регистрации в торговом реестре Республики Беларусь - 19.08.2019 г.<br>
                Режим работы: с 9.00 - 18.00 СБ, ВС - выходной<br>
            </p>
        </div>
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
<? if (env("useMetric", true)) : ?>
    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/counters.php"), false);?>
    <? /* ?>
    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/criteo.php"), false);?>
    <? */ ?>
<? endif ?>

<script>
    (function () {
        let regForm   = $('#reg-form'),
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
        "registr",
        array(
            "AUTH" => "Y",
            "REQUIRED_FIELDS" => array(
                0 => "NAME",
                1 => "PERSONAL_BIRTHDAY",
                2 => "PERSONAL_PHONE",
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
            "COMPONENT_TEMPLATE" => "registr",
            "USER_PROPERTY_NAME" => ""
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
</body>
</html>
