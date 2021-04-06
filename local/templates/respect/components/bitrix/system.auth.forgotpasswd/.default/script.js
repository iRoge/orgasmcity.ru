$(document).ready(function () {
    $("#phone").mask("+7 (999) 999-99-99", {autoclear: false});

    $("#phone").click(function(){
        if ($("#phone").val() == "+7 (___) ___-__-__") {
            $(this)[0].selectionStart = 4;
            $(this)[0].selectionEnd = 4;
        }
    });

    $("#phone").mouseover(function () {
        $("#phone").attr('placeholder', '+7 (___) ___-__-__');
    });

    $("#phone").mouseout(function () {
        $("#phone").attr('placeholder', 'Телефон');
    });

    phoneMaskCreate($('#phone'));
    $('#forgot_password_send').on('click', function() {
        var status = 0;
        var phone = $('#phone').val().trim();
        var email = $('#email').val().trim();

        $('#phone, #email').removeClass('red_border');

        if (!phone && !email) {
            $('#phone, #email').addClass('red_border');
            $('#forgot_password_error').html('<p>Заполните одно из полей</p>');
            return false;
        } else {
            if (phone && phone.replace(/\D+/g, '').length != 11) {
                status += 1;
                phone = '';
            }

            if (email && !/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email)) {
                status += 2;
                email = '';
            }
        }

        var selector = [];
        var errorMessage = '';

        if (status == 1 && !email || status == 3) {
            errorMessage += '<p>Неверно заполнено поле Телефон</p>';
            selector.push('#phone');
        }

        if (status == 2 && !phone || status == 3) {
            errorMessage += '<p>Неверно заполнено поле E-Mail</p>';
            selector.push('#email');
        }

        if (selector.length) {
            $(selector.join(', ')).addClass('red_border');
            $('#forgot_password_error').html(errorMessage);
            return false;
        }

        var handler = handleFields(phone, email);

        if (handler.status == 1 || handler.status == 3) {
            selector.push('#phone');
        }

        if (handler.status == 2 || handler.status == 3) {
            selector.push('#email');
        }

        if (handler.status > 0) {
            if (selector.length) {
                $(selector.join(', ')).addClass('red_border');
            }

            $('#forgot_password_error').html(handler.error);

            return false;
        }

        $('#forgot_password_error').html('');

        if (handler.case == "by_phone") {
            $('#phone').attr('disabled', true);
            $('#email').attr('disabled', true);
            $('#forgot_password_send').attr('disabled', true);
            $('#sms_code, #forgot_password_sms_send').css('display', 'inline-block');
            setTimeout(enableInput, 1000 * 60);
            return false;
        }
    });

    $("#forgot_password_sms_send").on('click', function() {
        var text_html = "";
        var flag = checkSMSCode();
        text_html += flag.textError;
        
        $("#forgot_password_error").html(text_html);
        return false;
    });
})

function handleFields(phone, email) {
    var flag = {
        'status': 0,
        'error': '',
        'case': '',
    };

    $.ajax({
        type: 'post',
        async: false,
        url: '/local/ajax/forgot_password_handler.php',
        data: {
            'phone': phone,
            'email': email,
        },
        success:function(data) {
            var parsed = JSON.parse(data);
            flag.case = parsed.method;
            switch (parsed.method) {
                case '':
                    if (parsed.status.includes('phone_not_found')) {
                        flag.error += '<p>Телефон не найден</p>';
                        flag.status += 1;
                    }

                    if (parsed.status.includes('email_not_found')) {
                        flag.error += '<p>E-mail не найден</p>';
                        flag.status += 2;
                    }

                    break;

                case 'by_email':
                    // Восстановление пароля по email
                    break;

                case 'by_phone':
                    if (parsed.status == 'ok') {
                        break;
                    }

                    if (parsed.status == 'sms_quantity_error') {
                        flag.error += '<p>Превышен лимит попыток восстановления пароля по номеру телефона на сегодня — 5</p>';
                        flag.status += 4;
                        break;
                    } 
                    
                    flag.error += '<p>Ошибка отправления SMS-кода</p>';
                    flag.status += 4;
                    break;
            }
        },
        error:function() {
            $("#forgot_password_error").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });

    return flag;
}

function checkSMSCode() {
    var flag = {
        'textError': '',
    };
    $.ajax({
        type:'post',
        async:false,
        url:'/local/ajax/check_sms_code.php',
        data:{
            'code': $("#sms_code").val(),
            'phone': $("#phone").val(),
        },
        success:function(data) {
            var parsed = JSON.parse(data);
            if (parsed.link === 1) {
                flag.textError += '<p>Неверно введен уникальный код</p>';
                $("#sms_code").addClass("red_border");
            } else if (parsed.link === 'numberOfInputAttemptsError') {
                flag.textError += '<p>Превышен лимит попыток по вводу кода. Необходимо снова отправить код на телефон</p>';
                $("#sms_code").val('');
                $("#sms_code").removeClass("red_border");
                $("#sms_code, #forgot_password_sms_send").css('display', 'none');
                enableInput();
            } else {
                window.location.replace('/auth/?' + parsed.link);
            }
        },
        error:function() {
            $("#forgot_password_error").html('<p>Ошибка соединения, попробуйте обновить страницу</p>');
        }
    });
    return flag;
}

function enableInput() {
    $("#phone").removeAttr('disabled');
    $("#email").removeAttr('disabled');
    $("#forgot_password_send").removeAttr('disabled');
}