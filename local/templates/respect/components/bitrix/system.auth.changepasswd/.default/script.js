$(function() {
    $("#change_pwd").on('click', function (e) {
        e.preventDefault();
        var cou_err = 0;
        var text_html = "";
        if ($("#forgotten_password").val().trim().length < 6) {
            text_html += "<p>Пароль должен быть не менее 6 символов длиной</p>";
            $("#forgotten_password").addClass("red_border");
            cou_err++;
        }
        if ($("#forgotten_confirm").val().trim().length < 6) {
            text_html += "<p>Подтверждение пароля должно быть не менее 6 символов длиной</p>";
            $("#forgotten_confirm").addClass("red_border");
            cou_err++;
        }
        if ($("#forgotten_password").val() != $("#forgotten_confirm").val()) {
            text_html += "<p>Пароли должны быть одинаковыми</p>";
            $("#forgotten_password").addClass("red_border");
            $("#forgotten_confirm").addClass("red_border");
            cou_err++;
        } else if ($("#forgotten_password").val().trim().length >= 6 && $("#forgotten_confirm").val().trim().length >= 6) {
            $("#forgotten_password").removeClass("red_border");
            $("#forgotten_confirm").removeClass("red_border");
        }
        $("#forgotten_error").html(text_html);
        if (cou_err > 0) {
            return;
        }
        var data = $('.js-password__input').serializeArray();
        data.push({name: 'AUTH_FORM', value: 'Y'});
        data.push({name: 'TYPE', value: 'CHANGE_PWD'});
        data.push({name: 'change_pwd', value: 'Y'});
        data.push({name: 'USER_LOGIN', value: $("[name='USER_LOGIN']").val()});
        data.push({name: 'USER_CHECKWORD', value: $("[name='USER_CHECKWORD']").val()});
        $("#change_pwd").attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: document.location.href, 
            data: data,
            success: function(data) {
                var error = $('<div>'+data+'</div>').find(".error-hint");
                if(error.length > 0) {
                    error = error.html().trim();
                } else {
                    error = "";
                }
                if(error == "" || error == "Пароль успешно сменен.<br>На ваш EMail высланы новые регистрационные данные.<br>") {
                    $("#change_pwd").addClass('button--muted').text('Пароль изменен')
                    $('[name="bform"]').submit();
                } else {
                    $("#forgotten_error").html("<p>"+(error.split("<br>").join("</p><p>"))+"</p>");
                    $("#change_pwd").removeAttr('disabled');
                }
            },
        });
    });
})