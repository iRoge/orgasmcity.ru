<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<script>
    $(function() {
        window.application.addUrl({
            'shopList': '<?= $APPLICATION->GetCurPage(); ?>?action=get_amount_json',
            'shopListPage': '<?= $APPLICATION->GetCurPage(); ?>?action=get_amount',
            'product': '<?= $APPLICATION->GetCurPage(); ?>?action=get_one_click'
        });
    });
    BX.message({
        'CATALOG_ELEMENT_TEMPLATE_PATH': '<?= $templateFolder; ?>',
        'IS_PARTNER': '<?= \Likee\Site\User::isPartner() ? 'Y' : 'N'; ?>',
        'ONE_CLICK_URL': '<?= $APPLICATION->GetCurPage(); ?>?action=get_one_click'
    });
</script>
<? if (!empty($arResult)) : ?>
    <? if ((empty($arResult['RESTS']['DELIVERY']) && empty($arResult['RESTS']['RESERVATION'])) || empty($arResult['PRICE_PRODUCT'])) : ?>
        <div class="product-page__na">Данный артикул недоступен для заказа в вашем городе.</div>
    <? endif; ?>
    <div class="product-page">
        <div class="wrap col-sm-6" style="padding: 15px;">
            <div id="example5" class="slider-pro">
                <div class="sp-slides">
                    <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                        <div class="sp-slide ">
                            <img class="sp-image sp-image_hide" src="<?= $arPhoto['SRC']; ?>" data-src="<?= $arPhoto['SRC']; ?>" alt="<?= $arPhoto['ALT']; ?>" />
                        </div>
                    <? endforeach; ?>
                </div>
                <div class="sp-thumbnails">
                    <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                        <div class="sp-thumbnail">
                            <div class="sp-thumbnail-image-container">
                                <img class="sp-thumbnail-image" src="<?= $arPhoto['THUMB']; ?>" alt="<?= $arPhoto['ALT']; ?>" />
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <? if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                <div class="col-sm-12 hidden-xs info--" style="margin-right: 20px;margin-top: 50px">
                    <? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) : ?>
                        <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                            <div class="p3">
                                <div class="l3"><?= $arProperty['NAME']; ?></div>
                                <div class="r3"><?= $arProperty['VALUE']; ?></div>
                            </div>
                        <? endif; ?>
                    <? endforeach; ?>
                    <div class="opisanie-after">Описание</div>
                </div>
            <? endif; ?>
        </div>
        <div class="col-sm-6 right-cartochka col-xs-12">
            <? foreach ($arResult['LABELS'] as $sClass => $arLabel) : ?>
                <div class="sale-sela <?= $sClass ?>"><?= $arLabel['NAME'] ?></div>
            <? endforeach ?>
            <? if (!empty($arResult['ARTICLE'])) : ?>
                <p class="grey-cart">Арт. <?= $arResult['ARTICLE'] ?></p>
            <? endif ?>
            <h1 class="h1-cart"><?= $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] ?></h1>
            <? if (!empty($arResult['PRICE_PRODUCT'])) : ?>
                <div class="right-cartochka__inner-wrap">
                    <p class="price<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red" ? " price--discount" : "" ?>
                                   <?= empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) ? " price--short" : "" ?>">
                        <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'], 0, "", " ") ?></b> р.
                    </p>
                    <? if (!empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) &&
                        $arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'] < $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) : ?>
                        <p class="percents">
                            -<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['PERCENT'] ?>%
                        </p>
                        <p class="old-price">
                            <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE'], 0, "", " ") ?></b> р.
                        </p>
                    <? endif ?>
                    <p class="grey-under bonus-text">
                        <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == 'Red' ? '*бонусная программа не действует' : '*по условиям бонусной программы' ?><br />
                        *цены на сайте могут отличаться от цен в магазинах
                    </p>
                </div>
            <? endif ?>
            <? if ((!empty($arResult['RESTS']['DELIVERY']) || !empty($arResult['RESTS']['RESERVATION'])) && !empty($arResult['PRICE_PRODUCT'])) : ?>
                <hr class="hr-cartochka" />
                <? if (!$arResult['SINGLE_SIZE']) : ?>
                    <h3 class="after-hr-cart">Размер</h3>
                <? endif; ?>
                <form method="post" name="name" style="width: 100%;" class="form-after-cart js-action-form">
                    <input type="hidden" name="action" value="ADD2BASKET">
                    <? if (!$arResult['SINGLE_SIZE']) : ?>
                        <div style="display: block; width: 100%;" class="js-size-selector base-sizes">
                            <? foreach ($arResult['RESTS']['ALL'] as $offerId => $size) : ?>
                                <div class="top-minus <?= $sClass ?>">
                                    <input type="radio" name="id" id="offer-<?= $offerId ?>" class="radio1 js-choose-size js-offer js-offer-<?= $offerId ?>" value="<?= $offerId ?>" />
                                    <label for="offer-<?= $offerId ?>" data-offer-id="<?= $offerId ?>"><?= $size ?></label>
                                </div>
                            <? endforeach; ?>
                            <div style="clear: both"></div>
                        </div>
                        <div style="display: none; width: 100%;" class="js-size-selector delivery-sizes">
                            <input type="hidden" id="del-popup-type" value="">
                            <? foreach ($arResult['RESTS']['DELIVERY'] as $offerId => $size) : ?>
                                <div class="top-minus <?= $sClass ?>">
                                    <input type="radio" name="size-del" id="del-offer-<?= $offerId ?>" class="radio1" value="<?= $offerId ?>" />
                                    <label class="delivery-sizes-input" for="offer-<?= $offerId ?>" data-offer-id="<?= $offerId ?>"><?= $size ?></label>
                                </div>
                            <? endforeach; ?>
                            <div style="clear: both"></div>
                        </div>
                        <div style="display: none; width: 100%;" class="js-size-selector reservation-sizes">
                            <? foreach ($arResult['RESTS']['RESERVATION'] as $offerId => $size) : ?>
                                <div class="top-minus <?= $sClass ?>">
                                    <input type="radio" name="size-res" id="res-offer-<?= $offerId ?>" class="radio1" value="<?= $offerId ?>" />
                                    <label class="reservation-sizes-input" for="offer-<?= $offerId ?>" data-offer-id="<?= $offerId ?>"><?= $size ?></label>
                                </div>
                            <? endforeach; ?>
                            <div style="clear: both"></div>
                        </div>
                        <div class="buttons-wrapper">
                            <?php if ($arResult["ONLINE_TRY_ON"]) : ?>
                                <input id="fittin_widget_button" class="button-bordered button-bordered--transparent button-bordered--fitting <?= $USER->IsAuthorized() ? 'authorized' : 'non-authorized'; ?>" type="button" value="Примерить онлайн">
                                <div id="fittin_widget_dialog"></div>
                            <?php endif; ?>
                            <div class="sizes-popup-area">
                                <a class="sizes-popup" href="#">Руководство по размерам</a>
                                <div class="sizes-popup-block" style="display:none;">
                                    <div class="tab-size-block">
                                        <?= $arResult['SECTION_SIZES_TAB']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                    <? include 'buy_block.php'; ?>
                </form>
            <? endif; ?>
            <? if (!empty($arResult['COLORS'])) : ?>
                <hr class="hr-cartochka" />
                <h3>Другие цвета</h3>
                <? foreach ($arResult['COLORS'] as $arColor) : ?>
                    <a href="<?= $arColor['DETAIL_PAGE_URL']; ?>" class="a-others">
                        <div style="padding: 7px; display: inline-block; margin-right: 10px; margin-top: 10px;">
                            <img src="<?= $arColor['FILE']; ?>" alt="<?= $arColor['NAME']; ?>" />
                        </div>
                    </a>
                <? endforeach; ?>
            <? endif; ?>
        </div>
        <? if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
            <div class="hidden-lg hidden-md hidden-sm col-xs-12 info--" style="margin-left: 20px;margin-top: 50px">
                <? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) : ?>
                    <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                        <div class="p3">
                            <div class="l3"><?= $arProperty['NAME']; ?></div>
                            <div class="r3"><?= $arProperty['VALUE']; ?></div>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        <? endif; ?>
    </div>
<? else : ?>
    <div class="container">
        <div class="column-8 pre-1">
            <div class="alert alert-danger">Элемент не найден!</div>
        </div>
    </div>
<? endif; ?>