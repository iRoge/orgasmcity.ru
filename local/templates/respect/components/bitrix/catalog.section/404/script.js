$(function () {
    $('.js-add-to-basket', grid).on('click', function (e) {
        e.preventDefault();

        var btn = $(this);
        btn.addClass('shortcut--active');

        $.ajax({
            method: 'get',
            url: '/local/ajax/sendFeedback.php?' + data,
            data: {},
            success: function (response) {
                response = JSON.parse(response);
                if (response.STATUS && response.STATUS == 'OK') {
                    btn.addClass('shortcut--active');
                } else {
                    btn.removeClass('shortcut--active');
                }
                $(document).trigger('update-basket-small', response);
            },
        });
    });
});