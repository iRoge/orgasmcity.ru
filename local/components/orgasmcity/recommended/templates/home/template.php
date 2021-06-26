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
    <h2 class="recommendeds-header">Хиты продаж</h2>
    <div class="slider">
        <div id="recommendeds-slider" class="main">
            <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                <div class="slides-item slider-one">
                    <div class="cards__item">
                        <div class="card">
                            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="card__img" target="_blank">
                                <div class="card__img-box">
                                    <img
                                            src="<?= $arItem['DETAIL_PICTURE'] ;?>"
                                            class="card__img-pic pic-active pic-one"
                                            alt="<?= $arItem['NAME'] ?>"
                                    >
                                </div>
                                <div class="card__info">
                                    <div class="card__meta">
                                        <div class="card__prices">
                                            <div class="card__prices-top">
                                                                <span class="card__price <?= $arItem['PRICE'] < $arItem['OLD_PRICE'] ? " card__price--discount" : "" ?>">
                                                                    <span class="card__price-num"><?= number_format($arItem['PRICE'], 0, '', ' '); ?></span> р.
                                                                </span>
                                                <? if (!empty($arItem['OLD_PRICE']) && $arItem['PRICE'] < $arItem['OLD_PRICE']) : ?>
                                                    <span class="card__discount">-<?= $arItem['DISCOUNT'] ?>%</span>
                                                <? endif ?>
                                            </div>
                                            <? if (!empty($arItem['OLD_PRICE']) && $arItem['PRICE'] < $arItem['OLD_PRICE']) : ?>
                                                <span class="card__price-old" style="display:block;"><?= number_format($arItem['OLD_PRICE'], 0, '', ' '); ?> р.</span>
                                            <? endif ?>
                                        </div>
                                    </div>
                                    <span class="card__title"><?= $arItem['NAME'] ?></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
<?php }?>