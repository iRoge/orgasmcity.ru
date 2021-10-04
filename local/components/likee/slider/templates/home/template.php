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
<? if (!empty($arResult['ITEMS'])) { ?>
    <div class="slider">
        <div id="main-slider" class="slides<? !empty($arResult['ITEMS'][0]['VIDEO']) and print ' slides--with-video' ?>"<? !empty($arResult['SLICK']) and print " data-slick='".json_encode($arResult['SLICK'])."'"; ?>>
            <? foreach ($arResult['ITEMS'] as $arItem) {
                if (!empty($arItem['VIDEO'])) {
            ?>
                <div class="slides-item slides-item--video">
                    <div class="comp-ver">
                        <video class="slides-item__video" loop="loop" <? if($arItem['PROPS']['AUTOPLAY']['VALUE']):?> autoplay data-play="yes" <?else:?> preload="metadata"<? !empty($arItem['PREVIEW_PICTURE']['SRC']) and print ' poster="'.$arItem['PREVIEW_PICTURE']['SRC'].'"'; ?><?endif;?> loop>
                            <? foreach ($arItem['VIDEO'] as $ext => $src) { ?>
                                <source src="<?=$src?>" type='video/<?= $ext ?>'>
                            <? } ?>
                        </video>
                        <span class="slides-item__video-play <? if($arItem['PROPS']['AUTOPLAY']['VALUE']):?> stop <?endif;?>" data-videoicon></span>
                    </div>
                    <? $gagLink = $arItem['PROPS']['VIDEO_GAG_LINK']['VALUE'];?>
                    <? if($gagLink) { ?>
                        <a href="<?=$gagLink;?>" class="slides-item slides-item--video mob-ver"></a>
                    <? } else { ?>
                        <div class="slides-item slides-item--video mob-ver"></div>
                    <? } ?>
                </div>
                <? } else {

                $bannerImg = '<img alt="Banner number ' . $arItem['ID'] . '" data-lazy="'.$arItem['PREVIEW_PICTURE']['SRC'].'" />';
            ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>

                    <? if (!empty($arItem['LINK'])) { ?>
                        <a href="<?= $arItem['LINK']; ?>" id="msi-<?= $arItem['ID'] ?>" class="slides-item slider-one" ><?= $bannerImg ?></a>
                    <? } else { ?>
                        <div id="msi-<?= $arItem['ID'] ?>" class="slides-item slider-one"><?= $bannerImg ?></div>
                    <? } ?>
                <? } ?>
            <? } ?>
        </div>
    </div>
<? } ?>