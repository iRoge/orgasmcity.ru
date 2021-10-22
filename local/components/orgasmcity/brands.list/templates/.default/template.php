<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var \CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var array $templateData */
/** @var \CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?php if (!empty($arResult['ITEMS'])) {?>
    <div class="default-section">
        <h2 class="default-header">Наши бренды</h2>
        <div class="brands-list main">
            <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                <div class="brand-card col-lg-2 col-md-4 col-sm-4 col-xs-6">
                    <a href="<?=$arItem['SECTION_PAGE_URL'];?>" title="<?=$arItem['NAME'];?>" class="brand-card-wrapper" style="text-decoration: none">
                        <?php if ($arItem['PREVIEW_PICTURE']) { ?>
                            <div class="card__img-box">
                                <img class="lazy-img" width="100%" data-src="<?=$arItem['PREVIEW_PICTURE']?>" alt="<?=$arItem['NAME'];?>"/>
                            </div>
                        <?php } else { ?>
                            <div class="text-in-vendor">
                                <span class="vendor-no-img-text text-in-vendor-title"><?=$arItem['NAME'];?></span>
                            </div>
                        <?php } ?>
                    </a>
                </div>
            <?php }?>
        </div>
    </div>
<?php }?>
