$(function () {
    inBasket = inBasket || [];

    //попап окно размеров, если не выбрали
    function respercEvent__pushSize(valueBtn) {
        let popupContent = $('.js-choose-size').clone(true, true);
        popupContent.find('.js-size-popup').html(valueBtn);
        Popup.show(popupContent, {
            className: 'popup--size-tab',
        });
        $(".delivery-sizes-input").on("click", function () {
            let offerId = $(this).data("offer-id");
            let isLocal = $(this).data("is-local");
            if ($(this).text() !== '' && $(this).text() !== undefined) {
                $('.product-main-div').attr('data-prod-size', $(this).text());
            }
            delPopupClickHandler(offerId, isLocal);
        });
        $(".reservation-sizes-input").on("click", function () {
            let offerId = $(this).data("offer-id");
            $('.product-main-div').attr('data-prod-size', $(this).text());
            resPopupClickHandler(offerId);
        });
        $(".popup__content .preorder_sizes_input").on("click", function () {
            let offerId = $(this).data("offer-id");
            $('.product-main-div').attr('data-prod-size', $(this).text());
            preorderPopupClickHandler(offerId);
        });
    }

    //обработка клика в поп ап окне доставки
    function delPopupClickHandler(offerId, isLocal = 'Y') {
        Popup.hide(true);
        if ($("#del-popup-type").val() == "basket") {
            basketHandler(offerId, isLocal);
        } else {
            oneClickHandler(offerId, isLocal);
        }
        $("#del-popup-type").val("");
    }

    //обработка клика в поп ап окне резерва
    function resPopupClickHandler(offerId) {
        Popup.hide(true);
        reserveHandler(offerId)
    }

    //обработка клика в поп ап окне резерва
    function preorderPopupClickHandler(offerId) {
        Popup.hide(true);
        preorderHandler(offerId)
    }

    //проверка размера для кнопки "Купить в 1 клик"
    $('#one-click-btn').click(function (e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            $("#del-popup-type").val("1click");
            respercEvent__pushSize($('.delivery-sizes').html());
            return;
        }
        oneClickHandler();
    });

    //функция для клика на кнопку "Купить в 1 клик"
    function oneClickHandler(offerId, isLocal) {
        offerId = offerId || $('#one-click-btn').data('offer-id');
        isLocal = isLocal || $('#one-click-btn').data('is-local');
        Popup.show($('#one-click-form').clone(true, true), {
            title: 'Быстрый заказ',
            onShow: (function (_this) {
                return function (popup) {
                    onOpenModalFastOrder(offerId, isLocal);
                    return CountInput.init();
                };
            })(this)
        });
        phoneMaskCreate($('.popup').find($('.one_click_phone')));
        try { rrApi.addToBasket(offerId,{'stockId': userShowcase}) } catch(e) {}
    }

    //проверка размера для кнопки "Добавить в корзину"
    $('#buy-btn').click(function (e) {
        e.preventDefault();
        if (!$(this).data('offer-id') || !$(this).data('is-local')) {
            $("#del-popup-type").val("basket");
            respercEvent__pushSize($('.delivery-sizes').html());
            return;
        }
        basketHandler();
    });

    //проверка размера для кнопки "Заказать"
    $('#preorder-btn').click(function (e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            respercEvent__pushSize($('.base-sizes').html());
            return;
        }
        preorderHandler();
    });

    $('#seller_buy-btn, #seller_btn-reserv').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: '/local/ajax/find_buyer.php?SHOW_POPUP=Y',
            success: function (data) {
                Popup.show(data);
            }
        });
    });

    //проверка размера для кнопки "Забрать в магазине"
    $('#reserved-btn').click(function (e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            respercEvent__pushSize($('.reservation-sizes').html());
            return;
        }
        if (window.offerId === undefined) {
            window.offerId = $(this).data('offer-id');
        }
        reserveHandler(window.offerId);
    });

    //обработка выбора размера
    $('.js-offer').on("change", function (e) {
        //получаем ID выбранного размера
        let jsOfferElem = $(".js-offer:checked");
        let offerId = jsOfferElem.val();
        let isLocal = jsOfferElem.data('is-local');
        window.offerId = offerId;
        //добавляем значение выбранного размера для GTM
        if (jsOfferElem.siblings('label').text() !== '' && jsOfferElem.siblings('label').text() !== undefined) {
            $('.product-main-div').attr('data-prod-size', $(".js-offer:checked").siblings('label').text());
        }
        //проверяем доступен ли он на доставку
        let delFlag = $("#del-offer-" + offerId).length;
        //проверяем доступен ли он на резерв
        let resFlag = $("#res-offer-" + offerId).length;
        //обработка доступности доставки
        if (delFlag) {
            $("#js-toggle-delivery-ok").removeClass("js-button-hide");
            $("#js-toggle-delivery-error").addClass("js-button-hide");
            $("#one-click-btn").data("offer-id", offerId);
            $("#buy-btn").data("offer-id", offerId);
            $("#one-click-btn").data("is-local", isLocal);
            $("#buy-btn").data("is-local", isLocal);
            if (inBasket[offerId]) {
                $("#buy-btn").val("В корзине");
            } else {
                $("#buy-btn").val("Добавить в корзину");
            }
        } else {
            $("#js-toggle-delivery-ok").addClass("js-button-hide");
            $("#js-toggle-delivery-error").removeClass("js-button-hide");
            $("#one-click-btn").data("offer-id", "");
            $("#buy-btn").data("offer-id", "");
            $("#one-click-btn").data("is-local", "");
            $("#buy-btn").data("is-local", "");
        }
        //обработка доступности резерва
        if (resFlag) {
            $("#js-toggle-reserve-ok").removeClass("js-button-hide");
            $("#js-toggle-reserve-error").addClass("js-button-hide");
            $("#reserved-btn").data("offer-id", offerId);
            $("#reserved-btn").data("is-local", "Y");
        } else {
            $("#js-toggle-reserve-ok").addClass("js-button-hide");
            $("#js-toggle-reserve-error").removeClass("js-button-hide");
            $("#reserved-btn").data("offer-id", "");
        }

        if (!delFlag && !resFlag) {
            $('#preorder-btn').data('offer-id', offerId);
        }
    });
    $('.js-animate-scroll').on('click', function (event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 200);
    });
    //попап с размерами обуви
    $('.sizes-popup').on('click', function (e) {
        e.preventDefault();
        var sizesBlock = $(this).next().html();

        function tableColor(element) {
            let target = element.target;
            let targetNum = $(target).data('index-number');
            let tdElement = $('.table-size__row td');
            tdElement.each(function () {
                let thisAttr = $(this).data('index-number');
                if (thisAttr == targetNum) {
                    if ($(this).hasClass('table-size__td-w')) {
                        $(this).addClass('table-size__active--w');
                    } else {
                        $(this).addClass('table-size__active');
                    }
                } else {
                    $(this).removeClass('table-size__active');
                    $(this).removeClass('table-size__active--w');
                }
            });
        };

        function deletColor() {
            let tdElement = $('.table-size__row td');
            tdElement.each(function () {
                $(this).removeClass('table-size__active');
                $(this).removeClass('table-size__active--w');
            })
        };
        Popup.show(sizesBlock, {
            className: 'popup--size-tab',
            onShow: function (popup) {
                onOpenModalOneClick();
                CountInput.init();
                var $tableData = $('.table-size__row td, .table-size__row');
                var $tableRows = $('.table-size__row');

                for (i = 0; i < $tableRows.length; i++) {
                    $tableRows.eq(i).data('index-row', i);
                    var j = 0;
                    $tableRows.eq(i).find('td').each(function () {
                        $(this).data('index-number', j);
                        j++;
                    });
                }
                $tableData.on('mouseleave', function () {
                    deletColor()
                });
                $tableData.on('mouseenter', function (element) {
                    tableColor(element);
                });
            }
        });
    });
    // Виджет примерки
    $('input#fittin_widget_button.non-authorized').on('click', () => $('#auth-button').trigger('click'));
    //Инициализация слайдера
    $(".sp-image_hide").removeClass("sp-image_hide");
    const isLoop = $(".sp-slide").length > 2 ? true : false;
    $("#example5").sliderPro({
        width: '100%',
        height: 500,
        // responsive: false,
        autoSlideSize: true,
        // aspectRatio: 1,
        orientation: "horizontal",
        loop: isLoop,
        arrows: true,
        fadeArrows: true,
        autoplay: false,
        buttons: false,
        autoHeight: false,
        thumbnailsPosition: "left",
        thumbnailPointer: false,
        thumbnailHeight: 96,
        topImage: true,
        smallSize: 768,
        imageScaleMode: 'cover',
        centerImage: true,
        resize: function() {
            // $(this)[0].$slidesMask[0].style.maxHeight=$(this)[0].$slidesMask[0].style.width;
            // $(this)[0].$slidesMask[0].style.height=$(this)[0].$slidesMask[0].style.maxHeight;
        },
        init: function(data) {
            // $(this)[0].$slidesMask[0].style.maxHeight=$(this)[0].$slidesMask[0].style.width;
            goZoom(data);
        },
        update: function() {
            // $(this)[0].$slidesMask[0].style.height=$(this)[0].$slidesMask[0].style.width;
        },
        gotoSlide: function(data) {
            goZoom(data);
        },
        breakpoints: {
            342: {
                thumbnailsPosition: "bottom",
                arrows: false,
                loop: isLoop,
                thumbnailPointer: true,
                orientation: "horizontal",
                imageScaleMode: 'contain',
            },
            990: {
                thumbnailsPosition: "bottom",
                arrows: false,
                loop: isLoop,
                thumbnailPointer: true,
                orientation: "horizontal",
                imageScaleMode: 'contain',
            }
        }
    });

});

