$(function () {
    $('.product-page').each(function () {
        var card = $(this),
            form = $('.js-action-form', card),
            prices = $('.offers .offer-price', card),
            size_btn_boxes = $('.js-size-btn-box', card),
            colors = $('.js-btn-color', card),
            sizes = $('.js-offer', card),
            size_selectors = $('.js-size-selector', card),
            sizes_btns = size_selectors.find('a'),
            error = $('.js-offer-error', card),
            is_partner = BX.message('IS_PARTNER') == 'Y';


        //переключение цветов
        if (!is_partner && 0) {
            colors.on('click', function (e) {
                e.preventDefault();

                colors.removeClass('selected');
                $(this).addClass('selected');

                var color = $(this).data('color'),
                    cur_size_selector = size_selectors.hide().filter(function () {
                        return $(this).data('color') == color;
                    }).show();

                //если был выбран размер
                var select_size = $.trim(sizes_btns.filter('.selected').text());

                sizes_btns.removeClass('selected');
                sizes.prop('disabled', true);

                if (select_size) {
                    var cur_size_link = cur_size_selector.find('a').filter(function () {
                        return select_size == $.trim($(this).text()) && !$(this).hasClass('missed');
                    });

                    if (cur_size_link.length > 0) {
                        selectOffer(cur_size_link.data('offer-id'));
                    }
                }
            });
        }

        //клик по размеру
        sizes_btns.on('click', function (e) {
            e.preventDefault();

            var btn = $(this);

            if (btn.hasClass('missed'))
                return;

            error.hide();

            if (btn.hasClass('selected')) {
                unSelectOffer(btn.data('offer-id'));
            } else {
                selectOffer(btn.data('offer-id'));
            }
        });

        //добавление в корзину
        form.on('submit', function (e) {
            e.preventDefault();

            var data = $(this).serializeArray();

            if (sizes.filter(':enabled').length == 0) {
                error.show();
                $('html, body').animate({
                    scrollTop: error.offset().top - ($(window).height() / 2)
                }, 200);
                return false;
            }

            $.post(BX.message('CATALOG_ELEMENT_TEMPLATE_PATH') + '/ajax.php', data, function (response) {
				console.log(response);
                if (response.STATUS == 'OK') {
                    if ('ITEM_IDS' in response) {
                        for (var i in response.ITEM_IDS) {
                            form.find(".js-size-btn-box[data-offer-id='" + response.ITEM_IDS[i] + "']  .js-cart-btn").val('В корзине').removeClass('button--outline');
                        }
                    }

                    error.hide();
                    sizes_btns.removeClass('selected');
                    sizes.prop('disabled', true);

                    if (is_partner && typeof response.COUNT != 'undefined') {
                        updateCount(response.COUNT);
                    }
                } else if (! response.hasOwnProperty('MESSAGE')) {
                    error.show();
                    $('html, body').animate({
                        scrollTop: error.offset().top - ($(window).height() / 2)
                    }, 200);
                }

                $(document).trigger('update-basket-small', response);
            }, 'json');
        });

        //резервирование
        $('.js-reserved-btn', card).on('click', function (e) {
            e.preventDefault();
			show_wait(form);
            $.get(window.page_url, {action: 'get_reservation_modal'}, function (response) {
                response = $(response);

                Popup.show(response, {
                    className: 'popup--preorder',
                    onShow: function (popup) {
                        onOpenModalOneClick();
                        CountInput.init();
                    }
                });
            }).always (function (data) {
				hide_wait(form);
            });
        });


        //Наличие на складах
        $('.js-shop-list-custom', card).on('click', function (e) {
            e.preventDefault();

            $.get(window.page_url, {action: 'get_amount'}, function (response) {
                response = $(response);

                Popup.show(response, {
                    className: 'popup--preorder',
                    onShow: function (popup) {
                        onOpenModalOneClick();
                        CountInput.init();
                    }
                });
            });
        });


        //добавление в избранное
        $('.js-add-to-favorites', card).on('click', function (e) {
            e.preventDefault();
            LikeeAjax.btnClick($(this));
        });

        //для партнеров обновляем кол-во товара в корзине
        if (is_partner) {
            $.post(BX.message('CATALOG_ELEMENT_TEMPLATE_PATH') + '/ajax.php', {action: 'get_count'}, function (response) {
                if (response.STATUS == 'OK' && typeof response.COUNT != 'undefined') {
                    updateCount(response.COUNT);
                }
            }, 'json');
        }

        //покупка в кредит, добавляем выбранный размер в корзину и переходим на страницу оформления заказа
        $('.js-credit', card).on('click', function (e) {
            e.preventDefault();

            if (sizes.filter(':enabled').length == 0) {
                error.show();
                $('html, body').animate({
                    scrollTop: error.offset().top - ($(window).height() / 2)
                }, 200);
                return false;
            }

            var data = form.serializeArray();

            $.post(BX.message('CATALOG_ELEMENT_TEMPLATE_PATH') + '/ajax.php', data, function (response) {
                if (response.STATUS == 'OK') {
                    document.location = '/order/?action=credit';
                } else {
                    error.show();
                    $('html, body').animate({
                        scrollTop: error.offset().top - ($(window).height() / 2)
                    }, 200);
                }
            }, 'json');
        });

        //выбираем ТП
        function selectOffer(offer_id) {
            //form.find('.js-cart-btn').val('Купить').addClass('button--outline');

            //простой люд может выбирать только один товар
            if (!is_partner) {
                sizes_btns.removeClass('selected');
                sizes.prop('disabled', true);
            }

            //выбираем кнопку-ссылку с нужным ТП
            sizes_btns.filter(function () {
                return $(this).data('offer-id') == offer_id;
            }).addClass('selected');

            sizes.filter('.js-offer-' + offer_id).prop('disabled', false);

            //показываем нужную цену
            prices.hide().filter(function () {
                return $(this).data('offer-id') == offer_id;
            }).show();

            //показываем нужный набор кнопок
            size_btn_boxes.hide().filter(function () {
                return $(this).data('offer-id') == offer_id;
            }).show();
        }

        function unSelectOffer(offer_id) {
            sizes_btns.filter(function () {
                return $(this).data('offer-id') == offer_id;
            }).removeClass('selected');
            sizes.filter('.js-offer-' + offer_id).prop('disabled', true);
        }

        //обновляем выбраные товары на карточке
        function updateCount(count) {
            var i = 0;

            $.each(count, function (offer_id, quantity) {
                var size_btn = sizes_btns.filter(function () {
                    return $(this).data('offer-id') == offer_id;
                });

                if (size_btn.length > 0) {
                    i += quantity;

                    selectOffer(offer_id);

                    if (size_btn.find('.size-selector__count').length == 0) {
                        size_btn.append('<div class="size-selector__count"></div>')
                    }

                    size_btn.find('.size-selector__count').text(quantity);
                }
            });

            card.find('.js-buy-button').val('Добавить в корзину (' + i + ')');
        }


    });

    $('.js-animate-scroll').on('click', function (event) {
        event.preventDefault();

        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 200);
    });

    //Попап с размерами обуви
    $('.sizes-popup').on('click', function (e) {
        e.preventDefault();

        var sizesBlock = $(this).next().html();
        Popup.show(sizesBlock, {
            className: 'popup--size-tab',
            onShow: function (popup) {
                onOpenModalOneClick();
                CountInput.init();
            }
        });
    });
});


