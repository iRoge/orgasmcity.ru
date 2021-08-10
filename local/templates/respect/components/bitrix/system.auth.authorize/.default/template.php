<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="column-8 column-center">
    <form id="auth-full-form" method="post" class="in-auth-form-full" target="_top"
          action="<?= $arResult["AUTH_URL"] ?>">
        <a href="#" rel="nofollow" title="Регистрация" class="link_reg">Зарегистрироваться</a>
        <div id="after-auth-in-err-full"><? ShowMessage($arParams["~AUTH_RESULT"]);
            ShowMessage($arResult['ERROR_MESSAGE']); ?></div>
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
        <input type="text" id="AUTH_EMAIL_FULL" name="USER_LOGIN" value="<?= $arResult['USER_LOGIN']; ?>"
               placeholder="*E-mail">
        <input type="password" id="AUTH_PASSWORD_FULL" name="USER_PASSWORD" placeholder="*Пароль" autocomplete="off">
        <a href="/auth/?forgot_password=yes" class="input-group__link" rel="nofollow">
            Забыли пароль?
        </a><span class="err-pass-full"></span>
        <input value="Войти" type="submit">
    </form>
</div>