//функция для клика на кнопку "Добавить в корзину"
function basketHandler(offerId, isLocal) {
    if ($('#buy-btn').val() == 'В корзине') {
        return false;
    }
    offerId = offerId || $('#buy-btn').data('offer-id');
    isLocal = isLocal || $('#buy-btn').data('is-local');
    let data = {
        action: "basketAdd",
        offerId: offerId,
        isLocal: isLocal,
        quantity: 1,
    };
    $.ajax({
        method: "POST",
        url: "/cart/",
        data: data,
        dataType: "json",
        success: function (data) {
            hide_wait();
            if (data.status == "ok") {
                gtmPush('add_to_cart', isLocal);
                fbq('track', 'AddToCart');
                inBasket[offerId] = offerId;
                updateSmallBasket(data.text);
                $('#buy-btn').val('В корзине');
                respercEvent__add_to_cart();
                dataFlock["ID"] = offerId.toString();
                return;
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
        }
    });
}

//функция для клика на кнопку "Забрать в мгазине"
function reserveHandler(offerId) {
    hide_wait();
    let needId = 'reserve-offer-' + offerId;
    $("#reserve-form [id *= 'reserve-offer-']").each(function (e) {
        $(this).removeAttr('checked');
    })
    $('#' + needId).attr('checked', 'checked');
    Popup.show($('#reserve-form').clone(true, true), {
        className: 'popup--preorder popup-reserv-padding',
        onShow: function (popup) {
            onOpenModalOneClick();
            CountInput.init();
        },
        onClose: function (popup) {
            gtmPush('remove_from_cart', 'reserve')
        },
    });

    gtmPush('add_to_cart', 'reserve');

    function cityListHeight() {
        const minusHeight = $(".product-preorder__container").height() - $(".product-preorder__article").find('.tabs').height() - $(".product-preorder__article").find('.search-mag').height() - $(".product-preorder__article").find('.clearfix').height();
        const asidesHeight = $('.aside-wrapper').height();

        if (minusHeight >= asidesHeight) {
            $("#reserved-shop-list").height(minusHeight);
        } else {
            $("#reserved-shop-list").height(asidesHeight);
        }
    }
    cityListHeight();
    $(window).resize(function() {
        cityListHeight();
    })
    phoneMaskCreate($('.popup').find($('.reservation_phone')));

    $('.cls-mail-div').click(function() {
        Popup.hide();
    });
}

function preorderHandler (offerId) {
    hide_wait();
    offerId = offerId || $('#preorder-btn').data("offer-id");

    Popup.show($('#preorder-form').clone(true, true), {
        className: 'preorder-popup',
        onShow: function (popup) {
            onOpenModalFastOrder();
            CountInput.init();
        }
    });

    $('#preorder-form').submit(function(e) {
        e.preventDefault();

        let prod = $('.product-main-div');
        let emailElem = $('.form-preorder .preorder_email');
        let email = emailElem.val().trim().toLowerCase();
        let policyElem = $('#one_click_checkbox_policy_checked');
        let mask = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        let errorCheck = false;
        let errPolicyElem = $('.error-policy.error-preorder');

        if (policyElem.is(':checked')) {
            errPolicyElem.html('');
            //policyElem.parent('#one_click_checkbox_policy').removeClass('red_border');
        } else {
            errPolicyElem.html('Требуется согласие');
            //policyElem.parent('#one_click_checkbox_policy').addClass('red_border');
            errorCheck = true;
        }

        let errEmailElem = $('.error-email.error-preorder');

        if (mask.test(email)) {
            errEmailElem.html('');
            emailElem.removeClass('red_border');
        } else {
            errEmailElem.html('Email указан не верно');
            emailElem.addClass('red_border');
            errorCheck = true;
        }

        if (errorCheck) {
            return false;
        }

        $("#button-preorder").hide();
        $(".buttonFastBuy-loader").show();

        $.ajax({
            method: "POST",
            url: "/local/ajax/preorder.php",
            data: {
                offerID: offerId,
                prodID: prod.data('prod-id'),
                prodArt: prod.data('prod-articul'),
                email: email,
                prodPrice: prod.data('prod-price'),
            },
            dataType: "json",
            success: function (data) {
                hide_wait();
                let popupContent;
                if (data.status === "add") {
                    $('#preorder-form .preorder_email').data('phone', email).val(email);

                    popupContent = '<div class="product-preorder-success">'
                        + '<header>СПАСИБО</header>'
                        + '<div class="preorder-ok">ТОВАР ДОБАВЛЕН В СПИСОК ОЖИДАНИЯ</div>'
                        + '<footer>'
                        + '<button class="js-popup-close button button--xxl button--primary button--outline button--blue">Продолжить покупки</button>'
                        + '<div>&nbsp;</div>'
                        + '</footer>'
                        + '</div>';
                } else if (data.status === "isexist") {
                    popupContent = '<div class="product-preorder-success">'
                        + '<header>Размер ранее уже был добавлен в предзаказ</header>'
                        + '<footer>'
                        + '<button class="js-popup-close button button--xxl button--primary button--outline button--blue">Продолжить покупки</button>'
                        + '<div>&nbsp;</div>'
                        + '</footer>'
                        + '</div>';
                } else if (data.status === 'error') {
                    let error_text = '<div class="product-preorder-success">'
                        + '<h2>Ошибка</h2>'
                        + '<div class="js-size-popup">'
                        + data.text.join("<br>")
                        + '</div>'
                        + '</div>';
                    Popup.show(error_text, {});

                    return;
                }

                $("#button-preorder").show();
                $(".buttonFastBuy-loader").hide();

                Popup.show(popupContent, {
                    className: 'popup--feedback'
                });

                return;
            },
            error: function (data) {
                hide_wait();
            }
        });
    })
}

function gtmPush(type, isLocalOrReserv, data) {
    window.dataLayer = window.dataLayer || [];

    let prodData = $('.product-main-div');
    let oPush = {
        'event': 'MTRENDO',
        'eventCategory': 'EEC',
        'eventAction': type,
    };

    function getGTMProp(prodData) {
        return {
            'name': prodData.attr('data-prod-name'),  // data-prod-name
            'id': prodData.attr('data-prod-id'),   // data-prod-id
            'articul': prodData.attr('data-prod-articul'), // data-prod-articul
            'price': prodData.attr('data-prod-price'),  // data-prod-price
            'category': prodData.attr('data-prod-category'), // data-prod-category
            'variant': prodData.attr('data-prod-variant'), // data-prod-variant
            'brand': prodData.attr('data-prod-brand'),  //  Бренд товара data-prod-brand
            'top-material': prodData.attr('data-prod-top-material'),  //  Материал верха data-prod-top-material
            'lining-material': prodData.attr('data-prod-lining-material'), //Материал подкладки data-prod-lining-material
            'season': prodData.attr('data-prod-season'),  //  Сезон data-prod-season
            'collection': prodData.attr('data-prod-collection'),  //  Коллекция data-prod-collection
            'size': prodData.attr('data-prod-size'),  //  Выбранный размер
            'quantity': 1, //  Количество товаров в корзине
        };
    }

    if (['add_to_cart', 'remove_from_cart'].includes(type)) {
        oPush['eventLabel'] = prodData.attr('data-prod-name');
        if (isLocalOrReserv === 'reserve') {
            oPush['order-type'] = 'Резервирование в магазине';
        } else if (isLocalOrReserv === 'Y') {
            oPush['order-type'] = 'Корзина местная';
        } else {
            oPush['order-type'] = 'Корзина неместная';

        }
    } else if (type === 'checkout') {
        let storeSelectedElem = $('.preorder-list-item.selected');
        let storeName = storeSelectedElem.find('.preorder-list-item__title').text().trim();
        let storeAddress = storeSelectedElem.find('.preorder-list-item__address').text().trim();
        let store = storeName + ', ' + storeAddress;
        oPush['checkout-payment'] = 'Наличными или картой при получении'; // Текущий способ оплаты
        oPush['checkout-delivery'] = 'Забрать в магазине'; // Текущий способ доставки
        oPush['checkout-affiliation'] = store; // Текущий способ доставки
        oPush['order-type'] = 'Резервирование в магазине'; // Тип корзины
    } else if (type === 'purchase') {
        oPush['transaction'] = {
            'transaction_id': data.id,
            'value': data.price, // Сумма заказа включая доставку, налоги, примененную скидку, купоны
            'tax': data.tax,
            'shipping': data.delivery, // Сумма доставки
            'coupon': data.coupon, // примененный промокод
        }
        oPush['order-type'] = 'Резервирование в магазине'; // Тип корзины
    }
    oPush['products'] = [getGTMProp(prodData)];
    dataLayer.push(oPush);
}

function goZoom(data) {
    // if (!navigator.userAgent.match(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i)) {
    //     let slide;
    //     if (data.type === 'init') {
    //         slide = 0;
    //     } else {
    //         slide = data.index;
    //     }
    //     $('.jq-zoom[data-index="'+slide+'"]').zoom({
    //         magnify: 1.2
    //     });
    // }
}

function redirectToProductByCode(code, id) {
    // Если вендор виджета примерки забудет передать в функцию артикул товара
    if (code === undefined) {
        $.ajax({
            url: '/local/ajax/product_code_by_id.php',
            type: 'POST',
            data: {'product_id': id},
            success: function (response) {
                response = JSON.parse(response);
                switch (response.status) {
                    case 'ok':
                        window.location.href = '/' + response.code;
                        break;
                    case 'error':
                        $('.btn_close').trigger('click');
                        Popup.show('<div class="error-popup"><p>Товар не найден.</p></div>');
                        break;
                }
            }
        });
        return;
    }
    window.location.href = '/' + transliterateCyrillic(code.replace(/\W+/g, '_'));
}

function transliterateCyrillic(string) {
    if (typeof string !== 'string') {
        return string;
    }
    string = string.toLowerCase();
    let replacement = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i',
        'й': 'i', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
        'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
        'у': 'u', 'ф': 'f', 'х': 'kh', 'ц': 'ts', 'ч': 'ch',
        'ш': 'sh', 'ш': 'sh', 'щ': 'shch', 'ъ': '’', 'ы': 'y',
        'ь': '’', 'э': 'e', 'ю': 'yu', 'я': 'ya'
    };
    let result = '';
    for (let i = 0; i < string.length; i++) {
        result += replacement[string[i]] || string[i];
    }
    return result;
}

