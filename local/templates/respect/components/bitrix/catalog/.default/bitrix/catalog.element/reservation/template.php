<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use \Bitrix\Main\UserTable;
?>
<script>
BX.message({'RESERVED_STORES_LIST': '<?= json_encode($arResult['SHOPS']) ?>'});
</script>
<form id="one-click-form" class="product-page product b-element-one-click js-reserv" action="/cart/" method="post">
    <?= bitrix_sessid_post(); ?>
    <input type="hidden" name="action" value="reserv">
    <input type="hidden" name="DELIVERY_STORE_ID" value="">
    <div class="product-preorder">
        <header>
            <div class="product-preorder__title">Резервирование</div>
        </header>
        <main>
            <div class="row product-preorder__container">
                <article class="col-md-6 col-lg-8 column-md-2 product-preorder__article column-66">
                    <div class="tabs tabs--shop js-tabs">
                        <a data-target="#list" class="tabs-item active">Список</a>
                        <a data-target="#map" class="tabs-item ">Карта</a>
                        <?if ($arResult['CITY_NAME'] == 'Москва') :?>
                            <a data-target="#subway" class="tabs-item">Метро</a>
                        <?endif;?>
                    </div>
                    <div class="tabs-targets">
                        <div id="list" class="active" data-init="list">
                            <div class="preorder-list" id="reserved-shop-list">
                            </div>
                        </div>
                        <div id="map" data-init="map">
                            <div class="shop-map--square js-shop-list-map shop-map" id="reserved-map"
                            data-lat="<?= $arResult['LOCATION']['LAT'] ?>" data-lon="<?= $arResult['LOCATION']['LON'] ?>">
                            </div>
                        </div>
                        <div id="subway" class="subway-map" data-init="metro">
                            <div class="preloader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
                </article>
                <aside class="col-md-6 col-lg-4 product-preorder__aside-info column-33 column-md-2">
                    <? if (!empty($arResult['PICTURE']) && is_array($arResult['PICTURE'])) : ?>
                        <span class="product-preorder__media phone--hidden">
                            <img src="<?= $arResult['PICTURE']['SRC'] ?>" alt="<?= $arResult['PICTURE']['ALT'] ?>">
                        </span>
                    <? endif ?>
                    <div class="product-preorder__short-info">
                        <? if (!empty($arResult['NAME'])) : ?>
                            <div class="product-preorder__name">
                                <?=$arResult['NAME']; ?>
                            </div>
                        <? endif; ?>
                        <? if (!empty($arResult['ARTICLE'])) : ?>
                            <div class="product-preorder__sku">
                                Арт: <?= $arResult['ARTICLE'] ?>
                            </div>
                        <? endif; ?>
                    </div>
                    <div class="product-preorder__info">
                        <div class="product-preorder__price">
                            <b class="product-preorder__main-price<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red" ? " product-preorder__main-price--discount" : "" ?>">
                                <?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'], 0, "", " ") ?> р.
                            </b>
                            <? if (!empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) &&
                                $arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'] < $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) : ?>
                                <div class="product-preorder__discount-percent">
                                    -<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['PERCENT'] ?>%
                                </div>
                                <div class="product-preorder__old-price">
                                    <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE'], 0, "", " ") ?></b> р.
                                </div>
                            <? endif ?>
                        </div>
                    </div>
                    <? if (!empty($arResult['PRICE_PRODUCT'])) : ?>
                        <div class="product-preorder__messages">
                            <div class="product-preorder__cost-segment-desc">
                                * <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red"  ? "бонусная программа не действует" : "по условиям бонусной программы" ?>
                            </div>
                            <div class="product-preorder__cost-segment-desc">
                                * цены на сайте могут отличаться от цен в магазинах
                            </div>
                        </div>
                    <? endif ?>
                    <? if (!$arResult['SINGLE_SIZE']) : ?>
                        <div class="product-preorder__size">
                            <label>Размеры</label>
                            <div class="size-selector size-selector--wrap js-size-selector">
                                <? foreach ($arResult['RESTS']['RESERVATION'] as $offerId => $size) :?>
                                    <div class="top-minus <?= $sClass ?>">
                                        <input type="radio"
                                               name="PRODUCTS[]"
                                               id="reserve-offer-<?= $offerId ?>"
                                               class="radio1 js-offer-res"
                                               value="<?= $offerId ?>"
                                               <?= ($offerId == $_GET['offerId']) ? "checked" : "" ?>/>
                                        <label class="reservation-popup-sizes-input" for="reserve-offer-<?= $offerId ?>" data-offer-id="<?= $offerId ?>"><?= $size ?></label>
                                    </div>
                                <? endforeach;?>
                                <div class="alert alert--danger js-offer-error" style="display: none;">
                                    <div class="alert-content noshop-block">
                                        <i class="icon icon-exclamation-circle"></i>
                                        Выберите размер
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? else : ?>
                        <input type="radio"
                               name="PRODUCTS[]"
                               id="reserve-offer-<?= $arResult['SINGLE_SIZE'] ?>"
                               class="radio1 js-offer-res single-size-input"
                               value="<?= $arResult['SINGLE_SIZE'] ?>"
                               checked/>
                    <? endif ?>
                </aside>
                <aside class="col-md-6 col-lg-4 product-preorder__aside-form">
                    <div class="product-preorder__form">
                        <div id="after-cart-in-err"></div>
                        <? if (CUser::IsAuthorized()) {
                            $arUser = UserTable::getList(array(
                                "select" => array(
                                    "NAME",
                                    "LAST_NAME",
                                    "SECOND_NAME",
                                    "PERSONAL_PHONE",
                                ),
                                "filter" => array(
                                    "ID" => $USER->GetID(),
                                ),
                                "limit" => 1,
                            ))->Fetch();
                            $arUser['FIO'] = str_replace('  ', ' ', $arUser['NAME'].' '.$arUser['SECOND_NAME'].' '.$arUser['LAST_NAME']);
                        } ?>
                        <input name="PROPS[FIO]" data-fio="<?= $arUser['FIO'] ?>" class="fio" placeholder="*ФИО" type="text" required>
                        <input name="PROPS[PHONE]" data-phone="<?= $arUser['PERSONAL_PHONE'] ?>" class="reservation_phone" placeholder="*Телефон" type="text" required>
                        <div class="alert alert--danger js-store-selected phone--only" style="display: none;">
                            <div class="alert-content">
                                Выбран магазин «<span class="js-store-selected-value"></span>»
                            </div>
                        </div>
                        <button form="one-click-form" class="js-preorder-submit cartochka-transparent js-preorder-submit--reservation">Резервировать</button>
                        <div class="buttonReservation-loader">
                            <div class="one-click-preloader-div">
                                <button class="reservation-preloader">ОЖИДАЙТЕ</button>
                            </div>
                        </div>
                        <div id="reservation_checkbox_policy" class="col-xs-12">
                            <input type="checkbox" id="reservation_checkbox_policy_checked" name="reservation_checkbox_policy" class="checkbox3" checked/>
                            <label for="reservation_checkbox_policy_checked" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a>.</label>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
    <div class="js-success-cont" style="display: none;">
        <article class="popup__content">
            <div class="product-preorder-success">
                <header>Спасибо!</header>
                <article>
                    <div class="product-preorder-success__title">Товар зарезервирован</div>
                    <div class="product-preorder-success__subtitle">Номер резерва</div>
                    <div class="product-preorder-success__number"></div>
                </article>
                <footer>
                    <button class="js-popup-close button button--xxl button--primary button--outline">ОК</button>
                </footer>
            </div>
        </article>
    </div>
