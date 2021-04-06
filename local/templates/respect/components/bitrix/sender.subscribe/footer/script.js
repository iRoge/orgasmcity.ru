$(function () {
    $('#subscribe-form').on('submit', function (e) {
        e.preventDefault();

        var form = $(this),
            data = form.serializeArray();

        $.post(form.attr('action'), data);
    });
});