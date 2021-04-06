<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<div class="form-forgot-password clearfix">
    <div class="column-6 column-center">
        <div class="alert"><?= GetMessage('AUTH_FORGOT_PASSWORD_1') ?></div>
    </div>
    <div class="column-2 column-center">
        <form name="bform" class="forgot-password" method="post" target="_top" action="<?= $arResult['AUTH_URL'] ?>">
            <div id="forgot_password_error"></div>
            <? if (!empty($arParams['~AUTH_RESULT'])) : ?>
                <div class="error-hint">
                    <?= ShowMessage($arParams['~AUTH_RESULT']); ?>
                </div>
            <? endif; ?>
            <? if (strlen($arResult['BACKURL']) > 0) : ?>
                <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
            <? endif; ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="SEND_PWD">

            <div>
                <input type="text" class="forgot-password__input" name="USER_PHONE" maxlength="50" id="phone" placeholder="<?= GetMessage('AUTH_PHONE') ?>">
            </div>
            <p><?= GetMessage('AUTH_OR') ?> </p>
            <div>
                <input type="text" class="forgot-password__input" name="USER_EMAIL" maxlength="255" id="email" placeholder="<?= GetMessage('AUTH_EMAIL') ?>">
            </div>

            <div class="input-group--actions">
                <? /*
                <input type="submit" name="send_account_info" value="<?= GetMessage('AUTH_SEND') ?>">
                */ ?>
                <button id="forgot_password_send" class="form-in-after-cart-sub col-xs-12 blue-btn" type="submit">
                    <?= GetMessage('AUTH_SEND') ?>
                </button>
            </div>
            <div>
                <input type="text" class="forgot-password__input" name="USER_SMS_CODE" maxlength="50" id="sms_code" placeholder="<?= GetMessage('AUTH_SMS_CODE') ?>">
                <button id="forgot_password_sms_send" class="form-in-after-cart-sub col-xs-12 blue-btn" type="submit">
                    <?= GetMessage('AUTH_SEND_CODE') ?>
                </button>
            </div>
        </form>
    </div>
</div>
