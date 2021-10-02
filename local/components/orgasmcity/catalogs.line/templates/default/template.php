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
global $DEVICE;
if (!empty($arResult['ITEMS'])) {?>
    <?php if ($arResult['SHOW_SLIDER']) {?>
        <div class="js-catalog-list-slider catalog-list-main catalog-list-no-flex">
            <?php foreach ($arResult['ITEMS'] as $item) {?>
                <div class="catalog-list-element-wrapper">
                    <a class="catalog-list-element<?=$arResult['SHOW_BACKGROUND'] ? ' catalog-list-element-background' : ''?>" href="<?=$item['SECTION_PAGE_URL']?>">
                        <div class="catalog-list-element-img-wrap">
                            <img src="<?=$item['IMG_PATH']?>" alt="<?=$item['CODE']?>">
                        </div>
                        <div class="catalog-list-element-text-wrap">
                            <span><?=$item['NAME']?></span>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="catalog-list-main">
            <?php if ($DEVICE->isMobile() || $DEVICE->isTablet()) { ?>
                <div class="catalog-list-left-shadow"></div>
                <div class="catalog-list-top-shadow"></div>
                <div class="catalog-list-right-shadow"></div>
                <div class="catalog-list-bottom-shadow"></div>
            <?php } ?>
            <div class="catalog-list-elements-wrapper">
                <?php foreach ($arResult['ITEMS'] as $item) {?>
                    <div class="catalog-list-element-wrapper">
                        <a class="catalog-list-element<?=$arResult['SHOW_BACKGROUND'] ? ' catalog-list-element-background' : ''?>" href="<?=$item['SECTION_PAGE_URL']?>">
                            <div class="catalog-list-element-img-wrap">
                                <img src="<?=$item['IMG_PATH']?>" alt="<?=$item['CODE']?>">
                            </div>
                            <div class="catalog-list-element-text-wrap">
                                <span><?=$item['NAME']?></span>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php }?>
<?php }?>