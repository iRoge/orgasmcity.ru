<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="infopage-block">
    <? if (!empty($arResult['PREVIEW_PICTURE'])) : ?>
        <img style="text-align: center" src="<?= $arResult['PREVIEW_PICTURE']; ?>"/>
    <? endif; ?>
    <? if (!empty($arResult['PREVIEW_TEXT'])) : ?>
        <span><?= $arResult['PREVIEW_TEXT']; ?></span>
    <? endif; ?>
</div>
<? if (!empty($arResult['ITEMS'])) : ?>
    <? foreach ($arResult['ITEMS'] as $arItem) :
        if ($arItem['ACTIVE'] == 1) : ?>
            <p class="<?= $arItem['COLLAPSE'] ? '' : 'active-'; ?>blue sectionEvent infopage-section">
                <?= $arItem['NAME'] ?>
                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/arr-up.png" <?= $arItem['COLLAPSE'] ? '' : 'style="display:block;"'; ?>
                     class="arr-up"/>
                <img src="<?= SITE_TEMPLATE_PATH; ?>/img/arr-dwn.png"
                     <?= $arItem['COLLAPSE'] ? '' : 'style="display:none;"'; ?>class="arr-down"/>
            </p>
            <div style="<?= $arItem['COLLAPSE'] ? '' : 'display:block;'; ?>margin-top:0"
                 class="after-blue"><?= $arItem['TEXT'] ?></div>
        <? endif; ?>
    <? endforeach; ?>
<? endif; ?>
