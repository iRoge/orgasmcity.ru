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
/** @var LikeeSliderComponent $component */
$this->setFrameMode(true);
?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div class="bio">
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <? if (!empty($arItem['LINK'])): ?>
                <a class="bio-item" href="<?= $arItem['LINK']; ?>"><img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="" /></a>
            <? else: ?>
                <div class="bio-item"><img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="" /></div>
            <? endif; ?>
        <? endforeach; ?>
    </div>
<? endif; ?>