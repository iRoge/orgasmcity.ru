filtered_form = {
    filter_button: document.getElementById('pvz_filter'),
    pvz: [],

    setFilterParams: function () {
        filtered_form.filter_form = document.getElementsByClassName('filter_form')[0];

        if (filtered_form.filter_form.elements.havepaysystem.checked) {
            filtered_form.havepaysystem = filtered_form.filter_form.elements.havepaysystem.value;
        } else {
            filtered_form.havepaysystem = 2;
        }

        filtered_form.pvz = [];
        if (filtered_form.filter_form.elements.pvz.length > 0) {
            filtered_form.filter_form.elements.pvz.forEach(function (item) {
                if (item.checked) {
                    filtered_form.pvz.push(item.value);
                }
            });
        } else {
            if (filtered_form.filter_form.elements.pvz.checked) {
                filtered_form.pvz.push(filtered_form.filter_form.elements.pvz.value);
            }
        }

        return [filtered_form.pvz, filtered_form.havepaysystem];
    },

    button_click: function () {
        filtered_form.filter_button.click();
    },

    reset_filter: function () {
        filtered_form.filter_form = document.getElementsByClassName('filter_form')[0];

        if (filtered_form.filter_form.elements.pvz.length > 0) {
            filtered_form.filter_form.elements.pvz.forEach(function (item) {
                item.checked = true;
            });
        } else {
            filtered_form.filter_form.elements.pvz.checked = true;
        }
        filtered_form.filter_form.elements.havepaysystem.checked = false;
    }
};

window.panel = {
    element: document.getElementsByClassName('pvz_panel')[0],
    header: document.getElementsByClassName('panel_header')[0].children[0],
    body: document.getElementsByClassName('panel_container')[0],

    showPanel: function () {
        panel.element.classList.toggle('panel_hidden');
        panel.element.classList.toggle('panel_show');
    },

    closePanel: function () {
        panel.element.classList.add('panel_hidden');
        panel.element.classList.remove('panel_show');
    },

    setHeader: function (title) {
        this.header.innerHTML = title;
    },

    setContent: function (content) {
        this.body.innerHTML = content;
    },

    choose: function (event, button) {
        let pvz, id, prepayment, delivery, input, address, pvzDisabledInputs, pvzEnabledInputs, paymentIds;

        event.preventDefault();
        pvzmap.map.balloon.close();

        pvz = button.getAttribute('pvz');
        address = button.getAttribute('pvz_address');
        id = button.getAttribute('pvz_id');
        prepayment = button.getAttribute('only_prepayment');

        window[pvz].choose(id, address);

        //Меняем форму отправки
        delivery = pvzObj.DELIVERY_SERVICES[pvz];
        input = $('.checkout__block--delivery').find('.is-pvz');
        input.attr('id', 'delivery_' + delivery.ID);
        input.siblings('label').attr('for', 'delivery_' + delivery.ID);
        input.attr('data-price', delivery.PRICE);
        input.val(delivery.ID);
        input.prop('checked', true);
        input.addClass('pvz-checked');
        pvzDisabledInputs =  $(".js__cdek-disabled");
        pvzDisabledInputs.addClass("is-hidden");
        pvzEnabledInputs =  $(".js__cdek-enabled");
        pvzEnabledInputs.removeClass("is-hidden");
        $('js-payment:checked').prop('checked', false);
        // Деактивируем оплаты
        paymentIds = input.data('allowed-payments-' + pvz.toLowerCase());
        if (typeof(paymentIds) != 'string') {
            paymentIds = String(paymentIds);
        }
        paymentIds = paymentIds.split(',');
        let paymentSelector = $('.payment__type');
        paymentSelector.each(function(index) {
            $(this).find('input').prop('checked', false);
            if (paymentIds.indexOf($(this).find('input').val()) === -1 || (prepayment === 'Y' && $(this).find('input').data('prepayment') !== 'Y')) {
                $(this).addClass('payment__type--disabled');
                $(this).find('input').prop('disabled', true);
            } else {
                $(this).removeClass('payment__type--disabled');
                $(this).find('input').prop('disabled', false);
            }
        });
        // Cортируем оплаты
        let arItems = $.makeArray(paymentSelector);
        arItems.sort(function(a, b) {
            if($(a).find('input').prop('disabled') == $(b).find('input').prop('disabled')){
                return $(a).find('input').data('sort') - $(b).find('input').data('sort')
            }
            return $(a).find('input').prop('disabled') - $(b).find('input').prop('disabled')
        });
        $(arItems).appendTo(paymentSelector.parent());

        let labels = $('#b-order').find('.delivery-label');
        $('#b-order .err-delivery').html('').removeClass('actual');
        labels.each(function() {
            $(this).removeClass('red-border');
        });

        $('#cart__delivery-price').html(delivery.PRICE > 0 ? formatPrice(delivery.PRICE) : 'Бесплатно');
        let sum = 0;
        $(".orders__price").each(function() {
            sum += parseInt($(this).find(".orders__price-num").data("price"));
        });
        sum = parseInt(delivery.PRICE) + parseInt(sum);
        $('#cart__total-price').html(formatPrice(sum));

        if (checkActiveCheckbox('b-order', 'js-delivery')) {
            hiddenBlock('close', 'b-order', 'all');
            hiddenBlock('open', 'b-order', 'checkout__form');
        }
    }
};

