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

$arFields = $arResult['PROFILE']['FIELDS'];
?>

<div class="container">
    <div class="column-5 column-center">
        <form class="form--splitted form--profile" method="post">
            <?= bitrix_sessid_post(); ?>
            <? if ($arResult['PROFILE']['ID'] > 0): ?>
                <input type="hidden" name="PROFILE[ID]" value="<?= $arResult['PROFILE']['ID']; ?>">
            <? endif; ?>

            <input type="hidden" name="action" value="update_profile">

            <div class="container">
                <? foreach ($arResult['GROUPS'] as $arGroup): ?>
                    <? if (empty($arGroup['PROPS'])) continue; ?>
                    <div class="column-5 column-md-2">
                        <fieldset>
                            <legend><?= $arGroup['NAME']; ?></legend>

                            <? foreach ($arGroup['PROPS'] as $sPropCode => $arProp): ?>
                                <?
                                $sValue = $arFields[$sPropCode]['VALUE'] ?: $arProp['VALUE'];
                                $bRequired = $arProp['REQUIED'] == 'Y';
                                $sTitle = ($bRequired ? '*' : '') . $arProp['NAME'];
                                $sType = 'text';
                                $sClass = $sGroupClass = '';
                                if ($arProp['CODE'] == 'EMAIL') $sType = 'email';
                                if ($arProp['CODE'] == 'PHONE' || $arProp['IS_PHONE'] == 'Y') {
                                    $sClass = 'phone';
                                    $sGroupClass = 'input-group--phone';
                                }
                                ?>
                                <div class="input-group <?= $sGroupClass; ?>">
                                    <? if ($arProp['CODE'] == 'REGION' || $arProp['CODE'] == 'CITY'): ?>
                                        <? $sKey = $arProp['CODE'] == 'REGION' ? 'REGIONS' : 'CITIES'; ?>

                                        <select class="selectize js-profile-<?= strtolower($sKey); ?>"
                                                name="PROFILE[<?= $arProp['CODE']; ?>]"
                                            <?= $bRequired ? 'required' : ''; ?>>
                                            <option value=""><?= $sTitle; ?></option>
                                            <? foreach ($arResult[$sKey] as $sRegion): ?>
                                                <option<?= $sValue == $sRegion ? ' selected' : ''; ?>><?= $sRegion; ?></option>
                                            <? endforeach; ?>
                                        </select>
                                    <? elseif ($arProp['TYPE'] == 'SELECT'): ?>
                                        <select class="selectize <?= $sClass; ?>"
                                                name="PROFILE[<?= $arProp['CODE']; ?>]"
                                            <?= $bRequired ? 'required' : ''; ?>>
                                            <option value=""><?= $arProp['NAME']; ?></option>
                                            <? foreach ($arProp['VALUES'] as $arVal): ?>
                                                <? $bSelected = $arVal['ID'] == $sValue; ?>
                                                <option value="<?= $arVal['ID']; ?>"<?= $bSelected ? ' selected' : ''; ?>><?= $arVal['NAME']; ?></option>
                                            <? endforeach; ?>
                                        </select>
                                    <? else: ?>
                                        <input class="<?= $sClass; ?>"
                                               type="text" name="PROFILE[<?= $arProp['CODE']; ?>]" value="<?= $sValue; ?>"
                                               placeholder="<?= $sTitle; ?>"
                                            <?= $bRequired ? 'required' : ''; ?>>
                                    <? endif; ?>
                                </div>
                            <? endforeach; ?>
                        </fieldset>
                    </div>
                <? endforeach; ?>
            </div>

            <div class="conrainer">
                <div class="column-5 column-md-2" style="padding: 0 1.5em;">
                    <fieldset>
                        <div style="margin-top: 0.5em"></div>

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

                        <br>
                    </fieldset>
                </div>
            </div>

            <? if (!empty($arResult['ERRORS'])): ?>
                <div class="container">
                    <div class="column-10">
                        <br>
                        <? foreach ($arResult['ERRORS'] as $sError): ?>
                            <? ShowError($sError); ?>
                        <? endforeach; ?>
                    </div>
                </div>
            <? endif; ?>

            <div class="container">
                <div class="column-10">
                    <button type="submit" class="button button--primary button--outline button--xl button--block">
                        Сохранить изменения
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
