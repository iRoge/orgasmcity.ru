<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs('/local/templates/respect/lib/jquery.zoom.min.js');
global $LOCATION;
global $APPLICATION;
global $USER;
global $DEVICE;
$freeDeliveryMinSum = Option::get("respect", "free_delivery_min_summ", 4000);
$availableRest = 0;
foreach ($arResult['OFFERS'] as $offer) {
    $availableRest += $offer['REST'];
}
?>
<script type="text/javascript">
    const OFFERS = <?=CUtil::PhpToJSObject($arResult['OFFERS'])?>;
    var previousOffer = <?=CUtil::PhpToJSObject($arResult['MIN_PRICE_OFFER'])?>;
</script>
<div id="main-card" class="col-xs-12 carto"
     data-id="<?=$arResult['MIN_PRICE_OFFER']['XML_ID']?>"
     data-name="<?=$arResult['NAME']?>"
     data-price="<?=$arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE']?>"
     data-brand="<?=$arResult['DISPLAY_PROPERTIES']['vendor']['VALUE']?>"
     data-category="<?=implode('/', array_column($arResult['PATH'], 'NAME'))?>"
     data-variant="<?=$arResult['AVAILABLE_OFFER_PROPS']['COLORS'][$arResult['MIN_PRICE_OFFER']['PROPERTIES']['COLOR']['VALUE']]['NAME']?>"