window.pvzmap = {
    pvzClasses: Object.keys(pvzObj.PVZ),

    init: function () {
        $(document).on('click', `.pvz__filter-toggle`, function () {
            $('#my-listbox').toggle();
        });
        $(window).on('resize', function () {
            if($(window).width() >= 992) {
            $('#my-listbox').show();} else
            {$('#my-listbox').hide()}
        });
        this.map = new ymaps.Map("pvzMap", {
            center: [pvzObj.CENTER.LATITUDE, pvzObj.CENTER.LONGITUDE],
            controls: [],
            zoom: 10,
        }),
            ListBoxLayout = ymaps.templateLayoutFactory.createClass(

                "<button id='my-listbox-header' class='pvz__filter-toggle'> " +
                "<span class='filter-toggle__icon'><img src='/local/templates/respect/img/svg/filter.svg' class='filter-toggle__icon-pic filter-toggle__icon-pic--hide'></span>" +
                "<span class='filter-toggle__text filter-toggle__text--mobile' style='display: inline-flex'>Фильтр</span></button>" +


                "<form id='my-listbox'" +
                " class='filter_form panel-list__item-checkbox-list' aria-labelledby='dropdownMenu' role='menu' aria-labelledby='dropdownMenu'>" +
                "</form>", {

                    build: function() {
                        ListBoxLayout.superclass.build.call(this);

                        this.childContainerElement = $('#my-listbox').get(0);
                        this.events.fire('childcontainerchange', {
                            newChildContainerElement: this.childContainerElement,
                            oldChildContainerElement: null
                        });
                    },
                    getChildContainerElement: function () {
                        return this.childContainerElement;
                    },

                    clear: function () {
                        this.events.fire('childcontainerchange', {
                            newChildContainerElement: null,
                            oldChildContainerElement: this.childContainerElement
                        });
                        this.childContainerElement = null;

                        ListBoxLayout.superclass.clear.call(this);
                    }
                }),

            ListBoxItemLayout = ymaps.templateLayoutFactory.createClass(
                '<input type="checkbox" name="{{data.name_li}}" id="{{data.name}}" {{data.checked}} value="{{data.value}}" onchange="filtered_form.button_click();"> <label class="{{data.class_li}}" for="{{data.name}}"><span><div class="box-filter"></div><img src="/local/templates/respect/img/svg/{{data.src}}.svg" class="img-icon img-icon--{{data.name}}" alt="">{{data.label}}</span></label>'
            ),
            listBoxItemsArray = [];
            //выводит ПВЗ Respect на первое место
            let arPVZNames = Object.keys(pvzObj.PVZ);

            arPVZNames.forEach(function (pvzClassName) {
                let pvzName;
                pvzName = pvzClassName;
                listBoxItemsArray.push (new ymaps.control.ListBoxItem({
                    data: {
                        content: pvzName,
                        name: pvzName,
                        label: pvz[pvzName],
                        value: pvzName,
                        class_li: 'pvz__label',
                        name_li:'pvz',
                        src: pvzClassName,
                        checked: 'checked'
                    }
                }))
            });

        listBoxItemsArray.push(new ymaps.control.ListBoxItem({
            data: {
                class: 'payment-item',
                name: 'havepaysystem',
                value: '1',
                label: 'Оплата при получении',
                class_li: 'havecashless__label',
                name_li:'havepaysystem',
                src: 'havePayment',
                checked: ''
            }
        })),

        listBox = new ymaps.control.ListBox({
            items: listBoxItemsArray,
            data: {
                content: 'Фильтр'
            },
            options: {
                itemLayout: ListBoxItemLayout,
                layout: ListBoxLayout,
                float:'left',
                floatIndex: 1
            }
        });

        this.map.controls.add(listBox, {float: 'left'});

        this.map.controls.add('geolocationControl', {
            position: {
                top: 80,
                right: 10
            },
        });

        this.map.controls.add('searchControl', {
            float: 'left',
            floatIndex: 0,
            size: 'auto',
            maxWidth: [28, 150, 400]
        });

        this.map.controls.add(new ymaps.control.Button({
            content: " ",
            options: {
                layout: ymaps.templateLayoutFactory.createClass('<button class="widget__popup__close-btn" onclick="pvzmap.close()" data-dismiss="modal"><svg class="icon-cross" viewBox="0 0 20 20" width="20" height="20"><path d="M18.2 20L0 1.7 1.7 0 20 18 18 20z"></path><path d="M1.7 20L20 1.7 18 0 0 18.2 1.7 20z"></path></svg></button>'),
                maxWidth: 32,
                selectOnClick: !1,
                position: {
                    top: 10,
                    right: 10
                }
            }
        }));

        this.map.controls.add('zoomControl', {
            position: {
                top: 120,
                right: 10
            },
            size: 'medium'
        });

        pvzmap.show();
    },

    addPoints: function () {
        this.pvzClasses.reverse();
        this.pvzClasses.forEach(function (name) {
            let filter = document.querySelector('label[for=' + name + ']');
            let flag = true;
            if (!pvzObj.DELIVERY_SERVICES.hasOwnProperty(name)) {
                flag = false;
            } else if (!pvzObj.DELIVERY_SERVICES[name].PRICE) {
                    flag = false;
            }
            if (flag) {
                filter.style.display = 'block';
            } else {
                filter.style.display = 'none';
            }
            pvzmap.map.geoObjects.add(window[name].changeFilteredPoints(2));
        });
    },

    changeFilteredPoints: function (event) {
        let params;

        event.preventDefault();
        params = filtered_form.setFilterParams();
        pvzmap.map.geoObjects.removeAll();
        params[0].forEach(function (name) {
            pvzmap.map.geoObjects.add(window[name].changeFilteredPoints(params[1]));
        });
    },

    show: function () {
        let widget;
        if (document.querySelector('label[for=' + this.pvzClasses[0] + ']') == null){
            setTimeout(
                function () {
                    pvzmap.show()
                },
                10
            );
            return false;
        }
        pvzmap.addPoints();
        filtered_form.reset_filter();
        widget = document.getElementsByClassName('widget__popup-mask')[0];
        widget.style.display = 'block';
        document.getElementsByClassName('load-more-btn-loader')[0].style.display = 'none';
        if (document.getElementsByClassName('load-more-btn-loader')[1] != null){
            document.getElementsByClassName('load-more-btn-loader')[1].style.display = 'none';
        }
    },

    close: function () {
        let widget;
        pvzmap.map.balloon.close();
        pvzmap.map.geoObjects.removeAll();
        widget = document.getElementsByClassName('widget__popup-mask')[0];
        widget.style.display = 'none';
        $('body').css('overflow', 'auto');
    }
};