function onOpenModalFastOrder(offerId, isLocal) {
    let $form = $('.js-one-click-form');
    let $sizeInput = $form.find('input[name="PRODUCTS[]"]');
    $sizeInput.val(offerId);
}

function onOpenModalOneClick() {
    $('.b-element-one-click').each(function () {
        let card = $(this),
            size_selectors = $('.js-size-selector'),
            selected_size = $('.product-page .js-size-selector a.selected').data('offer-id'),
            map_element = card.find('#reserved-map'),
            is_reserved = card.hasClass('js-reserv'),
            submit_reserv = true,
            stores = [];
        /*инициализация окошка резервирования*/
        if (is_reserved) {
            size_selectors.find('.js-reserve-select').on('click', function () {
                size_selectors.find('.js-reserve-select').removeClass('selected');
                $(this).addClass('selected');
                selected_size = $(this).data('offer-id');
            });
            $(".js-offer-res").on('change', function () {
                let prodDiv = $('.product-main-div');
                if ($(this).siblings('label').text() !== ''
                    && $(this).siblings('label').text() !== undefined
                    && prodDiv.attr('data-prod-size') !== $(this).siblings('label').text()
                ) {
                    gtmPush('remove_from_cart', 'reserve');
                    prodDiv.attr('data-prod-size', $(this).siblings('label').text());
                    gtmPush('add_to_cart', 'reserve');
                }
                initCurrentTab();
            });
            $("input[name=store_name]").on('input', function () {
                initCurrentTab();
            })
            card.find('.tabs-item').on('click', function (event) {
                $(".js-tabs .tabs-item").removeClass('active');
                $(this).addClass('active');
                $('.tabs-targets>div').removeClass('active');
                $($(this).data('target')).addClass('active');
                initCurrentTab();
            });
            if (card.is('.has-no-sizes')) {
                selected_size = size_selectors.find('.js-reserve-select').eq(0).data('offer-id');
                size_selectors.find('input:eq(0)').prop('checked', true).trigger('change');
            }
            initCurrentTab();
        }

        function initCurrentTab() {
            var currentTab = $('.tabs-targets>div.active');
            store_id = 0;
            if (currentTab.data('init') == 'map') {
                initMap();
            }
            if (currentTab.data('init') == 'list') {
                initList();
            }
            if (currentTab.data('init') == 'metro') {
                initMetro();
            }
        }

        function initList() {
            var center = {
                    lat: parseFloat(map_element.data('lat')) || 55.7494733,
                    lng: parseFloat(map_element.data('lon')) || 37.35232
                },
                size = 0,
                size_input = $('.js-offer-res:checked:eq(0)'),
                market_list = $('#reserved-shop-list');
            stores = JSON.parse(BX.message('RESERVED_STORES_LIST'));
            //фильтруем салоны по значению из инпута поиска
            stores = searchStores(stores, $.trim($('input[name=store_name]').val()));
            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.next().text()));
            }
            if (size) {
                var copy_stores = $.makeArray(stores);
                stores = [];
                $.each(copy_stores, function (i, store) {
                    if ($.inArray(size, store.sizes) !== -1) {
                        stores.push(store);
                    }
                });
            }
            var stores_html = '';
            var sel = 0;
            $(".preorder-list-item").each(function () {
                if ($(this).hasClass("selected")) {
                    sel = $(this).attr("data-index");
                }
            });
            var sel_real = 0;
            for (var i = 0; i < stores.length; i++) {
                if (sel == stores[i].index) {
                    sel_real = stores[i].index;
                    stores_html += '<div class="preorder-list-item selected" data-index="' + stores[i].index + '" >';
                } else {
                    stores_html += '<div class="preorder-list-item" data-index="' + stores[i].index + '"  >';
                }
                $('input[name="DELIVERY_STORE_ID"]').val(sel_real);
                stores_html += '<div class="preorder-list-item__info"><div class="preorder-list-item__title">' + stores[i].title + '</div>';
                stores_html += '<a class="" href="tel:' + stores[i].phone + '"><img class="preorder-list-item__phone" src="/local/templates/respect/img/call-answer.png">' + stores[i].phone + '</a>';
                stores_html += '<div class="preorder-list-item__address">' + stores[i].address + '</div>';
                if (stores[i].metro != null) {
                    for (var k in stores[i].metro) {
                        stores_html += '<div><svg style="background:#' + stores[i].metro[k].color + '; width:10px; height:10px; border-radius:2px;"></svg> ' + stores[i].metro[k].name + ' (' + stores[i].metro[k].distance + 'км)</div>';
                    }
                }

                if (stores[i].whatsapp_link !== '') {
                    stores_html += '<a href="' + stores[i].whatsapp_link + '" class="preorder-list-item__whatsapp" target="_blank"><img src="/img/whatsapp.svg"><span>получить видео консультацию</span></a><div class="whatsapp-video-tooltip">Онлайн чат с магазином доступен с компьютера и мобильного телефона, консультация с видео - только на мобильных устройствах.</div>';
                }
                stores_html += '</div>';
                if (stores[i].sizes.length > 0) {
                    stores_html += '<div class="preorder-list-item__sizes"><div><label>Размеры в магазине</label><div class="size-selector size-selector--wrap">';
                    for (var j in stores[i].sizes) {
                        if (stores[i].sizes[j]==size) {
                            stores_html += '<a style=" background: #4e4e4e; color: white;"; >' + stores[i].sizes[j] + '</a>';
                        }
                        else{
                            stores_html += '<a>' + stores[i].sizes[j] + '</a>';
                        }
                    }
                    stores_html +='</div></div>';
                }
                if (stores[i].whatsapp_link !== '') {
                    stores_html += '<div class="preorder-button-wrapper"><a href="' + stores[i].whatsapp_link + '" class="preorder-list-item__whatsapp preorder-list-item__whatsapp-mobile" target="_blank"><img src="/img/whatsapp.svg"><span>получить видео консультацию</span></a>';
                    stores_html += '<button type="button" class="preorder-list-item__take">выбрать магазин</button></div></div></div>';
                }else{
                    stores_html += '<button type="button" class="preorder-list-item__take">выбрать магазин</button></div></div>';
                }
            }
            if (sel_real == 0) {
                if (sel > 0) {
                    $('.js-store-selected').find('.js-store-selected-value').text("");
                    $('.js-stores-error').show();
                    $(".js-store-selected").hide();
                }
            }
            market_list.empty();
            market_list.append(stores_html);
            market_list.find('.preorder-list-item').on('click', function (event) {
                market_list.find('.preorder-list-item').removeClass('selected');
                $(this).addClass('selected');
                var store_index = $(this).data('index');
                store_id = store_index;
                card.find('.js-stores-error').hide();
                storeIdHook(store_id);
            });
        }

        function initMap() {
            var center = {
                    lat: parseFloat(map_element.data('lat')) || 55.7494733,
                    lng: parseFloat(map_element.data('lon')) || 37.35232
                },
                stores = JSON.parse(BX.message('RESERVED_STORES_LIST')),
                size = 0,
                size_input = $('.js-offer-res:checked:eq(0)'),
                market_list = $('#reserved-shop-list');

            //фильтруем салоны по значению из инпута поиска
            stores = searchStores(stores, $.trim($('input[name=store_name]').val()));
            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.next().text()));
            }
            if (size) {
                var copy_stores = $.makeArray(stores);
                stores = [];
                $.each(copy_stores, function (i, store) {
                    if ($.inArray(size, store.sizes) !== -1) {
                        stores.push(store);
                    }
                });
            }
            var map = new window.GoogleMapView(map_element, {
                items: stores,
                google: {
                    center: center
                },
                onSelect: function (index) {
                    store_id = index;
                    storeIdHook(store_id)
                }
            });
        }

        function initMetro() {
            var stores = JSON.parse(BX.message('RESERVED_STORES_LIST')),
                size = 0,
                size_input = $('.js-offer-res:checked:eq(0)');
            //фильтруем салоны по значению из инпута поиска
            stores = searchStores(stores, $.trim($('input[name=store_name]').val()));
            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.next().text()));
            }
            if (size) {
                var copy_stores = $.makeArray(stores);
                stores = [];
                $.each(copy_stores, function (i, store) {
                    if ($.inArray(size, store.sizes) !== -1) {
                        stores.push(store);
                    }
                });
            }
            var stores_name = [];
            $.ajax({
                method: 'get',
                url: '/local/templates/respect/images/moscow-metro.svg',
                success: function (data) {
                    $("#subway").empty();
                    $("#subway").append(new XMLSerializer().serializeToString(data.documentElement));
                    var len;
                    var shop;
                    var marker;
                    for (var i = 0; i < stores.length; i++) {
                        shop = stores[i];
                        if (shop.subway_trans) {
                            if ($("#subway #" + shop.subway_trans).length > 0) {
                                marker = new SubwayMapMarker(shop, $("#subway #" + shop.subway_trans));
                                marker.appendTo($("#subway"));
                            }
                        }
                    }
                }
            });
        }

        function storeIdHook(store_id) {
            var selectedStore = null;
            for (var i = 0; i < stores.length; i++) {
                if (store_id == stores[i].index) {
                    selectedStore = stores[i];
                    $('input[name="DELIVERY_STORE_ID"]').val(stores[i].index);
                }
            }
            if (selectedStore) {
                $('.js-store-selected')
                    .find('.js-store-selected-value')
                    .text(selectedStore.title)
                    .end()
                    .show();
                $('.popup--preorder .popup__container').animate({
                    scrollTop: $('.popup--preorder .popup__content').height()
                }, 1000);
            }
        }

        //аналог stristr в php
        function stristr( haystack, needle, bool ) {
            var pos = 0;
            pos = haystack.toLowerCase().indexOf( needle.toLowerCase() );
            if( pos == -1 ){
                return false;
            } else{
                if( bool ){
                    return haystack.substr( 0, pos );
                } else{
                    return haystack.slice( pos );
                }
            }
        }

        function searchStores (stores, filter) {
            if (filter !== '') {
                let filtered_stores = [];
                for (let i = 0; i < stores.length; i++) {
                    var metro,
                        metroResult = true;
                    for (var k in stores[i].metro) {
                        metro = stristr(stores[i].metro[k].name.toLowerCase(), filter) === false;
                        if (metro === false) {
                            metroResult = false;
                        }
                    }
                    if (stristr(stores[i].address.toLowerCase(), filter) === false && stristr(stores[i].title.toLowerCase(), filter) === false && metroResult) {
                        continue;
                    }
                    filtered_stores.push(stores[i]);
                }
                filtered_stores.filter(val => val);
                return filtered_stores;
            }
            return stores;
        }
    });
}

