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

$bChecked = false;

foreach ($arResult['RUBRICS'] as $arRubric) {
    $bChecked = $arRubric['CHECKED'] ? true : $bChecked;
}
?>

<div class="container">
    <div class="column-3 column-center">
        <form action="<?= $arResult['FORM_ACTION']; ?>" class="form--subscribe" method="post">
            <?= bitrix_sessid_post(); ?>
            <input type="hidden" name="SAVE_SUBSCRIBE" value="Y">
            <input type="hidden" name="PostAction" value="<?= $arResult['ID'] > 0 ? 'Update' : 'Add'; ?>">
            <input type="hidden" name="ID" value="<?= $arResult['SUBSCRIPTION']['ID']; ?>">
            <input type="hidden" name="EMAIL" value="<?= $arResult['SUBSCRIPTION']['EMAIL'] ?: $arResult['REQUEST']['EMAIL']; ?>">
            <input type="hidden" name="FORMAT" value="html">
            <fieldset>
                <legend>Подпишитесь на рассылку Orgasmcity, чтобы быть в курсе всех акций и новостей</legend>
                <div class="input-group">
                    <label class="checkbox">
                        <input class="js-rubrics-agree" type="checkbox" name="agree" value="Y" <?= $bChecked ? ' checked' : ''; ?>>
                        <span>Я хочу получать рассылку Orgasmcity</span>
                    </label>
                </div>
            </fieldset>
            <fieldset style="display: none;" class="js-rubrics-fieldset">
                <? foreach ($arResult['RUBRICS'] as $iItemID => $arRubric) : ?>
                    <input type="hidden" name="RUB_ID[]" value="<?= $arRubric['ID']; ?>">
                <? endforeach; ?>
            </fieldset>
            <fieldset class="with-padding">
                <input type="submit" name="Save" class="button button--primary button--outline button--block button--xxl" value="Сохранить изменения">
            </fieldset>
        </form>
    </div>
</div>