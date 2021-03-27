$(function () {
    $('.bestsellers').each(function () {
        var component = $(this);

        $('.js-add-to-favorites', component).on('click', function (e) {
            e.preventDefault();
            LikeeAjax.btnClick($(this));
        });
    });
});