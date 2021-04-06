<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<div class="column-5 column-md-2">
    <form method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
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

        <div class="input-group">
            <input type="text" name="USER_LOGIN" value="<?= $arResult['USER_LOGIN']; ?>"
                   placeholder="*Email">
        </div>

        <div class="input-group">
            <input type="password" name="USER_PASSWORD" value="<?= $arResult['USER_LOGIN']; ?>"
                   placeholder="*Пароль" autocomplete="off">
            <a href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>" class="input-group__link" rel="nofollow">
                Забыли пароль?
            </a>
        </div>

        <div class="input-group input-group--actions">
            <button type="submit" class="button button--pink button--outline button--block button--xxl">
                Войти
            </button>
        </div>
    </form>
</div>

<div class="column-5 column-md-2">
    <div class="auth__continue">
        
        <? if ($arParams['SHOW_ORDER_NO_AUTH_FORM']==='Y') :  ?>
        <form action="/order/" method="post">
            <button class="button button--third button--block"
                    type="submit" name="action" value="skip_auth">
                ПРОДОЛЖИТЬ БЕЗ РЕГИСТРАЦИИ
            </button>
        </form>
        <? endif;  ?>
        <!--<a class="button button--third button--block js-auth-one-click-short">
            КУПИТЬ В 1 КЛИК
        </a>-->

        <? if ($arResult["AUTH_SERVICES"]) : ?>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:socserv.auth.form",
                "",
                array(
                    "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                    "AUTH_URL" => $arResult["AUTH_URL"],
                    "POST" => $arResult["POST"],
                    "POPUP" => "Y",
                    "SUFFIX" => "form",
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
            ?>
        <? endif ?>
    </div>
</div>