window.SubwayMapMarker = (function () {
    function SubwayMapMarker(data, point) {
        this.data = data;
        this.point = point;
        this.marker = $('<div class="map-marker">');
        this.bubble = $(this._infoWindowTemplate(this.data)).appendTo(this.marker);
        this.marker.on('click', this.show.bind(this));
        $(document).on('mouseup', (function (_this) {
            return function (event) {
                if (!_this.marker.is(event.target) && _this.marker.has(event.target).length === 0) {
                    return _this.hide();
                }
            };
        })(this));
    }

    SubwayMapMarker.prototype.appendTo = function (container) {
        var coordinates, position, svg;
        $(container).append(this.marker);
        svg = $('svg', container);
        coordinates = {
            top: this.point.offset().top - $(container).offset().top,
            left: this.point.offset().left - $(container).offset().left
        };
        position = {
            left: ((coordinates.left + this.point.width() / 2) / $(container).width() * 100) + "%",
            top: ((coordinates.top + this.point.height() / 2) / $(container).outerHeight() * 100) + "%"
        };
        return this.marker.css(position);
    };
    SubwayMapMarker.prototype.show = function () {
        store_id = this.data.index;
        return this.marker.addClass('with-bubble');
    };
    SubwayMapMarker.prototype.hide = function () {
        store_id = 0;
        return this.marker.removeClass('with-bubble');
    };
    SubwayMapMarker.prototype._infoWindowTemplate = function (data) {
        var template;
        if ($('*').is('.' + data.subway_trans)) {
            return $('.' + data.subway_trans).append('<div class="map-bubble__title">' + data.title + '</div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>' + data.subway + '</div>\n  <div class="map-bubble__address">' + data.address + '</div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span>' + data.worktime + '</span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span>' + data.phone + '</span>\n    </li>\n  </ul>');
        } else {
            template = _.template('<div class="map-bubble <%=subway_trans%>">\n  <div class="map-bubble__title"><%=title%></div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n  </ul>\n</div>');
            return template(data);
        }
    };
    return SubwayMapMarker;
})();

