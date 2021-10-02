<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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
/** @var LikeeSliderComponent $component */
$this->setFrameMode(true);
?>
<?php if (!empty($arResult['ITEMS'])) { ?>
<div class="slider" style="padding: 0">
    <div class="main">
            <div id="main-slider<?= $arParams['CUSTOM_NUMBER'] ?>" class="slides<?php !empty($arResult['ITEMS'][0]['VIDEO']) and print ' slides--with-video' ?>"<?php !empty($arResult['SLICK']) and print " data-slick='".json_encode($arResult['SLICK'])."'"; ?>>
                <?php $counter = 1;
                foreach ($arResult['ITEMS'] as $arItem) {

                    if (!empty($arItem['VIDEO'])) {
                        ?>
                    <div class="slides-item slides-item--video banner_item">
                        <div class="comp-ver">
                            <video class="slides-item__video" loop="loop" <?php if ($arItem['PROPS']['AUTOPLAY']['VALUE']) {
                                ?> autoplay data-play="yes"
                                <?php } else { ?> preload="metadata"<?php !empty($arItem['PREVIEW_PICTURE']['SRC']) and print ' poster="'.$arItem['PREVIEW_PICTURE']['SRC'].'"'; ?>

                                <?php } ?> loop>
                                <?php foreach ($arItem['VIDEO'] as $ext => $src) { ?>
                                    <source src="<?=$src?>" type='video/<?= $ext ?>'>
                                <?php } ?>
                            </video>
                            <span class="slides-item__video-play<?=$arItem['PROPS']['AUTOPLAY']['VALUE'] ? ' stop' : ''?>" data-videoicon></span>
                        </div>
                        <?php $gagLink = $arItem['PROPS']['VIDEO_GAG_LINK']['VALUE'];?>
                        <?php if ($gagLink) { ?>
                            <a href="<?=$gagLink;?>" class="slides-item slides-item--video mob-ver">
                                <img data-lazy="<?=$arItem['VIDEO_GAG_SRC']?>" alt="Banner number <?=$arItem['ID']?>">
                            </a>
                        <?php } else { ?>
                            <div class="slides-item slides-item--video mob-ver">
                                <img data-lazy="<?=$arItem['VIDEO_GAG_SRC']?>" alt="Banner number <?=$arItem['ID']?>">
                            </div>
                        <?php } ?>
                    </div>
                    <?php } else {
                        $bannerImg = '<img data-lazy="'.$arItem['PREVIEW_PICTURE']['SRC'].'" alt="" />';
                    /*if (! empty($arItem['MOBILE_SRC'])) {
                        $mobImg = '<img src="'.$arItem['MOBILE_SRC'].'" alt="" />';
                    }*/
                        ?>
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>
                        <?php if ($arItem['PROPS']['ACTIVE_MULTIPLY_LINKS']['VALUE'] == 'Y') { ?>
                        <div class="cards__banner stock-banner stock-banner--internal banner_item">
                            <div class="stock-banner__wrapper">
                                <img class="stock-banner__img" data-lazy="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"
                                     alt="">
                                <?php foreach ($arItem['BANNER']['MULTIPLY_LINKS'] as $arLink) { ?>
                                    <a class="stock-banner__link" href="<?= $arLink['LINK'] ?>"
                                       style="<?= $arLink['STYLE'] ?>; outline: none" ></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } elseif (!empty($arItem['LINK'])) { ?>
                            <a
                                    href="<?= $arItem['LINK']; ?>"
                                    id="msi-<?= $arItem['ID'] ?>"
                                    class="slides-item slider-one banner_item"
                                    data-title="<?= $arItem['NAME']; ?>"

                            >
                                <?=$bannerImg?>
                            </a>
                    <?php } else { ?>
                            <div
                                    id="msi-<?=$arItem['ID'] ?>"
                                    class="slides-item slider-one banner_item"
                                    data-mob-bg="
                                    <?=$arItem['MOBILE_SRC']?>"
                            >
                                <?=$bannerImg ?>
                            </div>
                    <?php } ?>
                    <?php } ?>
                    <?php $counter++; ?>
                <?php } ?>
            </div>
    </div>
</div>
<?php } ?>