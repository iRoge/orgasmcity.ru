$(function () {
    var form = $('.js-password');

    form.on('submit', function (e) {
        e.preventDefault();

        var th = $(this),
            data = th.serializeArray(),
            button = th.find('button[type=submit]');

        data.push({
            name: 'save',
            value: 'Y'
        });

        if($("[name=NEW_PASSWORD]", form).val() !== $("[name=NEW_PASSWORD_CONFIRM]", form).val()) {
            $('input', form).each(function () {
                $(this).addClass('error-block__number');
            });
        } else {
            $.post(form.attr('action'), data, function () {
                button
                    .addClass('button--muted')
                    .text('Пароль изменен')
                    .attr('disabled', 'disabled');
            })
        }
    });

    $('input', form).on('change input', function (e) {
        $('input', form).each(function () {
            $(this).removeClass('error-block__number');
        });
    });
})