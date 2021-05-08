$(document).ready(function () {
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

    // выставляет радио инпуты в соответствии с оффером
    function setPropsByOffer(offer) {
        if (offer['PROPERTIES']['SIZE']['VALUE']) {
            let sizeInput = $('input#size-' + offer['PROPERTIES']['SIZE']['VALUE']);
            sizeInput.prop('checked', true);
        }

        if (offer['PROPERTIES']['COLOR']['VALUE']) {
            let colorInput = $('input#color-' + offer['PROPERTIES']['COLOR']['VALUE']);
            colorInput.prop('checked', true);
        }

        let rightCartochka = $('.right-cartochka__inner-wrap');
        let priceSpan = rightCartochka.find('span.js-price-span');
        if (offer['PROPERTIES']['PRICE']['VALUE']) {
            let price = new Intl.NumberFormat('ru-RU').format(offer['PROPERTIES']['PRICE']['VALUE']);
            let oldPrice = new Intl.NumberFormat('ru-RU').format(offer['PROPERTIES']['PRICE']['OLD_VALUE']);
            let percent = new Intl.NumberFormat('ru-RU').format(offer['PROPERTIES']['PRICE']['PERCENT']);

            priceSpan.html(price);
            if (price === oldPrice) {
                rightCartochka.find('div.js-old-price-block').hide();
            } else {
                rightCartochka.find('div.js-old-price-block').show();
                rightCartochka.find('span.js-price-percent-span').html(percent);
                rightCartochka.find('span.js-old-price-span').html(oldPrice)
            }
        } else {
            priceSpan.html(10000000);
            rightCartochka.find('div.js-old-price-block').hide();
        }
    }

    function getOfferByFilter(color, size) {
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

        return currentOffer;
    }

    //функция для клика на кнопку "Добавить в корзину"
    function basketHandler() {
        let offerId = $('#buy-btn').data('offer-id');
        let quantity = $('.quantity-num').val();
        let data = {
            action: "basketAdd",
            offerId: offerId,
            quantity: quantity,
        };
        $.ajax({
            method: "POST",
            url: "/cart/",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status == "ok") {
                    updateSmallBasket(data.text);
                    respercEvent__add_to_cart();
                    return;
                }
                let error_text = '<div class="product-preorder-success">'
                    + '<h2>Ошибка</h2>'
                    + '<div class="js-size-popup text-danger">'
                    + data.text.join("<br>")
                    + '</div>'
                    + '</div>';
                Popup.show(error_text, {});
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    function goZoom(data) {
        if (!navigator.userAgent.match(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i)) {
            let slide;
            if (data.type === 'init') {
                slide = 0;
            } else {
                slide = data.index;
            }
            $('.jq-zoom[data-index="'+slide+'"]').zoom({
                magnify: 1.5
            });
        }
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
        let sizeInput = $('.js-size-selector input[name=size]:checked');
        let size = null;
        if (sizeInput) {
            size = sizeInput.val();
        }
        let color = null;
        let colorInput = $('.js-color-selector input[name=color]:checked');
        if (colorInput) {
            color = colorInput.val();
        }
        let currentOffer = getOfferByFilter(color, size);
        if (currentOffer) {
            previousOffer = currentOffer;
            setPropsByOffer(currentOffer);
            let colorFilter = previousOffer['PROPERTIES']['COLOR']['VALUE'];
            let sizeFilter = previousOffer['PROPERTIES']['SIZE']['VALUE'];
            if (sizeFilter) {
                $('.js-color-selector input[name=color]').each(function (index) {
                    let input = $(this);
                    $('label[for="' + input.prop('id') + '"]').css('opacity', '1');
                    // input.prop('disabled', false);
                    let offer = getOfferByFilter($(this).val(), sizeFilter);
                    if (!offer) {
                        // input.prop('disabled', true);
                        $('label[for="' + input.prop('id') + '"]').css('opacity', '0.2')
                    }
                })
            }
            if (colorFilter) {
                $('.js-size-selector input[name=size]').each(function (index) {
                    let input = $(this);
                    $('label[for="' + input.prop('id') + '"]').css('opacity', '1');
                    // input.prop('disabled', false);
                    let offer = getOfferByFilter(colorFilter, $(this).val());
                    if (!offer) {
                        // input.prop('disabled', true);
                        $('label[for="' + input.prop('id') + '"]').css('opacity', '0.2')
                    }
                })
            }
            $('.js-error-block').hide();
            $('#buy-btn').data('offer-id', currentOffer['ID']);
            $('#one-click-btn').data('offer-id', currentOffer['ID'])
        } else {
            $('.js-size-selector input[value=' + previousOffer['PROPERTIES']['SIZE']['VALUE'] + ']').prop('checked', true);
            $('.js-color-selector input[value=' + previousOffer['PROPERTIES']['COLOR']['VALUE'] + ']').prop('checked', true);
            $('.js-error-block').show();
            setTimeout(function () {
                $('.js-error-block').hide();
            }, 5000);
            setPropsByOffer(previousOffer);
        }
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
                    for (let key in data.info) {
                        items.push({"id": key, "qnt": 1,  "price": data.info[key].BASKET_PRICE})
                    }
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

    var $quantityNum = $(".quantity-num");
    // Увеличение количества для добавления в корзину
    $(".quantity-arrow-minus").on('click', function (event) {
        event.preventDefault();
        if ($quantityNum.val() > 1) {
            $quantityNum.val(+$quantityNum.val() - 1);
        }
    });
    $(".quantity-arrow-plus").on('click', function (event) {
        event.preventDefault();
        $quantityNum.val(+$quantityNum.val() + 1);
    });

    //проверка размера для кнопки "Добавить в корзину"
    $('#buy-btn').click(function (e) {
        e.preventDefault();
        basketHandler();
    });

    // Выставляем дефолтную выборку оффера
    setPropsByOffer(previousOffer);

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
});
