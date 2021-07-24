<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if ($arResult['SUBSCRIPTIONS']['email'] != 'checked' || $arResult['SUBSCRIPTIONS']['sms'] != 'checked') : ?>
<div class="subscribe_row">
<div class="form__field form__field--1-2 <?= $arResult['SUBSCRIPTIONS']['email'] == 'checked' ? 'subscribe_disabled' : '' ?>">
    <? if ($arResult['SUBSCRIPTIONS']['email'] != 'checked') : ?>
    <div class="input-group--subscribe js-check-input" id="one_click_checkbox_subscribe_email">
            <input type="checkbox" id="one_click_checkbox_subscribe_email_checked<?=$arParams['CART_NUMBER']?>"
                   name="PROPS[SUBSCRIBE_EMAIL]"
                   class="checkbox3"/>
            <label for="one_click_checkbox_subscribe_email_checked<?=$arParams['CART_NUMBER']?>"
                   class="checkbox--_"><?= isset($arResult['MAIL']) && !preg_match('`.*@orgasmcity.ru`i', $arResult['MAIL']) ? 'Подписать ' . $arResult['MAIL'] . ' на e-mail рассылки' : 'Подписаться на e-mail рассылки' ?></label>
    </div>
    <? endif; ?>
</div>
<div class="form__field form__field--1-2 <?= $arResult['SUBSCRIPTIONS']['sms'] == 'checked' ? 'subscribe_disabled' : '' ?>">
    <? if ($arResult['SUBSCRIPTIONS']['sms'] != 'checked') : ?>
    <div class="input-group--subscribe js-check-input" id="one_click_checkbox_subscribe_sms">
            <input type="checkbox" id="one_click_checkbox_subscribe_sms_checked<?=$arParams['CART_NUMBER']?>"
                   name="PROPS[SUBSCRIBE_SMS]"
                   class="checkbox3"/>
            <label for="one_click_checkbox_subscribe_sms_checked<?=$arParams['CART_NUMBER']?>"
                   class="checkbox--_"><?= isset($arResult['PHONE']) ? 'Подписать ' . $arResult['PHONE'] . ' на sms рассылки' : 'Подписаться на sms рассылки' ?></label>
    </div>
    <? endif; ?>
</div>
</div>
<? endif; ?>