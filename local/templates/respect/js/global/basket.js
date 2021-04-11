(function (w, $) {
    var LikeeBasket = function () {
        this.basket = [];
    };

    LikeeBasket.prototype = {
        btnClass: '.js-add-to-favorites',
        path: '/catalog/basket/#ID#/',

        init: function () {
            var that = this;

            $.get(that.path.replace('#ID#', 'get'), function (response) {
                if (response.STATUS == 'OK') {
                    that.favorites = response.FAVORITES;
                }

                that.updateBtn();
                that.updateCount();
            }, 'json');

            $(document).on('update-favorites-btn', function () {
                that.updateBtn();
                that.updateCount();
            });
        },

        updateBtn: function () {
            var that = this;

            $(function () {
                $(that.btnClass).each(function () {
                    var btn = $(this),
                        id = btn.data('id');

                    if ($.inArray(id, that.favorites) === -1) {
                        btn.attr('title', 'Добавить в избранное').removeClass('shortcut--active');
                        if (btn.hasClass('js-favorites-filled'))
                            btn.find('.icon').addClass('icon-heart').addClass('icon-heart-filled');
                    } else {
                        btn.attr('title', 'Удалить из избранного').addClass('shortcut--active');
                        if (btn.hasClass('js-favorites-filled'))
                            btn.find('.icon').addClass('icon-heart-filled').removeClass('icon-heart');
                    }
                });
            });
        },

        updateCount: function () {
            var box = $('#favorites-link');

            if (box.find('.shortcut-informer').length == 0) {
                box.append('<span class="shortcut-informer"></span>');
            }

            if (this.favorites.length > 0) {
                box.find('.shortcut-informer').text(this.favorites.length).show();
            } else {
                box.find('.shortcut-informer').hide();
            }

        },

        btnClick: function (btn) {
            var that = this,
                id = parseInt(btn.data('id'));

            if (id > 0) {
                $.get(that.path.replace('#ID#', id + ''), function (response) {
                    if (response.STATUS == 'OK') {
                        that.favorites = response.FAVORITES || [];
                        
                        if (document.location.pathname == '/catalog/favorites/') {
                            $(document).trigger('catalog-load');

                            var data = $('.filters .form').serializeArray();

                            $.get(document.location, data, function (response) {
                                $('.js-catalog-section').html(response);
                                $(document).trigger('catalog-init');
                                that.updateBtn();
                                that.updateCount();
                            });
                        }
                    }

                    that.updateBtn();
                    that.updateCount();
                });
            }
        }
    };

    w.LikeeFavorites = new LikeeBasket();
})(window, jQuery);

LikeeFavorites.init();