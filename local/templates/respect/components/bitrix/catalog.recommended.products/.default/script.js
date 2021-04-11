$(function () {
    $('.products-item__content').each(function () {
        var grid = $(this);

        $('.js-add-to-favorites', grid).on('click', function (e) {
            e.preventDefault();
            var btn = $(this);
            LikeeAjax.btnClick(btn, btn.data('id'));
        });

        $('.js-add-to-basket', grid).on('click', function (e) {
            e.preventDefault();

            var btn = $(this);

            $.post(btn.attr('href'), {ajax_basket: 'Y'}, function (response) {
                if (response.STATUS && response.STATUS == 'OK') {
                    //btn.addClass();
                }
            }, 'json');
        });
    });
});