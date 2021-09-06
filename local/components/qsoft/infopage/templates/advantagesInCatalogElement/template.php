<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="infopage-block">
    <? if (!empty($arResult['PREVIEW_PICTURE'])) { ?>
        <img style="text-align: center" src="<?= $arResult['PREVIEW_PICTURE']; ?>"/>
    <? } ?>
    <? if (!empty($arResult['PREVIEW_TEXT'])) { ?>
        <span><?= $arResult['PREVIEW_TEXT']; ?></span>
    <? } ?>
</div>
<? if (!empty($arResult['ITEMS'])) { ?>
    <? foreach ($arResult['ITEMS'] as $arItem) {
        if ($arItem['ACTIVE'] == 1) { ?>
            <div class="<?= $arItem['COLLAPSE'] ? '' : 'active-'; ?>blue sectionEvent infopage-section">
                <span><?=$arItem['NAME']?></span>
                <div class="arr-up" <?=$arItem['COLLAPSE'] ? '' : 'style="display:block;"';?>></div>
                <div class="arr-down"></div>
            </div>
            <div style="<?= $arItem['COLLAPSE'] ? '' : 'display:block;'; ?>margin-top:0; font-family: gilroyRegular; font-size: 16px;"
                 class="after-blue"><?=$arItem['TEXT']?></div>
        <? } ?>
    <? } ?>
<? } ?>
