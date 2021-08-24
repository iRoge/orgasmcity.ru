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

if (!empty($arResult['ITEMS'])) {?>
    <div class="<?=$arResult['SHOW_SLIDER'] ? 'js-catalog-list-slider ' : ''?>catalog-list-main">
        <?php foreach ($arResult['ITEMS'] as $item) {?>
            <a class="col-lg-1 col-md-2 col-sm-2 catalog-list-element" href="<?=$item['SECTION_PAGE_URL']?>">
                <div class="catalog-list-element-img-wrap">
                    <img src="<?=$item['IMG_PATH']?>" alt="<?=$item['CODE']?>">
                </div>
                <span style="height: 25%"><?=$item['NAME']?></span>
            </a>
        <?php } ?>
    </div>
<?php }?>