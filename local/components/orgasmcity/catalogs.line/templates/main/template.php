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
    <div class="catalog-list-main">
        <div class="catalog-list-elements-wrapper">
            <?php foreach ($arResult['ITEMS'] as $item) {?>
                <div class="col-lg-1 col-md-2 col-sm-2 catalog-list-element-wrapper">
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