window.CDEK = {
    pvzlist: pvzObj.PVZ.CDEK,
    filtered_list: [],

    createClusterer: function () {
        this.clusterer = new ymaps.Clusterer({
            gridSize: 64,
            groupByCoordinates: false,
            hasBalloon: false,
            hasHint: false,
            margin: 10,
            maxZoom: 14,
            minClusterSize: 3,
            showInAlphabeticalOrder: false,
            viewportMargin: 128,
            zoomMargin: 0,
            clusterDisableClickZoom: false,
            preset: 'islands#darkGreenClusterIcons'
        })
    },

    addPoint: function (item, index) {
        let havePaySystem = '';
        let phone = '';
        let images = '';
        if (!pvzObj['DELIVERY_SERVICES'].hasOwnProperty('CDEK')) {
            return;
        }
        if (!pvzObj['DELIVERY_SERVICES']['CDEK'].hasOwnProperty('PRICE')) {
            return;
        }
        let price = parseInt(pvzObj['DELIVERY_SERVICES']['CDEK']['PRICE']) > 0 ? pvzObj['DELIVERY_SERVICES']['CDEK']['PRICE'] + ' руб.' : 'Бесплатно';
        if (item.officeImageList) {
            item.officeImageList.forEach(function (img) {
                images += '<a href="' + img.url + '" target="_blanc"><img src="' + img.url + '" width="130" height="130"></a>'
            })
        }
        let Placemark = new ymaps.Placemark(
            [item.coordY, item.coordX],
            {
                balloonContent: '<p class="panel-details__block-head">' + (item.type == 'PVZ' ? 'Пункт выдачи CDEK' : 'Постамат CDEK') + '</p>' +
                    '<div class="panel-details__block"><button class="widget__choose btn" data-label="Выбрать" pvz_name="' + item.name + '" pvz_id="' + item.code + '" pvz="CDEK" pvz_address=\'' + (item.metroStation ? 'м.' + item.metroStation + ' ' : '') + item.address + '\' only_prepayment="' + (item.haveCash || item.haveCashless ? 'N' : 'Y') + '" onclick="panel.choose(event, this);">Выбрать</button> <span class="panel-details__cost-delivery">' + price + '</span> </div>' +
                    '<p class="panel-details__block-text panel-details__block-text--left-padding"><img src="/local/templates/respect/img/svg/location.svg" class="widget__location-icon">' + (item.metroStation ? 'м. ' + item.metroStation + ' ' : '') + item.address + '</p>' +
                    `${phone = item.phone ? 
                        '<p class="panel-details__block-text panel-details__block-text--left-padding"><img src="/local/templates/respect/img/icon-phone.svg" class="widget__location-icon">' + item.phone + '</p>' 
                        : ''
                    }` +
                    '<p class="panel-details__block-text panel-details__block-text--left-padding"><img src="/local/templates/respect/img/svg/time.svg" class="widget__time-icon">'+ item.workTime + '</p>' +
                    `${item.haveCashless ?
                        "<p class='panel-details__block-text panel-details__block-text--blue panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/card.svg' class='img-icon img-icon--havecashless' alt=''>Возможна оплата картой при получении</span></p>"
                        : "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cardNo.svg' class='img-icon img-icon--havecashless' alt=''>Нет оплаты картой</span></p>"
                    }` +
                    `${item.haveCash ?
                        "<p class='panel-details__block-text panel-details__block-text--blue panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cash.svg' class='img-icon img-icon--havecashless' alt=''>Возможна оплата наличными</span></p>"
                        : "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cashNo.svg' class='img-icon img-icon--havecashless' alt=''>Нет оплаты наличными</span></p>"
                    }` +
                    `${!item.haveCashless && !item.haveCash ? "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/onlyPrepayment.svg' class='img-icon img-icon--havecashless' alt=''>Возможна только предоплата</span></p>"
                        : ""
                    }` +
                    '<p class="panel-details__block-text">' + item.addressComment + '</p>' +
                    '<div class="pvz__images">' + images + '</div>'
            },
            {
                preset: 'islands#darkGreenIcon',
                hideIconOnBalloonOpen: false,
                hasBalloon: true,
                balloonMinHeight: 120,
                balloonMaxWidth: 300,
                balloonPanelMaxMapArea: 48e4,
            }
        );

        Placemark.link = index;

        Placemark.events.add(['balloonopen', 'click'], function (metka) {
            if (panel.element.classList.contains('panel_hidden')) {
                sidebar.burger.changeIcon();
            }
        });

        this.clusterer.add(Placemark);
    },

    getItemsByFilter: function (havepaysystem) {
        this.pvzlist.forEach((item) => this.addItem(item, havepaysystem));
    },

    addItem: function (item, havepaysystem) {
        let havepaysystem_result;

        if (havepaysystem == 2) {
            havepaysystem_result = true;
        } else {
            havepaysystem_result = item.haveCash || item.haveCashless;
        }

        if (havepaysystem_result) {
            this.filtered_list.push(item);
        }
    },

    changeFilteredPoints: function (havepaysystem) {
        this.createClusterer();
        this.filtered_list = [];
        this.getItemsByFilter(havepaysystem);
        this.clusterer.removeAll();
        if (this.filtered_list.length > 0) {
            this.filtered_list.forEach((item, index) => this.addPoint(item, index));
        }
        return this.clusterer;
    },

    choose: function (id, name) {
        let input, button;

        input = document.getElementById('cart__delivery-cdek-input');
        button = document.getElementById('cart__delivery-cdek-button');
        input.value = id;
        button.value = name;
        pvzmap.close();
    }
};

