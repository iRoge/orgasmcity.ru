$(function () {
    $('.b-recommended-product').each(function () {
        var component = $(this);

        $('.js-add-to-basket', component).on('click', function (e) {
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

        $('.js-add-to-favorites', component).on('click', function (e) {
            e.preventDefault();
            LikeeAjax.btnClick($(this));
        });
    });
});