//Покупка в 1 клик
$(document).ready(function () {
    //Покупка в 1 клик
    var presetDataPhone = $("input.one_click_phone").data('phone');
    $("input.one_click_phone").val(presetDataPhone);

    $('#one-click-form').on('submit', function (e) {
        e.preventDefault();
        var cou_err = 0;
        var text_html = "";
        if ($("input.one_click_phone").val().trim() == "") {
            cou_err++;
            text_html += '<p>Необходимо заполнить поле *Телефон</p>';
            $("input.one_click_phone").addClass("red_border");
        } else {
            var inputPhoneValue = $("input.one_click_phone").val().replace(/\D+/g, '');
            if (inputPhoneValue.length - 1 < 10) {
                text_html += "<p>Неверно заполнено поле *Телефон</p>";
                $("input.one_click_phone").addClass("red_border");
                cou_err++;
            } else {
                $("input.one_click_phone").removeClass("red_border");
            }
        }
        if (!($("#one_click_checkbox_policy_checked").prop('checked'))) {
            cou_err++;
            text_html += "<p>Необходимо согласие с политикой конфиденциальности</p>";
        }
        $("#after-cart-in-err").html(text_html);
        if (cou_err > 0) {
            return false;
        }
        $("#one-click-form").addClass("loader-one-click-form");
        $(".input-group--phone").addClass("loader-one-click-element");
        $("#button-one-click").hide();
        $(".buttonFastBuy-loader").show();
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {
                if (data.status == "ok") {
                    let paymentType = 'default';
                    let items = [];
                    for (var key in data.info) {
                        items.push({"id": key, "qnt": 1,  "price": data.info[key].BASKET_PRICE})
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
                    window.location = '/order-success/?orderId=' + data.text + '&orderType=' + paymentType;
                    return false;
                }
                $("#one-click-form").removeClass("loader-one-click-form");
                $(".input-group--phone").removeClass("loader-one-click-element");
                $("#button-one-click").show();
                $(".buttonFastBuy-loader").hide();
                $("#after-cart-in-err").html(data.text.join("<br>"));
            },
            error: function (jqXHR, exception) {
                $("#one-click-form").removeClass("loader-one-click-form");
                $(".input-group--phone").removeClass("loader-one-click-element");
                $("#button-one-click").show();
                $(".buttonFastBuy-loader").hide();
            },
        });
        return false;
    });


});

