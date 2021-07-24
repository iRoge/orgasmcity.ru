<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}?>
<? if ($arResult['SUBSCRIPTIONS']['email'] != 'checked') : ?>
    <div class="col-xs-12">
        <input type="checkbox" id="preorder_checkbox_subscribe_email_checked"
               name="PROPS[SUBSCRIBE_EMAIL]"
               class="checkbox3" />
        <label for="preorder_checkbox_subscribe_email_checked"
               class="checkbox--_"><?= isset($arResult['MAIL']) && !preg_match('`.*@orgasmcity.ru`i', $arResult['MAIL']) ? 'Подписать ' . $arResult['MAIL'] . ' на e-mail рассылки' : 'Подписаться на e-mail рассылки' ?></label>
    </div>
<? endif ?>