$(document).ready(function () {
    $('.link_reg').on('click', function (e) {
        e.preventDefault();
        $('.auth-div-full').toggle(0);
        $('.podlozhka').toggle(0);
        $('#reg-form-popup').show();
        $('#vkl20').prop("checked", true);
    });

    $('form#auth-full-form').submit(function (e) {
        e.preventDefault();

        let emailElem = $("#AUTH_EMAIL_FULL");

        var cou_err = '';
        var text_html = "";
        var userEmail = emailElem.val().trim();
        var errSpan = $('.err-phone-email-full');

        if (userEmail == "") {
            errSpan.html('Необходимо ввести email или телефон');
            cou_err = 'all-error';
            emailElem.addClass("red_border");
        } else if (userEmail != "") {
            if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(emailElem.val().trim())) {
                errSpan.html('Неверный E-Mail');
                emailElem.addClass("red_border");
                cou_err = 'email-error';
            } else {
                emailElem.removeClass("red_border");
                errSpan.html('');
            }
        } else {
            errSpan.html('');
        }

        let passELem = $("#AUTH_PASSWORD_FULL");
        let errPass = $('.err-pass-full');

        if (passELem.val().trim().length === 0 && cou_err !== 'all-error') {
            errPass.html('Необходимо ввести пароль');
            passELem.addClass("red_border");
            cou_err = 'pass-error';
        } else if (passELem.val().trim().length < 6 && cou_err !== 'all-error') {
            errPass.html('Пароль должен быть больше 6 символов');
            passELem.addClass("red_border");
            cou_err = 'pass-error';
        } else {
            passELem.removeClass("red_border");
            errPass.html('');
        }

        $("#after-auth-in-err-full").html(text_html);
        if (cou_err !== '') {
            return false;
        }
        var flag = ajaxAuthPhoneCheck();
        if (flag.errorText != null) {
            $("#after-auth-in-err-full").html(flag.errorText);
             if (flag.event === 'email') {
                emailElem.addClass("red_border");
                passELem.addClass("red_border");
            }
            return false;
        } else {
            let getParams = (new URL(document.location)).searchParams;
            let url = getParams.get("back_url")
            if (url) {
                document.location = url;
            } else {
                document.location.reload();
            }
        }
    });
});

function ajaxAuthPhoneCheck() {
    var flag = {
        'errorText': null,
        'event': null,
    };
    $.ajax({
        type: 'post',
        async: false,
        url: '/local/ajax/auth_phone_check.php',
        data: {
            'email': $("#AUTH_EMAIL_FULL").val(),
            'password': $("#AUTH_PASSWORD_FULL").val(),
        },
        success: function (data) {
            var parsed = JSON.parse(data);
            if (parsed.loginError != null) {
                flag.errorText = parsed.loginError;
                flag.event = parsed.event2;
            }
        },
        error: function () {
            $("#after-auth-in-err-full").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}