>
    <div class="main">
        <?php if (!empty($arResult)) : ?>
            <?php if ((empty($arResult['OFFERS'])) || empty($arResult['MIN_PRICE_OFFER'])) { ?>
                <div class="product-page__na"><?= Loc::getMessage("OUT_STOCK") ?></div>
            <?php } elseif ($availableRest <= 3) {?>
                <div class="product-page__na"> <b>Поспешите!</b> Данного товара на складе осталось всего <?=$availableRest?>шт.</div>
            <?php } ?>
            <div class="product-page product-main-div">
                <div class="col-sm-6 slider-pro-container col-image">
                    <div id="example5" class="slider-pro">
                        <div class="sp-slides">
                            <?php foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-slide jq-zoom">
                                    <img class="sp-image sp-image_hide sp-image-test"
                                         src=""
                                         data-src="<?= $arPhoto['SRC_ORIGINAL']; ?>"
                                         data-small="<?= $DEVICE->isMobile() || $DEVICE->isTablet() ? $arPhoto['SRC_MEDIUM'] : $arPhoto['SRC_ORIGINAL']; ?>"
                                         alt=""
                                         style="height: 600px;"/>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="sp-thumbnails">
                            <?php foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-thumbnail">
                                    <div class="sp-thumbnail-image-container">
                                        <img class="sp-thumbnail-image sp-image_hide" src="<?= $arPhoto['THUMB']; ?>"
                                             alt=""/>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if ($arResult['DETAIL_TEXT']) :?>
                        <div class="hidden-xs left-block-title left-block-title-revert">
                            Описание товара:
                        </div>
                        <div class="hidden-xs detail-element-text">
                            <?=$arResult['DETAIL_TEXT']?>
                        </div>
                    <?php endif; ?>
                    <div class="hidden-xs" style="margin-top: 40px">
                        <?php $APPLICATION->IncludeComponent(
                            "orgasmcity:feedback.list",
                            "product",
                            [
                                'PRODUCT_ID' => $arResult['ID'],
                                'FILTERS' => [
                                    'IBLOCK_ID' => IBLOCK_FEEDBACK,
                                    'ACTIVE' => 'Y',
                                    'PROPERTY_PRODUCT_ID' => $arResult['ID']
                                ],
                            ],
                            false
                        ); ?>
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12 right-cartochka__container">
                    <div class="right-cartochka">
                        <div class="right-cartochka__top-block">
                            <?php if (!empty($arResult['ARTICLE'])) : ?>
                                <p class="grey-cart"><?= Loc::getMessage("ARTICLE_PREFIX") ?><?= $arResult['ARTICLE'] ?></p>
                            <?php endif ?>
                            <button type="button" class="heart-btn<?=!empty($arResult['FAVORITES']) ? ' active' : '' ?> js-favour-heart" data-id="<?= $arResult['ID'] ?>">
                                <svg width="30" height="30" viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 5.86414C0 -0.440483 8.73003 -2.77704 11.4163 4.52139C14.1025 -2.77704 22.8325 -0.440483 22.8325 5.86414C22.8325 12.714 11.4163 21.3989 11.4163 21.3989C11.4163 21.3989 0 12.714 0 5.86414Z" fill="black"></path>
                                </svg>
                            </button>
                        </div>
                        <h1 class="h1-cart"><?= $arResult["NAME"] ?></h1>
                        <?php if (!empty($arResult['MIN_PRICE_OFFER'])) : ?>
                            <div class="right-cartochka__inner-wrap">
                                <div>
                                    <?php if (!empty($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) &&
                                        $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'] < $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) : ?>
                                        <div class="js-old-price-block" style="display: inherit;">
                                            <p class="old-price">
                                                <b><span class="js-old-price-span"><?= number_format($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE'], 0, "", " ") ?></span></b>
                                                <?= Loc::getMessage("RUB") ?>
                                            </p>
                                            <p class="percents">-<span class="js-price-percent-span"><?= $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['PERCENT'] ?></span>%</p>
                                        </div>
                                    <?php endif ?>
                                    <p class="price price--discount<?=empty($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) ? " price--short" : "" ?>">
                                        <b><span class="js-price-span"><?= number_format($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'], 0, "", " ") ?></span></b>
                                        <?= Loc::getMessage("RUB") ?>
                                    </p>
                                </div>
                                <br>
                                <?php if ($arResult['USER_DISCOUNT']) { ?>
                                <p>
                                        *скидка по бонусной программе в размере
                                        <span class="discount-yellow">
                                            <b><?=$arResult['USER_DISCOUNT']?>
                                                %
                                            </b>
                                        </span>
                                        включена в общую скидку <br>
                                </p>
                                <?php }?>
                                <div class="advantage-image-wrapper">
                                    <?php if ($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'] >= $freeDeliveryMinSum) { ?>
                                        <img class="advantage-image" src="<?= SITE_TEMPLATE_PATH; ?>/img/freeDelivery.webp" alt="Бесплатная доставка">
                                    <?php }?>
                                    <img class="advantage-image" src="<?= SITE_TEMPLATE_PATH; ?>/img/prepaymentSale.webp" alt="Скидка по предоплате">
                                    <img class="advantage-image" src="<?= SITE_TEMPLATE_PATH; ?>/img/mostLowPrice.webp" alt="Самая низкая цена среди интернет магазинов">
                                </div>
                                <?php if ($USER->GetID() == 1 || $USER->GetID() == 15) {
                                    $wholesaleprice = $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['WHOLEPRICE'];
                                    ?>
                                    Цена закупки <?=$wholesaleprice?> ₽
                                    <br>
                                    Наценка <?=(int)(($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'] - $wholesaleprice)*100/$wholesaleprice)?>%
                                <?php }?>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($arResult['OFFERS']) && !empty($arResult['MIN_PRICE_OFFER'])) : ?>
                            <form method="post" name="name" style="width: 100%; margin-top: 30px;" class="form-after-cart js-action-form">
                                <input type="hidden" name="action" value="ADD2BASKET">
                                <?php if (!$arResult['SINGLE_SIZE']) : ?>
                                <div style="display: block; position: relative">
                                    <?php if (!empty($arResult['AVAILABLE_OFFER_PROPS']['SIZES'])) : ?>
                                    <h3 class="after-hr-cart"><?= Loc::getMessage("SIZE") ?></h3>
                                    <div style="display: block; width: 100%;" class="js-size-selector">
                                        <?php foreach ($arResult['AVAILABLE_OFFER_PROPS']['SIZES'] as $sizeValue) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="size" id="size-<?= $sizeValue ?>"
                                                       class="radio1 js-choose-size js-offer js-offer-<?= $sizeValue ?>"
                                                       value="<?= $sizeValue ?>"/>
                                                <label for="size-<?= $sizeValue ?>"
                                                       data-value="<?= $sizeValue ?>"><?= $sizeValue ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <?php endif; ?>
<!--                                    <div class="buttons-wrapper">-->
<!--                                        <div class="sizes-popup-area">-->
<!--                                            <a class="sizes-popup" href="#">-->
                                    <?php //= Loc::getMessage("SIZES_INFO") ?><!--</a>-->
<!--                                            <div class="sizes-popup-block" style="display:none;">-->
<!--                                                <div class="tab-size-block">-->
<!--                                                    --><?php //= $arResult['SECTION_SIZES_TAB']; ?>
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <?php if (!empty($arResult['AVAILABLE_OFFER_PROPS']['COLORS'])) : ?>
                                    <h3 class="after-hr-cart"><?= Loc::getMessage("COLOR") ?></h3>
                                    <div style="display: block; width: 100%;" class="js-color-selector">
                                        <?php foreach ($arResult['AVAILABLE_OFFER_PROPS']['COLORS'] as $colorCode => $color) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="color" id="color-<?= $colorCode ?>"
                                                       class="radio1 js-choose-size js-offer js-offer-<?= $colorCode ?>"
                                                       value="<?= $colorCode ?>"/>
                                                <label
                                                        class="color-label"
                                                        for="color-<?= $colorCode ?>"
                                                        data-value="<?= $colorCode ?>">
                                                    <img title="<?=$color['NAME']?>" src="<?=$color['IMG_SRC']?>" alt="<?=$color['NAME']?>" width="31" height="31">
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="js-error-block" style="display: none; position: absolute;bottom: -20px;">
                                        <p style="color: #cd1030;font-size: 13px;font-family: 'gilroyBold';">
                                            Такого ассортимента сейчас нет в наличии!
                                        </p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div id="wrap" class="btns-wrap">
                                    <div id="js-toggle-delivery-ok">
                                        <div class="quantity-block">
                                            <button class="quantity-arrow-minus"><span>-</span></button>
                                            <input class="quantity-num" type="number" value="1" />
                                            <button class="quantity-arrow-plus"><span>+</span></button>
                                        </div>
                                        <div class="buy-btn-wrapper">
                                            <button data-offer-id="<?=$arResult['MIN_PRICE_OFFER'] ? $arResult['MIN_PRICE_OFFER']['ID'] : "" ?>"
                                                    id="buy-btn"
                                                    class="js-cart-btn js-cart-redirect"
                                                    type="button"
                                                    value="Добавить в корзину"
                                            >
                                                <span>Добавить товар в корзину</span>
                                                <div class="buy-btn-svg-wrapper">
                                                    <svg viewBox="0 0 42 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 12.8017C0 13.6024 0.00297083 13.6038 1.81208 13.6838L3.62388 13.7642L3.85158 14.8498C3.97663 15.4469 4.67402 19.111 5.40112 22.9922C6.12822 26.8733 7.16323 32.3694 7.70153 35.2057C8.23956 38.0419 8.79785 41.0342 8.94209 41.8552C9.08632 42.6763 9.32994 43.9893 9.48309 44.7729L9.76183 46.1978H24.4629H39.164V45.2479V44.2979H25.1641H11.1642L10.8795 42.7373C10.7226 41.8791 10.6488 41.085 10.715 40.9731C10.7814 40.8613 16.8146 40.7693 24.122 40.7688L37.4084 40.768L38.0312 37.8511C38.374 36.2468 38.9468 33.5908 39.3044 31.9487C40.9917 24.201 41.832 20.221 41.9125 19.5995L42 18.921L24.1896 18.7853L6.37887 18.6496L6.03044 16.7497C5.83868 15.7048 5.5494 14.2087 5.38761 13.4249L5.09348 12H2.54674H0V12.8017ZM39.8633 21.1601C39.7188 21.6256 38.321 28.0629 37.2555 33.1701C36.9597 34.5882 36.6118 36.176 36.4822 36.6984C36.3528 37.2209 36.1719 37.9844 36.0798 38.3947L35.9126 39.1411H23.1105H10.3085L10.0276 37.8248C9.87311 37.1009 9.13385 33.223 8.3846 29.2075C7.63563 25.1919 6.95985 21.6316 6.8826 21.2958L6.74215 20.6852H23.3763C39.0946 20.6852 40.0026 20.7112 39.8633 21.1601ZM13.2347 29.9131V35.6128H14.045H14.8553V29.9131V24.2135H14.045H13.2347V29.9131ZM17.5563 29.9131V35.6128H18.3666H19.1768V29.9131V24.2135H18.3666H17.5563V29.9131ZM21.8778 29.9131V35.6128H22.6881H23.4984V29.9131V24.2135H22.6881H21.8778V29.9131ZM26.1994 29.9131V35.6128H27.0096H27.8199V29.9131V24.2135H27.0096H26.1994V29.9131ZM30.5209 29.9302V35.6472L31.3987 35.562L32.2765 35.4771L32.3495 29.8453L32.4227 24.2135H31.4716H30.5209V29.9302ZM13.0497 48.2106C12.5765 48.4245 11.999 48.9692 11.7668 49.4208C10.0959 52.6674 14.1193 55.5965 16.6312 52.9619C18.9035 50.5787 16.0842 46.8386 13.0497 48.2106ZM33.3569 48.0852C32.5844 48.4063 31.7004 49.4699 31.5014 50.3181C31.1567 51.7848 32.3986 53.6684 33.8966 53.9509C35.7168 54.294 37.5434 52.7896 37.5434 50.9475C37.5434 48.8503 35.2646 47.2924 33.3569 48.0852ZM15.3415 50.0518C15.5197 50.231 15.6656 50.634 15.6656 50.9475C15.6656 51.261 15.5197 51.664 15.3415 51.8432C15.1632 52.0223 14.7621 52.1689 14.4502 52.1689C13.3719 52.1689 12.8012 50.8132 13.5588 50.0518C13.7371 49.8727 14.1382 49.7262 14.4502 49.7262C14.7621 49.7262 15.1632 49.8727 15.3415 50.0518ZM35.2254 49.8909C35.7303 50.0855 35.798 51.3714 35.3286 51.8432C35.1504 52.0223 34.7493 52.1689 34.4373 52.1689C34.1253 52.1689 33.7242 52.0223 33.546 51.8432C33.3677 51.664 33.2219 51.261 33.2219 50.9475C33.2219 49.9294 34.1105 49.461 35.2254 49.8909Z" fill="white" stroke="black" stroke-width="0.2" mask="url(#path-1-inside-1)"/>
                                                        <path d="M22.0767 0.886268C22.0842 0.886268 22.0916 0.88632 22.165 0.886443C22.165 0.935804 22.165 1.01025 22.1651 1.0953C22.1652 1.34104 22.1654 1.67525 22.1648 1.74836L22.1648 1.74862L22.1294 11.5019L22.129 11.6224L22.2146 11.5376L25.4905 8.2933C25.6036 8.29328 25.6795 8.29313 25.7421 8.293C25.8439 8.29279 25.9105 8.29266 26.0441 8.2933C26.0941 8.29354 26.1394 8.29368 26.1825 8.2938C26.3171 8.2942 26.4297 8.29453 26.5978 8.29794C26.5979 8.38226 26.598 8.44635 26.5981 8.5025C26.5983 8.61047 26.5985 8.68906 26.5978 8.82567C26.5976 8.87054 26.5975 8.90689 26.5974 8.9394C26.5969 9.0639 26.5967 9.13194 26.5924 9.40557L21.8434 14.0505L21.8431 14.0507C21.7841 14.1092 21.7642 14.1312 21.7376 14.1605C21.7284 14.1707 21.7185 14.1817 21.7058 14.1954C21.6586 14.2465 21.5722 14.337 21.3468 14.5629C21.0479 14.3101 20.9308 14.2168 20.871 14.1691C20.8463 14.1494 20.8321 14.1381 20.8184 14.1262C20.7991 14.1092 20.781 14.0909 20.7359 14.0454L16.0908 9.3551C16.0908 9.25406 16.0906 9.17732 16.0905 9.11024C16.0903 8.9925 16.0901 8.90453 16.0908 8.76721C16.091 8.71874 16.0911 8.67606 16.0913 8.63636C16.0917 8.5073 16.092 8.4098 16.0954 8.24773C16.2024 8.2477 16.2743 8.24755 16.3329 8.24743C16.4331 8.24723 16.4948 8.2471 16.627 8.24774C16.6736 8.24797 16.7124 8.2481 16.7479 8.24822C16.8769 8.24865 16.96 8.24893 17.2032 8.25308L20.4594 11.5409L20.5443 11.6267L20.5449 11.506L20.5921 1.74096L20.5921 1.74076L20.5921 0.886391L20.9786 0.886391L21.1817 0.886391C21.2302 0.886391 21.2823 0.886281 21.3344 0.886172C21.4383 0.885954 21.5418 0.885736 21.6149 0.886391L21.6153 0.886391C21.6683 0.88643 21.679 0.886483 21.6878 0.886525C21.6968 0.886569 21.7038 0.886603 21.7529 0.886603L21.7529 0.886603L21.9989 0.886385C22.0626 0.886312 22.0697 0.886267 22.0767 0.886268Z" fill="white" stroke="black" stroke-width="0.1"/>
                                                    </svg>
                                                </div>
                                            </button>
                                        </div>
                                        <?php if ($arResult['SHOW_ONE_CLICK']) :?>
                                            <div class="buy-btn-wrapper one-click-wrapper">
                                                <button data-offer-id="<?=$arResult['MIN_PRICE_OFFER'] ? $arResult['MIN_PRICE_OFFER']['ID'] : "" ?>"
                                                        id="one-click-btn"
                                                        class="js-one-click"
                                                        type="button"
                                                        value="Купить в 1 клик"
                                                >
                                                    <span>Купить товар в 1 клик</span>
                                                    <div class="buy-btn-svg-wrapper">
                                                        <svg viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M40.7786 0.612723C40.5448 0.231655 40.1298 0 39.6832 0H25.541C25.0313 0 24.5696 0.301339 24.3643 0.767787L13.2526 26.0232C13.0778 26.4212 13.1151 26.8801 13.3528 27.2436C13.59 27.6077 13.995 27.8268 14.4294 27.8268H23.724L18.2681 43.2861C18.0638 43.865 18.2967 44.5072 18.8243 44.8198C19.0286 44.9416 19.2553 45 19.4797 45C19.835 45 20.1859 44.8531 20.437 44.5731L44.671 17.5731C45.01 17.1958 45.0951 16.654 44.8886 16.1907C44.6817 15.7268 44.2219 15.4286 43.7143 15.4286H33.9602L40.8301 1.86642C41.0319 1.4684 41.0125 0.993164 40.7786 0.612723ZM30.721 16.1336C30.5191 16.5316 30.5386 17.0068 30.7724 17.3873C31.0063 17.7683 31.4212 18 31.8679 18H40.8326L22.8467 38.0391L26.7532 26.9692C26.8923 26.5756 26.8317 26.1393 26.5906 25.7984C26.3496 25.4581 25.9582 25.2554 25.541 25.2554H16.3996L26.38 2.57143H37.5909L30.721 16.1336ZM3.55343 35.3571C3.64383 35.3571 3.7358 35.3477 3.82808 35.3276L11.2014 33.7236C11.8951 33.573 12.3352 32.888 12.1842 32.1943C12.0335 31.5006 11.3524 31.0612 10.6546 31.2112L3.2813 32.8152C2.58762 32.9659 2.14756 33.6508 2.29854 34.3445C2.42943 34.9466 2.96177 35.3571 3.55343 35.3571ZM0 25.7143C0 26.4243 0.575655 27 1.28565 27H8.65931C9.36931 27 9.94496 26.4243 9.94496 25.7143C9.94496 25.0043 9.36931 24.4286 8.65931 24.4286H1.28565C0.575655 24.4286 0 25.0043 0 25.7143ZM11.2014 17.7049L3.82808 16.1009C3.13503 15.9503 2.44952 16.3897 2.29854 17.0841C2.14756 17.7778 2.58762 18.4627 3.2813 18.6133L10.6546 20.2174C10.7469 20.2374 10.8386 20.2469 10.9293 20.2469C11.521 20.2469 12.0533 19.8363 12.1842 19.2342C12.3352 18.5405 11.8951 17.8556 11.2014 17.7049Z" fill="white"/>
                                                        </svg>
                                                    </div>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($arResult['LAST_BUY_DATE_TEXT']) { ?>
                                            <div class="last-buy-date-wrapper">
                                                <div>
                                                    <span>Этот товар был куплен последний раз: <span style="font-family: gilroyRegular; font-weight: 600;"><?=$arResult['LAST_BUY_DATE_TEXT']?></span></span>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                        <?php $APPLICATION->IncludeComponent(
                        "qsoft:infopage",
                        "advantagesInCatalogElement",
                        array(
                            "IBLOCK_CODE" => 'advantagesInCatalogElement',
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "86400"
                        ),
                        false
                    ); ?>
                    </div>
                    <?php if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                        <div class="col-sm-12 hidden-xs" style="margin-right: 20px;margin-top: 50px">
                            <?php if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                                <div class="p3">
                                    <div class="l3">Размер</div>
                                    <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                                </div>
                            <?php endif;
                            foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                                <?php if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                    <div class="p3 for-relative">
                                        <div class="l3"><?= $arProperty['NAME']; ?></div>
                                        <?php if ($key == 'vendor') { ?>
                                        <div class="r3">
                                            <a href="/brands/<?= $arProperty['CODE_VALUE'] ?>/" target="_blank"><?= $arProperty['VALUE']; ?>
                                            </a>
                                        </div>
                                        <?php } else { ?>
                                        <div class="r3"><?= $arProperty['VALUE']; ?></div>
                                        <?php } ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <div class="opisanie-after"><?= Loc::getMessage("DESCRIPTION_HEADER") ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                    <div class="hidden-lg hidden-md hidden-sm col-xs-12 info--"
                    <div class="hidden-lg hidden-md hidden-sm col-xs-12 info--"
                         style="margin-left: 20px;margin-top: 50px">
                        <?php if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                            <div class="p3">
                                <div class="l3">Размер</div>
                                <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                            </div>
                        <?php endif;
                        foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                            <?php if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                <div class="p3">
                                    <div class="l3"><?= $arProperty['NAME']; ?></div>
                                    <?php if ($key == 'BRAND') { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip-mob' : '' ?>"
                                            <?= $arProperty['TOOLTIP'] ? ' data-tooltipname="' . $arProperty['CODE'] . '"' : '' ?>
                                        >
                                            <a href='<?= $arResult['BRAND_PAGE'] ?>'><?= $arProperty['VALUE']; ?></a>
                                        </div>
                                    <?php } else { ?>
                                        <div class="r3">
                                            <?= $arProperty['VALUE']; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($arResult['DETAIL_TEXT']) :?>
                            <div class="left-block-title left-block-title-revert">
                                Описание товара:
                            </div>
                            <div class="detail-element-text">
                                <?=$arResult['DETAIL_TEXT']?>
                            </div>
                        <?php endif; ?>
                        <?php $APPLICATION->IncludeComponent(
                            "orgasmcity:feedback.list",
                            "product",
                            [
                                'PRODUCT_ID' => $arResult['ID'],
                                'FILTERS' => [
                                    'IBLOCK_ID' => IBLOCK_FEEDBACK,
                                    'ACTIVE' => 'Y',
                                    'PROPERTY_PRODUCT_ID' => $arResult['ID']
                                ],
                            ],
                            false
                        ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hidden-divs" style="display: none">
                <form id="one-click-form"
                      class="product-page product b-element-one-click one-click-form js-one-click-form one-click-content"
                      action="/cart/" method="post">
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="action" value="1click">
                    <input type="hidden" name="PRODUCTS[]" value="">
                    <div id="after-cart-in-err"></div>
                    <div class="container container--quick-order">
                        <div class="column-5 column-md-2">
                            <div class="form">
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="one_click_fio"
                                           value="<?=$arResult['USER']['NAME'] ? $arResult['USER']['NAME'] . ' ' . $arResult['USER']['LAST_NAME'] : ''?>"
                                           type="text" name="PROPS[FIO]" placeholder="<?=$arResult['USER']['NAME'] ? $arResult['USER']['NAME'] . ' ' . $arResult['USER']['LAST_NAME'] : 'ФИО'?>">
                                </div>
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="one_click_phone"
                                           data-phone="<?=$arResult['USER']['PERSONAL_PHONE'];?>"
                                           type="text" name="PROPS[PHONE]" placeholder="*Телефон" required>
                                </div>
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="one_click_email"
                                           data-email="<?=$arResult['USER']['EMAIL'];?>"
                                           type="text" name="PROPS[EMAIL]" placeholder="*Почта" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order">
                        <div class="column-4 pre-3 column-md-2">
                            <button id="button-one-click"
                                    class="buttonFastBuy"><?= Loc::getMessage("MAKE_ORDER") ?></button>
                            <div class="buttonFastBuy-loader">
                                <div class="one-click-preloader-div">
                                    <button class="one-click-preloader"><?= Loc::getMessage("WAIT") ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order product__footer">
                        <?php $APPLICATION->IncludeComponent('qsoft:subscribe.manager', 'popUp'); ?>
                        <div id="one_click_checkbox_policy_error"></div>
                        <div id="one_click_checkbox_policy" class="col-xs-12">
                            <input type="checkbox" id="one_click_checkbox_policy_checked"
                                   name="one_click_checkbox_policy"
                                   class="checkbox3" checked/>
                            <label for="one_click_checkbox_policy_checked"
                                   class="checkbox--_"><?= Loc::getMessage('AGREEMENT') ?></label>
                        </div>
                    </div>
                </form>

                <div class="product-preorder-success js-choose-size">
                    <h2><?= Loc::getMessage("CHOOSE_SIZE") ?></h2>
                    <form method="post" name="name" class="form-after-cart js-action-form-popup-size">
                        <input type="hidden" name="action" value="">
                        <div class="js-size-popup">
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <div class="container">
                <div class="column-8 pre-1">
                    <div class="alert alert-danger"><?= Loc::getMessage("ELEMENT_NOT_FOUND") ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$APPLICATION->IncludeComponent(
    'orgasmcity:products.line',
    'default',
    [
        'TITLE' => 'Похожие товары',
        'FILTERS' => [
            "IBLOCK_ID" => IBLOCK_CATALOG,
            "ACTIVE" => "Y",
            'SECTION_ID' => $arResult['IBLOCK_SECTION_ID']
        ],
    ]
);
