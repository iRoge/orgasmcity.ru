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
<div class="col-xs-12 news-section">
<div id="wrap">
<? if (!empty($arResult['ITEMS'])) : ?>
    <? foreach ($arResult['ITEMS'] as $arItem) : ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'));
        ?>
<a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL']; ?>" class="a-bez-un">
<div class="in-news">
<div class="col-xs-12" style="padding: 0!important;">
<div class="news-sel"><?= date('d M Y', MakeTimeStamp($arItem['ACTIVE_FROM'])); ?></div>
<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" class="col-xs-12"/>
</div>
<div class="text-in-news col-xs-12">
<h4><?= $arItem['NAME']; ?></h4>
<p><?= substr(strip_tags($arItem['DETAIL_TEXT']), 0, 100); ?>...</p><br />
</div>
</div>
</a>
    <? endforeach; ?>
<a href="st.html" class="a-bez-un" style="visibility: hidden; height: 1px;"><div class="in-news"></div></a>
<div class="clear-blocks"></div>
</div>
<div style="clear: both"></div> 
</div>
    <? if (!empty($arResult['NAV_STRING'])) : ?>
        <?= $arResult['NAV_STRING']; ?>
    <? endif; ?>
<? else : ?>
            <div>В данном разделе записи отсутствуют</div>
<? endif; ?>
