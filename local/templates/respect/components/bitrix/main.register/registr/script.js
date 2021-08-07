$(document).ready(function () {
    let dateElem = $("#DATE_CALENDAR");
    dateElem.mask("99.99.9999", {placeholder: "дд.мм.гггг"});

    dateElem.mouseover(function () {
        dateElem.attr('placeholder', 'дд.мм.гггг');
    });

    dateElem.mouseout(function () {
        dateElem.attr('placeholder', '*Дата рождения');
    });

    $('form[name=regform]').on('click', function () {
        var date_val = $(this).parent().parent().find('.reg_date').val();
        $(this).parent().parent().find('.reg_date_value').val(date_val);
    });

    var charCheckFunc = function () {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    };

    $('input.fio').keyup(charCheckFunc).keydown(charCheckFunc).change(charCheckFunc);

    $('form#reg-form, form#reg-form2').submit(function (e) {
        var arr = {
            "REGISTER[NAME]": "Необходимо заполнить поле Имя",
            "REGISTER[LAST_NAME]": "Необходимо заполнить поле Фамилия",
            "REGISTER[EMAIL]": "Необходимо заполнить поле E-mail",
            "REGISTER[PERSONAL_PHONE]": "Необходимо заполнить поле Телефон",
            "REGISTER[PASSWORD]": "Необходимо заполнить поле Пароль",
            "DATE_CALENDAR": "Необходимо заполнить поле Дата рождения",
            "captcha_word": "Необходимо ввести код с картинки"
        };
        var cou_err = 0;
        $.each(arr, function (key, value) {
            let elem = $("[id='" + key + "']");
            let errElem = $("[id='err-" + key + "']")
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

        let persBirthElem = $("#REGISTER\\[PERSONAL_BIRTHDAY\\]");
        let dateCalElem = $("#DATE_CALENDAR");
        let errDateCalElem = $("#err-DATE_CALENDAR");
        if (persBirthElem.val() !== "" && persBirthElem.val() !== undefined) {
            if (!/^(?:(?:31(\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test(persBirthElem.val())) {
                errDateCalElem.text('* Неверная дата рождения').addClass('actual');
                dateCalElem.addClass("red_border");
                cou_err++;
            } else {
                var dateCalendar = persBirthElem.val().replace(/^(\d+)\.(\d+)\.(\d+)/, '$3-$2-$1');
                if (Date.parse(dateCalendar) > Date.now()) {
                    errDateCalElem.text('* Дата рождения не может быть больше текущего момента').addClass('actual');
                    dateCalElem.addClass("red_border");
                    cou_err++;
                } else {
                    errDateCalElem.text('').removeClass('actual');
                    dateCalElem.removeClass("red_border");
                }
            }
        }

        let passElem = $('#REGISTER\\[PASSWORD\\]');
        let passConfElem = $('#REGISTER\\[CONFIRM_PASSWORD\\]');
        let errPassElem = $("#err-REGISTER\\[PASSWORD\\]");
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
        let emailElem = $("#REGISTER\\[EMAIL\\]");
        let errEmailElem = $("#err-REGISTER\\[EMAIL\\]");
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

        if (!($("#regform_checked").prop('checked'))) {
            $('#err-conf').text('* Необходимо согласие с политикой конфиденциальности').addClass('actual');
            cou_err++;
        } else {
            $('#err-conf').text('').removeClass('actual');
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
            'captcha_word': $("#captcha_word").val(),
            'captcha_sid': $("#captchaSid").val(),
            'email': $("#REGISTER\\[EMAIL\\]").val(),
        },
        success: function (data) {
            var parsed = JSON.parse(data);
            if (parsed.email === 1) {
                let emailElem = $("#REGISTER\\[EMAIL\\]");
                flag.emailUsed = '* Пользователь с таким e-mail уже существует';
                flag.countError++;
            } else if (parsed.email !== "noRemoveClass") {
                flag.emailRedBorder = 0;
            }

            if (parsed.captcha === 1) {
                flag.textError += '<p>Неверно введено слово с картинки</p>';
                $("#captcha_word").addClass("red_border");
                $("#err-captcha_word").text('* Неверно введено слово с картинки').addClass('actual');
                flag.countError++;
            } else if (parsed.captcha !== "noRemoveClass") {
                $("#captcha_word").removeClass("red_border");
                $('#err-captcha_word').text('').removeClass('actual');
            }
        },
        error: function () {
            $("#after-cart-in-err").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}