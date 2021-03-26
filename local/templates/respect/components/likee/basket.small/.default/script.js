$(function () {
    $(document).on('update-basket-small', function () {
        $.ajax(BX.message('BASKET_SMALL_AJAX_PATH'), {
            dataType: 'json',
            success: function (response) {
                var basket = $('#basket-small');

                if (basket.find('.shortcut-informer').length == 0) {
                    basket.append('<span class="shortcut-informer count"></span>');
                }

                if (response.COUNT && response.COUNT > 0) {
                    basket.find('.shortcut-informer').text(response.COUNT).show();
                } else {
                    basket.find('.shortcut-informer').text(0).hide();
                }

                $(document).trigger('basket-small-data', response);
            }
        });
    });
});
