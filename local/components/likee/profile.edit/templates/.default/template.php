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

$arBig = ['HOUSE'];
$arSmallProps = [];
?>

<? $this->SetViewTarget('PROFILE_1'); ?>

<? if ($arResult['PROFILE']['ID'] > 0): ?>
    <input type="hidden" name="PROFILE[ID]" value="<?= $arResult['PROFILE']['ID']; ?>">
<? endif; ?>

    <input type="hidden" name="action" value="update_profile">

<? if (!empty($arResult['PROPS']['REGION']) || !empty($arResult['PROPS']['CITY']) || !empty($arResult['PROPS']['STREET'])): ?>
    <fieldset>
        <legend>Адрес доставки</legend>

        <? if (!empty($arResult['PROPS']['REGION'])): ?>
            <div class="input-group">
                <select class="selectize js-profile-regions" name="PROFILE[REGION]" required>
                    <option value="">*<?= $arResult['PROPS']['REGION']['NAME']; ?></option>
                    <? foreach ($arResult['REGIONS'] as $sRegion): ?>
                        <option<?= $arFields['REGION']['VALUE'] == $sRegion ? ' selected' : ''; ?>><?= $sRegion; ?></option>
                    <? endforeach; ?>
                </select>
            </div>
        <? endif; ?>

        <? if (!empty($arResult['PROPS']['CITY'])): ?>
            <div class="input-group">
                <select class="selectize js-profile-cities" name="PROFILE[CITY]" required>
                    <option value="">*<?= $arResult['PROPS']['CITY']['NAME']; ?></option>
                    <? foreach ($arResult['CITIES'] as $sCity): ?>
                        <option<?= $arFields['CITY']['VALUE'] == $sCity ? ' selected' : ''; ?>><?= $sCity; ?></option>
                    <? endforeach; ?>
                </select>
            </div>
        <? endif; ?>

        <? if (!empty($arResult['PROPS']['STREET'])): ?>
            <div class="input-group">
                <input type="text"
                       name="PROFILE[STREET]"
                       value="<?= $arFields['STREET']['VALUE']; ?>"
                       placeholder="*<?= $arResult['PROPS']['STREET']['NAME']; ?>" required>
            </div>
        <? endif; ?>
    </fieldset>
<? endif; ?>

<? $this->EndViewTarget(); ?>

<? $this->SetViewTarget('PROFILE_2'); ?>

<? foreach ($arResult['PROPS'] as $sPropCode => $arProp): ?>
    <? if ($arProp['UTIL'] == 'Y' || in_array($sPropCode, ['REGION', 'CITY', 'STREET'])) continue; ?>

    <? if ($arProp['TYPE'] == 'SELECT'): ?>
        <div class="container">
            <div class="column-10">
                <div class="input-group">
                    <select class="selectize" name="PROFILE[<?= $arProp['CODE']; ?>]">
                        <option><?= $arProp['NAME']; ?></option>
                        <? foreach ($arProp['VALUES'] as $arVal): ?>
                            <? $bSelected = $arVal['ID'] == $arFields[$sPropCode]['VALUE']; ?>
                            <option value="<?= $arVal['ID']; ?>"<?= $bSelected ? ' selected' : ''; ?>><?= $arVal['NAME']; ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    <? elseif (in_array($sPropCode, $arBig)): ?>
        <div class="container">
            <div class="column-10">
                <div class="input-group">
                    <input type="text" name="PROFILE[<?= $arProp['CODE']; ?>]" value="<?= $arFields[$sPropCode]['VALUE']; ?>"
                           placeholder="<? if ($arProp['REQUIRED'] == 'Y'): ?>*<? endif; ?><?= $arProp['NAME']; ?>"
                           <? if ($arProp['REQUIRED'] == 'Y'): ?>required<? endif; ?>>
                </div>
            </div>
        </div>
    <? else: ?>
        <? $arSmallProps[$sPropCode] = $arProp; //небыло времени писать другое ?>

        <? if (count($arSmallProps) == 3): ?>
            <div class="container">
                <? foreach ($arSmallProps as $sPropCode => $arProp): ?>
                    <div class="column-33">
                        <div class="input-group">
                            <label>
                                <input type="text" name="PROFILE[<?= $arProp['CODE']; ?>]" value="<?= $arFields[$sPropCode]['VALUE']; ?>"
                                       placeholder="<? if ($arProp['REQUIRED'] == 'Y'): ?>*<? endif; ?><?= $arProp['NAME']; ?>"
                                       <? if ($arProp['REQUIRED'] == 'Y'): ?>required<? endif; ?>>
                            </label>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>

            <? $arSmallProps = []; ?>
        <? endif; ?>
    <? endif; ?>
<? endforeach; ?>

<? $this->EndViewTarget(); ?>