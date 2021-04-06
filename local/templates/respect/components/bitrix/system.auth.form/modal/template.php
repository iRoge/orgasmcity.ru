<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form id="auth-form" method="post" class="in-auth-form" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
    <div id="after-auth-in-err"></div>
    <?= bitrix_sessid_post(); ?>

    <? if (!empty($arResult['BACKURL'])) : ?>
        <input type="hidden" name="backurl" value="<?= $arResult['BACKURL']; ?>">
    <? endif ?>

    <? foreach ($arResult['POST'] as $key => $value) : ?>
        <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
    <? endforeach ?>

    <input type="hidden" name="AUTH_FORM" value="Y">
    <input type="hidden" name="TYPE" value="AUTH">

    <? if (!empty($arResult['ERROR_MESSAGE']['MESSAGE'])) : ?>
        <div class="error-hint">
            <? ShowError($arResult['ERROR_MESSAGE']['MESSAGE']); ?>
        </div>
    <? endif; ?>
    <input type="text" id="AUTH_EMAIL" name="USER_LOGIN" value="<?= $arResult['USER_LOGIN']; ?>" placeholder="E-mail">
    <p>или <span class="err-phone-email"></span></p>
    <input type="text" id="AUTH_PHONE" name="USER_PHONE" placeholder="Телефон">
    <input type="password" id="AUTH_PASSWORD" name="USER_PASSWORD" placeholder="*Пароль" autocomplete="off">
    <a href="/auth/?forgot_password=yes" class="input-group__link" rel="nofollow">
        Забыли пароль?
    </a><span class="err-pass"></span>
    <input value="Войти в личный кабинет" type="submit">
</form>
