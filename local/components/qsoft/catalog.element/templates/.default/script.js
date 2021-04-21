$(document).ready(function () {
    inBasket = inBasket || [];

    // Выставляем дефолтную выборку оффера
    if (previousOffer['PROPERTIES']['SIZE']['VALUE']) {
        let sizeInput = $('input#size-' + previousOffer['PROPERTIES']['SIZE']['VALUE']);
        sizeInput.prop('checked', true);
    }

    if (previousOffer['PROPERTIES']['SIZE']['VALUE']) {
        let colorInput = $('input#color-' + previousOffer['PROPERTIES']['SIZE']['VALUE']);
        colorInput.prop('checked', true);
    }

    //попап окно размеров, если не выбрали
    function respercEvent__pushSize(valueBtn) {
        let popupContent = $('.js-choose-size').clone(true, true);
        popupContent.find('.js-size-popup').html(valueBtn);
        Popup.show(popupContent, {
            className: 'popup--size-tab',
        });
        $(".delivery-sizes-input").on("click", function () {
            let offerId = $(this).data("offer-id");
            if ($(this).text() !== '' && $(this).text() !== undefined) {
                $('.product-main-div').attr('data-prod-size', $(this).text());
            }
            delPopupClickHandler(offerId);
        });
    }

    //обработка клика в поп ап окне доставки
    function delPopupClickHandler(offerId) {
        Popup.hide(true);
        if ($("#del-popup-type").val() == "basket") {
            basketHandler(offerId);
        } else {
            oneClickHandler(offerId);
        }
        $("#del-popup-type").val("");
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
                let $tableData = $('.table-size__row td, .table-size__row');
                let $tableRows = $('.table-size__row');

                for (let i = 0; i < $tableRows.length; i++) {
                    $tableRows.eq(i).data('index-row', i);
                    let j = 0;
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
        imageScaleMode: 100,
        centerImage: true,
        init: function(data) {
            goZoom(data);
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
                imageScaleMode: 100,
                height: 200,
            },
            900: {
                thumbnailsPosition: "bottom",
                arrows: false,
                loop: isLoop,
                thumbnailPointer: true,
                orientation: "horizontal",
                imageScaleMode: 100,
                height: 250,
            },
            990: {
                thumbnailsPosition: "bottom",
                arrows: false,
                loop: isLoop,
                thumbnailPointer: true,
                orientation: "horizontal",
                imageScaleMode: 100,
                height: 300,
            },
            1090: {
                imageScaleMode: 100,
                height: 300,
            },
            1205: {
                imageScaleMode: 65,
            },
            1275: {
                imageScaleMode: 75,
            },
            1350: {
                imageScaleMode: 80,
            },
            1500: {
                imageScaleMode: 85,
            }
        }
    });

    //обработка выбора оффера
    $('.js-offer').on("change", function() {
        selectOffer();
    });

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

//функция для выбора оффера
function selectOffer() {
    let size = $('.js-size-selector input[name=size]:checked');
    if (size) {
        size = size.val();
    }
    let color = $('.js-size-selector input[name=color]:checked');
    if (color) {
        color = color.val();
    }
    let currentOffer = null;
    for (let offerID in OFFERS) {
        if (size && color) {
            if (
                OFFERS[offerID]['PROPERTIES']['COLOR']['VALUE'] === color
                && OFFERS[offerID]['PROPERTIES']['SIZE']['VALUE'] === size
            ) {
                currentOffer = OFFERS[offerID];
                break;
            }
        } else if (size) {
            if (OFFERS[offerID]['PROPERTIES']['SIZE']['VALUE'] === size) {
                currentOffer = OFFERS[offerID];
                break;
            }
        } else {
            if (OFFERS[offerID]['PROPERTIES']['COLOR']['VALUE'] === color) {
                currentOffer = OFFERS[offerID];
                break;
            }
        }
    }
    console.log(currentOffer);
}



//функция для клика на кнопку "Добавить в корзину"
function basketHandler(offerId) {
    if ($('#buy-btn').val() == 'В корзине') {
        return false;
    }
    offerId = offerId || $('#buy-btn').data('offer-id');
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

function goZoom(data) {
    // if (!navigator.userAgent.match(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i)) {
    //     let slide;
    //     if (data.type === 'init') {
    //         slide = 0;
    //     } else {
    //         slide = data.index;
    //     }
    //     $('.jq-zoom[data-index="'+slide+'"]').zoom({
    //         magnify: 1.5
    //     });
    // }
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