//Резервирование
$(document).ready(function() {
    var presetDataFIO = $("input.fio").data('fio');
    var presetDataPhone = $("input.reservation_phone").data('phone');
    $("input.fio").val(presetDataFIO);
    $("input.reservation_phone").val(presetDataPhone);
    $("input.fio").click(function(){
        if ($("input.fio").val() == presetDataFIO) {
            $("input.fio").val('');
        }
    });

    $('#reserve-form').on('submit',function(e) {
        e.preventDefault();
        var arr = {
            "PROPS[FIO]":"",
        };
        var cou_err = 0;
        var text_html = "";

        if (typeof checkStoreSellerCookie === "function" && !checkStoreSellerCookie()) {
            return false;
        }

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
        $(".js-preorder-submit").hide();
        $(".buttonReservation-loader").show();
        $(".product-preorder__article").addClass("loader-one-click-element");
        $(".product-preorder__aside-info").addClass("loader-one-click-element");
        $(".phone").addClass("loader-one-click-element");
        $(".alert--danger").addClass("loader-one-click-element");
        $('#reserve-form').addClass("loader-one-click-form");
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if (data.status == "ok") {
                    let paymentType = 'default';
                    let items = [];
                    for (var key in data.info) {
                        items.push({"id": key, "qnt": 1, "price": data.info[key].BASKET_PRICE})
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

                    gtmPush('checkout', 'reserve');
                    gtmPush('purchase', 'reserve', data.gtmData);

                    window.location = '/order-success/?orderId=' + data.text + '&orderType=' + paymentType;
                    return false;
                }
                hide_wait();
                $(".js-preorder-submit").show();
                $(".buttonReservation-loader").hide();
                $(".product-preorder__article").removeClass("loader-one-click-element");
                $(".product-preorder__aside-info").removeClass("loader-one-click-element");
                $(".phone").removeClass("loader-one-click-element");
                $(".alert--danger").removeClass("loader-one-click-element");
                $('#reserve-form').removeClass("loader-one-click-form");
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
                $('#reserve-form').removeClass("loader-one-click-form");
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

$(document).ready(function (){
    $('.have-tooltip-mob').on('click', function() {
        var dd = $( this ).data('tooltipname');
        Popup.show('</br>' + propsTooltip[dd]);
    });
});