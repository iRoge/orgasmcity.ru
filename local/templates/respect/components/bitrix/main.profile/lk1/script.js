$(document).ready(function () {
    $("#PERSONAL_BIRTHDAY").mask("99.99.9999", {placeholder: "дд.мм.гггг"});

    $("#PERSONAL_BIRTHDAY").mouseover(function () {
        $("#PERSONAL_BIRTHDAY").attr('placeholder', 'дд.мм.гггг');
    });

    $("#PERSONAL_BIRTHDAY").mouseout(function () {
        $("#PERSONAL_BIRTHDAY").attr('placeholder', 'Дата рождения');
    });

    phoneMaskCreate($("input.profile_phone"), false);

    var checkChar = function () {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    };

    $('input.fio').keyup(checkChar).keydown(checkChar).change(checkChar);

    $('.form-in-after-lk-sub').on('click', function (e) {
        var cou_err = 0;

        let birthElem = $("#PERSONAL_BIRTHDAY");
        if (birthElem.length > 0) {
            let errBirthElem = $("#err-personal-birthday");
            if (birthElem.val() !== "" && birthElem.val() !== undefined) {
                if (!/^(?:(?:31(\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test(birthElem.val())) {
                    birthElem.addClass("red_border");
                    errBirthElem.text("* Неверная дата рождения").addClass('actual');
                    cou_err++;
                } else {
                    var dateCalendar = birthElem.val().replace(/^(\d+)\.(\d+)\.(\d+)/, '$3-$2-$1');
                    if (Date.parse(dateCalendar) > Date.now()) {
                        birthElem.addClass("red_border");
                        errBirthElem.text("* Дата рождения не может быть больше текущего момента").addClass('actual');
                        cou_err++;
                    } else {
                        var year = parseInt(birthElem.val().replace(/^\d+\.\d+\.(\d+)/, '$1'));
                        if (year < 1900) {
                            birthElem.addClass("red_border");
                            errBirthElem.text("* Год рождения не может быть меньше 1900").addClass('actual');
                            cou_err++;
                        } else {
                            birthElem.removeClass("red_border");
                            errBirthElem.text("").removeClass('actual');
                        }
                    }
                }
            }
        }

        let phoneElem = $('#PERSONAL_PHONE');
        let emailElem = $('#EMAIL');
        let inputPhoneValue = '';
        let phoneEmpty = false;
        let emailEmpty = false;
        let errEmailPhone = $('#err-personal-email-phone');
        let errPhoneElem = $('#err-personal-phone');
        var flag = ajaxFieldsCheck();

        if (phoneElem.length > 0) {

            inputPhoneValue = phoneElem.val().replace(/\D+/g, '');
            if (phoneElem.val().trim() !== '') {
                if (inputPhoneValue.length - 1 < 10 && phoneElem.val().trim() !== "") {
                    phoneElem.addClass("red_border");
                    errPhoneElem.text('* Неверно заполнено поле Телефон').addClass('actual');
                    cou_err++;
                } else if (flag.phoneUsed !== '') {
                    phoneElem.addClass('red_border');
                    errPhoneElem.text(flag.phoneUsed).addClass('actual');
                    cou_err++;
                } else {
                    phoneElem.removeClass("red_border");
                    errPhoneElem.text('').removeClass('actual');
                }
            } else if (flag.userPhone !== '') {
                phoneElem.val(flag.userPhone).removeClass("red_border");
                errPhoneElem.text('* Было введено некорректное значение').addClass('actual');
                cou_err++;
            } else {
                phoneEmpty = true;
                phoneElem.removeClass("red_border");
                errPhoneElem.text('').removeClass('actual');
            }
        }

        if (emailElem.length > 0) {
            if (emailElem.val().trim() !== "") {
                if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(emailElem.val().trim())) {
                    emailElem.addClass("red_border");
                    errEmailPhone.text('* Некорректный E-Mail').addClass('actual');
                    cou_err++;
                } else if (flag.emailUsed !== '') {
                    emailElem.addClass("red_border");
                    errEmailPhone.text(flag.emailUsed).addClass('actual');
                    cou_err++;
                } else {
                    emailElem.removeClass("red_border");
                    errEmailPhone.text('').removeClass('actual');
                }
            } else if (flag.userEmail !== '') {
                emailElem.val(flag.userEmail).removeClass("red_border");
                errEmailPhone.text('* Было введено некорректное значение').addClass('actual');
                cou_err++;
            } else {
                emailEmpty = true;
                emailElem.removeClass("red_border");
                errEmailPhone.text('').removeClass('actual');
            }
        }

        if (phoneEmpty === true && emailEmpty === true) {
            phoneElem.addClass("red_border");
            emailElem.addClass('red_border');
            errEmailPhone.text('* Должно быть заполненно хотя бы одно поле').addClass('actual');
            errPhoneElem.text('').removeClass('actual');
            cou_err++;
        }

        let nameElem = $('input.imya');
        let errNameElem = $('#err-personal-name');

        if (nameElem.val() === '' && flag.userName !== ''){
            nameElem.val(flag.userName);
            errNameElem.text('* Было введено некорректное значение').addClass('actual');
            cou_err++;
        }
        let passElem = $('#personal-pass');
        let errPassElem = $('#err-personal-pass');
        let passConfElem = $('#personal-pass-conf');

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

        if (cou_err > 0) {
            return false;
        } else {
            if (emailElem.length > 0 && emailElem.val().trim() === '') {
                emailElem.css('color', 'white');
                emailElem.val('no@email.rr');
            }
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
        'userName': '',
        'userEmail': '',
        'userPhone': '',
    };
    $.ajax({
        type: 'post',
        async: false,
        url: '/local/ajax/registration_fields_check.php',
        data: {
            'id': $('#userId').val(),
            'email': $("#EMAIL").val(),
            'personal_phone': $('#PERSONAL_PHONE').val()
        },
        success: function (data) {
            var parsed = JSON.parse(data);
            if (parsed.email === 1) {
                flag.emailUsed = '* Пользователь с таким e-mail уже существует';
                flag.countError++;
            } else if (parsed.email !== "noRemoveClass") {
                flag.emailRedBorder = 0;
            }

            if (parsed.phone === 1) {
                flag.phoneUsed = '* Данный номер телефона уже используется';
                flag.countError++;
            } else if (parsed.phone !== "noRemoveClass") {
                flag.phoneRedBorder = 0;
            }

            flag.userName = parsed.userName;
            if (!/\d+@rshoes\.ru$/.test(parsed.userEmail)) {
                flag.userEmail = parsed.userEmail;
            }
            flag.userPhone = parsed.userPhone;
        },
        error: function () {
            $("#after-cart-in-err").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}