function onOpenModalFastOrder (offerId) {
	console.log(offerId);
	var $form = $('.js-one-click-form');
	var $sizeInput = $form.find('input[name="PRODUCTS[]"]');
    $sizeInput.val (offerId);
}


function onOpenModalOneClick() {
    $('.b-element-one-click').each(function () {
        //TODO сейчас обработка форм одна для разных типов. Разнести по разным методам.
        var card = $(this),
            size_selectors = $('.js-size-selector', card),
            selected_size = $('.product-page .js-size-selector a.selected').data('offer-id'),
            map_element = card.find('#reserved-map'),
            is_reserved = card.hasClass('js-reserv'),
            submit_reserv = true,
            stores = [];

        window.store_id = 0;


        if (selected_size) {
            $('.size-selector input[value="' + selected_size + '"]', size_selectors).prop('checked', true).trigger('change');
        }

        card.on('submit', function (e) {
			
            if (card.find('.js-offer:checked').length == 0) {
                submit_reserv = false;
                e.preventDefault();
                card.find('.js-offer-error').show();
            } else if (is_reserved && store_id == 0) {
                submit_reserv = false;
                e.preventDefault();
                card.find('.js-stores-error').show();
            } else {
                submit_reserv = true;
                card.find('.js-offer-error').hide();
                card.find('.js-stores-error').hide();
            }

            card.find('input[name="DELIVERY_STORE_ID"]').val(store_id);


            if (is_reserved) {
                e.preventDefault();

                if (submit_reserv) {
                    submitReservation();
                }
            }
        });

        /*инициализация окошка резервирования*/
        if (is_reserved) {
            size_selectors.find('.js-reserve-select').on('click', function () {
                size_selectors.find('.js-reserve-select').removeClass('selected');
                $(this).addClass('selected');
                selected_size = $(this).data('offer-id');
                size_selectors.find('input[value="' + selected_size + '"]').prop('checked', true).trigger('change');
            });

            size_selectors.find('.js-offer').on('change', function () {
                store_id = 0;
                initCurrentTab();
                //setMaximumQuantity();
            });

            card.find('.tabs-item').on('click', function (event) {
                $(".js-tabs .tabs-item").removeClass('active');
                $(this).addClass('active');
                $('.tabs-targets>div').removeClass('active');
                $($(this).data('target')).addClass('active');
                initCurrentTab();
            });

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
        }

        /*function setMaximumQuantity () {
        	var selected=size_selectors.find('.js-offer:checked');
        	var counter=card.find('.counter');

        	if (selected.length<1)
				selected=size_selectors.find('.js-offer').first();

			if (selected.length<1)
				return false;

			try {
				var maxValue, value;
				value = parseInt(counter.val());
				maxValue=parseInt(selected.data('quantity'));
			} catch (e) {
				console.log('error quantity parse');
				return false;
			}

			if (value>maxValue)
				counter.val(maxValue);

			counter.data('maximum',selected.data('quantity'));
			return true;
		}*/


        function initList() {

            var center = {
                    lat: parseFloat(map_element.data('lat')) || 55.7494733,
                    lng: parseFloat(map_element.data('lon')) || 37.35232
                },
                size = 0,
                size_input = size_selectors.find('.js-offer:checked'),
                market_list = $('#reserved-shop-list');

            stores = JSON.parse(BX.message('RESERVED_STORES_LIST'));

            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.prev().text()));
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
            for (var i = 0; i < stores.length; i++) {

                stores_html += '<div class="preorder-list-item" data-index="' + stores[i].index + '">';
                stores_html += '<div class="preorder-list-item__info"><div class="preorder-list-item__title">' + stores[i].title + '</div><div class="preorder-list-item__address">' + stores[i].address + '</div></div>';
                stores_html += '<div class="preorder-list-item__sizes"><label>Размеры</label><div class="size-selector size-selector--wrap">';

                for (var j in stores[i].sizes) {
                    stores_html += '<a>' + stores[i].sizes[j] + '</a>';
                }
                stores_html += '</div></div>';
                stores_html += '</div>';
                //stores_html+='<li class="shop-list-item" data-index="'+stores[i].index+'"><div class="shop-list-item__title">'+stores[i].title+'</div><div class="shop-list-item__address">'+stores[i].address+'</div>';
                //stores_html+='</li>';
            }

            market_list.empty();
            market_list.append(stores_html);

            market_list.find('.preorder-list-item').on('click', function (event) {
                market_list.find('.preorder-list-item').removeClass('selected');
                $(this).addClass('selected');
                var store_index = $(this).data('index');
                store_id = store_index;
                storeIdHook(store_id)
                //var marker = map._markers[store_index];
                //google.maps.event.trigger(marker, 'click');
            });

        }

        function initMap() {
            var center = {
                    lat: parseFloat(map_element.data('lat')) || 55.7494733,
                    lng: parseFloat(map_element.data('lon')) || 37.35232
                },
                stores = JSON.parse(BX.message('RESERVED_STORES_LIST')),
                size = 0,
                size_input = size_selectors.find('.js-offer:checked'),
                market_list = $('#reserved-shop-list');

            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.prev().text()));
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
                size_input = size_selectors.find('.js-offer:checked');

            if (size_input.length > 0) {
                size = parseInt($.trim(size_input.prev().text()));
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
                        if ($("#subway #" + shop.subway_trans).length > 0) {
                            marker = new SubwayMapMarker(shop, $("#subway #" + shop.subway_trans));
                            marker.appendTo($('#subway'));
                        }
                        //results.push(_this._markers[shop.subway_alias] = _this.marker);
                    }
                }
            });
        }

        function storeIdHook(store_id)
        {
            var selectedStore = null;
            for (var i = 0; i < stores.length; i++) {
                if (store_id == stores[i].index) {
                    selectedStore = stores[i];
                }
            }

            if (selectedStore) {
                $('.js-store-selected')
                    .find('.js-store-selected-value')
                        .text(selectedStore.title)
                        .end()
                    .show();

                $('.popup--preorder .popup__container').animate({ 
                    scrollTop: $('.js-preorder-submit').offset().top
                }, 1000);
            }
        }
    });
}


