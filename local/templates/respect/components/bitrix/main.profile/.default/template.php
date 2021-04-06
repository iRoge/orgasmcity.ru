<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) exit;
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */

$arUser = $arResult['arUser'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $arResult['DATA_SAVED'] == 'Y') {
    LocalRedirect($APPLICATION->GetCurPageParam('success=y', ['success']));
}
?>

<div class="container">
    <div class="column-5 column-center">
        <form method="post" class="form--splitted form--profile" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
            <?= $arResult['BX_SESSION_CHECK']; ?>

            <input type="hidden" name="lang" value="<?= LANG ?>">
            <input type="hidden" name="ID" value=<?= $arResult['ID']; ?>>
            <input type="hidden" name="LOGIN" value="<?= $arUser['LOGIN']; ?>">

            <div class="container">
                <div class="column-5">
                    <fieldset>
                        <h3>Личные данные</h3>
                        <? $arFields = [
                            'NAME' => 'Имя',
                            'LAST_NAME' => 'Фамилия',
                            'SECOND_NAME' => 'Отчество'
                        ]; ?>

                        <? foreach ($arFields as $sCode => $sTitle): ?>
                            <div class="input-group">
                                <input class="col-xs-12" type="text" name="<?= $sCode; ?>" value="<?= $arUser[$sCode] ?>"
                                       placeholder="*<?= $sTitle; ?>" required>
                            </div>
                        <? endforeach; ?>
                    </fieldset>
                </div>

                <div class="column-5">
                    <? $APPLICATION->ShowViewContent('PROFILE_1'); ?>
                </div>
            </div>

            <div class="container">
                <div class="column-5">
                    <div class="input-group input-group--padding">
                        <label class="radio">
                            <input type="radio" name="PERSONAL_GENDER" value="M"<?= empty($arUser['PERSONAL_GENDER']) || $arUser['PERSONAL_GENDER'] == 'M' ? ' checked' : ''; ?>>
                            <span>Мужчина</span>
                        </label>
                        <label class="radio">
                            <input type="radio" name="PERSONAL_GENDER" value="F"<?= $arUser['PERSONAL_GENDER'] == 'F' ? ' checked' : ''; ?>>
                            <span>Женщина</span>
                        </label>
                    </div>

                    <div class="input-group input-group--calendar">
                        <label>
                            <input class="datepicker"
                                   type="text"
                                   name="PERSONAL_BIRTHDAY"
                                   value="<?= $arUser['PERSONAL_BIRTHDAY'] ?>"
                                   placeholder="*Дата рождения"
                                   required>
                        </label>
                    </div>

                    <div class="input-group">
                        <input type="email"
                               name="EMAIL"
                               value="<?= $arUser['EMAIL'] ?>"
                               placeholder="*E-mail"
                               required>
                    </div>

                    <div class="input-group input-group--phone">
                        <input class="phone"
                               type="text"
                               name="PERSONAL_PHONE"
                               value="<?= $arUser['PERSONAL_PHONE'] ?>"
                               placeholder="*Телефон"
                               required>
                    </div>
                </div>

                <div class="column-5">
                    <? $APPLICATION->ShowViewContent('PROFILE_2'); ?>
                </div>
            </div>

            <div class="container">
                <div class="column-5">
                    <div class="input-group">
                        <a data-toggle data-target="change-password"
                           class="button button--third button--l button--block js-password-btn">
                            Изменить пароль
                        </a>
                    </div>

                    <div id="change-password" data-toggled class="input-group">
                        <div class="input-group">
                            <input class="js-password-input" type="password" name="NEW_PASSWORD" autocomplete="off" placeholder="*Новый пароль" disabled required>
                        </div>
                        <div class="input-group">
                            <input class="js-password-input" type="password" name="NEW_PASSWORD_CONFIRM" autocomplete="off" placeholder="*Новый пароль еще раз" disabled required>
                        </div>
                    </div>
                </div>

                <div class="column-5">
                    <div class="auth__social">
                        <p>Привяжите аккаунты социальных сетей</p>
                        <div class="social-icons">
                            <? if (!empty($arResult['SOCSERV_ENABLED'])) {
                                $APPLICATION->IncludeComponent(
                                    "bitrix:socserv.auth.split",
                                    "personal",
                                    array(
                                        "SHOW_PROFILES" => "Y",
                                        "ALLOW_DELETE" => "Y"
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                );
                            } ?>
                        </div>
                    </div>
                </div>
            </div>

            <? if (!empty($arResult['strProfileError'])): ?>
                <div class="container">
                    <div class="column-5">
                        <? ShowError($arResult['strProfileError']); ?>
                    </div>
                </div>
            <? endif; ?>

            <? if (isset($_REQUEST['success']) || $arResult['DATA_SAVED'] == 'Y'): ?>
                <div class="container text--center" style="font-size: 1.2rem;">
                    <p><?= GetMessage('PROFILE_DATA_SAVED'); ?></p>
                </div>
            <? endif; ?>

            <div class="container">
                <div class="column-10">
                    <input class="button button--primary button--outline button--xl button--block" type="submit" name="save" value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">
                </div>
            </div>
        </form>
    </div>
</div>