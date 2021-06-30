var clickedElem;
$(document).ready(function(){
    var activeAjax = false;
    const arPVZDeliveryIds = arDelIdsJs.map(item => parseInt(item));
    var arOnlinePaymentIdsInt = [];
    var lastClickedDeliveryRadio = false;
    if (arOnlinePaymentIds) {
        arOnlinePaymentIdsInt = arOnlinePaymentIds.map(item => parseInt(item));
    }

    function basketAddProd(offerId, quantity) {
        let data = {
            action: "basketAdd",
            offerId: offerId,
            quantity: quantity,
        };
        let currentBasketPrice = 0;
        $(".orders__price").each(function () {
            currentBasketPrice += parseInt($(this).find(".orders__price-num").attr("data-price"));
        });
        $.ajax({
            method: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function (data) {
                let basketPrice = null;
                if (data.status == "ok") {
                    activeAjax = false;
                    // Получаем итоговую сумму корзины
                    basketPrice = parseInt(data['text']);
                    // Если сумма изменилась до такой сетпени, что доставка стала бесплатной,
                    // то перезагружаем весь блок корзины
                    console.log(basketPrice);
                    console.log(currentBasketPrice);
                    console.log(freeDeliveryMinSum);
                    if (
                        currentBasketPrice > freeDeliveryMinSum && basketPrice < freeDeliveryMinSum
                        || currentBasketPrice < freeDeliveryMinSum && basketPrice > freeDeliveryMinSum
                    ) {
                        reloadBasket();
                        return;
                    }
                } else {
                    let errorText = '<div class="product-preorder-success">'
                        + '<h2>Ошибка</h2>'
                        + '<div class="js-size-popup text-danger">'
                        + data.text.join("<br>")
                        + '</div>'
                        + '</div>';
                    Popup.show(errorText, {});
                }
                reloadProducts();
                if (basketPrice === null) {
                    basketPrice = 0;
                    $(".orders__price").each(function () {
                        basketPrice += parseInt($(this).find(".orders__price-num").attr("data-price"));
                    });
                }
                checkProducts(basketPrice);
            },
            error: function (data) {
                hide_wait();
                activeAjax = false;
            }
        });
    }

    // удаление товара
    function delProduct(el, quantity) {
        if (activeAjax) {
            return;
        }
        activeAjax = true;
        let data = {
            action: "basketDel",
            offerId: parseInt(el.data("id")),
            quantity: quantity,
        };
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function(data) {
                if (data['status'] == "ok") {
                    let currentBasketPrice = 0;
                    $(".orders__price").each(function () {
                        currentBasketPrice += parseInt($(this).find(".orders__price-num").attr("data-price"));
                    });
                    if (data['info'] < 1) {
                        updateSmallBasket(-1);
                        let basketPrice = parseInt(data['text']);
                        // Если сумма изменилась до такой сетпени, что доставка стала платной,
                        // то перезагружаем весь блок корзины
                        if (
                            currentBasketPrice > freeDeliveryMinSum && basketPrice < freeDeliveryMinSum
                            || currentBasketPrice < freeDeliveryMinSum && basketPrice > freeDeliveryMinSum
                        ) {
                            reloadBasket();
                        } else {
                            el.closest('.js-card').slideUp("normal", function () {
                                $(this).remove();
                                reloadProducts();
                                checkProducts(basketPrice);
                            });
                        }
                    } else {
                        let basketPrice = parseInt(data['text']);
                        // Если сумма изменилась до такой сетпени, что доставка стала платной,
                        // то перезагружаем весь блок корзины
                        if (
                            currentBasketPrice > freeDeliveryMinSum && basketPrice < freeDeliveryMinSum
                            || currentBasketPrice < freeDeliveryMinSum && basketPrice > freeDeliveryMinSum
                        ) {
                            reloadBasket();
                        } else {
                            let quantityNum = $(this).find(".quantity-num");
                            quantityNum.val(data['info']);
                            reloadProducts();
                            checkProducts(basketPrice);
                        }
                    }
                } else if (data['status'] == "error") {
                    el.closest('.js-card').slideUp("normal", function() {
                        // $(this).remove();
                        let leftBlock = $('.left-cart-block-local');
                        let rightBlock = $('.right-cart-block-local');
                        leftBlock.empty();
                        rightBlock.empty();
                        rightBlock.append('<div class="checkout__error-wrapper"><p class="checkout__error-text text-danger">' + data['text'] + '</p></div>');
                    });
                    reloadBasket();
                    activeAjax = false;
                } else {
                    activeAjax = false;
                    console.log(data['text']);
                }
            },
            error: function(jqXHR, exception) {
                activeAjax = false;
                ajaxError(jqXHR, exception);
            },
        });
    }

    // обновление блока корзины, в случае отсутствия позиций или наличия ошибок в блоке ошибок
    function checkProducts(basketPrice) {
        if ($(".orders__row--product").length == 0) {
            reloadBasket();
        } else {
            if ($(".checkout__error-wrapper").length != 0) {
                reloadBasket();
            } else {
                // подсчет суммы позиций
                activeAjax = false;
                calculatePrice(basketPrice);
            }
        }
    }

    function reloadBasket() {
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
    }

    // переключаем блоки способа оплаты при выборе способа доставки
    function checkPaymentWay(paymentIds) {
        if (typeof(paymentIds) != 'string') {
           paymentIds = String(paymentIds);
        }
        paymentIds = paymentIds.split(',');
        $('.payment__type').each(function(index) {
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
        sortPaymentWay();
    }

    //сортировка способов оплаты
    function sortPaymentWay() {
        let paymentSelector = $('.payment__type')
        let arItems = $.makeArray(paymentSelector);
        arItems.sort(function(a, b) {
            if($(a).find('input').prop('disabled') == $(b).find('input').prop('disabled')){
                return $(a).find('input').data('sort') - $(b).find('input').data('sort')
            }
            return $(a).find('input').prop('disabled') - $(b).find('input').prop('disabled')
        });
        $(arItems).appendTo(paymentSelector.parent());
    }

    // переключаем блоки при выборе ПВЗ
    function checkPVZ() {
        let delId = parseInt($(".js-delivery:checked").val());
        if ($.inArray(delId, arPVZDeliveryIds) != -1) {
            if (!$(".is-pvz").hasClass('pvz-checked')) {
                $(".is-pvz").prop('checked', false);
                if (lastClickedDeliveryRadio) {
                    lastClickedDeliveryRadio.prop('checked', true);
                }
            }
            return true;
        } else {
            if (delId === deliveryMoscowSelfId) {
                $(".js__pvz-disabled").addClass("is-hidden");
                $(".js__pvz-enabled").addClass("is-hidden");
            } else {
                $(".js__pvz-disabled").removeClass("is-hidden");
                $(".js__pvz-enabled").addClass("is-hidden");
            }
            $(".is-pvz").removeClass("pvz-checked");
            lastClickedDeliveryRadio = $(".js-delivery:checked");
            return false;
        }
    }

    function applyCoupon() {
        let cart_coupon = $("#cart__coupon");
        let cart_coupon_error = $("#cart__coupon-error");
        cart_coupon.removeClass("form__error-border");
        cart_coupon_error.html("");
        let coupon = cart_coupon.val().trim();
        if (!coupon) {
            cart_coupon.addClass("form__error-border");
            cart_coupon_error.html("Введите промокод");
            return;
        }
        $("#art__coupon-button").attr("disabled", "disabled");
        let currentBasketPrice = 0;
        $(".orders__price").each(function () {
            currentBasketPrice += parseInt($(this).find(".orders__price-num").attr("data-price"));
        });
        let data = {
            action: "coupon",
            coupon: coupon,
        };
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function(data) {
                $("#art__coupon-button").removeAttr("disabled");
                if (data.status == "ok") {
                    let basketPrice = parseInt(data.text);
                    // Если сумма изменилась до такой сетпени, что доставка стала платной,
                    // то перезагружаем весь блок корзины
                    if (
                        currentBasketPrice > freeDeliveryMinSum && basketPrice < freeDeliveryMinSum
                        || currentBasketPrice < freeDeliveryMinSum && basketPrice > freeDeliveryMinSum
                    ) {
                        reloadBasket();
                        return;
                    }
                    let sum = 0;
                    $(".orders__price-num").each(function() {
                        sum += parseInt($(this).data("price"));
                    });
                    if (basketPrice < sum) {
                        reloadProducts();
                    } else {
                        if (basketPrice >= sum) {
                            if (coupon == data.coupon && $(".orders__price").find(".orders__old-price-num").data("price")){
                                $("#cart__coupon").addClass("form__error-border");
                                $("#cart__coupon-error").html("Данный промокод уже применен к корзине");
                            } else {
                                $("#cart__coupon").addClass("form__error-border");
                                $("#cart__coupon-error").html("Промокод не соответствует условиям");
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

    function reloadProducts() {
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
                resetCardJsHandlers();
            },
            error: function (jqXHR, exception) {
                ajaxError(jqXHR, exception);
            },
        });
    }

    // перерассчёт цены
    function calculatePrice(basketPrice, changeDeliveryBasketNum) {
        basketPrice = basketPrice || 0;
        let sum = 0;
        let oldSum = 0;
        $(".orders__price").each(function () {
            sum += parseInt($(this).find(".orders__price-num").data("price"));
            if ($(this).children().length == 2) {
                oldSum += parseInt($(this).find(".orders__old-price-num").data("price"));
            } else {
                oldSum += parseInt($(this).find(".orders__price-num").data("price"));
            }
        });
        if (basketPrice != 0 && (basketPrice != sum && basketPrice == oldSum)) {
            return delDiscount();
        }
        let delPrice = $(".js-delivery:checked").attr("data-price") ? parseInt($(".js-delivery:checked").attr("data-price")) : 0;
        if (changeDeliveryBasketNum === 1) {
            $("#cart__delivery-price").html(basketPrice >= freeDeliveryMinSum ? formatPrice(0) : formatPrice(delPrice));
        }
        $("#cart__total-price").html(formatPrice(sum + delPrice));
        if (oldSum == 0 || oldSum == sum) {
            $("#cart__discount-block").addClass("is-hidden");
        } else {
            $("#cart__discount-block").removeClass("is-hidden");
            $("#cart__discount-price").html(formatPrice(oldSum - sum));
        }
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

    function resetCardJsHandlers() {
        // инициируем событие на удаление товара
        $(document).on('click', '.js-card-remove', function() {
            delProduct($(this), parseInt($(this).parent().data("qty")));
        });
        // Увеличение количества для добавления в корзину
        $(".quantity-arrow-minus").on('click', function (event) {
            event.preventDefault();
            delProduct($(this).closest('.js-card'), 1);
        });
        $(".quantity-arrow-plus").on('click', function (event) {
            event.preventDefault();
            basketAddProd($(this).closest('.js-card').data('id'), 1);
        });
    }

    // рестартает все события
    function resetJsHandlers() {
        // доставка
        $("#cart__delivery-cdek-button").on("click", function() {
            window.loadPVZMap();
        });
        $(".js-delivery").on("click", function() {
            clickedElem = $(this);

            if (checkPVZ()) {
                $('body').css('overflow', 'hidden');
                window.loadPVZMap();
            } else {
                if (checkIfCheckboxIsActive('js-delivery')) {
                    hiddenBlock('close','all');
                    calculatePrice(false, 1);
                    let labels = $('#b-order').find('.delivery-label');
                    labels.each(function () {
                        $(this).removeClass('red-border');
                    });
                    checkPaymentWay($(this).data('allowedPayments'));

                    hiddenBlock('open','checkout__form');
                }
            }
        });
        $(".js-payment-local").on("click", function() {
            clickedElem = $(this);

            if (checkIfCheckboxIsActive('js-payment-local')) {
                hiddenBlock('close', 'checkout__block--contact-info');
                hiddenBlock('close', 'basket-textarea');

                let labels = $('#b-order').find('.payment-label');
                labels.each(function () {
                    $(this).removeClass('red-border');
                });

                hiddenBlock('open', 'checkout__block--contact-info');
                hiddenBlock('open', 'basket-textarea');
            }
        });
        $('.needComment').on('change', function () {
            clickedElem = $(this);
            if ($(this).prop('checked') === true) {
                hiddenBlock('open', 'form__elem--textarea');
            } else {
                hiddenBlock('close', 'form__elem--textarea');
            }
        });
        $('.havePromocode').on('change', function () {
            clickedElem = $(this);
            if ($(this).prop('checked') === true) {
                hiddenBlock('open', 'coupon-container');
            } else {
                hiddenBlock('close', 'coupon-container');
            }
        });

        setTimeout(function () {
                $('.is-pvz').removeAttr('disabled');
        },200);

        // применение промокода
        $(document).on('click', '#cart__coupon-button', function() {
            applyCoupon();
        });
        // submit
        $(document).on("submit", "#b-order", function(e) {
            e.preventDefault();
            let bOrder = '#b-order';
            let errorCount = 0;
            let errorText = '';

            let delId = parseInt($(".js-delivery:checked").val());
            if ($.inArray(delId, arPVZDeliveryIds) == -1) {
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
                    if (
                        ( delId === deliveryMoscowSelfId && !parent.hasClass("js__pvz-enabled") && !parent.hasClass("js__pvz-disabled") ) ||
                        ( delId !== deliveryMoscowSelfId && $.inArray(delId, arPVZDeliveryIds) != -1 && (parent.hasClass("js__pvz-enabled") || !parent.hasClass("js__pvz-disabled")) ) ||
                        ( delId !== deliveryMoscowSelfId && $.inArray(delId, arPVZDeliveryIds) == -1 && (!parent.hasClass("js__pvz-enabled") || parent.hasClass("js__pvz-disabled")) )
                    ) {
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
            // if (!($("#cart__order-policy").prop('checked'))) {
            //     $(bOrder + ' .err-policy').html('Требуется согласие').addClass('actual');
            //     $("#cart__order-policy").siblings('label').addClass("form__error-border");
            //     errorCount++;
            // } else {
            //     $("#cart__order-policy").siblings('label').removeClass("form__error-border");
            // }
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
                        window.location = '/order-success/?orderId=' + data.text + '&orderType=' + paymentType;
                        return false;
                    }
                    for(let key in data.text){
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
                    console.log(jqXHR);
                    console.log(exception);
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
        resetCardJsHandlers();

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
                },
                onSelectNothing: () => {
                    $('#b-order .err-PROPS\\[STREET_USER\\]').html('Выберите адрес из выпадающего списка').addClass('actual');
                    $("#street_user").addClass('form__error-border').html();
                    $("#postal_code").val("");
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
                },
                onSelectNothing: () => {
                    $('#b-order .err-PROPS\\[HOUSE_USER\\]').html('Выберите дом из выпадающего списка').addClass('actual');
                    $("#house_user").addClass('form__error-border').html();
                    $("#postal_code").val("");
                    if (!($("#street_user").val())) {
                        $("#street_user").addClass("js-required");
                    }
                }
            });
        }
    }

    // Закоменчена проверка первично-выбранных блоков, т.к. сейчас вообще нет первично-выбранных блоков
    // checkPVZ();
    sortPaymentWay();
    resetJsHandlers();
    //клик на деактивированный способ оплаты
    $(document).on('click', '.payment__type--disabled', function() {
        Popup.show('<div class="text-danger" style="text-align: center; padding: 0px 40px;"><article style="font-size: 1.4em;">' + paymentWayErrorText + '</article></div>');
    });
    $('.cart-city-input').click(function() {
        document.cookie = 'user_fio=' + $('#b-order').find($('[name = "PROPS[FIO]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[FIO]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
        document.cookie = 'user_email=' + $('#b-order').find($('[name = "PROPS[EMAIL]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[EMAIL]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
        document.cookie = 'user_phone=' + $('#b-order').find($('[name = "PROPS[PHONE]"]')).val() + '~' + $('#b-order2').find($('[name = "PROPS[PHONE]"]')).val() + ';domain=' + currentHost + ';path=/;max-age=3600;';
    });

});

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

// проверяет активен ли блок выбора доставки или оплаты
function checkIfCheckboxIsActive(className) {
    let bOrder = 'b-order';
    if (!clickedElem.hasClass('opened')) {
        $('#' + bOrder + ' .' + className).each(function () {
            $(this).removeClass('opened');
        })

        let paymentClass = '';

        if (className === 'js-delivery') {
            paymentClass = 'js-payment-local';
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

function hiddenBlock(action, blockClass) {
    let bOrder = 'b-order';
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
