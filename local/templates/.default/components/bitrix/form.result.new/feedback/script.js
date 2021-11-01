$(function() {

    $(document).on( "click", ".cls-mail-div",function() {
        $('.podlozhka').hide(0);
        $('.mail-div').hide(0);
        $('.auth-div-full').hide(0);
        $('.popup').hide(0);
        $('body').removeClass('with--popup');
    });

    $(document).on( "click", 'form[name="SIMPLE_FORM_1"] #reloadCaptcha', function() {
        $.getJSON('/local/ajax/reload_captcha.php', function(data) {
            $('form[name="SIMPLE_FORM_1"] #captchaImg').attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
            $('form[name="SIMPLE_FORM_1"] #captchaSid').val(data);
        });
        return false;
    });

    $(document).on( "click", ".button--outline", function() {
        $('.podlozhka').hide(0);
        $('.mail-div').hide(0);
        $('.auth-div-full').hide(0);
        $('.popup').hide(0);
        $('body').removeClass('with--popup');
    });

    $(document).on("keyup", '[name=form_text_1]', function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });

    $(document).on("keyup", '[name=form_text_1]', function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });

    $(document).on("keyup", '[name=form_text_1]', function() {
        var $th = $(this);
        $th.val($th.val().replace(/[^ёЁа-яА-Яa-zA-Z\-'` ]/g, ''));
    });

    $(document).on("change", ".file-upload input[type=file]", function() {
        var filename = $(this).val().replace(/.*\\/, "");
        $("#filename").html(filename);
        $('.file-upload').css('background', '#034078');
        $('.file-upload').css('color', '#fff');
    });

    $(document).on('click', '.js-feedback-btn', function (e) {
        var arr = {
            "form_textarea_4":"*Сообщение",
            "form_text_2":"*Тема сообщения",
            "form_email_1":"*Ваш email",
            "form_text_3":"*Ваше имя",
        };

        var cou_err = 0;
        var text_html = "";
        $.each(arr, function(key,value) {
            if ($("[name='" + key + "']").val().trim() == "" && ($("[name='" + key + "']").hasClass("required") || key == 'form_text_3') ) {
                cou_err++;
                if ($("[name='" + key + "']").attr('placeholder') != "" && $("[name='" + key + "']").attr('placeholder') != undefined) {
                    value = $("[name='" + key + "']").attr('placeholder');
                }
                text_html += "<p>Необходимо заполнить поле " + value + "</p>";
                $("[name='" + key + "']").addClass("red_border");

            } else {
                $("[name='" + key + "']").removeClass("red_border");
            }
        });
        if (document.getElementById("feedback-file").files[0] !== undefined) {
            var validExpansions = ['doc', 'docx', 'xls', 'xlsx', 'jpeg', 'jpg', 'pdf', 'ppt', 'pptx'];
            var file = document.getElementById("feedback-file").files[0];
            var type = file.name.split('.').pop();
            var size = file.size;
            if (validExpansions.indexOf(type) == -1 || size >= 5 * 1024 * 1024) {
                cou_err++;
                document.getElementById("feedback-file").value = "";
                $('#filename').html("Прикрепить файл");
                $('#feedback-file-div').removeAttr("style").addClass("red_border");
                text_html += "<p>Файл не соответствует требованиям: " +
                "<span>Размер файла не более 5 мб; допустимые форматы: doc, docx, xls, xlsx, jpeg, jpg, pdf, ppt, pptx</span></p>";
            } else {
                $('#feedback-file-div').removeClass("red_border");
            }
        } else {
            $('#feedback-file-div').removeClass("red_border");
        }

        // if (!($("#feedback_checkbox_policy_checked").prop('checked'))) {
        //     text_html += "<p>Необходимо согласие с политикой конфиденциальности</p>";
        //     cou_err++;
        // }

        $("#after-feedback-in-err").html(text_html);
        if (cou_err > 0) {
            return false;
        }
    });
});