function submitReservation() {
    var reserv_form = $('.js-reserv');
	var error_class = '.error-hint';	
	
	show_wait(reserv_form);	
    $.ajax({
        url: reserv_form.attr('action'),
        type: 'POST',
        data: reserv_form.serialize(),
        success: function (content) {

            //id заказа
            var id = $('<div>' + content + '</div>').find("#hidden-order-id").val();

            //Контент всплывашки
            var popup_content = $(".js-success-cont");


			//Ошибки при создании заказа
			var errors_content = $('<div>' + content + '</div>').find(error_class);
			
			
			if (errors_content.length>0) {
				popup_content.find('header').html ('ошибка');
				popup_content.find('.product-preorder-success__subtitle').remove();
				popup_content.find('.product-preorder-success__number').remove();
				popup_content.find('.product-preorder-success__title').html (errors_content.html());
			} else {
				if (id) {
                    popup_content.find('.product-preorder-success__number').html(id);
				} else {
					popup_content.find('.product-preorder-success__subtitle').remove();
					popup_content.find('.product-preorder-success__number').remove();
                }

                var reponseScript = $('<div>' + content + '</div>').find("script[data-ajax-process]");
                $.each(reponseScript, function(idx, val) { eval(val.text); } );
                $(window).trigger('site-ajax');
			}

            popup_content = popup_content.html();

            //Popup.hide();
            Popup.show(popup_content, {
                className: 'popup--preorder-success',
                onShow: function (popup) {
                }
            });
        }
    }).always (function (data) {
		hide_wait();
	});

    //var number=content.find('.product-preorder-success__number');
    //console.log(number);
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
        template = _.template('<div class="map-bubble">\n  <div class="map-bubble__title"><%=title%></div>\n  <div class="map-bubble__metro">\n    <i class="icon icon-metro"></i>\n    <%=subway%>\n  </div>\n  <div class="map-bubble__address"><%=address%></div>\n  <ul class="map-bubble__info">\n    <li>\n      <i class="icon icon-clock"></i>\n      <span><%=worktime%></span>\n    </li>\n    <li>\n      <i class="icon icon-phone"></i>\n      <span><%=phone%></span>\n    </li>\n  </ul>\n</div>');
        return template(data);
    };

    return SubwayMapMarker;
})();