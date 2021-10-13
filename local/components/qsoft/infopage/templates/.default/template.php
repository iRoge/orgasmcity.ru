<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
global $DEVICE;
?>
<div class="infopage-block">
    <? if (!empty($arResult['PREVIEW_PICTURE'])) : ?>
        <img style="text-align: center" src="<?= $arResult['PREVIEW_PICTURE']; ?>"/>
    <? endif; ?>
    <? if (!empty($arResult['PREVIEW_TEXT'])) : ?>
        <span><? echo $arResult['PREVIEW_TEXT']; ?></span>
    <? elseif ($arResult['SHOW_BUTTONS']) : ?>
        <span><?= $arResult['PREVIEW_TEXT_1']; ?></span>
        <span>
    <? $APPLICATION->IncludeComponent(
        'bitrix:news.list',
        'contacts_buttons',
        [
            'IBLOCK_TYPE' => 'CONTENT',
            'IBLOCK_ID' => 'contacts_buttons',
            'PROPERTY_CODE' => [
                0 => 'IMG',
            ],
            'SORT_BY1' => 'SORT',
            'SORT_ORDER1' => 'ASC',
            'SET_TITLE' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000000',
            'CACHE_FILTER' => 'N',
            'CACHE_GROUPS' => 'N',
            'IS_MOBILE' => $DEVICE->isMobile() || $DEVICE->isTablet()
        ]
    ); ?>
</span>
        <span><?= $arResult['PREVIEW_TEXT_2']; ?></span>
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

