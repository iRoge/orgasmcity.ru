$(function () {
    $('.js-password-btn').on('click', function (e) {
        e.preventDefault();
        $('.js-password-input').each(function () {
            $(this).prop('disabled', !$(this).prop('disabled'));
        });
    });
});