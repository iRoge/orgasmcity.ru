$(function () {
    $('.js-add-to-basket', grid).on('click', function (e) {
        e.preventDefault();

        var btn = $(this);
        btn.addClass('shortcut--active');

        BX.ajax.loadJSON(btn.attr('href'), {ajax_basket: 'Y'}, function (response) {
            if (response.STATUS && response.STATUS == 'OK') {
                btn.addClass('shortcut--active');
            } else {
                btn.removeClass('shortcut--active');
            }

            $(document).trigger('update-basket-small', response);
        });
    });
});