</form>
<script>
$(document).ready(function() {
    var presetDataFIO = $("input.fio").data('fio');
    var presetDataPhone = $("input.reservation_phone").data('phone');
    $("input.fio").val(presetDataFIO);
    $("input.reservation_phone").val(presetDataPhone);
    $("input.reservation_phone").mask("+7 (999) 999-99-99", {autoclear: false});
    $("input.fio").click(function(){
        if ($("input.fio").val() == presetDataFIO) {
            $("input.fio").val('');
        }
    });
    $("input.reservation_phone").click(function(){
        if ($("input.reservation_phone").val() == presetDataPhone) {
            $("input.reservation_phone").mask("");
            $("input.reservation_phone").val('');
            $("input.reservation_phone").mask("+7 (999) 999-99-99", {autoclear: false});
            $("input.reservation_phone").val('+7 (___) ___-__-__');
        }
        if ($("input.reservation_phone").val() == "+7 (___) ___-__-__") {
            $(this)[0].selectionStart = 4;
            $(this)[0].selectionEnd = 4;
        }
    });
    $("input.reservation_phone").mouseover(function () {
        $("input.reservation_phone").attr('placeholder', '+7 (___) ___-__-__');
    });
    $("input.reservation_phone").mouseout(function () {
        $("input.reservation_phone").attr('placeholder', '*Телефон');
    });
    $('#one-click-form').on('submit',function(e) {
        e.preventDefault();
        var arr = {
            "PROPS[FIO]":"",
        };
        var cou_err = 0;
        var text_html = "";
        $.each(arr, function(key,value) {
            if ($("[name='"+key+"']").val().trim() == "") {
                value = $("[name='"+key+"']").attr('placeholder');
                cou_err++;
                text_html += "<p>Необходимо заполнить поле "+value+"</p>";
                $("[name='"+key+"']").addClass("red_border");
            } else {
                $("[name='"+key+"']").removeClass("red_border");
            }
        });
        if ($("input.reservation_phone").val().trim() == "") {
            text_html += '<p>Необходимо заполнить поле *Телефон</p>';
            $("input.reservation_phone").addClass("red_border");
            cou_err++;
        } else {
            var inputPhoneValue = $("input.reservation_phone").val().replace(/\D+/g, '');
            if (inputPhoneValue.length - 1 < 10) {
                text_html += "<p>Неверно заполнено поле *Телефон</p>";
                $("input.reservation_phone").addClass("red_border");
                cou_err++;
            } else {
                $("input.reservation_phone").removeClass("red_border");
            }
        }
        if (!($("#reservation_checkbox_policy_checked").prop('checked'))) {
            cou_err++;
            text_html += "<p>Необходимо согласие с политикой конфиденциальности</p>";
        }
        if (parseInt($("[name='DELIVERY_STORE_ID']").val().trim()) == 0) {
            cou_err++;
            text_html += "<p>Выберите магазин</p>";
        }
        $("#after-cart-in-err").html(text_html);
        if (cou_err > 0) {
            return false;
        }
        show_wait($('.js-reserv'));
        $(".js-preorder-submit").hide();
        $(".buttonReservation-loader").show();
        $(".product-preorder__article").addClass("loader-one-click-element");
        $(".product-preorder__aside-info").addClass("loader-one-click-element");
        $(".phone").addClass("loader-one-click-element");
        $(".alert--danger").addClass("loader-one-click-element");
        $('#one-click-form').addClass("loader-one-click-form");
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if (data.status == "ok") {
                    window.location = "/order-success/?orderId="+data.text;
                    return false;
                }
                hide_wait();
                $(".js-preorder-submit").show();
                $(".buttonReservation-loader").hide();
                $(".product-preorder__article").removeClass("loader-one-click-element");
                $(".product-preorder__aside-info").removeClass("loader-one-click-element");
                $(".phone").removeClass("loader-one-click-element");
                $(".alert--danger").removeClass("loader-one-click-element");
                $('#one-click-form').removeClass("loader-one-click-form");
                $("#after-cart-in-err").html(data.text.join("<br>"));
            },
            error: function(jqXHR, exception) {
                hide_wait();
                $(".js-preorder-submit").show();
                $(".buttonReservation-loader").hide();
                $(".product-preorder__article").removeClass("loader-one-click-element");
                $(".product-preorder__aside-info").removeClass("loader-one-click-element");
                $(".phone").removeClass("loader-one-click-element");
                $(".alert--danger").removeClass("loader-one-click-element");
                $('#one-click-form').removeClass("loader-one-click-form");
            },
        });
        return false;
    });
    $('input.fio').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });
    $('input.fio').keydown(function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });
    $('input.fio').change(function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });
});
</script>