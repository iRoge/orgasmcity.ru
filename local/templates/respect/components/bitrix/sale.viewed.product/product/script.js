function userHistory() {
}

userHistory.prototype = {
    options: {
        container: 'user-list-wrap'
    },

    setOptions: function (opt) {
        if (opt == null) {
            opt = {};
        }
        for (var i in opt) {
            this.options[i] = opt[i];
        }
    },
    initAll: function () {
        this.initBuyBtn();
        this.initShowMoreBtn();
        this.initLikeBtn();
        LikeeAjax.updateBtn();
    },
    initLikeBtn: function () {
        var container = $('.' + this.options.container);
        container.find('.js-add-to-favorites').on('click', function (e) {
            e.preventDefault();
            LikeeAjax.btnClick($(this));
        });
    },
    initBuyBtn: function () {
        var container = $('.' + this.options.container);

        $('.js-add-to-basket', container).on('click', function (e) {
            e.preventDefault();

            var btn = $(this);

            BX.ajax.get(btn.attr('href'), {action: 'get_buy_modal'}, function (response) {
                var form = $(response);

                Popup.show(form, {
                    className: 'popup--size',
                    title: 'Выберите размер',
                    onShow: function () {
                        CountInput.init();

                        form.on('submit', function (e) {
                            e.preventDefault();

                            if (form.find('input:checked').length == 0)
                                return;

                            var data = form.serialize();

                            BX.ajax.loadJSON(document.location.pathname + '?' + data, function (response) {
                                if (response.STATUS && response.STATUS == 'OK') {
                                    btn.addClass('shortcut--active');
                                } else {
                                    btn.removeClass('shortcut--active');
                                }

                                $(document).trigger('update-basket-small', response);
                                Popup.hide();
                            });
                        });
                    }
                });
            });
        });
    },
    initShowMoreBtn: function () {
        var _self = this;
        var container = $('.' + this.options.container);
        $('.js-show-more').on('click', function (e) {
            e.preventDefault();

            $(this).off('click');

            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    AJAX_MODE: 'Y',
                    SHOW_MORE: 'Y',
                    bxajaxid: _self.options.ajax_key,
                },
                success: function (data) {
                    var content = $('<div>' + data + '</div>').find('.' + _self.options.container);

                    var itemsContainer = container.find('.js-products-slider');
                    itemsContainer.empty().html(content.find('.js-products-slider').html());

                    if (itemsContainer.is('.slick-initialized')) {
                        itemsContainer.slick('refresh');
                        setTimeout(function() {
                            itemsContainer.slick('slickGoTo', 4);
                        }, 1000);
                        
                    }

                    container.find('.show-more').replaceWith('<div class="spacer--3"></div>');

                    _self.initAll();
                }
            });
        });
    },
}