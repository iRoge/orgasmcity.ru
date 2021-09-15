<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option;

$this->addExternalJS("/local/templates/respect/js/jquery.suggestions.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css");
$this->addExternalJS("/local/templates/respect/js/select2/select2.min.js");

global $LOCATION;
$cookieAddress = false;

if ($LOCATION->code == $_COOKIE['user_location_code']) {
    $cookieAddress = true;
}

$freeDeliveryMinSum = Option::get("respect", "free_delivery_min_summ", 4000);
$basketMinSum = Option::get("respect", "basket_min_num", 1000);

global $LOCATION, $APPLICATION; ?>
<?php if (!empty($arResult["ITEMS"])) { ?>
<script>
    var arDelIdsJs = <?=CUtil::PhpToJSObject($arResult["DELIVERY"]["PVZIDS"])?>;
    var deliveryMoscowSelfId = <?=MOSCOW_SELF_DELIVERY_ID?>;
    var arOnlinePaymentIds = <?=CUtil::PhpToJSObject($arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"])?>;
    var token = "<?=DADATA_TOKEN?>";
    var paymentWayErrorText = "<?=Option::get("respect", "disabled_payment_click_text", "");?>";
    var freeDeliveryMinSum = <?=$freeDeliveryMinSum?>;
    var dadata_status = false;
    <?php if ($arResult['DADATA_STATUS']) { ?>
    dadata_status = true;
    <?php }?>
    var type = "ADDRESS";
    var region = "<?=$arResult['DADATA_REGION_NAME']?>";
    var city = "<?=$arResult['DADATA_CITY_NAME']?>";
    var currentHost = "<?=$arResult['CURRENT_HOST']?>";
    var PVZHidePostamat = <?=CUtil::PhpToJSObject($arResult['DELIVERY']['PVZ_HIDE_POSTAMAT'])?>;
    var PVZHideOnlyPrepayment = <?=CUtil::PhpToJSObject($arResult['DELIVERY']['PVZ_HIDE_ONLY_PREPAYMENT'])?>;
    var currentLocationCode = <?=$LOCATION->code; ?>;
</script>
    <?php $APPLICATION->addHeadString('<script type="text/javascript" src="/local/templates/respect/cdek_widget/widget.js" id="widget_script"></script>') ?>
<div id="main-basket-block">
    <?php if ($this->__component->ajax) { ?>
        <?php $APPLICATION->RestartBuffer() ?>
    <?php } ?>
    <div class="orders__select-size js-choose-size">
        <h2>Выберите размер</h2>
        <form method="post" name="name" class="form-after-cart js-action-form-popup-size">
            <input type="hidden" name="action" value="">
            <div class="js-size-popup">
            </div>
        </form>
    </div>
    <?php if (!empty($arResult['ITEMS'])) { ?>
    <div id="full_basket" class="col-xs-12 full_basket">
        <div class="main main--banner full_basket-container full_basket-container--local">
            <div class="checkout ">
                <form id="b-order" class="checkout__inner clearfix" action="<?= $APPLICATION->GetCurPageParam() ?>" style="display: block" method="post">
                    <!-- main -->
                    <div class="checkout__col checkout__col--main col-md-8 left-side-container">
                        <!-- orders -->
                        <div class="checkout__orders orders">
                            <div id="orders__row-container">
                                <?php if ($this->__component->checkType(array("offers"))) { ?>
                                    <?php $APPLICATION->RestartBuffer() ?>
                                <?php } ?>
                                <!-- orders cards rows -->
                                <?php foreach ($arResult['ITEMS'] as $id => $arItem) { ?>
                                    <?php $arResult["BASKET"][] = [
                                        "id" => sprintf('%s-%s', $arItem["PRODUCT_ID"], $arResult['BRANCH_ID']),
                                        "price" => $arItem["PRICE"],
                                        "quantity" => $arItem["QUANTITY"],
                                    ];
                                    ?>
                                    <div class="flex-product orders__row orders__row--product<?=$arResult["PROBLEM_OFFERS"][$id] ? " orders__row--product--error" : "" ?> js-card"
                                         data-offer-id="<?=$id?>"
                                         data-id="<?=$arItem['XML_ID']?>"
                                         data-name="<?=$arItem['NAME']?>"
                                         data-price="<?=$arItem['PRICE']/$arItem["QUANTITY"]?>"
                                         data-variant="<?=$arItem['COLOR']?>"
                                         data-quantity="<?=$arItem["QUANTITY"]?>"
                                    >
                                        <?php //блок изображения ?>
                                        <div class="flex-product--img orders__block orders__block--img">
                                            <div class="orders__col">
                                                <a href="/<?=$arItem['CODE'] ?>/"
                                                   class="orders__img <?= (count($arItem["SRC"]) == 2) ? 'orders__img--multi' : '' ?>"
                                                   title="Перейти на детальную страницу">

                                            <span class="orders__img-box">
                                                <img src="<?=$arItem["SRC"][0] ?>" alt="<?=$arItem['NAME'] ?>"
                                                     class="orders__img-pic orders__img-pic--main">
                                            </span>
                                                </a>
                                            </div>
                                        </div>
                                        <?php //блок изображения end ?>

                                        <?php //наименование ?>
                                        <div class="flex-product--name orders__col">
                                            <span class="orders__label--only-pc orders__article"><?= $arItem['ARTICLE'] ?></span>
                                            <h3 class="orders__title"><?= $arItem['NAME_WITH_ADDITIONS'] ?></h3>
                                        </div>
                                        <?php //наименование end ?>

                                        <?php //количество ?>
                                        <div class="flex-product--count orders__col">
                                            <span class="orders__label--only-mobile">Кол-во:</span>
                                            <div class="quantity-block">
                                                <div class="quantity-arrow-minus">-</div>
                                                <input data-offer-id="<?=$id?>" class="quantity-num" type="number" value="<?=$arItem['QUANTITY']?>" />
                                                <div class="quantity-arrow-plus">+</div>
                                            </div>
                                        </div>
                                        <?php //количество end ?>

                                        <?php //стоимость ?>
                                        <div class="flex-product--price orders__col">
                                            <?php if ($arItem['OLD_CATALOG_PRICE'] !== $arItem['PRICE']) { ?>
                                            <div>
                                                <span class="orders__label-old">Цена:&nbsp;</span>
                                                <span class="orders__old-catalog-price"><?= number_format($arItem['OLD_CATALOG_PRICE'], 0, "", "&nbsp;") ?> ₽</span>
                                            </div>
                                            <?php } ?>

                                            <div class="coupon-success<?=isset($arItem['OLD_PRICE']) && $arItem['OLD_PRICE'] > $arItem['PRICE'] ? ' coupon-active' : ''?>">
                                                Применен промокод
                                            </div>

                                            <div>
                                                <span class="orders__label">Итого:&nbsp;</span>
                                                <span class="<?= ($arItem['OLD_CATALOG_PRICE'] !== $arItem['PRICE']) ? 'orders__result-price--red' : 'orders__result-price'?>"><?= number_format($arItem['PRICE'], 0, "", "&nbsp;") ?>&nbsp;₽</span>
                                            </div>

                                            <div class="orders__col orders__col--price" style="display: none!important;">
                                                <span class="orders__label">Стоимость:</span>
                                                <span class="orders__price">
                                                <span class="orders__price-num"
                                                      data-price="<?= $arItem['PRICE'] ?>"><?= number_format($arItem['PRICE'], 0, "", "&nbsp;") ?> ₽</span>
                                                <?php if ($arItem['OLD_PRICE'] && $arItem['PRICE'] < $arItem['OLD_PRICE']) { ?>
                                                    <s><span class="orders__old-price-num"
                                                             data-price="<?= $arItem['OLD_PRICE'] ?>"><?= number_format($arItem['OLD_PRICE'], 0, "", "&nbsp;") ?>&nbsp;₽</span></s>
                                                <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php //стоимость end ?>

                                        <a class="orders__remove js-card-remove"
                                           data-offer-id="<?= $arItem["PRODUCT_ID"] ?>"
                                           data-id="<?= $id ?>"
                                           title="Удалить"></a>
                                    </div>
                                <?php } ?>
                                <!-- /orders cards rows -->
                                <?php if ($this->__component->checkType(array("offers"))) { ?>
                                    <?php //$APPLICATION->FinalActions() ?>
                                    <?php die() ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="left-cart-block-local">
                            <!-- /orders -->
                            <?php if (empty($arResult["ERRORS"])) { ?>
                                <!-- delivery -->
                                <div class="checkout__block checkout__block--delivery">
                                    <div class="delivery-blocks-title-wrapper">
                                        Выберите способ доставки
                                    </div>
                                    <span class="err-order err-delivery"></span>
                                    <?php if (!empty($arResult["WAYS_DELIVERY"])) { ?>
                                        <div class="checkout__block checkout__block--flex">
                                            <?php foreach ($arResult["WAYS_DELIVERY"] as $arDeliveryWay) { ?>
                                                <div class="form__box form__box--1-2">
                                                    <div class="cart-delivery">
                                                        <div class="cart-delivery__wrapper">
                                                            <?php //Указывается нулевой индекс потому что несколько элементов прилетает только для ПВЗ, а логика их выбора описана в компоненте карты ПВЗ?>
                                                            <input id="delivery_<?= $arDeliveryWay['DELIVERY'][0] ?>" class="<?=$arDeliveryWay['PVZ'] ? 'is-pvz ' : ''?>checkbox4 js-delivery cart-delivery__input" type="radio" name="DELIVERY"
                                                                   data-allowed-payments="<?=implode(',', $arDeliveryWay['ALLOWED_PAYMENTS'])?>"
                                                                <?php if ($arDeliveryWay['PVZ']) { ?>
                                                                    <?php foreach ($arDeliveryWay['ALLOWED_PVZ_PAYMENTS'] as $class => $paymentsIds) { ?>
                                                                        data-allowed-payments-<?=$class?>="<?=implode(',', $paymentsIds)?>"
                                                                    <?php }?>
                                                                <?php } ?>
                                                                   data-price="<?= min($arDeliveryWay['PRICES']) ?>"
                                                                   value="<?= $arDeliveryWay['DELIVERY'][0] ?>"
                                                                <?=$arDeliveryWay['PVZ'] ? ' disabled="disabled" ' : ''?>
                                                            >
                                                            <label for="delivery_<?= $arDeliveryWay['DELIVERY'][0] ?>" class="cart-delivery__label delivery-label">
                                                                <?php if ($arDeliveryWay['PVZ']) { ?>
                                                                    <div class="load-more-btn-loader filter-btn-loader"></div>
                                                                <?php }?>
                                                                <div class="cart-delivery__header">
                                                                    <?= $arDeliveryWay['NAME'] ?>
                                                                </div>
                                                                <div class="cart-delivery__label-description">
                                                                    <?= $arDeliveryWay['DESCRIPTION'] ?>
                                                                </div>
                                                                <?php if ($arDeliveryWay['PVZ']) { ?>
                                                                    <div class="text-danger-pvz">
                                                                        Внимание! Для корректного отображения пунктов нужно выбрать ваш город слева сверху на сайте
                                                                    </div>
                                                                <?php }?>
                                                                <div class="cart-delivery__price">
                                                                    <?= min($arDeliveryWay['PRICES']) == max($arDeliveryWay['PRICES']) ? max($arDeliveryWay['PRICES']) == 0 ? 'Бесплатно' : 'Стоимость доставки ' . number_format(max($arDeliveryWay['PRICES']), 0, "", "&nbsp;")." ₽" : 'Стоимость доставки от ' . number_format(min($arDeliveryWay['PRICES']), 0, "", "&nbsp;")."&nbsp;р."?>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="text--center">
                                            В вашем регионе не настроена ни одна служба доставки.<br>Сообщите об этом оператору интернет-магазина.
                                        </div>
                                    <?php } ?>
                                </div>
                                <!-- /delivery -->
                                <!-- form -->
                                <div class="checkout__form hidden-block">
                                    <input type="hidden" name="action" value="order">
                                    <div class="checkout__form-fields form">
                                        <div class="form__errors" id="form__errors-block"></div>
                                        <div class="form__field">
                                            <div class="form__box">
                                                <div class="checkout__block--payment">
                                                    <div class="delivery-blocks-title-wrapper">
                                                        Выберите способ оплаты
                                                    </div>
                                                    <span class="err-order err-payment"></span>
                                                    <div class="checkout__block--flex">
                                                        <?php foreach ($arResult["WAYS_PAYMENT"] as $arPaymentWay) {?>
                                                            <div class="form__box form__box--1-2 payment__type payment__type--disabled">
                                                                <div class="cart-delivery">
                                                                    <div class="cart-delivery__wrapper">
                                                                        <input id="Payment_<?= $arPaymentWay['PAYMENT'] ?>"
                                                                               class="checkbox4 cart-delivery__input js-payment"
                                                                               type="radio"
                                                                               name="PAYMENT"
                                                                               data-sort="<?=$arPaymentWay['SORT']?>"
                                                                               data-prepayment="<?=$arPaymentWay['PREPAYMENT']?>"
                                                                               value="<?= $arPaymentWay['PAYMENT'] ?>"
                                                                               disabled
                                                                        >
                                                                        <label for="Payment_<?= $arPaymentWay['PAYMENT']?>" class="cart-delivery__label payment-label">
                                                                            <div class="cart-delivery__header">
                                                                                <?= $arPaymentWay["NAME"] ?>
                                                                            </div>
                                                                            <div class="cart-delivery__label-description">
                                                                                <?= $arPaymentWay["DESCRIPTION"] ?>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="checkout__block--contact-info hidden-block">
                                            <div class="delivery-blocks-title-wrapper">
                                                Укажите контактные данные
                                            </div>
                                            <div class="form__box">
                                                <div class="form__field">
                                                    <input class="form__elem js-required js-fio" type="text" name="PROPS[FIO]" value="<?= (trim($arResult["USER"]["NAME"]." ".$arResult["USER"]["SECOND_NAME"]." ".$arResult["USER"]["LAST_NAME"])) ?: ($arResult["COOKIE_FIO"][0] ?: ($cookieAddress ? $_COOKIE['user_fio'] : ''))  ?>" placeholder="*Имя">
                                                    <div class="err-order err-PROPS[FIO]"></div>
                                                </div>
                                                <div class="form__field form__field--1-2">
                                                    <?php
                                                    $email = $arResult["USER"]["EMAIL"] ?: ($arResult["COOKIE_EMAIL"][1] ?: ($cookieAddress ? $_COOKIE['user_email'] : ''));
                                                    $email = !preg_match('`.*@orgasmcity.ru`i', $email) ? $email : '';
                                                    ?>
                                                    <input class="form__elem js-required js-email" type="text" name="PROPS[EMAIL]" value="<?= $email ?>" placeholder="*E-Mail">
                                                    <div class="err-order err-PROPS[EMAIL]"></div>
                                                </div>
                                                <div class="form__field form__field--1-2">
                                                    <input class="form__elem js-required js-phone" type="text" name="PROPS[PHONE]" value="<?= $arResult["USER"]["PERSONAL_PHONE"] ?: ($arResult["COOKIE_PHONE"][0]  ?: ($cookieAddress ? $_COOKIE['user_phone'] : ''))  ?>" placeholder="*Телефон">
                                                    <div class="err-order err-PROPS[PHONE]"></div>
                                                </div>
                                                <?php $APPLICATION->IncludeComponent(
                                                    'qsoft:geolocation',
                                                    'cart',
                                                    array(
                                                        'CACHE_TYPE' => 'A',
                                                        'CACHE_TIME' => 31536000,
                                                    )
                                                ); ?>
                                                <?php if ($arResult["DELIVERY"]["PVZ"]) { ?>
                                                    <div class="form__field form__field--1-2 js__pvz-enabled is-hidden">
                                                        <input id="cart__delivery-cdek-button" class="form__elem" type="button" value="Выбрать пункт выдачи заказов">
                                                        <input id="cart__delivery-cdek-input" class="form__elem js-required" type="hidden" name="PROPS[PICKPOINT_ID]" value="" placeholder="*Пункт выдачи">
                                                    </div>
                                                <?php } ?>
                                                <div class="form__field form__field--1-2 js__pvz-disabled js-dadata-street">
                                                    <input id="street_user" class="form__elem js-required" type="text" name="PROPS[STREET_USER]" value="<?= $arResult["USER"]["PERSONAL_STREET"] ?: ($cookieAddress ? $_COOKIE['user_street'] : '') ?>" placeholder="*Улица">
                                                    <div class="err-order err-PROPS[STREET_USER]"></div>
                                                </div>
                                                <div class="form__field form__field--1-2 js__pvz-disabled js-dadata-house">
                                                    <input id="house_user" class="form__elem js-required" type="text" name="PROPS[HOUSE_USER]" value="<?= $arResult["USER"]["UF_HOUSE"] ?: ($cookieAddress ? $_COOKIE['user_house'] : '') ?>" placeholder="*Дом, корпус, строение">
                                                    <div class="err-order err-PROPS[HOUSE_USER]"></div>
                                                </div>
                                                <div class="form__field form__field--1-2 js__pvz-disabled">
                                                    <input style="margin-bottom: 20px" <?=$arResult['DADATA_STATUS'] ? 'readonly ' : ''?>id="postal_code"  class="form__elem" type="number" name="PROPS[POSTALCODE]" value="<?= $arResult["USER"]["UF_POSTALCODE"] ?: ($cookieAddress ? $_COOKIE['user_index'] : '')  ?>" placeholder="<?=$arResult['DADATA_STATUS'] ? 'Индекс, заполняется автоматически' : 'Индекс (не обязательно)'?>">
                                                </div>
                                                <div class="form__field form__field--1-4 js__pvz-disabled">
                                                    <input id="flat" class="form__elem" type="text" name="PROPS[FLAT]" value="<?= $arResult["USER"]["UF_APARTMENT"] ?: ($cookieAddress ? $_COOKIE['user_flat'] : '')  ?>" placeholder="Кв/офис">
                                                </div>
                                                <div class="form__field form__field--1-4 js__pvz-disabled">
                                                    <input id="porch" class="form__elem" type="text" name="PROPS[PORCH]" value="<?= $arResult["USER"]["UF_ENTRANCE"] ?: ($cookieAddress ? $_COOKIE['user_porch'] : '')  ?>" placeholder="Подъезд">
                                                </div>
                                                <div class="form__field form__field--1-4 js__pvz-disabled">
                                                    <input id="floor" class="form__elem" type="text" name="PROPS[FLOOR]" value="<?= $arResult["USER"]["UF_FLOOR"] ?: ($cookieAddress ? $_COOKIE['user_floor'] : '')  ?>" placeholder="Этаж">
                                                </div>
                                                <div class="form__field form__field--1-4 js__pvz-disabled">
                                                    <input id="intercom" class="form__elem" type="text" name="PROPS[INTERCOM]" value="<?= $arResult["USER"]["UF_INTERCOM"] ?: ($cookieAddress ? $_COOKIE['user_intercom'] : '')  ?>" placeholder="Домофон">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form__field basket-textarea hidden-block">
                                            <input type="checkbox" class="needComment checkbox3" id="needComment">
                                            <label for="needComment" class="needCommentLabel">Оставить комментарий</label>
                                            <textarea class="form__elem form__elem--textarea hidden-block" name="USER_DESCRIPTION" placeholder="Ваш комментарий" disabled></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- /form -->
                            <?php } ?>
                        </div>
                    </div>
                    <!-- /main -->
                    <!-- sidebar -->
                    <div class="checkout__col checkout__col--sidebar col-md-4">
                        <div class="right-cart-block">
                            <?php if (empty($arResult["ERRORS"])) { ?>
                                <!-- promocode -->
                                <div class="havePromocodeDiv">
                                    <input type="checkbox" class="havePromocode checkbox3" id="havePromocode">
                                    <label for="havePromocode" class="havePromocodeLabel">У меня есть промокод</label>
                                </div>
                                <div class="checkout__block coupon-container hidden-block">
                                    <div class="checkout__title">Промокод</div>
                                    <div class="form__box-coupon">
                                        <div class="form__field-coupon">
                                            <input id="cart__coupon" class="form__elem-coupon" type="text" name="COUPON" value="<?= ($arResult["COUPON"] && $arResult["DISCOUNT"]) ? $arResult["COUPON"] : "" ?>" placeholder="Введите промокод">
                                        </div>
                                        <div id="cart__coupon-error" class="form__field-coupon" style="display: none"></div>
                                        <div id="cart__coupon-success" class="form__field-coupon" style="display: none"></div>
                                        <div class="form__field-coupon">
                                            <input id="cart__coupon-button" type="button" class="form__btn-coupon bttn-coupon" value="Применить скидку">
                                        </div>
                                    </div>
                                </div>
                                <!-- /promocode -->
                                <!-- cost -->
                                <div class="checkout__block">
                                    <div class="checkout__title">Общая стоимость</div>
                                    <div class="form__box">
                                        <div class="form__field">
                                            <div class="p">
                                                <div class="l">Доставка:</div>
                                                <div id="cart__delivery-price" class="r">
                                                    <?=$arResult["PRICE"] > $freeDeliveryMinSum ?
                                                        'Бесплатно'
                                                        : 'Доставка не выбрана'
                                                    ?>
                                                </div>
                                            </div>
                                            <div id="cart__discount-block" class="p <?= !$arResult["DISCOUNT"] ? "is-hidden" : "" ?>">
                                                <div class="l">Скидка по промокоду:</div>
                                                <div id="cart__discount-price" class="r"><?='- ' . number_format($arResult["DISCOUNT"], 0, "", "&nbsp;") ?> ₽</div>
                                            </div>
                                            <div class="p is-hidden">
                                                <div class="l">Скидка "Онлайн оплата":</div>
                                                <div id="cart__prepayment-discount-price" class="r"></div>
                                            </div>
                                            <div class="p">
                                                <div class="l total-sum-text">Всего к оплате:</div>
                                                <div id="cart__total-price" data-value="<?=$arResult["PRICE"]?>" class="r total-sum-price"><?= number_format($arResult["PRICE"], 0, "", "&nbsp;") ?> ₽</div>
                                            </div>
                                        </div>
                                        <div class="form__field" style="margin-bottom: 0">
                                            <input id="cart__order-button" class="form__btn bttn" type="submit" name="submit" value="Оформить заказ">
                                        </div>
                                        <!--                                    <div id="basket_checkbox_policy" class="col-xs-12">-->
                                        <!--                                        <div class="err-order err-policy"></div>-->
                                        <!--                                        <input id="cart__order-policy" type="checkbox" name="basket_checkbox_policy" class="checkbox3 js-required" checked="">-->
                                        <!--                                        <label for="cart__order-policy" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных.</label>-->
                                        <!--                                    </div>-->
                                    </div>
                                </div>
                                <!-- /cost -->
                            <?php } else { ?>
                                <div class="checkout__error-wrapper">
                                    <?php foreach ($arResult["ERRORS"] as $error) { ?>
                                        <p class="checkout__error-text"><?= $error ?></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- /sidebar -->
                </form>
            </div>
        </div>
    </div>
</div>
    <?php } ?>
    <?php
    if ($this->__component->ajax) {
        //$APPLICATION->FinalActions();
        die();
    } ?>
<?php } else { ?>
    <?php if ($this->__component->checkType(array("offers")) || $this->__component->ajax) : ?>
        <?php $APPLICATION->RestartBuffer() ?>
    <?php endif ?>
    <div class="page-massage">
        <p>Ваша корзина пока пуста</p>
    </div>
    <?php if ($this->__component->checkType(array("offers")) || $this->__component->ajax) { ?>
        <?php //$APPLICATION->FinalActions() ?>
        <?php die() ?>
    <?php } ?>
<?php } ?>
<?php
if (!empty($arResult["ITEMS"]) && isset($arResult["PRICE"]) && $arResult["PRICE"] < $basketMinSum) {
    $APPLICATION->IncludeComponent(
        'orgasmcity:products.line',
        'default',
        [
            'TITLE' => 'Чем дополнить корзину',
            'FILTERS' => [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "PRICE_FROM" => $basketMinSum - $arResult["PRICE"],
                "PRICE_TO" => ($basketMinSum - $arResult["PRICE"]) * 1.2 + 100,
            ],
        ]
    );
}
?>

<?php $APPLICATION->IncludeComponent('qsoft:pvzmap', '');?>

