$(document).ready(function(){
    var clickedElem;
    var activeAjax = false;
    const arDelIdsJsInt = arDelIdsJs.map(item => parseInt(item));
    var arOnlinePaymentIdsInt = [];
    var lastClickedDeliveryRadio = false;
    var lastClickedDeliveryRadio2 = false;
    if (arOnlinePaymentIds) {
        arOnlinePaymentIdsInt = arOnlinePaymentIds.map(item => parseInt(item));
    }

    //функция для клика на кнопку "Добавить в корзину"
    function basketAddProd(offerId, isChangeSize = false, parentPropElem = false) {
        if (!isChangeSize){
            Popup.hide();
        }
        let data = {
            action: "basketAdd",
            offerId: offerId,
            quantity: 1,
        };
        $.ajax({
            method: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status == "ok") {
                    gtmElem.elem = data.gtmData;
                    gtmElem.actual = true;
                    updateSmallBasket(data.text);
                    activeAjax = false;
                    let basketPrice = parseInt(data.text);
                    reloadProduct(true, offerId);
                    reloadProduct(false, offerId);
                    checkProduct(basketPrice, 'Y');
                    checkProduct(basketPrice, 'N');
                    return; // возможно это не нужно
                }
                let error_text = '<div class="product-preorder-success">'
                    + '<h2>Ошибка</h2>'
                    + '<div class="js-size-popup">'
                    + data.text.join("<br>")
                    + '</div>'
                    + '</div>';
                Popup.show(error_text, {});
            },
            error: function (data) {
                hide_wait();
                activeAjax = false;
            }
        });
    }

    function resetSizeSelector(notLocal) {
        $('select[name=select-size' + notLocal + ']').select2({
            dropdownParent: $('#select-size-container' + notLocal),
            width: 'resolve',
            minimumResultsForSearch: -1
        })
    }

    function resetSizeSelectorHandlers(isLocal){
        let notLocal = '';
        let localBasket = 'Y';
        if (!isLocal) {
            notLocal = '2';
            localBasket = 'N'
        }

        resetSizeSelector(notLocal);

        $('select[name=select-size' + notLocal + ']').change(function () {
            let that = $(this);
            let parentPropElem = that.parents('.js-card');
            basketAddProd(that.val(),true, parentPropElem);
            delProduct(that.parents('.orders__row').find('.orders__remove'), localBasket);
        });

        $('.orders__add-btn' + notLocal).click(function (e) {
            e.preventDefault();
            let parentPropElem = $(this).parents('.js-card');
            let popupContent = $('.js-choose-size').clone(true, true);
            popupContent.find('.js-size-popup').html($('.sizes-' + $(this).val()).html());
            Popup.show(popupContent, {
                className: 'popup--size-tab',
            });
            $(".sizes-input").on("click", function () {
                let that = $(this);
                let offerId = that.data("offer-id");
                basketAddProd(offerId, false, parentPropElem);
            });
        });
    }

    // удаление товара
    function delProduct(el, localBasket) {
        if (activeAjax) {
            return;
        }
        activeAjax = true;
        let data = {
            action: "basketDel",
            offerId: parseInt(el.data("id")),
            needLocalBasketPrice: localBasket,
        };
        let dataFlock = {
            ID: el.data("id").toString(),
            QUANTITY: 1,
        };
        el.closest('.js-card').slideUp("normal", function() {});//визуально скрываем товар
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.status == "ok") {
                    gtmPush('deleteProduct', el.closest('.js-card'));
                    updateSmallBasket(-1);
                    let basketPrice = parseInt(data.text);
                    el.closest('.js-card').slideUp("normal", function() {
                        $(this).remove();
                        if (basketPrice <= prepayment_min_summ) {
                            let basketType = '';
                            if (localBasket !== 'Y') {
                                basketType = 2;
                            }
                            $('.js-delivery' + basketType).each(function (e){
                                let delElem = $(this);
                                let payArr = delElem.attr('data-allowed-payments');
                                let payIntersect = arOnlinePaymentIdsInt.filter(value => payArr.includes(value));
                                if (payIntersect !== []) {
                                    if (delElem.attr('data-old-price') !== undefined) {
                                        delElem.attr('data-price', delElem.attr('data-old-price'));
                                        delElem.siblings('label').children('.cart-delivery__price').html('Стоимость доставки ' + formatPrice(delElem.attr('data-price')));
                                        if(delElem.prop('checked') === true) {
                                            $("#cart__delivery-price" + basketType).html(formatPrice(delElem.attr('data-price')));
                                        }
                                    }
                                }
                            })
                        }
                        reloadProduct(true);
                        reloadProduct(false);
                        checkProduct(basketPrice, localBasket);
                    });
                } else if (data.status == "error") {
                    el.closest('.js-card').slideUp("normal", function() {
                        $(this).remove();
                        let leftBlock = $('.left-cart-block-' + (localBasket === 'Y' ? 'local' : 'not-local'));
                        let rightBlock = $('.right-cart-block-' + (localBasket === 'Y' ? 'local' : 'not-local'));
                        leftBlock.empty();
                        rightBlock.empty();
                        rightBlock.append('<div class="checkout__error-wrapper2"><p class="checkout__error-text">' + data.text + '</p></div>');
                    });
                    activeAjax = false;
                } else {
                    activeAjax = false;
                    console.log(data.text);
                }
            },
            error: function(jqXHR, exception) {
                activeAjax = false;
                ajaxError(jqXHR, exception);
            },
        });
    }

    function gtmPush(type = '', item = false, orderData = false) {
        window.dataLayer = window.dataLayer || [];

        function getGTMelObj(el, plusData) {
            return {
                'name': el.attr('data-prod-name'),  // data-prod-name
                'id': el.attr('data-prod-id'),   // data-prod-id
                'articul': el.attr('data-prod-articul'), // data-prod-articul
                'price': el.attr('data-prod-price'),  // data-prod-price
                'category': el.attr('data-prod-category'), // data-prod-category
                'variant': el.attr('data-prod-variant'), // data-prod-variant
                'brand': el.attr('data-prod-brand'),  //  Бренд товара data-prod-brand
                'top-material': el.attr('data-prod-top-material'),  //  Материал верха data-prod-top-material
                'lining-material': el.attr('data-prod-lining-material'), //Материал подкладки data-prod-lining-material
                'season': el.attr('data-prod-season'),  //  Сезон data-prod-season
                'collection': el.attr('data-prod-collection'),  //  Коллекция data-prod-collection
                'size': (plusData !== undefined && plusData.size !== undefined) ? plusData.size : el.attr('data-prod-size'), //  Выбранный размер
                'quantity': 1, //Количество товаров в корзине
            };
        }

        function getGTMPushData(basket, action, orderData = false) {
            let oPush = {
                'event': 'MTRENDO',
                'eventCategory': 'EEC',
                'eventAction': action,
            };

            let prods = [];

            if (action === 'purchase') {
                oPush['transaction'] = {
                    'transaction_id': orderData.id,
                    'value': orderData.price, // Сумма заказа включая доставку, налоги, примененную скидку, купоны
                    'tax': orderData.tax,
                    'shipping': orderData.delivery, // Сумма доставки
                    'coupon': orderData.coupon, // примененный промокод
                }
            }

            if (action === 'checkout') {
                let deliveryLable = basket.find('.delivery-label');
                if (deliveryLable.length === 1) {
                    oPush['checkout-delivery'] = deliveryLable.children('.cart-delivery__header').text().trim();
                }
                let paymentLable = basket.find('.payment-label');
                if (paymentLable.length === 1) {
                    oPush['checkout-payment'] = paymentLable.children('.cart-delivery__header').text().trim();
                }
            }

            if (action === 'add_to_cart' && orderData.item !== undefined) {
                if (item.hasClass('orders__row--product')) {
                    oPush['order-type'] = 'Корзина местная';
                } else {
                    oPush['order-type'] = 'Корзина неместная';
                }

                let elem = orderData.item;
                oPush['eventLabel'] = elem.attr('data-prod-name');
                prods.push(getGTMelObj(elem, orderData.plusData))
            }

            if (basket !== false) {
                basket.find('.js-card').each(function () {
                    let el = $(this);
                    prods.push(getGTMelObj(el));
                });
            }

            oPush['products'] = prods;

            return oPush;
        }

        if (type === 'checkout' && item === false) {
            let localBasket = $('.full_basket-container--local');
            if (localBasket.length > 0) {
                let oPushLocal = {};
                oPushLocal = getGTMPushData(localBasket, 'checkout');
                oPushLocal['order-type'] = 'Корзина местная'; // Тип корзины
                dataLayer.push(oPushLocal);
            }

            let regionalBasket = $('.full_basket-container--regional');
            if (regionalBasket.length > 0) {
                let oPushRegional = {};
                oPushRegional = getGTMPushData(regionalBasket, 'checkout');
                oPushRegional['order-type'] = 'Корзина не местная'; // Тип корзины
                dataLayer.push(oPushRegional);
            }
        } else if (type === 'deleteProduct' && item !== false) {
            dataLayer.push({
                'event': 'MTRENDO',
                'eventCategory': 'EEC',
                'eventAction': 'remove_from_cart',
                'eventLabel': item.attr('data-prod-name'),  // data-prod-name
                'products': [getGTMelObj(item)]
            });
        } else if (type === 'orderSuccess1' && item === false && orderData !== false) {
            let basket = $('.full_basket-container--local');
            if (basket.length > 0) {
                let oPush = {};
                oPush = getGTMPushData(basket, 'purchase', orderData);
                dataLayer.push(oPush);
            }
        } else if (type === 'orderSuccess2' && item === false && orderData !== false) {
            let basket = $('.full_basket-container--regional');
            if (basket.length > 0) {
                let oPush = {};
                oPush = getGTMPushData(basket, 'purchase', orderData);
                dataLayer.push(oPush);
            }
        } else if (type === 'add_to_cart' && item !== false) {
            let oPush = getGTMPushData(false, 'add_to_cart', {'item':item, 'plusData':orderData});
            dataLayer.push(oPush);
        }
    }

    function findAddedProd() {
        if (gtmElem.actual === true) {
            let elem = $('[data-prod-id=' + gtmElem.elem.prodId + '][data-prod-size=' + gtmElem.elem.size + ']');
            if (elem.length !== 0) {
                gtmElem.actual = false;
                gtmPush('add_to_cart', elem);
            }
        }
    }

    // проверка наличия продуктов на странице
    function checkProduct(basketPrice, checkBasketLocationType) {
        if ($(".orders__row--product").length == 0 && $(".orders__row--product2").length == 0) {
            let data = {
                action: "cart",
                ajax: "Y",
            };
            $.ajax({
                type: "POST",
                url: "/cart/",
                data: data,
                success: function(data) {
                    activeAjax = false;
                    $("#main-basket-block").html(data);
                    resetJsHandlers();
                    findAddedProd();
                },
                error: function(jqXHR, exception) {
                    activeAjax = false;
                    ajaxError(jqXHR, exception);
                },
            });
        } else if (checkBasketLocationType == 'Y') {
            if ($(".orders__row--product").length == 0) {
                $("#full_basket").slideUp("normal", function() {
                    $(this).remove();
                });
                let data = {
                    action: "cart",
                    ajax: "Y",
                };
                $.ajax({
                    type: "POST",
                    url: "/cart/",
                    data: data,
                    success: function(data) {
                        activeAjax = false;
                        $("#main-basket-block").html(data);
                        resetJsHandlers();
                        findAddedProd();
                    },
                    error: function(jqXHR, exception) {
                        activeAjax = false;
                        ajaxError(jqXHR, exception);
                    },
                });
            } else if ($(".checkout__error-wrapper").length != 0) {
                let data = {
                    action: "cart",
                    ajax: "Y",
                };
                $.ajax({
                    type: "POST",
                    url: "/cart/",
                    data: data,
                    success: function(data) {
                        activeAjax = false;
                        $("#main-basket-block").html(data);
                        resetJsHandlers();
                    },
                    error: function(jqXHR, exception) {
                        activeAjax = false;
                        ajaxError(jqXHR, exception);
                    },
                });
            } else {
                activeAjax = false;
                calculatePrice(basketPrice);
            }
        } else {
            if ($(".orders__row--product2").length == 0) {
                $("#full_basket2").slideUp("normal", function() {
                    $(this).remove();
                });
                let data = {
                    action: "cart",
                    ajax: "Y",
                };
                $.ajax({
                    type: "POST",
                    url: "/cart/",
                    data: data,
                    success: function(data) {
                        activeAjax = false;
                        $("#main-basket-block").html(data);
                        resetJsHandlers();
                        findAddedProd();
                     },
                    error: function(jqXHR, exception) {
                        activeAjax = false;
                        ajaxError(jqXHR, exception);
                    },
                });
            } else if ($(".checkout__error-wrapper2").length != 0) {
                let data = {
                    action: "cart",
                    ajax: "Y",
                };
                $.ajax({
                    type: "POST",
                    url: "/cart/",
                    data: data,
                    success: function(data) {
                        activeAjax = false;
                        $("#main-basket-block").html(data);
                        resetJsHandlers();
                    },
                    error: function(jqXHR, exception) {
                        activeAjax = false;
                        ajaxError(jqXHR, exception);
                    },
                });
            } else {
                activeAjax = false;
                calculatePrice(basketPrice);
                resetJsHandlers();
            }
        }
    }

    // переключаем блоки способа оплаты при выборе способа доставки
    function checkPaymentWay(nonLocal, paymentIds) {
        if (typeof(paymentIds) != 'string') {
           paymentIds = String(paymentIds);
        }
        paymentIds = paymentIds.split(',');
        $('.payment__type' + nonLocal).each(function(index) {
            let that = $(this);
            that.find('input').prop('checked', false);
            if(paymentIds.indexOf(that.find('input').val()) === -1){
                that.addClass('payment__type--disabled');
                that.find('input').prop('disabled', true);
            } else{
                that.removeClass('payment__type--disabled');
                that.find('input').prop('disabled', false);
            }
        });
        sortPaymentWay(nonLocal);
    }

    //сортировка способов оплаты
    function sortPaymentWay(nonLocal){
        let paymentSelector = $('.payment__type' + nonLocal)
        let arItems = $.makeArray(paymentSelector);
        arItems.sort(function(a, b) {
            if($(a).find('input').prop('disabled') == $(b).find('input').prop('disabled')){
                return $(a).find('input').data('sort') - $(b).find('input').data('sort')
            }
            return $(a).find('input').prop('disabled') - $(b).find('input').prop('disabled')
        });
        $(arItems).appendTo(paymentSelector.parent());
    }

    // переключаем блоки при выборе ПВЗ в local корзине
    function checkCDEK() {
        let delId = parseInt($(".js-delivery:checked").val());
        if ($.inArray(delId, arDelIdsJsInt) != -1) {
            if (!$(".is-pvz").hasClass('pvz-checked')) {
                $(".is-pvz").prop('checked', false);
                if (lastClickedDeliveryRadio) {
                    lastClickedDeliveryRadio.prop('checked', true);
                }
            }
            return true;
        } else {
            $(".js__cdek-disabled").removeClass("is-hidden");
            $(".is-pvz").removeClass("pvz-checked");
            $(".js__cdek-enabled").addClass("is-hidden");
            lastClickedDeliveryRadio = $(".js-delivery:checked");
            return false;
        }
    }

    function applyCoupon(isLocal = true) {
        let type = isLocal ? '' : '2';
        let cart_coupon = $("#cart__coupon" + type);
        let cart_coupon_error = $("#cart__coupon-error" + type);
        cart_coupon.removeClass("form__error-border");
        cart_coupon_error.html("");
        let coupon = cart_coupon.val().trim();
        if (!coupon) {
            cart_coupon.addClass("form__error-border");
            cart_coupon_error.html("Введите промокод");
            return;
        }
        $("#art__coupon-button" + type).attr("disabled", "disabled");
        let data = {
            action: "coupon",
            coupon: coupon,
            needLocalCoupon: isLocal ? 'Y' : 'N',
        };
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function(data) {
                $("#art__coupon-button" + type).removeAttr("disabled");
                if (data.status == "ok") {
                    var basketPrice = parseInt(data.text);
                    var sum = 0;
                    $(".orders__price-num" + type).each(function() {
                        sum += parseInt($(this).data("price"));
                    });
                    if (basketPrice < sum) {
                        reloadProduct(isLocal);
                    } else {
                        if (basketPrice >= sum) {
                            if (coupon == data.coupon && $(".orders__price" + type).find(".orders__old-price-num" + type).data("price")){
                                $("#cart__coupon" + type).addClass("form__error-border");
                                $("#cart__coupon-error" + type).html("Данный промокод уже применен к корзине");
                            } else {
                                $("#cart__coupon" + type).addClass("form__error-border");
                                $("#cart__coupon-error" + type).html("Промокод не соответствует условиям");
                            }
                        }
                        calculatePrice(basketPrice);
                    }
                    return;
                }
                $("#cart__coupon" + type).addClass("form__error-border");
                $("#cart__coupon-error" + type).html(data.text);
            },
            error: function(jqXHR, exception) {
                $("#art__coupon-button" + type).removeAttr("disabled");
                ajaxError(jqXHR, exception);
            },
        });
    }

    function reloadProduct(isLocal = true, offerId = '') {
        if (isLocal) {
            let data = {
                action: "offers",
            };
            $.ajax({
                type: "POST",
                url: "/cart/",
                data: data,
                success: function (data) {
                    $("#orders__row-container").html(data);
                    calculatePrice();
                    resetSizeSelectorHandlers(isLocal);
                },
                error: function (jqXHR, exception) {
                    ajaxError(jqXHR, exception);
                },
            });
        } else {
            let data = {
                action: "offers2",
            };
            $.ajax({
                type: "POST",
                url: "/cart/",
                data: data,
                success: function (data) {
                    $("#orders__row-container2").html(data);
                    if(offerId != '') {
                        if ($('.orders__remove[data-id=' + offerId + ']').length !== 0) {
                            $('html, body').animate({
                                scrollTop: $('.orders__remove[data-id=' + offerId + ']').offset().top - 250// класс объекта к которому приезжаем
                            }, 1000);
                        } else {
                            $('html, body').animate({
                                scrollTop: 0
                            }, 1000);
                        }
                    }
                    calculatePrice();
                    resetSizeSelectorHandlers(isLocal);
                },
                error: function (jqXHR, exception) {
                    ajaxError(jqXHR, exception);
                },
            });
        }
    }

    // перерассчёт цены
    function calculatePrice(basketPrice, changeDeliveryBasketNum) {
        basketPrice = basketPrice || 0;
        let sum = 0;
        let sum2 = 0;
        let oldSum = 0;
        let oldSum2 = 0;
        $(".orders__price").each(function () {
            sum += parseInt($(this).find(".orders__price-num").data("price"));
            if ($(this).children().length == 2) {
                oldSum += parseInt($(this).find(".orders__old-price-num").data("price"));
            } else {
                oldSum += parseInt($(this).find(".orders__price-num").data("price"));
            }
        });
        $(".orders__price2").each(function () {
            sum2 += parseInt($(this).find(".orders__price-num2").data("price"));
            if ($(this).children().length == 2) {
                oldSum2 += parseInt($(this).find(".orders__old-price-num2").data("price"));
            } else {
                oldSum2 += parseInt($(this).find(".orders__price-num2").data("price"));
            }
        });
        if (basketPrice != 0 && (basketPrice != sum && basketPrice == oldSum || basketPrice != sum2 && basketPrice == oldSum2)) {
            return delDiscount();
        }
        let delPrice = $(".js-delivery:checked").attr("data-price") ? parseInt($(".js-delivery:checked").attr("data-price")) : 0;
        let delPrice2 = $(".js-delivery2:checked").attr("data-price") ? parseInt($(".js-delivery2:checked").attr("data-price")) : 0;
        if (changeDeliveryBasketNum === 1) {
            $("#cart__delivery-price").html(formatPrice(delPrice));
        }
        $("#cart__total-price").html(formatPrice(sum + delPrice));
        if (oldSum == 0 || oldSum == sum) {
            $("#cart__discount-block").addClass("is-hidden");
        } else {
            $("#cart__discount-block").removeClass("is-hidden");
            $("#cart__discount-price").html(formatPrice(oldSum - sum));
        }
        if (changeDeliveryBasketNum === 2) {
            $("#cart__delivery-price2").html(formatPrice(delPrice2));
        }
        $("#cart__total-price2").html(formatPrice(sum2 + delPrice2));
        if (oldSum2 == 0 || oldSum2 == sum2) {
            $("#cart__discount-block2").addClass("is-hidden");
        } else {
            $("#cart__discount-block2").removeClass("is-hidden");
            $("#cart__discount-price2").html(formatPrice(oldSum2 - sum2));
        }
        findAddedProd();
    }

    // удаление скидки
    function delDiscount() {
        $(".orders__old-price-num").each(function() {
            var price = parseInt($(this).data("price"));
            $(this).parents(".orders__price").children(".orders__price-num")
            .data("price", price).html(formatPrice(price));
        });
        $(".orders__old-price-num").fadeOut(300, function() {
            $(this).remove();
        });
        $("#cart__coupon").val("");
        $("#cart__coupon").addClass("form__error-border");
        $("#cart__coupon-error").html("Промокод перестал удовлетворять условиям и был отменён");
        $("#cart__coupon2").val("");
        $("#cart__coupon2").addClass("form__error-border");
        $("#cart__coupon-error2").html("Промокод перестал удовлетворять условиям и был отменён");
        return calculatePrice();
    }

    function replaceFioVal($fioEl) {
        var fioVal = $fioEl.val();
        fioVal = fioVal.replace(/[^а-яА-Яa-zA-Z ]/g, '');
        $fioEl.val(fioVal);
    }

    function replaceEmailVal($emailEl) {
        var emailVal = $emailEl.val();
        emailVal = emailVal.replace(/ /, '');
        $emailEl.val(emailVal);
    }

    // ajax error
    function ajaxError(jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connect, verify Network';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500]';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed';
        } else if (exception === 'timeout') {
            msg = 'Time out error';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted';
        } else {
            msg = 'Uncaught Error: '+jqXHR.responseText;
        }
        console.log(msg);
    }

    //открытие локальной и местной корзины
    function openCart() {
        let iOS = navigator.userAgent.match(/iPhone|iPad|iPod/i);
        let event = "click";

        if(iOS != null) {
            event = "touchstart";
        }
        $(document).on(event, `.opening-cart`, function () {
            $(this).closest('.checkout').find('.checkout__inner').slideToggle('fast');
            checkHeightProductBlock();
        });
        $(window).resize(function () {
            if ($(window).width() > 768) {
                $(`.opening-cart`).closest('.checkout').find('.checkout__inner').css('display', '');
            }
        });
    }

    //подбор высоты блока товара
    function checkHeightProductBlock() {
        $('.flex-product').each(function () {
            if ($(window).width() > 767) {
                $('.flex-product').css('height', 'inherit');
                return false;
            }
            let firstColHeight = $('.flex-product--img', $(this)).height() + $('.flex-product--size', $(this)).height() + $('.flex-product--count', $(this)).height();
            let twostColHeight = $('.flex-product--name', $(this)).height() + $('.flex-product--price', $(this)).height();
            if (firstColHeight > twostColHeight) {
                $(this).css('height', firstColHeight + 15);
            } else {
                $(this).css('height', twostColHeight + 15);
            }
        });
    }

    // форматирование цены
    function formatPrice(num) {
        if (parseInt(num) == 0) {
            return "Бесплатно";
        }
        num = String(num);
        var format = "";
        var o;
        o = num.length % 3;
        if (o) {
            format += num.substr(0, o) + " ";
        }
        if (num.length >= 3) {
            format += num.substr(o).replace(/(\d{3})(?=\d)/g, "$1&nbsp;");
        }
        format += " р.";
        return format;
    }

    function checkActiveCheckbox(bOrder, className) {
        if (!clickedElem.hasClass('opened')) {
            $('#' + bOrder + ' .' + className).each(function () {
                $(this).removeClass('opened');
            })

            let paymentClass = '';

            if (className === 'js-delivery') {
                paymentClass = 'js-payment-local';
            } else if (className === 'js-delivery2') {
                paymentClass = 'js-payment-not-local';
            }

            if (paymentClass !== '') {
                $('#' + bOrder + ' .' + paymentClass).each(function () {
                    $(this).removeClass('opened');
                })
            }

            return true;
        } else {
            return false;
        }
    }

    function hiddenBlock(action, bOrder, blockClass) {
        if (blockClass === 'all') {
            let blocks = $('#' + bOrder + ' .hidden-block');

            blocks.each(function () {
                $(this).slideUp().removeClass('active');
            })
        } else {
            let elem = $('#' + bOrder + ' .' + blockClass + '.hidden-block');

            if (action === 'open') {
                elem.slideDown().addClass('active');

                if (blockClass === 'form__elem--textarea') {
                    elem.prop('disabled', false);
                }

                clickedElem.addClass('opened');
            } else if (action === 'close') {
                elem.slideUp().removeClass('active');

                if (blockClass === 'form__elem--textarea') {
                    elem.prop('disabled', true);
                }
            }
        }
    }

    function saveAddressInCookie(bOrder) {
        let address = {
            location_code: currentLocationCode,
            fio: $(bOrder + ' [name="PROPS[FIO]"]').val(),
            email: $(bOrder + ' [name="PROPS[EMAIL]"]').val(),
            phone: $(bOrder + ' [name="PROPS[PHONE]"]').val(),
            street: $(bOrder + ' [name="PROPS[STREET_USER]"]').val(),
            house: $(bOrder + ' [name="PROPS[HOUSE_USER]"]').val(),
            index: $(bOrder + ' [name="PROPS[POSTALCODE]"]').val(),
            flat: $(bOrder + ' [name="PROPS[FLAT]"]').val(),
            porch: $(bOrder + ' [name="PROPS[PORCH]"]').val(),
            floor: $(bOrder + ' [name="PROPS[FLOOR]"]').val(),
            intercom: $(bOrder + ' [name="PROPS[INTERCOM]"]').val(),
        };

        for (const [key, value] of Object.entries(address)) {
            document.cookie = 'user_' + key + '=' + value + ';domain=' + currentHost + ';path=/;max-age=3600;';
        }
    }

    // рестартает все события
    function resetJsHandlers() {
        resetSizeSelectorHandlers(true);
        // доставка
        $("#cart__delivery-cdek-button").on("click", function() {
            window.isLocalCart = true;
            window.loadPVZMap();
        });
        $(".js-delivery").on("click", function() {
            clickedElem = $(this);

            if (checkCDEK()) {
                window.isLocalCart = true;
                window.loadPVZMap();
            } else {
                if (checkActiveCheckbox('b-order', 'js-delivery')) {
                    hiddenBlock('close', 'b-order', 'all', 'js-delivery');
                    calculatePrice(false, 1);
                    let labels = $('#b-order').find('.delivery-label');
                    labels.each(function () {
                        $(this).removeClass('red-border');
                    });
                    checkPaymentWay('', $(this).data('allowedPayments'));

                    hiddenBlock('open', 'b-order', 'checkout__form', 'js-delivery');
                }
            }
        });
        $(".js-payment-local").on("click", function() {
            clickedElem = $(this);

            if (checkActiveCheckbox('b-order', 'js-payment-local')) {
                hiddenBlock('close', 'b-order', 'checkout__block--contact-info', 'js-payment-local');
                hiddenBlock('close', 'b-order', 'basket-textarea', 'js-payment-local');

                let labels = $('#b-order').find('.payment-label');
                labels.each(function () {
                    $(this).removeClass('red-border');
                });

                hiddenBlock('open', 'b-order', 'checkout__block--contact-info', 'js-payment-local');
                hiddenBlock('open', 'b-order', 'basket-textarea', 'js-payment-local');
            }
        });
        $('.needComment').on('change', function () {
            clickedElem = $(this);
            if ($(this).prop('checked') === true) {
                hiddenBlock('open', 'b-order', 'form__elem--textarea');
            } else {
                hiddenBlock('close', 'b-order', 'form__elem--textarea');
            }
        });
        $('.havePromocode').on('change', function () {
            clickedElem = $(this);
            if ($(this).prop('checked') === true) {
                hiddenBlock('open', 'b-order', 'coupon-container');
            } else {
                hiddenBlock('close', 'b-order', 'coupon-container');
            }
        })
        $('.cart-delivery__input').on('change', function() {
            let that = $(this);
            let type = '';

            let valuePush = that.siblings('label').children('.cart-delivery__header').text().trim();

            if ((that.hasClass('js-delivery') || that.hasClass('js-delivery2')) && !that.hasClass('is-pvz2') && !that.hasClass('is-pvz')) {
                type = 'checkout-delivery';
            } else if (that.hasClass('js-payment-local') || that.hasClass('js-payment-not-local')) {
                type = 'checkout-payment';
                if (typeof (prepayment_min_summ) !== "undefined") {
                    let basketType = '';
                    if (that.hasClass('js-payment-not-local')) {
                        basketType = '2';
                    }
                    if (that.attr('data-prepayment') === 'Y') {
                        let delElem = $('input.js-delivery' + basketType + ':checked');
                        let basketPrice = 0;
                        $(".orders__price" + basketType).each(function () {
                            basketPrice += parseInt($(this).find(".orders__price-num").attr("data-price"));
                        });
                        if (basketPrice >= prepayment_min_summ) {
                            delElem.siblings('label').children('.cart-delivery__price').text('Бесплатно');
                            delElem.attr('data-old-price', delElem.attr('data-price'));
                            delElem.attr('data-price', 0);
                            calculatePrice(basketPrice, 1);
                        }
                    } else {
                        let delElems = $('input.js-delivery' + basketType);
                        delElems.each(function () {
                            let delElem = $(this);
                            if (delElem.attr('data-old-price') !== undefined) {
                                delElem.attr('data-price', delElem.attr('data-old-price'));
                            }
                            delElem.siblings('label').children('.cart-delivery__price').html('Стоимость доставки ' + formatPrice(delElem.attr('data-price')));
                        })
                        calculatePrice(0, 1);
                    }
                }
            }
        });

        setTimeout(
            function () {
                $('.is-pvz').removeAttr('disabled');
            },
            200
        );

        // инициируем событие на удаление товара
        $(document).on('click', '.js-card-remove', function() {
            delProduct($(this), 'Y');
        });
        // применение промокода
        $(document).on('click', '#cart__coupon-button', function() {
            applyCoupon(true);
        });
        // submit
        $(document).on("submit", "#b-order", function(e) {
            e.preventDefault();
            let bOrder = '#b-order';
            let errorCount = 0;
            let errorText = '';

            if (typeof checkStoreSellerCookie === "function" && !checkStoreSellerCookie()) {
                return false;
            }

            let delId = parseInt($(".js-delivery:checked").val());
            if ($.inArray(delId, arDelIdsJsInt) == -1) {
                if ($(bOrder + ' .err-PROPS\\[STREET_USER\\]').html() === 'Выберите адрес из выпадающего списка') {
                    errorCount++;
                }
                if ($(bOrder + ' .err-PROPS\\[HOUSE_USER\\]').html() === 'Выберите дом из выпадающего списка') {
                    errorCount++;
                }
            }
            $("#b-order input, #b-order select").each(function() {
                let that = $(this);
                if (that.hasClass("js-required")) {
                    let parent = that.parent(".form__field");
                    if ($.inArray(delId, arDelIdsJsInt) != -1 && (parent.hasClass("js__cdek-enabled") || !parent.hasClass("js__cdek-disabled")) ||
                        $.inArray(delId, arDelIdsJsInt) == -1 && (!parent.hasClass("js__cdek-enabled") || parent.hasClass("js__cdek-disabled"))) {
                        let val = that.val().trim();
                        let flag = false;
                        if (val) {
                            if (that.attr("name") === "PROPS[PHONE]") {
                                val = val.replace(/\D+/g, '');
                                if (val.length == 11) {
                                    flag = true;
                                }
                            } else if (that.attr("name") === "PROPS[EMAIL]") {
                                let mask = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
                                if (mask.test(val)) {
                                    flag = true;
                                }
                            } else {
                                flag = true;
                            }
                            if (!flag) {
                                that.siblings(".err-order").html(that.attr("placeholder").replace('*', '') + ' заполнено некорректно').addClass('actual');
                            }
                        } else {
                            that.siblings(".err-order").html('Необходимо заполнить поле ' + that.attr("placeholder").replace('*', '')).addClass('actual');
                        }
                        if (!flag) {
                            that.addClass("form__error-border");
                            errorCount++
                        } else if (that.attr("name") !== "PROPS[HOUSE_USER]" && that.attr("name") !== "PROPS[STREET_USER]") {
                            that.removeClass("form__error-border");
                            that.siblings('.err-order').html('').removeClass('actual');
                        }
                    }
                }
            });
            if (!($("#cart__order-policy").prop('checked'))) {
                $(bOrder + ' .err-policy').html('Требуется согласие').addClass('actual');
                $("#cart__order-policy").siblings('label').addClass("form__error-border");
                errorCount++;
            } else {
                $("#cart__order-policy").siblings('label').removeClass("form__error-border");
            }
            if ($(".js-payment-local:checked").length == 0) {
                $(bOrder + ' .err-payment').html('Выберите способ оплаты').addClass('actual');
                let labels = $('#b-order').find('.payment-label');
                labels.each(function() {
                    $(this).addClass('red-border');
                });
                errorCount++;
            } else {
                $(bOrder + ' .err-payment').html('').removeClass('actual');
            }
            if ($(".js-delivery:checked").length == 0) {
                $(bOrder + ' .err-delivery').html('Выберите способ доставки').addClass('actual');
                let labels = $('#b-order').find('.delivery-label');
                labels.each(function() {
                    $(this).addClass('red-border');
                });
                errorCount++;
            } else {
                $(bOrder + ' .err-delivery').html('').removeClass('actual');
            }
            $("#form__errors-block").html(errorText);
            if (errorCount > 0) {
                let userScrollTop = $(window).scrollTop();
                let userScrollBottom = userScrollTop + $(window).innerHeight();
                let errElem = $(bOrder + ' .err-order.actual:first');
                let errorScrollTop = errElem.offset().top - 10;
                let errorScrollBottom = errorScrollTop + errElem.outerHeight() + 10;
                if (errElem.is('div')) {
                    errorScrollTop = errorScrollTop - 50;
                    errorScrollBottom = errorScrollBottom + 50;
                }
                if (userScrollTop > errorScrollTop) {
                    if ($(window).width() <= 767) {
                        errorScrollTop = errorScrollTop - 60;
                    }
                    $('html, body').animate({
                        scrollTop: errorScrollTop
                    }, 1000);
                } else if (userScrollBottom < errorScrollBottom) {
                    $('html, body').animate({
                        scrollTop: errorScrollBottom - $(window).innerHeight() + 60
                    }, 1000);
                }
                return false;
            }
            $("#cart__order-button").attr("disabled", "");
            saveAddressInCookie(bOrder);
            $.ajax({
                type: "POST",
                url: "/cart/",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.status == "ok") {
                        let paymentType = $.inArray(parseInt($('.js-payment-local:checked').val()), arOnlinePaymentIdsInt) == -1 ? 'default' : 'prepayment_s1';
                        let items = [];
                        for (var key in data.info) {
                            if (data.info[key].IS_LOCAL === 'Y') {
                                items.push({"id": key, "qnt": 1, "price": data.info[key].BASKET_PRICE})
                            }
                        }
                        // RetailRocket
                        (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                            try {
                                rrApi.order({
                                    "transaction": data.text,
                                    "items": items,
                                });
                            } catch(e) {}
                        });
                        // подписка на рассылки RR
                        // if ($('#one_click_checkbox_subscribe_email_checked1').prop("checked")) {
                        //     let email = $('#b-order[name=\'PROPS[EMAIL]\']').val();
                        //     (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                        //         rrApi.setEmail(
                        //             email,
                        //             {
                        //                 "stockId": userShowcase
                        //             }
                        //         );
                        //     });
                        // }

                        gtmPush('orderSuccess1', false, data.gtmData);
                        window.location = '/order-success/?orderId=' + data.text + '&orderType=' + paymentType;
                        return false;
                    }
                    for(key in data.text){
                        $(bOrder + ' .err-PROPS\\[' + key + '\\]').html(data.text[key]).addClass('actual');
                    }
                    if($(bOrder + ' .err-PROPS\\[PHONE\\]').html() === 'Указанный номер телефона зарегистрирован на другого клиента'){
                        Popup.show('<div style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">Указанный номер телефона зарегистрирован на другого клиента. Баллы за покупку будут начислены на бонусный счет, привязанный к данному номеру телефона.</article><br>' +
                            '<button class="js-popup-close button button--xxl button--primary button--outline change-phone-numer">Изменить номер</button>' +
                            '<button class="js-popup-close button button--xxl button--outline skip-check-phone">Продолжить</button>' +
                            '</div>');
                    }
                    $("#cart__order-button").removeAttr("disabled");
                },
                error: function(jqXHR, exception) {
                    $("#cart__order-button").removeAttr("disabled");
                    ajaxError(jqXHR, exception);
                },
            });
            return false;
        });
        $(document).on('click', '.change-phone-numer', function() {
            $('#b-order').find('.js-phone').focus();
            $('html, body').animate({
                scrollTop:$('#b-order').find('.js-phone').offset().top - 250 // класс объекта к которому приезжаем
            }, 1000);
        });
        $(document).on('click', '.skip-check-phone', function() {
            $('.checkout__form').append('<input type="hidden" name="PROPS[SKIP_CHECK_PHONE]" value="true">');
            $('#b-order').submit();
        });
        // form
        // phone
        phoneMaskCreate($('.js-phone'));
        // email
        $('.js-email').keydown(function() {
            replaceEmailVal($(this))
        }).keyup(function() {
            replaceEmailVal($(this));
        }).change(function() {
            replaceEmailVal($(this))
        });
        // fio
        $('.js-fio').keydown(function() {
            replaceFioVal($(this))
        }).keyup(function() {
            replaceFioVal($(this));
        }).change(function() {
            replaceFioVal($(this))
        });
        //dadata
        if (dadata_status) {
            // улица
            $("#street_user").suggestions({
                token: token,
                type: type,
                hint: false,
                bounds: "street",
                // ограничиваем поиск
                constraints:
                    {
                        label: false,
                        locations: {
                            region: region,
                            city: city,
                        },
                        deletable: false,
                    },
                onSelect: (suggestion) => {
                    $("p.dadata-street-select-nothing").remove();
                    $("#street_user").removeClass("form__error-border");
                    $('#b-order .err-PROPS\\[STREET_USER\\]').html('').removeClass('actual');
                    $("#postal_code").val(suggestion.data.postal_code);
                    arDadataProps.forEach(function(item, i, arr) {
                        $("#" + item).val(suggestion.data[item]);
                    });
                },
                onSelectNothing: () => {
                    $('#b-order .err-PROPS\\[STREET_USER\\]').html('Выберите адрес из выпадающего списка').addClass('actual');
                    $("#street_user").addClass('form__error-border').html();
                    $("#postal_code").val("");
                    arDadataProps.forEach(function(item, i, arr) {
                        $("#" + item).val("");
                    });
                }
            });
            // дом
            $("#house_user").suggestions({
                token: token,
                type: type,
                hint: false,
                bounds: "house",
                // ограничиваем поиск
                constraints: $("#street_user"),
                onSelect: (suggestion) => {
                    console.log(suggestion);
                    $("p.dadata-house-select-nothing").remove();
                    $("#house_user").removeClass("form__error-border");
                    $('#b-order .err-PROPS\\[HOUSE_USER\\]').html('').removeClass('actual');
                    $("#postal_code").val(suggestion.data.postal_code);
                    $('div.js-dadata-street').find('.dadata-street-select-nothing').remove();
                    $("#street_user").removeClass("js-required");
                    $("#street_user").removeClass('form__error-border');
                    arDadataProps.forEach(function(item, i, arr) {
                        $("#" + item).val(suggestion.data[item]);
                    });
                },
                onSelectNothing: () => {
                    $('#b-order .err-PROPS\\[HOUSE_USER\\]').html('Выберите дом из выпадающего списка').addClass('actual');
                    $("#house_user").addClass('form__error-border').html();
                    $("#postal_code").val("");
                    if (!($("#street_user").val())) {
                        $("#street_user").addClass("js-required");
                    }
                    arDadataProps.forEach(function(item, i, arr) {
                        $("#" + item).val("");
                    });
                }
            });
        }
    }

    // Закоменчена проверка первично-выбранных блоков, т.к. сейчас вообще нет первично-выбранных блоков
    // checkCDEK();
    // checkCDEK2();
    sortPaymentWay('');
    resetJsHandlers();
    //клик на деактивированный способ оплаты
    $(document).on('click', '.payment__type--disabled', function() {
        Popup.show('<div style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">' + paymentWayErrorText + '</article></div>');
    });
    openCart();
    $('.cart-city-input').click(function() {
        document.cookie = 'user_fio=' + $('#b-order').find($('[name = "PROPS[FIO]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[FIO]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
        document.cookie = 'user_email=' + $('#b-order').find($('[name = "PROPS[EMAIL]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[EMAIL]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
        document.cookie = 'user_phone=' + $('#b-order').find($('[name = "PROPS[PHONE]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[PHONE]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
    });
    let resizeDelayTimeout;
    $(window).resize(function () {
        if ($(window).width() > 767) {
            $('.flex-product').css('height', 'inherit');
            return false;
        }

        if (resizeDelayTimeout) {
            clearTimeout(resizeDelayTimeout);
        }
        resizeDelayTimeout = setTimeout( function (){
            checkHeightProductBlock()
        }, 200 );
    });
});
