<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
$freeDeliveryMinSum = Option::get("respect", "free_delivery_min_summ", 4000);
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
        <? if (!empty($arResult)) : ?>
            <? if ((empty($arResult['OFFERS'])) || empty($arResult['MIN_PRICE_OFFER'])) : ?>
            <div class="product-page__na"><?= Loc::getMessage("OUT_STOCK") ?></div>
            <? endif; ?>
            <div class="product-page product-main-div">
                <div class="col-sm-6 slider-pro-container col-image">
                    <div id="example5" class="slider-pro">
                        <div class="sp-slides">
                            <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-slide jq-zoom">
                                    <img class="sp-image sp-image_hide sp-image-test"
                                         src=""
                                         data-src="<?= $arPhoto['SRC_ORIGINAL']; ?>"
                                         data-small="<?= Functions::checkMobileDevice() ? $arPhoto['SRC_MEDIUM'] : $arPhoto['SRC_ORIGINAL']; ?>"
                                         alt=""
                                         style="height: 600px;"/>
                                </div>
                            <? endforeach; ?>
                        </div>
                        <div class="sp-thumbnails">
                            <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-thumbnail">
                                    <div class="sp-thumbnail-image-container">
                                        <img class="sp-thumbnail-image sp-image_hide" src="<?= $arPhoto['THUMB']; ?>"
                                             alt=""/>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                    <? if ($arResult['DETAIL_TEXT']) :?>
                        <div class="hidden-xs detail-element-text">
                            <?=$arResult['DETAIL_TEXT']?>
                        </div>
                    <? endif; ?>
                </div>

                <div class="col-sm-6 col-xs-12 right-cartochka__container">
                    <div class="right-cartochka">
                        <div class="right-cartochka__top-block">
                            <? if (!empty($arResult['ARTICLE'])) : ?>
                                <p class="grey-cart"><?= Loc::getMessage("ARTICLE_PREFIX") ?><?= $arResult['ARTICLE'] ?></p>
                            <? endif ?>
                            <button type="button" class="heart__btn<?=!empty($arResult['FAVORITES']) ? ' active' : '' ?>" data-id="<?= $arResult['ID'] ?>">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 20 18" xml:space="preserve">
                                                                        <g>
                                                                            <path d="M18.4,1.8c-1-1.1-2.5-1.8-4-1.8l-3.1,1.1c-0.5,0.4-0.9,0.8-1.3,1.3c-0.4-0.5-0.8-1-1.3-1.3
                                                                                   C7.8,0.4,6.7,0,5.6,0c-1.5,0-3,0.6-4,1.8C0.6,2.9,0,4.4,0,6.1C0,7.8,0.6,9.4,2,11c1.2,1.5,2.9,3,5,4.7c0.7,0.6,1.5,1.3,2.3,2
                                                                                   C9.4,17.9,9.7,18,10,18s0.6-0.1,0.8-0.3c0.8-0.7,1.6-1.4,2.3-2c2-1.7,3.8-3.2,5-4.7c1.4-1.6,2-3.2,2-4.9C20,4.4,19.4,2.9,18.4,1.8
                                                                                   z"/>
                                                                        </g>
                                                                        </svg>
                            </button>
                        </div>
                        <h1 class="h1-cart"><?= $arResult["NAME"] ?></h1>
                        <?php if (!empty($arResult['MIN_PRICE_OFFER'])) : ?>
                            <div class="right-cartochka__inner-wrap">
                                <p class="price price--discount
                                   <?= empty($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) ? " price--short" : "" ?>">
                                    <b><span class="js-price-span"><?= number_format($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'], 0, "", " ") ?></span></b>
                                    <?= Loc::getMessage("RUB") ?>
                                </p>
                                <?php if (!empty($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) &&
                                    $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'] < $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE']) : ?>
                                    <div class="js-old-price-block" style="display: inherit;">
                                        <p class="percents">-<span class="js-price-percent-span"><?= $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['PERCENT'] ?></span>%</p>
                                        <p class="old-price">
                                            <b><span class="js-old-price-span"><?= number_format($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['OLD_VALUE'], 0, "", " ") ?></span></b>
                                            <?= Loc::getMessage("RUB") ?>
                                        </p>
                                    </div>
                                <?php endif ?>
                                <br><p style="margin-bottom: 10px;width: 100%;">
                                    <span class="text-success">Самая низкая цена среди интернет магазинов.</span>
                                    <br>Нашли дешевле? <a href="/company_price_garanty/" target="_blank">Сообщите нам</a>
                                </p>
                                <p>
                                    <?php if ($arResult['USER_DISCOUNT']) { ?>
                                        *скидка по бонусной программе в размере
                                        <span class="discount-yellow"><b><?=$arResult['USER_DISCOUNT']?>%</b></span>
                                        включена в общую скидку <br>
                                    <?php }?>
                                    <?php if ($arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['VALUE'] >= $freeDeliveryMinSum) { ?>
                                        *по данному товару осуществляется бесплатная доставка
                                    <?php }?>
                                </p>
                                <?php if ($USER->GetID() == 1) {
                                    $wholesaleprice = $arResult['MIN_PRICE_OFFER']['PROPERTIES']['PRICE']['WHOLEPRICE'];
                                    ?>
                                    Цена закупки <?=$wholesaleprice?>р.
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
<!--                                            <a class="sizes-popup" href="#">--><?//= Loc::getMessage("SIZES_INFO") ?><!--</a>-->
<!--                                            <div class="sizes-popup-block" style="display:none;">-->
<!--                                                <div class="tab-size-block">-->
<!--                                                    --><?//= $arResult['SECTION_SIZES_TAB']; ?>
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <? if (!empty($arResult['AVAILABLE_OFFER_PROPS']['COLORS'])) : ?>
                                    <h3 class="after-hr-cart"><?= Loc::getMessage("COLOR") ?></h3>
                                    <div style="display: block; width: 100%;" class="js-color-selector">
                                        <? foreach ($arResult['AVAILABLE_OFFER_PROPS']['COLORS'] as $colorCode => $color) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="color" id="color-<?= $colorCode ?>"
                                                       class="radio1 js-choose-size js-offer js-offer-<?= $colorCode ?>"
                                                       value="<?= $colorCode ?>"/>
                                                <label
                                                        class="color-label"
                                                        for="color-<?= $colorCode ?>"
                                                        data-value="<?= $colorCode ?>"><img title="<?=$color['NAME']?>" src="<?=$color['IMG_SRC']?>" alt="<?=$color['NAME']?>"></label>
                                            </div>
                                        <? endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <? endif; ?>
                                    <div class="js-error-block" style="display: none; position: absolute;bottom: -20px;">
                                        <p style="color: #cd1030;font-size: 13px;font-family: 'firabold';">
                                            Такого ассортимента сейчас нет в наличии!
                                        </p>
                                    </div>
                                </div>
                                <? endif; ?>
                                <div id="wrap" class="btns-wrap">
                                    <div id="js-toggle-delivery-ok">
                                        <div class="quantity-block">
                                            <button class="quantity-arrow-minus"> - </button>
                                            <input class="quantity-num" type="number" value="1" />
                                            <button class="quantity-arrow-plus"> + </button>
                                        </div>
                                        <input data-offer-id="<?=$arResult['MIN_PRICE_OFFER'] ? $arResult['MIN_PRICE_OFFER']['ID'] : "" ?>"
                                               id="buy-btn"
                                               class="js-cart-btn cartochka-orange yellow-btn js-cart-redirect"
                                               type="button"
                                               value="Добавить в корзину"
                                               />
                                        <?php if ($arResult['SHOW_ONE_CLICK']) :?>
                                            <input data-offer-id="<?=$arResult['MIN_PRICE_OFFER'] ? $arResult['MIN_PRICE_OFFER']['ID'] : "" ?>"
                                                   id="one-click-btn"
                                                   class="js-one-click cartochka-blue blue-btn"
                                                   type="button"
                                                   value="Купить в 1 клик"/>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        <? endif; ?>
                    <? $APPLICATION->IncludeComponent(
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
                    <?if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                        <div class="col-sm-12 hidden-xs" style="margin-right: 20px;margin-top: 50px">
                            <? if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                                <div class="p3">
                                    <div class="l3">Размер</div>
                                    <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                                </div>
                            <? endif;
                            foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                                <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                    <div class="p3 for-relative">
                                        <div class="l3"><?= $arProperty['NAME']; ?></div>
                                        <? if ($key == 'vendor') { ?>
                                        <div class="r3">
                                            <a href="/brands/<?= $arProperty['CODE_VALUE'] ?>/" target="_blank"><?= $arProperty['VALUE']; ?>
                                            </a>
                                        </div>
                                        <? } else { ?>
                                        <div class="r3"><?= $arProperty['VALUE']; ?></div>
                                        <? } ?>
                                    </div>
                                <? endif; ?>
                            <? endforeach; ?>
                            <div class="opisanie-after"><?= Loc::getMessage("DESCRIPTION_HEADER") ?></div>
                        </div>
                    <? endif; ?>
                </div>
                <? if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                    <div class="hidden-lg hidden-md hidden-sm col-xs-12 info--"
                         style="margin-left: 20px;margin-top: 50px">
                        <? if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                            <div class="p3">
                                <div class="l3">Размер</div>
                                <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                            </div>
                        <? endif;
                        foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                            <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                <div class="p3">
                                    <div class="l3"><?= $arProperty['NAME']; ?></div>
                                    <? if ($key == 'BRAND') { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip-mob' : '' ?>"
                                            <?= $arProperty['TOOLTIP'] ? ' data-tooltipname="' . $arProperty['CODE'] . '"' : '' ?>
                                        >
                                            <a href='<?= $arResult['BRAND_PAGE'] ?>'><?= $arProperty['VALUE']; ?></a>
                                        </div>
                                    <? } else { ?>
                                        <div class="r3">
                                            <?= $arProperty['VALUE']; ?>
                                        </div>
                                    <? } ?>
                                </div>
                            <? endif; ?>
                        <? endforeach; ?>
                        <? if ($arResult['DETAIL_TEXT']) :?>
                            <div class="detail-element-text">
                                <?=$arResult['DETAIL_TEXT']?>
                            </div>
                        <? endif; ?>
                    </div>
                <? endif; ?>
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
                        <? $APPLICATION->IncludeComponent('qsoft:subscribe.manager', 'popUp'); ?>
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
        <? else : ?>
            <div class="container">
                <div class="column-8 pre-1">
                    <div class="alert alert-danger"><?= Loc::getMessage("ELEMENT_NOT_FOUND") ?></div>
                </div>
            </div>
        <? endif; ?>
    </div>
</div>
<?php
$APPLICATION->IncludeComponent(
    'orgasmcity:products.line',
    'default',
    [
        'TITLE' => 'Похожие товары',
        'TYPE' => 'similar',
        'SECTION_ID' => $arResult['IBLOCK_SECTION_ID'],
    ]
);
