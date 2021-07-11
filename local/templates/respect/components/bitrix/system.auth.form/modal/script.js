$(document).ready(function () {
    phoneMaskCreate($("#AUTH_PHONE"), false);

    $('form#auth-form').submit(function (e) {
        e.preventDefault();

        let phoneElem = $("#AUTH_PHONE");
        let emailElem = $("#AUTH_EMAIL");

        var cou_err = '';
        var text_html = "";
        var userEmail = emailElem.val().trim();
        var userPhone = phoneElem.val().trim();
        var errSpan = $('.err-phone-email');

        if (userEmail == "" && userPhone == "") {
            errSpan.html('Необходимо ввести email или телефон');
            cou_err = 'all-error';
            emailElem.addClass("red_border");
            phoneElem.addClass("red_border");
        } else if (userEmail != "") {
            if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(emailElem.val().trim())) {
                errSpan.html('Неверный E-Mail');
                emailElem.addClass("red_border");
                cou_err = 'email-error';
            } else {
                emailElem.removeClass("red_border");
                errSpan.html('');
            }
            phoneElem.removeClass("red_border");
        } else if (userPhone != "") {
            var inputPhoneValue = phoneElem.val().replace(/\D+/g, '');
            if (inputPhoneValue.length - 1 < 10 && phoneElem.val().trim() != "") {
                errSpan.html('Неверно заполнено поле Телефон');
                phoneElem.addClass("red_border");
                cou_err = 'phone-error';
            } else {
                phoneElem.removeClass("red_border");
                errSpan.html('');
            }
            emailElem.removeClass("red_border");
        } else {
            errSpan.html('');
        }

        let passELem = $("#AUTH_PASSWORD");
        let errPass = $('.err-pass');

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

        $("#after-auth-in-err").html(text_html);
        if (cou_err !== '') {
            return false;
        }
        let flag = ajaxAuthPhoneCheck();
        if (flag.errorText != null) {
            $("#after-auth-in-err").html(flag.errorText);
            if (flag.event === 'phone') {
                phoneElem.addClass("red_border");
                passELem.addClass("red_border");
            } else if (flag.event === 'email') {
                emailElem.addClass("red_border");
                passELem.addClass("red_border");
            }
            return false;
        } else {
            document.location.reload();
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
            'email': $("#AUTH_EMAIL").val(),
            'phone': $("#AUTH_PHONE").val(),
            'password': $("#AUTH_PASSWORD").val(),
        },
        success: function (data) {
            var parsed = JSON.parse(data);
            if (parsed.loginError != null) {
                flag.errorText = parsed.loginError;
                flag.event = parsed.event2;
            }
        },

        error: function (data) {
            console.log(data);
            $("#after-auth-in-err").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}
