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
<div style="float: left; width: 100%; margin: 25px 0">
    <h2 class="default-header"><?=$arParams['TITLE']?></h2>
    <div id="recommendeds-slider" class="main">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <div class="product-card">
                <div class="product-card-wrapper">
                    <div class="product-icons-wrap">
                        <?php if ($arItem['PROPERTY_BESTSELLER_VALUE']) { ?>
                            <img style="margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/hitProduct.svg" alt="Sale">
                        <?php } ?>
                        <?php if ($arItem['PROPERTY_NEW_VALUE']) { ?>
                            <img style="margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/newProduct.svg" alt="Sale">
                        <?php } ?>
                        <?php if ($arItem['DISCOUNT']) { ?>
                            <img style="margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/saleProduct.svg" alt="Sale">
                            <div class="sale-tooltip" title="Размер скидки"><?=-$arItem['DISCOUNT']?>%</div>
                        <?php } ?>
                    </div>
                    <button title="Добавить в избранное" type="button" class="heart__btn<?=isset($arResult['FAVORITES_PROD_IDS'][$arItem['ID']]) ? ' active' : '' ?> js-favour-heart" data-id="<?=$arItem['ID']?>">
                        <svg width="30" height="30" viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 5.86414C0 -0.440483 8.73003 -2.77704 11.4163 4.52139C14.1025 -2.77704 22.8325 -0.440483 22.8325 5.86414C22.8325 12.714 11.4163 21.3989 11.4163 21.3989C11.4163 21.3989 0 12.714 0 5.86414Z" fill="black"/>
                        </svg>
                    </button>
                    <a target="_blank" class="product-href-wrapper" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                        <div class="product-img-wrapper">
                            <img class="product-img" src="<?=$arItem["DETAIL_PICTURE"]?>" alt="<?=$arItem['NAME']?>">
                        </div>
                        <span class="product-title"><?=$arItem['NAME']?></span>
                    </a>
                    <div class="product-card-bottom">
                        <div class="product-card-price-wrapper">
                            <?php if ($arItem['DISCOUNT']) { ?>
                            <span class="product-card-old-price"><?=number_format($arItem['OLD_PRICE'], 0, '', ' ');?> ₽</span>
                            <?php } ?>
                            <span class="product-card-price<?=$arItem['DISCOUNT'] ? ' price-red' : ''?>"><?=number_format($arItem['PRICE'], 0, '', ' ');?> ₽</span>
                        </div>
                        <div class="product-card-buy-btn-wrapper">
                            <button
                                data-url="/<?=$arItem['CODE']?>/"
                                <?=count($arItem['OFFERS']) > 1 ? '' : 'data-id="' . reset($arItem['OFFERS'])['ID'] . '"'?>
                                onclick="addItemToCartOrOpenDetail(this)"
                                class="product-card-buy-btn"
                                data-name="<?=$arItem['NAME']?>"
                                data-price="<?=$arItem['PRICE']?>"
                            >
                                <?=count($arItem['OFFERS']) > 1 ? 'Купить' : 'В корзину'?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
</div>
<?php }?>

