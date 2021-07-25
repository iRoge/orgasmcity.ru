<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if (!$arResult['SUBSCRIBED']) : ?>
<!--    <div class="col-xs-12">-->
<!--        <input type="checkbox" id="one_click_checkbox_subscribe_sms_checked"-->
<!--               name="PROPS[SUBSCRIBE_SMS]"-->
<!--               class="checkbox3" />-->
<!--        <label for="one_click_checkbox_subscribe_sms_checked"-->
<!--               class="checkbox--_">--><?//= isset($arResult['PHONE']) ? 'Подписать ' . $arResult['PHONE'] . ' на sms рассылки' : 'Подписаться на sms рассылки' ?><!--</label>-->
<!--    </div>-->
    <div class="col-xs-12">
        <input type="checkbox" id="one_click_checkbox_subscribe_email_checked"
               name="PROPS[SUBSCRIBE]"
               class="checkbox3" />
        <label for="one_click_checkbox_subscribe_email_checked"
               class="checkbox--_"><?= isset($arResult['MAIL']) && !preg_match('`.*@orgasmcity.ru`i', $arResult['MAIL']) ? 'Подписать ' . $arResult['MAIL'] . ' на e-mail рассылки' : 'Подписаться на e-mail рассылки' ?></label>
    </div>
<? endif ?>