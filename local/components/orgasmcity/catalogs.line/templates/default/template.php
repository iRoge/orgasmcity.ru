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
<?php if (!empty($arResult['ITEMS'])) {?>
    <div class="catalog-list-main main">
        <?php foreach ($arResult['ITEMS'] as $item) {?>
            <a class="col-lg-1 col-md-1 catalog-list-element" href="<?=$item['SECTION_PAGE_URL']?>">
                <div class="catalog-list-element-img-wrap">
                    <img src="<?=SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $item['CODE'] . '.svg'?>" alt="<?=$item['CODE']?>">
                </div>
                <span style="height: 25%"><?=$item['NAME']?></span>
            </a>
        <?php } ?>
    </div>
<?php }?>