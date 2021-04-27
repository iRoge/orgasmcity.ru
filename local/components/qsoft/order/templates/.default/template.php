<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option;

$this->addExternalJS("/local/templates/respect/js/jquery.suggestions.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css");
$this->addExternalJS("/local/templates/respect/js/select2/select2.min.js");
global $LOCATION, $APPLICATION; ?>
<? if (!empty($arResult["ITEMS"])) : ?>
<script>
    var arDelIdsJs = <?=CUtil::PhpToJSObject($arResult["DELIVERY"]["PVZIDS"])?>;
    var arOnlinePaymentIds = <?=CUtil::PhpToJSObject($arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"])?>;
    var token = "<?=DADATA_TOKEN?>";
    var paymentWayErrorText = "<?= Option::get("respect", "disabled_payment_click_text", "");?>";
    <? $prepaymentMinSumm = Option::get("respect", "prepayment_min_summ");
    if ($prepaymentMinSumm > 0) { ?>
    var prepayment_min_summ = <?= $prepaymentMinSumm ?>;
    <? } ?>
    var dadata_status = false;
    <? if ($arResult['DADATA_STATUS']) : ?>
    dadata_status = true;
    <?endif;?>
    var arDadataProps = <?=CUtil::PhpToJSObject($arResult['DADATA_PROPS'])?>;
    var type = "ADDRESS";
    var region = "<?=$arResult['DADATA_REGION_NAME']?>";
    var city = "<?=$arResult['DADATA_CITY_NAME']?>";
    var currentHost = "<?=$arResult['CURRENT_HOST']?>";
    var PVZHidePostamat = <?=CUtil::PhpToJSObject($arResult['DELIVERY']['PVZ_HIDE_POSTAMAT'])?>;
    var PVZHideOnlyPrepayment = <?=CUtil::PhpToJSObject($arResult['DELIVERY']['PVZ_HIDE_ONLY_PREPAYMENT'])?>;
</script>
    <? $APPLICATION->addHeadString('<script type="text/javascript" src="/local/templates/respect/cdek_widget/widget.js" id="widget_script"></script>') ?>
<div id="main-basket-block">
    <? if ($this->__component->ajax) : ?>
        <? $APPLICATION->RestartBuffer() ?>
    <? endif ?>
    <div class="orders__select-size js-choose-size">
        <h2>Выберите размер</h2>
        <form method="post" name="name" class="form-after-cart js-action-form-popup-size">
            <input type="hidden" name="action" value="">
            <div class="js-size-popup">
            </div>
        </form>
    </div>
    <? if (!empty($arResult['ITEMS'])) : ?>
    <div id="full_basket" class="col-xs-12 col-xs-12--padding-sm-0 full_basket">
        <div class="main main--banner full_basket-container full_basket-container--local">
            <div class="checkout ">
                <form id="b-order" class="checkout__inner clearfix" action="<?= $APPLICATION->GetCurPageParam() ?>" style="<?=empty($arResult['ITEMS']['NOT_LOCAL']) ? 'display: block' : ''?>" method="post">
                    <!-- main -->
                    <div class="checkout__col checkout__col--main col-md-8">
                        <!-- orders -->
                        <div class="checkout__orders orders">

                            <!-- orders header row -->
                            <div class="orders__row orders__row--header">
                                    <div class="orders__col orders__col--img">Товар</div>
                                    <div class="orders__col orders__col--name"></div>
                                    <div class="orders__col orders__col--size">Размер</div>
                                    <div class="orders__col orders__col--count">Кол-во</div>
                                    <div class="orders__col orders__col--price">Стоимость</div>
                            </div>
                            <!-- /orders header row -->
                            <div id="orders__row-container">
                            <? if ($this->__component->checkType(array("offers"))) : ?>
                                <? $APPLICATION->RestartBuffer() ?>
                            <? endif ?>
                            <!-- orders cards rows -->
                            <? foreach ($arResult['ITEMS']['LOCAL'] as $id => $arItem) : ?>
                                <? $arResult["BASKET"][] = [
                                    "id" => sprintf('%s-%s', $arItem["PRODUCT_ID"], $arResult['BRANCH_ID']),
                                    "price" => $arItem["PRICE"],
                                    "quantity" => $arItem["QUANTITY"],
                                ];

                                $propCat = $arItem['RHODEPRODUCT'];
                                $propCat .= $arItem['VID'] ? '/' . $arItem['VID'] : '';
                                $propCat .= $arItem['TYPEPRODUCT'] ? '/' . $arItem['TYPEPRODUCT'] : '';
                                $propCat .= $arItem['SUBTYPEPRODUCT'] ? '/' . $arItem['SUBTYPEPRODUCT'] : '';
?>
                                <div class="flex-product orders__row orders__row--product <?= $arResult["PROBLEM_LOCAL_OFFERS"][$id] ? "orders__row--product--error" : "" ?> js-card"
                                     data-prod-id="<?= $arItem['PRODUCT_ID'] ?>"
                                     data-prod-articul="<?= $arItem['ARTICLE'] ?>"
                                     data-prod-name="<?= $arItem['NAME'] . ($arItem['ARTICLE'] ? ' | ' . $arItem['ARTICLE'] : '') ?>"
                                     data-prod-brand="<?= $arItem['BRAND'] ?>"
                                     data-prod-top-material="<?= $arItem['UPPERMATERIAL'] ?>"
                                     data-prod-lining-material="<?= $arItem['LININGMATERIAL'] ?>"
                                     data-prod-season="<?= $arItem['SEASON'] ?>"
                                     data-prod-variant="<?= $arItem['COLORSFILTER'] ?>"
                                     data-prod-collection="<?= $arItem['COLLECTION'] ?>"
                                     data-prod-category="<?= $propCat ?>"
                                     data-prod-price="<?= number_format($arItem['PRICE'], 0, '', ''); ?>"
                                     data-prod-size="<?= $arItem['SIZE'] ?>"
                                >
                                    <? //блок изображения ?>
                                    <div class="flex-product--img orders__block orders__block--img">
                                        <div class="orders__col">
                                            <a href="/<?= $arItem['CODE'] ?>/"
                                               class="orders__img <?= (count($arItem["SRC"]) == 2) ? 'orders__img--multi' : '' ?>"
                                               title="Перейти на детальную страницу">

                                            <span class="orders__img-box">
                                                <img src="<?= $arItem["SRC"][0] ?>" alt="<?= $arItem['NAME'] ?>"
                                                     class="orders__img-pic orders__img-pic--main">
                                                <? if ($arItem["SRC"][1]) : ?>
                                                    <img src="<?= $arItem["SRC"][1] ?>"
                                                         alt="<?= $arItem['NAME'] ?>"
                                                         class="orders__img-pic orders__img-pic--additional">
                                                <? endif ?>
                                            </span>
                                            </a>
                                        </div>
                                    </div>
                                    <? //блок изображения end ?>

                                    <? //наименование ?>
                                    <div class="flex-product--name orders__col">
                                        <span class="orders__article"><?= $arItem['ARTICLE'] ?></span>
                                        <h3 class="orders__title"><?= $arItem['NAME'] ?></h3>
                                    </div>
                                    <? //наименование end ?>

                                    <? //селектор размера и кнопка добавления ?>
                                    <div class="flex-product--size select-size-container">
                                        <? if (!empty($arItem['AVAILABLE_SIZES'])) : ?>
                                            <div class="orders__col--select-size-container"
                                                 id="select-size-container">
                                                <select name="select-size" id="select-size-<?= $id ?>">
                                                    <option value="<?= $id ?>"
                                                            selected="selected"><?= $arItem['SIZE'] ?></option>
                                                    <? foreach ($arItem['AVAILABLE_SIZES'] as $size => $offerId) : ?>
                                                        <option value="<?= $offerId ?>"><?= $size ?></option>
                                                    <? endforeach ?>
                                                </select>
                                            </div>
                                            <div class="orders__col orders__col--add-btn-container">
                                                <button id="buy-btn-<?= $id ?>" class="orders__add-btn"
                                                        type="button" value="<?= $id ?>">
                                                    Добавить размер
                                                </button>
                                            </div>
                                        <? elseif (!empty($arItem['SIZE'])) : ?>
                                            <div class="orders__col--select-size-container"
                                                 id="select-size-container">
                                                <span title="Размер"
                                                      class="orders__size"><?= $arItem['SIZE'] ?></span>
                                            </div>
                                        <? endif ?>
                                    </div>
                                    <? //селектор размера и кнопка добавления end ?>

                                    <? //количество ?>
                                    <div class="flex-product--count orders__col">
                                        <span class="orders__label--only-mobile">Кол-во:</span>
                                        <input type="text" class="orders__count-input" name="count" value="1" disabled>
                                    </div>
                                    <? //количество end ?>

                                    <? //стоимость ?>
                                    <div class="orders__col orders__col--price" style="display: none!important;">
                                        <span class="orders__label">Стоимость:</span>
                                        <span class="orders__price">
                                            <span class="orders__price-num"
                                                  data-price="<?= $arItem['PRICE'] ?>"><?= number_format($arItem['PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                            <? if ($arItem['OLD_PRICE'] && $arItem['PRICE'] < $arItem['OLD_PRICE']) : ?>
                                                <s><span class="orders__old-price-num"
                                                         data-price="<?= $arItem['OLD_PRICE'] ?>"><?= number_format($arItem['OLD_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span></s>
                                            <? endif ?>
                                        </span>
                                    </div>
                                    <div class="flex-product--price">
                                        <div class="orders__col">
                                            <span class="orders__label">Цена:&nbsp;</span>
                                            <span>
                                                <? if ($arItem['BRANCH'] == 'Red') : ?>
                                                    <? if (isset($arItem['OLD_PRICE']) && $arItem['OLD_PRICE'] > $arItem['PRICE']) : ?>
                                                        <span class="orders__old-catalog-price--red"><?= number_format($arItem['OLD_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                    <? else : ?>
                                                        <span class="orders__old-catalog-price"><?= number_format($arItem['OLD_CATALOG_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                    <? endif ?>
                                                <? endif ?>
                                                <? if ($arItem['BRANCH'] == 'White') : ?>
                                                    <span class="orders__old-catalog-price--white"><?= number_format($arItem['OLD_CATALOG_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                <? endif ?>
                                                <? if ($arItem['BRANCH'] == 'Yellow') : ?>
                                                    <? if ($arItem['OLD_CATALOG_PRICE'] == $arItem['PRICE']) : ?>
                                                        <span class="orders__old-catalog-price--yellow"><?= number_format($arItem['OLD_CATALOG_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                    <? else : ?>
                                                        <span class="orders__old-catalog-price--yellow-sale"><?= number_format($arItem['OLD_CATALOG_PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                    <? endif ?>
                                                <? endif ?>
                                            </span>
                                        </div>

                                        <div class="orders__col orders__col--annotation">
                                            <? if ($arItem['BRANCH'] == 'White') : ?>
                                                Цена с учетом скидки по бонусной карте.
                                            <? elseif ($arItem['BRANCH'] == 'Red') : ?>
                                                Товар участвует в распродаже. Цена с учетом скидки по бонусной карте.
                                            <? elseif ($arItem['BRANCH'] == 'Yellow') : ?>
                                                Цена с учетом скидки по бонусной карте.
                                            <? endif ?>
                                            <? if (isset($arItem['OLD_PRICE']) && $arItem['OLD_PRICE'] > $arItem['PRICE']) : ?>
                                                Применен промокод.
                                            <? endif ?>
                                        </div>

                                        <div class="orders__col">
                                            <span class="orders__label">Итого:&nbsp;</span>
                                            <span>
                                                <? if ($arItem['BRANCH'] == 'Red') : ?>
                                                    <span class="orders__result-price--red"><?= number_format($arItem['PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                <? else : ?>
                                                    <span class="orders__result-price"><?= number_format($arItem['PRICE'], 0, "", "&nbsp;") ?>&nbsp;р.</span>
                                                <? endif ?>
                                            </span>
                                        </div>
                                    </div>
                                    <? //стоимость end ?>

                                    <? //попап добавления еще одного размера ?>
                                    <div style="display: none; width: 100%;"
                                         class="js-size-selector sizes-<?= $id ?>">
                                        <input type="hidden" id="del-popup-type" value="">
                                        <? foreach ($arItem['AVAILABLE_SIZES'] as $size => $offerId) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="size" id="offer-<?= $offerId ?>"
                                                       class="radio1"
                                                       value="<?= $offerId ?>"/>
                                                <label class="sizes-input" for="offer-<?= $offerId ?>"
                                                       data-offer-id="<?= $offerId ?>"><?= $size ?></label>
                                            </div>
                                        <? endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <? //попап добавления еще одного размера end ?>

                                    <a class="orders__remove js-card-remove"
                                       data-product-id="<?= $arItem["PRODUCT_ID"] ?>"
                                       data-id="<?= $id ?>"
                                       title="Удалить"></a>
                                </div>
                            <? endforeach ?>
                            <!-- /orders cards rows -->
                            <? if ($this->__component->checkType(array("offers"))) : ?>
                                <? //$APPLICATION->FinalActions() ?>
                                <? die() ?>
                            <? endif ?>
                            </div>
                        </div>
                        <div class="left-cart-block-local">
                        <!-- /orders -->
                        <? if (empty($arResult["ERRORS"]['LOCAL'])) : ?>
                            <!-- delivery -->
                            <div class="checkout__block checkout__block--delivery">
                                <h2 class="checkout__title checkout__title--delivery">Доставка <span class="err-order err-delivery"></span></h2>
                                <? if (!empty($arResult["WAYS_DELIVERY"]['LOCAL'])) : ?>
                                    <div class="checkout__block checkout__block--flex">
                                        <? foreach ($arResult["WAYS_DELIVERY"]['LOCAL'] as $arDeliveryWay) : ?>
                                            <div class="form__box form__box--1-2">
                                                <div class="cart-delivery">
                                                    <div class="cart-delivery__wrapper">
                                                        <?//Указывается нулевой индекс потому что несколько элементов прилетает только для ПВЗ, а логика их выбора описана в компоненте карты ПВЗ?>
                                                        <input id="delivery_<?= $arDeliveryWay['DELIVERY'][0] ?>" class="<?=$arDeliveryWay['PVZ'] ? 'is-pvz ' : ''?>checkbox4 js-delivery cart-delivery__input" type="radio" name="DELIVERY"
                                                               data-allowed-payments="<?=implode(',', $arDeliveryWay['ALLOWED_PAYMENTS'])?>"
                                                                <? if ($arDeliveryWay['PVZ']) : ?>
                                                                    <? foreach ($arDeliveryWay['ALLOWED_PVZ_PAYMENTS'] as $class => $paymentsIds) :?>
                                                                        data-allowed-payments-<?=$class?>="<?=implode(',', $paymentsIds)?>"
                                                                    <? endforeach;?>
                                                                <?endif;?>
                                                               data-price="<?= min($arDeliveryWay['PRICES']) ?>"
                                                               value="<?= $arDeliveryWay['DELIVERY'][0] ?>"
                                                                <?=$arDeliveryWay['PVZ'] ? ' disabled="disabled" ' : ''?>
                                                        >
                                                        <label for="delivery_<?= $arDeliveryWay['DELIVERY'][0] ?>" class="cart-delivery__label delivery-label">
                                                            <? if ($arDeliveryWay['PVZ']) : ?>
                                                            <div class="load-more-btn-loader filter-btn-loader"></div>
                                                            <? endif?>
                                                            <div class="cart-delivery__header">
                                                                <?= $arDeliveryWay['NAME'] ?>
                                                            </div>
                                                            <div class="cart-delivery__label-description">
                                                                <?= $arDeliveryWay['DESCRIPTION'] ?>
                                                            </div>
                                                            <div class="cart-delivery__price">
                                                                <?= min($arDeliveryWay['PRICES']) == max($arDeliveryWay['PRICES']) ? max($arDeliveryWay['PRICES']) == 0 ? 'Бесплатно' : 'Стоимость доставки ' . number_format(max($arDeliveryWay['PRICES']), 0, "", "&nbsp;")."&nbsp;р." : 'Стоимость доставки от ' . number_format(min($arDeliveryWay['PRICES']), 0, "", "&nbsp;")."&nbsp;р."?>
                                                            </div>
    <!--                                                        Закомментировано поле "Желаемое время доставки" по решению заказчика в тикете 117573-->
    <!--                                                        <div class="form__field form__field--1-2 js__cdek-disabled">-->
    <!--                                                            <select class="form__elem form__elem--grey-first" name="PROPS[DELIVERY_TIME]">-->
    <!--                                                                <option value="" selected>Желаемое время доставки</option>-->
    <!--                                                                <option value="1" --><?//= $arResult["USER"]["UF_TIME"] == 1 ? "selected" : "" ?>
    <!--                                                                >10-14</option>-->
    <!--                                                                <option value="2" --><?//= $arResult["USER"]["UF_TIME"] == 2 ? "selected" : "" ?>
    <!--                                                                >14-18</option>-->
    <!--                                                                <option value="3" --><?//= $arResult["USER"]["UF_TIME"] == 3 ? "selected" : "" ?>
    <!--                                                                >16-20</option>-->
    <!--                                                            </select>-->
    <!--                                                        </div>-->
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <? endforeach ?>
                                    </div>
                                <? else : ?>
                                    <div class="text--center">
                                        В вашем регионе не настроена ни одна служба доставки.<br>Сообщите об этом оператору интернет-магазина.
                                    </div>
                                <? endif ?>
                            </div>
                            <!-- /delivery -->
                            <!-- form -->
                            <div class="checkout__form">
                                <input type="hidden" name="action" value="order">
                                <input type="hidden" name="PROPS[IS_LOCAL]" value="Y" hidden>
                                <div class="checkout__form-fields form">
                                    <div class="form__errors" id="form__errors-block"></div>
                                    <div class="form__field form__field--payment">
                                        <div class="form__box">
                                            <div class="checkout__block--payment">
                                                <h2 class="checkout__title checkout__title--delivery">Оплата <span class="err-order err-payment"></span></h2>
                                                <div class="checkout__block--flex">
                                                <? foreach ($arResult["WAYS_PAYMENT"]['LOCAL'] as $arPaymentWay) :?>
                                                    <div class="form__box form__box--1-2 payment__type payment__type--disabled">
                                                        <div class="cart-delivery">
                                                            <div class="cart-delivery__wrapper">
                                                                <input id="Payment_<?= $arPaymentWay['PAYMENT'] ?>"
                                                                       class="checkbox4 cart-delivery__input js-payment-local"
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
                                                <? endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="checkout__block--contact-info">
                                        <div class="form__box">
                                            <div class="form__field">
                                                <h2 class="checkout__title checkout__title--contact-info">Контактные данные</h2>
                                                <input class="form__elem js-required js-fio" type="text" name="PROPS[FIO]" value="<?= (trim($arResult["USER"]["NAME"]." ".$arResult["USER"]["SECOND_NAME"]." ".$arResult["USER"]["LAST_NAME"])) ?: $arResult["COOKIE_FIO"][0] ?>" placeholder="*Ф.И.О.">
                                                <div class="err-order err-PROPS[FIO]"></div>
                                            </div>
                                            <div class="form__field form__field--1-2">
                                                <input class="form__elem js-required js-email" type="text" name="PROPS[EMAIL]" value="<?= !preg_match('`.*@rshoes.ru`i', $arResult["USER"]["EMAIL"] ?: $arResult["COOKIE_EMAIL"][0]) ? ($arResult["USER"]["EMAIL"] ?: $arResult["COOKIE_EMAIL"][0]) : '' ?>" placeholder="*E-Mail">
                                                <div class="err-order err-PROPS[EMAIL]"></div>
                                            </div>
                                            <div class="form__field form__field--1-2">
                                                <input class="form__elem js-required js-phone" type="text" name="PROPS[PHONE]" value="<?= $arResult["USER"]["PERSONAL_PHONE"] ?: $arResult["COOKIE_PHONE"][0] ?>" placeholder="*Телефон">
                                                <div class="err-order err-PROPS[PHONE]"></div>
                                            </div>
<!--                                            --><?// $APPLICATION->IncludeComponent(
//                                                'qsoft:subscribe.manager',
//                                                'cart',
//                                                [
//                                                    'SOURCE' => 'cart',
//                                                    'CART_NUMBER' => 1,
//                                                ]
//                                            ); ?>
                                            <? $APPLICATION->IncludeComponent(
                                                'qsoft:geolocation',
                                                'cart',
                                                array(
                                                    'CACHE_TYPE' => 'A',
                                                    'CACHE_TIME' => 31536000,
                                                    'BASKET_TYPE' => 'LOCAL',
                                                )
                                            ); ?>
                                            <? if ($arResult["DELIVERY"]["PVZ"]) : ?>
                                            <div class="form__field form__field--1-2 js__cdek-enabled is-hidden">
                                                <input id="cart__delivery-cdek-button" class="form__elem" type="button" value="Выбрать пункт выдачи заказов">
                                                <input id="cart__delivery-cdek-input" class="form__elem js-required" type="hidden" name="PROPS[PVZ_ID]" value="" placeholder="*Пункт выдачи">
                                            </div>
                                            <? endif ?>
                                            <div class="form__field form__field--1-2 js__cdek-disabled js-dadata-street">
                                                <input id="street_user" class="form__elem js-required" type="text" name="PROPS[STREET_USER]" value="<?= $arResult["USER"]["PERSONAL_STREET"] ?>" placeholder="*Улица">
                                                <div class="err-order err-PROPS[STREET_USER]"></div>
                                            </div>
                                            <div class="form__field form__field--1-2 js__cdek-disabled js-dadata-house">
                                                <input id="house_user" class="form__elem js-required" type="text" name="PROPS[HOUSE_USER]" value="<?= $arResult["USER"]["UF_HOUSE"] ?>" placeholder="*Дом, корпус, строение">
                                                <div class="err-order err-PROPS[HOUSE_USER]"></div>
                                            </div>
                                            <div class="form__field form__field--1-2 js__cdek-disabled">
                                                <input <?=$arResult['DADATA_STATUS'] && COption::GetOptionInt("likee", "dadata_active") ? 'readonly ' : ''?>id="postal_code"  class="form__elem" type="number" name="PROPS[POSTALCODE]" value="<?= $arResult["USER"]["UF_POSTALCODE"] ?>" placeholder="<?=$arResult['DADATA_STATUS'] && COption::GetOptionInt("likee", "dadata_active") ? 'Индекс, заполняется автоматически' : 'Индекс (не обязательно)'?>">
                                            </div>
                                            <div class="form__field form__field--1-4 js__cdek-disabled">
                                                <input id="flat" class="form__elem" type="text" name="PROPS[FLAT]" value="<?= $arResult["USER"]["UF_APARTMENT"] ?>" placeholder="Кв/офис">
                                            </div>
                                            <div class="form__field form__field--1-4 js__cdek-disabled">
                                                <input id="porch" class="form__elem" type="text" name="PROPS[PORCH]" value="<?= $arResult["USER"]["UF_ENTRANCE"] ?>" placeholder="Подъезд">
                                            </div>
                                            <div class="form__field form__field--1-4 js__cdek-disabled">
                                                <input id="floor" class="form__elem" type="text" name="PROPS[FLOOR]" value="<?= $arResult["USER"]["UF_FLOOR"] ?>" placeholder="Этаж">
                                            </div>
                                            <div class="form__field form__field--1-4 js__cdek-disabled">
                                                <input id="intercom" class="form__elem" type="text" name="PROPS[INTERCOM]" value="<?= $arResult["USER"]["UF_INTERCOM"] ?>" placeholder="Домофон">
                                            </div>
                                            <? foreach ($arResult['DADATA_PROPS'] as $dadataProp) :?>
                                                <div class="form__field js__cdek-disabled" hidden>
                                                    <input hidden id="<?= $dadataProp ?>"  class="form__elem" type="text" name="PROPS[<?= mb_strtoupper($dadataProp) ?>]" value="<?= $arResult["USER"]["UF_" . mb_strtoupper($dadataProp)] ?>" placeholder="Заполняется автоматически">
                                                </div>
                                            <? endforeach; ?>
                                            <div class="clear-blocks"></div>
                                        </div>
                                    </div>
                                    <div class="form__field basket-textarea">
                                        <textarea class="form__elem form__elem--textarea" name="USER_DESCRIPTION" placeholder="Комментарий к заказу"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- /form -->
                        <? endif ?>
                        </div>
                    </div>
                    <!-- /main -->
                    <!-- sidebar -->
                    <div class="right-cart-block-local checkout__col checkout__col--sidebar col-md-4">
                        <? if (empty($arResult["ERRORS"]['LOCAL'])) : ?>
                        <!-- promocode -->
                        <div class="checkout__block">
                            <h2 class="checkout__title">Промокод</h2>
                            <div class="form__box">
                                <div class="form__field">
                                    <input id="cart__coupon" class="form__elem" type="text" name="COUPON" value="<?= ($arResult["COUPON"] && $arResult["DISCOUNT"]) ? $arResult["COUPON"] : "" ?>" placeholder="Введите промокод при его наличии">
                                </div>
                                <div id="cart__coupon-error" class="form__field"></div>
                                <div class="form__field">
                                    <input id="cart__coupon-button" type="button" class="form__btn bttn bttn--yellow" value="ПРИМЕНИТЬ ПРОМОКОД">
                                </div>
                            </div>
                        </div>
                        <!-- /promocode -->
                        <!-- cost -->
                        <div class="checkout__block">
                            <h2 class="checkout__title">Общая стоимость</h2>
                            <div class="form__box">
                                <div class="form__field">
                                    <div class="p">
                                        <div class="l">Доставка</div>
                                        <div id="cart__delivery-price" class="r">Способ доставки не выбран</div>
                                    </div>
                                    <div id="cart__discount-block" class="p <?= !$arResult["DISCOUNT"]['LOCAL'] ? "is-hidden" : "" ?>">
                                        <div class="l">Скидка</div>
                                        <div id="cart__discount-price" class="r"><?= number_format($arResult["DISCOUNT"]['LOCAL'], 0, "", "&nbsp;") ?>&nbsp;р.</div>
                                    </div>
                                    <div class="p">
                                        <div class="l">Всего к оплате</div>
                                        <div id="cart__total-price" class="r"><?= number_format($arResult["PRICE"]['LOCAL'], 0, "", "&nbsp;") ?>&nbsp;р.</div>
                                    </div>
                                </div>
                                <div class="form__field">
                                    <input id="cart__order-button" class="form__btn bttn" type="submit" name="submit" value="Отправить заказ">
                                </div>
                                <div id="basket_checkbox_policy" class="col-xs-12">
                                    <div class="err-order err-policy"></div>
                                    <input id="cart__order-policy" type="checkbox" name="basket_checkbox_policy" class="checkbox3 js-required" checked="">
                                    <label for="cart__order-policy" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a>.</label>
                                </div>
                            </div>
                        </div>
                        <!-- /cost -->
                        <? else : ?>
                            <div class="checkout__error-wrapper">
                            <? foreach ($arResult["ERRORS"]['LOCAL'] as $error) : ?>
                                <p class="checkout__error-text"><?= $error ?></p>
                            <? endforeach ?>
                            </div>
                        <? endif ?>
                    </div>
                    <!-- /sidebar -->
                </form>
            </div>
        </div>
    </div>
    <? endif; ?>
    <? if ($this->__component->ajax) : ?>
        <? //$APPLICATION->FinalActions() ?>
        <? die() ?>
    <? endif ?>
<? else : ?>
    <? if ($this->__component->checkType(array("offers")) || $this->__component->ajax) : ?>
        <? $APPLICATION->RestartBuffer() ?>
    <? endif ?>
    <div class="page-massage">
        <p>Ваша корзина пока пуста</p>
    </div>
    <? if ($this->__component->checkType(array("offers")) || $this->__component->ajax) : ?>
        <? //$APPLICATION->FinalActions() ?>
        <? die() ?>
    <? endif ?>
<? endif ?>
</div>

<?$APPLICATION->IncludeComponent('qsoft:pvzmap', '');?>

