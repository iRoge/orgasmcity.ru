<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeProfileEditComponent $component */
$this->setFrameMode(true);
?>
<div class="column-8 column-center">
    <div class="form-forgor-password clearfix">
        <div class="column-4 column-center">
            <form name="bform" method="post" class="js-password" target="_top" action="/">
                <div id="forgotten_error"></div>
                <? if (!empty($arResult['ERROR'])) : ?>
                    <div class="error-hint"><?= $arResult['ERROR'] ?></div>
                <? endif; ?>
                <? if (strlen($arResult['BACKURL']) > 0) : ?>
                    <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>">
                <? endif ?>
                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="AUTH">
                <input type="hidden" name="USER_CHECKWORD" value="<?= $arResult['USER_CHECKWORD'] ?>">
                <? if (!empty($arResult['LAST_LOGIN'])) : ?>
                    <input type="hidden" name="USER_LOGIN" value="<?= $arResult['LAST_LOGIN'] ?>">
                <? endif ?>
                <div>
                    <input id="forgotten_password"
                           type="password"
                           name="USER_PASSWORD"
                           value="<?= $arResult['USER_PASSWORD']; ?>"
                           maxlength="50"
                           autocomplete="off"
                           class="js-password__input"
                           placeholder="*Новый пароль">
                </div>
                <div>
                    <input id="forgotten_confirm"
                           type="password"
                           name="USER_CONFIRM_PASSWORD"
                           value="<?= $arResult['USER_CONFIRM_PASSWORD']; ?>"
                           maxlength="50"
                           autocomplete="off"
                           class="js-password__input"
                           placeholder="*Повторите пароль">
                </div>
                <div class="input-group--actions">
                    <input id="change_pwd" type="button" class="form-in-after-cart-sub col-xs-12 blue-btn" name="change_pwd" value="Изменить">
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
document.bform.USER_PASSWORD.focus();
</script>