window.PickPoint = {
    pvzlist: pvzObj.PVZ.PickPoint,
    filtered_list: [],

    createClusterer: function () {
        this.clusterer = new ymaps.Clusterer({
            gridSize: 64,
            groupByCoordinates: false,
            hasBalloon: false,
            hasHint: false,
            margin: 10,
            maxZoom: 14,
            minClusterSize: 3,
            showInAlphabeticalOrder: false,
            viewportMargin: 128,
            zoomMargin: 0,
            clusterDisableClickZoom: false,
            preset: 'islands#brownClusterIcons'
        })
    },

    addPoint: function (item, index) {
        let images = '';
        if (!pvzObj['DELIVERY_SERVICES'].hasOwnProperty('PickPoint')) {
            return;
        }
        if (!pvzObj['DELIVERY_SERVICES']['PickPoint'].hasOwnProperty('PRICE')) {
            return;
        }
        let price = parseInt(pvzObj['DELIVERY_SERVICES']['PickPoint']['PRICE']) > 0 ? pvzObj['DELIVERY_SERVICES']['PickPoint']['PRICE'] + ' руб.' : 'Бесплатно';
        if (item['File0']) {
            let url = "https://e-solution.pickpoint.ru/api/" + item['File0'];
            images += '<a href="' + url + '" target="_blanc"><img src="' + url + '" width="130" height="130"></a>'
        }
        if (item['File1']) {
            let url = "https://e-solution.pickpoint.ru/api/" + item['File1'];
            images += '<a href="' + url + '" target="_blanc"><img src="' + url + '" width="130" height="130"></a>'
        }
        if (item['File2']) {
            let url = "https://e-solution.pickpoint.ru/api/" + item['File2'];
            images += '<a href="' + url + '" target="_blanc"><img src="' + url + '" width="130" height="130"></a>'
        }
        let Placemark = new ymaps.Placemark(
            [item['Latitude'], item['Longitude']],
            {
                balloonContent: '<p class="panel-details__block-head">' + (item['TypeTitle'] === 'ПВЗ' ? 'Пункт выдачи PickPoint' : 'Постамат PickPoint') + '</p>' +
                    '<div class="panel-details__block"><button class="widget__choose btn" data-label="Выбрать" pvz_name="' + item['Name'] + '" pvz_id="' + item['Number'] + '" pvz="PickPoint" pvz_address=\'' + (item['Metro'] ? 'м.' + item['Metro'] + ' ' : '') + item['Address'] + '\' only_prepayment="' + ((item['Cash'] || item['Card'] === 1) ? 'N' : 'Y') + '" onclick="panel.choose(event, this);">Выбрать</button> <span class="panel-details__cost-delivery">' + price + '</span> </div>' +
                    '<p class="panel-details__block-text panel-details__block-text--left-padding"><img src="/local/templates/respect/img/svg/location.svg" class="widget__location-icon">' + (item['Metro'] ? 'м. ' + item['Metro'] + ' ' : '') + item['Address'] + '</p>'
                     +
                    '<p class="panel-details__block-text panel-details__block-text--left-padding"><img src="/local/templates/respect/img/svg/time.svg" class="widget__time-icon">'+ item['WorkTimeSMS'] + '</p>' +
                    `${item['Card'] === 1 ?
                        "<p class='panel-details__block-text panel-details__block-text--blue panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/card.svg' class='img-icon img-icon--havecashless' alt=''>Возможна оплата картой при получении</span></p>"
                        : "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cardNo.svg' class='img-icon img-icon--havecashless' alt=''>Нет оплаты картой</span></p>"
                    }` +
                    `${item['Cash'] ?
                        "<p class='panel-details__block-text panel-details__block-text--blue panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cash.svg' class='img-icon img-icon--havecashless' alt=''>Возможна оплата наличными</span></p>"
                        : "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/cashNo.svg' class='img-icon img-icon--havecashless' alt=''>Нет оплаты наличными</span></p>"
                    }` +
                    `${!(item['Cash'] || item['Card'] === 1) ? "<p class='panel-details__block-text panel-details__block-text--red panel-details__block-text--left-padding'><span><img src='/local/templates/respect/img/svg/onlyPrepayment.svg' class='img-icon img-icon--havecashless' alt=''>Возможна только предоплата</span></p>"
                        : ""
                    }` +
                    '<p class="panel-details__block-text">' + item['OutDescription'] + ' ' + item['InDescription'] + '</p>' +
                    '<div class="pvz__images">' + images + '</div>'
            },
            {
                preset: 'islands#brownIcon',
                hideIconOnBalloonOpen: false,
                hasBalloon: true,
                balloonMinHeight: 120,
                balloonMaxWidth: 300,
                balloonPanelMaxMapArea: 48e4,
            }
        );

        Placemark.link = index;

        Placemark.events.add(['balloonopen', 'click'], function (metka) {
            if (panel.element.classList.contains('panel_hidden')) {
                sidebar.burger.changeIcon();
            }
        });

        this.clusterer.add(Placemark);
    },

    getItemsByFilter: function (havepaysystem) {
        this.pvzlist.forEach((item) => this.addItem(item, havepaysystem));
    },

    addItem: function (item, havepaysystem) {
        let havepaysystem_result;

        if (havepaysystem == 2) {
            havepaysystem_result = true;
        } else {
            havepaysystem_result = item['Cash'] || item['Card'] === 1;
        }

        if (havepaysystem_result) {
            this.filtered_list.push(item);
        }
    },

    changeFilteredPoints: function (havepaysystem) {
        this.createClusterer();
        this.filtered_list = [];
        this.getItemsByFilter(havepaysystem);
        this.clusterer.removeAll();
        if (this.filtered_list.length > 0) {
            this.filtered_list.forEach((item, index) => this.addPoint(item, index));
        }
        return this.clusterer;
    },

    choose: function (id, name) {
        let input, button;

        input = document.getElementById('cart__delivery-cdek-input');
        button = document.getElementById('cart__delivery-cdek-button');
        input.value = id;
        button.value = name;
        pvzmap.close();
    }
};

window.sidebar = {
    burger: {
        element: document.getElementsByClassName('sidebar-burger')[0],
        changeIcon: function () {
            sidebar.burger.element.classList.toggle('close');
            sidebar.burger.element.classList.toggle('open');
            panel.showPanel();
        },

        showPanel: function () {
            panel.setHeader('Фильтр');
            panel.setContent(sidebar.burger.getFilterForm());
        },

        getFilterForm: function () {
            return document.getElementById('filter_block').innerHTML;
        }
    },

    sidebar_menu: {
        element: document.getElementsByClassName('sidebar-menu')[0],
        items: document.getElementsByClassName('sidebar-menu')[0].children,

        activate: function (event) {
            let active; // флаг активности элемента

            active = event.target.closest('li').classList.contains('active');

            if (event.target.tagName == 'LI' || event.target.tagName == 'DIV' || event.target.tagName == 'SPAN') {
                for (let i = 0; i < sidebar.sidebar_menu.items.length; i++) {
                    sidebar.sidebar_menu.items[i].classList.remove('active');
                }
                if (active == false) {
                    event.target.closest('li').classList.add('active');
                }
            }
        }
    }
};
