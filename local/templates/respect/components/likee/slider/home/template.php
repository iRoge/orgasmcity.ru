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
<?php if (!empty($arResult['ITEMS'])) : ?>
<div class="slider" style="padding: 0">
    <div class="main">
            <div id="main-slider<?= $arParams['CUSTOM_NUMBER'] ?>" class="slides<?php !empty($arResult['ITEMS'][0]['VIDEO']) and print ' slides--with-video' ?>"<?php !empty($arResult['SLICK']) and print " data-slick='".json_encode($arResult['SLICK'])."'"; ?>>
                <?php $counter = 1;
                foreach ($arResult['ITEMS'] as $arItem) :
                    $dataProps = 'data-rblock-id="' . $arItem['ID'] .'" '; // id баннера
                    $dataProps .= 'data-rblock-name="' . $arParams['BANNER_TYPE'] . '" ';  // Тип баннера
                    //$dataProps .= 'data-prod-brand="Respect" ';  // Бренд баннера
                    $dataProps .= 'data-prod-creative="' . $arItem['NAME'] . ' | ' . $arItem['ACTIVE_FROM'] . '" ';  // Название и начало активности баннера
                    $dataProps .= 'data-prod-position="' . $counter . '" ';  // Номер баннера

                    if (!empty($arItem['VIDEO'])) :
                        ?>
                    <div class="slides-item slides-item--video banner_item" <?= $dataProps?>>
                        <div class="comp-ver">
                            <video class="slides-item__video" loop="loop" <?php if ($arItem['PROPS']['AUTOPLAY']['VALUE']) :
                                ?> autoplay data-play="yes" <?php
                                                                          else :
                                                                                ?> preload="metadata"<?php !empty($arItem['PREVIEW_PICTURE']['SRC']) and print ' poster="'.$arItem['PREVIEW_PICTURE']['SRC'].'"'; ?><?php
                                                                          endif;?> loop>
                                <?php foreach ($arItem['VIDEO'] as $ext => $src) : ?>
                                    <source src="<?=$src?>" type='video/<?= $ext ?>'>
                                <?php endforeach; ?>
                            </video>
                            <span class="slides-item__video-play <?php if ($arItem['PROPS']['AUTOPLAY']['VALUE']) :
                                ?> stop <?php
                                                                 endif;?>" data-videoicon></span>
                        </div>
                        <?php $gagLink = $arItem['PROPS']['VIDEO_GAG_LINK']['VALUE'];?>
                        <?php if ($gagLink) :?>
                            <a href="<?=$gagLink;?>" class="slides-item slides-item--video mob-ver" style="background-image:url('<?=$arItem['VIDEO_GAG_SRC']?>'); background-size:cover;"></a>
                        <?php else :?>
                            <div class="slides-item slides-item--video mob-ver" style="background-image:url('<?=$arItem['VIDEO_GAG_SRC']?>'); background-size:cover;"></div>
                        <?php endif;?>
                    </div>
                    <?php
                    else :
                        $bannerImg = '<img src="'.$arItem['PREVIEW_PICTURE']['SRC'].'" alt="" data-mob-img="'.$arItem['MOBILE_SRC'].'" />';
                    /*if (! empty($arItem['MOBILE_SRC'])) {
                        $mobImg = '<img src="'.$arItem['MOBILE_SRC'].'" alt="" />';
                    }*/
                        ?>
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>
                        <?php if ($arItem['PROPS']['ACTIVE_MULTIPLY_LINKS']['VALUE'] == 'Y') : ?>
                        <div class="cards__banner stock-banner stock-banner--internal banner_item" <?= $dataProps?>>
                            <div class="stock-banner__wrapper">
                                <img class="stock-banner__img" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>"
                                     alt="">
                                <?php foreach ($arItem['BANNER']['MULTIPLY_LINKS'] as $arLink) : ?>
                                    <a class="stock-banner__link" href="<?= $arLink['LINK'] ?>"
                                       style="<?= $arLink['STYLE'] ?>; outline: none" ></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php elseif (!empty($arItem['LINK'])) : ?>
                            <a
                                    href="<?= $arItem['LINK']; ?>"
                                    id="msi-<?= $arItem['ID'] ?>"
                                    class="slides-item slider-one banner_item"
                                    style="background-image:url('<?=$arItem['PREVIEW_PICTURE']['SRC']?>');"
                                    <?=$dataProps?>
                                    data-mob-bg="<?=$arItem['MOBILE_SRC']?>"
                                    data-mob-link="<?=$arItem['PROPS']['MOBILE_LINK']['VALUE']?>"
                                    data-title="<?= $arItem['NAME']; ?>"
                            >
                                <?=$bannerImg?>
                            </a>
                    <?php else : ?>
                            <div
                                    id="msi-<?=$arItem['ID'] ?>"
                                    class="slides-item slider-one banner_item"
                                    style="background-image:url('<?=$arItem['PREVIEW_PICTURE']['SRC']?>');"
                                    <?=$dataProps?>
                                    data-mob-bg="
                                    <?=$arItem['MOBILE_SRC']?>"
                            >
                                <?=$bannerImg ?>
                            </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php $counter++; ?>
                <?php endforeach; ?>
            </div>
    </div>
</div>
<?php endif; ?>
<?php if (! empty($arResult['HOME_BG_SRC'])) : ?>
    <div style="background-image: url('<?= $arResult['HOME_BG_SRC']; ?>')" class="home-bg__item"></div>
<?php endif; ?>