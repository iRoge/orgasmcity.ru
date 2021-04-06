$(function () {
    inBasket = inBasket || [];
    //попап окно размеров, если не выбрали
    function respercEvent__pushSize(valueBtn)
    {
        var popupContent = '<div class="product-preorder-success">'
        +     '<h2>Выберите размер</h2>'
        +     '<form method="post" name="name" class="form-after-cart js-action-form-popup-size">'
        +         '<input type="hidden" name="action" value="">'
        +         '<div class="js-size-popup">'
        +             valueBtn
        +         '</div>'
        +     '</form>'
        + '</div>';
        Popup.show(popupContent, {
            className: 'popup--size-tab',
        });
        $(".delivery-sizes-input").on("click", function() {
            var offerId = $(this).data("offer-id");
            delPopupClickHandler(offerId);
        });
        $(".reservation-sizes-input").on("click", function() {
            var offerId = $(this).data("offer-id");
            resPopupClickHandler(offerId);
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
    //обработка клика в поп ап окне резерва
    function resPopupClickHandler(offerId) {
        Popup.hide(true);
        reserveHandler(offerId)
    }
    //проверка размера для кнопки "Купить в 1 клик"
    $('#one-click-btn').click(function(e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            $("#del-popup-type").val("1click");
            respercEvent__pushSize($('.delivery-sizes').html());
            return;
        }
        oneClickHandler();
    });
    //функция для клика на кнопку "Купить в 1 клик"
    function oneClickHandler(offerId) {
        show_wait($('.js-action-form'));
        offerId = offerId || $('#one-click-btn').data('offer-id');
        $.ajax({
            method: 'get',
            url: window.application.getUrl('product'),
            success: function(response) {
                hide_wait();
                return Popup.show($(response), {
                    title: 'Быстрый заказ',
                    onShow: (function(_this) {
                        return function(popup) {
                            onOpenModalFastOrder(offerId);
                            return CountInput.init();
                        };
                    })(this)
                });
            }
        });
    }
    //проверка размера для кнопки "Добавить в корзину"
    $('#buy-btn').click(function(e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            $("#del-popup-type").val("basket");
            respercEvent__pushSize($('.delivery-sizes').html());
            return;
        }
        basketHandler();
    });
    //функция для клика на кнопку "Добавить в корзину"
    function basketHandler(offerId) {
        if ($('#buy-btn').val() == 'В корзине') {
            return false;
        }
        show_wait($('.js-action-form'));
        offerId = offerId || $('#buy-btn').data('offer-id');
        var data = {
            action: "basketAdd",
            offerId: offerId,
            quantity: 1,
        }
        $.ajax({
            method: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function(data) {
                hide_wait();
                if (data.status == "ok") {
                    fbq('track', 'AddToCart');
                    inBasket[offerId] = offerId;
                    updateSmallBasket(data.text);
                    $('#buy-btn').val('В корзине');
                    respercEvent__add_to_cart();
                    return;
                }
                var error_text = '<div class="product-preorder-success">'
                +     '<h2>Ошибка</h2>'
                +     '<div class="js-size-popup">'
                +         data.text.join("<br>")
                +     '</div>'
                + '</div>';
                Popup.show(error_text, {});
            },
            error: function(data) {
                hide_wait();
            }
        });
    }
    //проверка размера для кнопки "Забрать в магазине"
    $('#reserved-btn').click(function(e) {
        e.preventDefault();
        if (!$(this).data('offer-id')) {
            respercEvent__pushSize($('.reservation-sizes').html());
            return;
        }
        reserveHandler();
    });
    //функция для клика на кнопку "Забрать в мгазине"
    function reserveHandler(offerId) {
        show_wait($('.js-action-form'));
        offerId = offerId || $('#reserved-btn').data('offer-id');
        var data = {
            action: "get_reservation_modal",
            offerId: offerId,
        }
        $.get(document.location, data, function (response) {
            hide_wait();
            response = $(response);
            Popup.show(response, {
                className: 'popup--preorder popup-reserv-padding',
                onShow: function (popup) {
                    onOpenModalOneClick();
                    CountInput.init();
                }
            });
        });
    }
    //обработка выбора размера
    $('.js-offer').on("change", function() {
        //получаем ID выбранного размера
        var offerId = $(".js-offer:checked").val();
        //проверяем доступен ли он на доставку
        var delFlag = $("#del-offer-" + offerId).length;
        //проверяем доступен ли он на резерв
        var resFlag = $("#res-offer-" + offerId).length;
        //обработка доступности доставки
        if (delFlag) {
            $("#js-toggle-delivery-ok").removeClass("js-button-hide");
            $("#js-toggle-delivery-error").addClass("js-button-hide");
            $("#one-click-btn").data("offer-id", offerId);
            $("#buy-btn").data("offer-id", offerId);
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
        }
        //обработка доступности резерва
        if (resFlag) {
            $("#js-toggle-reserve-ok").removeClass("js-button-hide");
            $("#js-toggle-reserve-error").addClass("js-button-hide");
            $("#reserved-btn").data("offer-id", offerId);
        } else {
            $("#js-toggle-reserve-ok").addClass("js-button-hide");
            $("#js-toggle-reserve-error").removeClass("js-button-hide");
            $("#reserved-btn").data("offer-id", "");
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
                        $(this).addClass('table-size__active--w');}
                    else {
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
                $(this).removeClass('table-size__active--w');})
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
                $tableData.on('mouseleave', function (){
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
});
function redirectToProductByCode(code, id) {
    // Если вендор виджета примерки забудет передать в функцию артикул товара
    if (code === undefined) {
        $.ajax({
            url: '/local/ajax/product_code_by_id.php',
            type: 'POST',
            data: {'product_id': id},
            success: function(response) {
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
function onOpenModalFastOrder (offerId) {
    var $form = $('.js-one-click-form');
    var $sizeInput = $form.find('input[name="PRODUCTS[]"]');
    $sizeInput.val (offerId);
}

function onOpenModalOneClick() {
    $('.b-element-one-click').each(function () {
        var card = $(this),
            size_selectors = $('.js-size-selector'),
            selected_size = $('.product-page .js-size-selector a.selected').data('offer-id'),
            map_element = card.find('#reserved-map'),
            is_reserved = card.hasClass('js-reserv'),
            submit_reserv = true,
            stores = [];
        /*инициализация окошка резервирования*/
        if (is_reserved) {
            size_selectors.find('.js-reserve-select').on('click', function() {
                size_selectors.find('.js-reserve-select').removeClass('selected');
                $(this).addClass('selected');
                selected_size = $(this).data('offer-id');
            });
            $(".js-offer-res").on('change', function() {
                initCurrentTab();
            });
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
                console.log('init map');
                initMap();
            }
            if (currentTab.data('init') == 'list') {
                console.log('init list');
                initList();
            }
            if (currentTab.data('init') == 'metro') {
                console.log('init metro');
                initMetro();
            }
            $('#reserved-shop-list').css('max-height',$('.popup--preorder').height()-200);
        }
        function initList() {
            var center = {
                    lat: parseFloat(map_element.data('lat')) || 55.7494733,
                    lng: parseFloat(map_element.data('lon')) || 37.35232
                },
                size = 0,
                size_input = $('.js-offer-res:checked'),
                market_list = $('#reserved-shop-list');
            stores = JSON.parse(BX.message('RESERVED_STORES_LIST'));
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
            var sel=0;
            $(".preorder-list-item").each(function(){
                if($(this).hasClass("selected")) {
                      sel=$(this).attr("data-index");
                }
            });
            var sel_real=0;
            for (var i = 0; i < stores.length; i++) {
                if(sel==stores[i].index){
                    sel_real=stores[i].index;
                    stores_html += '<div class="preorder-list-item selected" data-index="' + stores[i].index + '" >';
                }else{
                    stores_html += '<div class="preorder-list-item" data-index="' + stores[i].index + '"  >';
                }
                $('input[name="DELIVERY_STORE_ID"]').val(sel_real);
                stores_html += '<div class="preorder-list-item__info"><div class="preorder-list-item__title">' + stores[i].title + '</div><div class="preorder-list-item__address">' + stores[i].address + '</div></div>';
                if (stores[i].sizes.length > 0) {
                    stores_html += '<div class="preorder-list-item__sizes"><label>Размеры</label><div class="size-selector size-selector--wrap">';
                    for (var j in stores[i].sizes) {
                        stores_html += '<a>' + stores[i].sizes[j] + '</a>';
                    }
                    stores_html += '</div></div>';
                }
                stores_html += '</div>';
            }
            if(sel_real==0){
                if(sel>0){
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
                size_input = $('.js-offer-res:checked'),
                market_list = $('#reserved-shop-list');

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
                size_input = $('.js-offer-res:checked');
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
                        if(shop.subway_trans){
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
        if($('*').is('.'+data.subway_trans)){
            return $('.'+data.subway_trans).append('<div class="map-bubble__title">'+data.title+'</div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>'+data.subway+'</div>\n  <div class="map-bubble__address">'+data.address+'</div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span>'+data.worktime+'</span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span>'+data.phone+'</span>\n    </li>\n  </ul>');
        }else{
            template = _.template('<div class="map-bubble <%=subway_trans%>">\n  <div class="map-bubble__title"><%=title%></div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n  </ul>\n</div>');
            return template(data);
        }
    };
    return SubwayMapMarker;
})();

