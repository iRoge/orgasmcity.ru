$(document).ready(function () {
    phoneMaskCreate($("input.registration_phone"));

    var charCheckFunc = function () {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    };

    $('#REGISTER\\[NAME\\]-reg-form-popup').keyup(charCheckFunc).keydown(charCheckFunc).change(charCheckFunc);

    $('form#reg-form-popup').submit(function (e) {
        var arr = {
            "REGISTER[NAME]": "Необходимо заполнить поле Имя",
            "REGISTER[EMAIL]": "Необходимо заполнить поле E-mail",
            "REGISTER[PERSONAL_PHONE]": "Необходимо заполнить поле Телефон",
            "REGISTER[PASSWORD]": "Необходимо заполнить поле Пароль",
            "captcha_word": "Необходимо ввести код с картинки"
        };
        var cou_err = 0;
        $.each(arr, function (key, value) {
            let elem = $("[id='" + key + "-reg-form-popup']");
            let errElem = $("[id='err-" + key + "-reg-form-popup']")
            if (elem.val().trim() === "" && (elem.hasClass("required") || key === "DATE_CALENDAR" || key === "captcha_word")) {
                cou_err++;
                if (value === "" && elem.attr('placeholder') !== "" && elem.attr('placeholder') !== undefined) {
                    value = elem.attr('placeholder').replace(/\*/g, '');
                }
                errElem.text('* ' + value).addClass('actual');
                elem.addClass('red_border');
            } else {
                errElem.text('').removeClass('actual');
                elem.removeClass('red_border');
            }
        });

        let passElem = $('#REGISTER\\[PASSWORD\\]-reg-form-popup');
        let passConfElem = $('#REGISTER\\[CONFIRM_PASSWORD\\]-reg-form-popup');
        let errPassElem = $("#err-REGISTER\\[PASSWORD\\]-reg-form-popup");
        if (passElem.val().trim() !== '' && passElem.val().trim().length < 6) {
            errPassElem.text("* Пароль должен быть не менее 6 символов длиной").addClass('actual');
            passElem.addClass("red_border");
            cou_err++;
        }

        if (passElem.val() !== passConfElem.val()) {
            errPassElem.text('* Пароли должны быть одинаковыми').addClass('actual');
            passElem.addClass("red_border");
            passConfElem.addClass("red_border");
            cou_err++;
        } else if (passElem.val().trim().length >= 6 && passConfElem.val().trim().length >= 6) {
            errPassElem.text('').removeClass('actual');
            passElem.removeClass("red_border");
            passConfElem.removeClass("red_border");
        }

        var flag = ajaxFieldsCheck();
        cou_err += flag.countError;
        let emailElem = $("#REGISTER\\[EMAIL\\]-reg-form-popup");
        let errEmailElem = $("#err-REGISTER\\[EMAIL\\]-reg-form-popup");
        if (emailElem.val().trim() !== "") {
            if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(emailElem.val().trim())) {
                emailElem.addClass("red_border");
                errEmailElem.text('* Некорректный E-Mail').addClass('actual');
                cou_err++;
            } else if (flag.emailUsed !== '') {
                emailElem.addClass("red_border");
                errEmailElem.text(flag.emailUsed).addClass('actual');
            } else {
                emailElem.removeClass("red_border");
                errEmailElem.text('').removeClass('actual');
            }
        }

        let phoneElem = $("#REGISTER\\[PERSONAL_PHONE\\]-reg-form-popup");
        let errPhoneElem = $("#err-REGISTER\\[PERSONAL_PHONE\\]-reg-form-popup");
        var inputPhoneValue = phoneElem.val().replace(/\D+/g, '');
        if (phoneElem.val().trim() !== '') {
            if (inputPhoneValue.length - 1 < 10 && phoneElem.val().trim() !== "") {
                phoneElem.addClass("red_border");
                errPhoneElem.text('* Неверно заполнено поле Телефон').addClass('actual');
                cou_err++;
            } else if (flag.phoneUsed !== '') {
                phoneElem.addClass('red_border');
                errPhoneElem.text(flag.phoneUsed).addClass('actual');
            } else {
                phoneElem.removeClass("red_border");
                errPhoneElem.text('').removeClass('actual');
            }
        }

        if (!($("#regform_checked-reg-form-popup").prop('checked'))) {
            $('#err-conf-reg-form-popup').text('* Необходимо согласие с политикой конфиденциальности').addClass('actual');
            cou_err++;
        } else {
            $('#err-conf-reg-form-popup').text('').removeClass('actual');
        }

        if (cou_err > 0) {
            let userScrollTop = $(window).scrollTop();
            let errorScrollTop = $('.error-field.actual:first').offset().top - 100;
            if (userScrollTop > errorScrollTop) {
                $('html, body').animate({
                    scrollTop: errorScrollTop
                }, 1000);
            }
            return false;
        }
    });
});

function ajaxFieldsCheck() {
    var flag = {
        'countError': 0,
        'textError': '',
        'emailUsed': '',
        'emailRedBorder': 1,
        'phoneUsed': '',
        'phoneRedBorder': 1,
    };
    $.ajax({
        type: 'post',
        async: false,
        url: '/local/ajax/registration_fields_check.php',
        data: {
            'captcha_word': $("#captcha_word-reg-form-popup").val(),
            'captcha_sid': $("#captchaSid-reg-form-popup").val(),
            'email': $("#REGISTER\\[EMAIL\\]-reg-form-popup").val(),
            'personal_phone': $("#REGISTER\\[PERSONAL_PHONE\\]-reg-form-popup").val()
        },
        success: function (data) {
            var parsed = JSON.parse(data);
            if (parsed.email === 1) {
                let emailElem = $("#REGISTER\\[EMAIL\\]-reg-form-popup");
                flag.emailUsed = '* Пользователь с таким e-mail уже существует';
                flag.countError++;
            } else if (parsed.email !== "noRemoveClass") {
                flag.emailRedBorder = 0;
            }

            if (parsed.phone === 1) {
                let phoneElem = $("#REGISTER\\[PERSONAL_PHONE\\]-reg-form-popup");
                flag.phoneUsed = '* Данный номер телефона уже используется';
                flag.countError++;
            } else if (parsed.phone !== "noRemoveClass") {
                flag.phoneRedBorder = 0;
            }

            if (parsed.captcha === 1) {
                flag.textError += '<p>Неверно введено слово с картинки</p>';
                $("#captcha_word-reg-form-popup").addClass("red_border");
                $("#err-captcha_word-reg-form-popup").text('* Неверно введено слово с картинки').addClass('actual');
                flag.countError++;
            } else if (parsed.captcha !== "noRemoveClass") {
                $("#captcha_word-reg-form-popup").removeClass("red_border");
                $('#err-captcha_word-reg-form-popup').text('').removeClass('actual');
            }
        },
        error: function () {
            $("#after-cart-in-err").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}