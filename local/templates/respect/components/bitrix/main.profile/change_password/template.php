<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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

<div class="container">
<div class="column-4 column-center">
    <div class="form-forgor-password">
        <div class="column-12 column-center">
            <div class="alert">
                Укажите новый пароль
            </div>
        </div>
        
        <form method="post" class="js-password" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
            <?= $arResult["BX_SESSION_CHECK"] ?>
            <input type="hidden" name="lang" value="<?= LANG ?>"/>
            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
            <input type="hidden" name="LOGIN" value="<? echo $arResult["arUser"]["LOGIN"] ?>"/>
            <input type="hidden" name="EMAIL" value="<? echo $arResult["arUser"]["EMAIL"] ?>"/>
            <input type="hidden" name="PERSONAL_GENDER" value="<? echo $arResult["arUser"]["PERSONAL_GENDER"] ?>"/>

                <? if (!empty($arResult['strProfileError'])): ?>
                    <div class="error-hint"><?= $arResult['strProfileError']; ?></div>
                <? endif; ?>

                <div class="input-group">
                    <input id="password"
                           type="password"
                           name="NEW_PASSWORD"
                           value=""
                           maxlength="50"
                           autocomplete="off"
                           required
                           placeholder="Новый пароль">
                </div>

                <div class="input-group">
                    <input id="password_confirm"
                           type="password"
                           name="NEW_PASSWORD_CONFIRM"
                           value=""
                           maxlength="50"
                           autocomplete="off"
                           required
                           placeholder="Пароль еще раз">
                </div>

                <div class="input-group input-group--actions">
                    <button class="button button--pink button--outline button--block button--xxl"
                            type="submit" name="save" value="Y">
                        Изменить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        document.form1.NEW_PASSWORD.focus();
    </script>

</div>
</div>
<div class="spacer--3"></div>