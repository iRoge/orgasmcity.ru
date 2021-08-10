<?php

$jsValidationFieldsListData = '';
?>

<?= $arResult["FORM_HEADER"] ?>

<h2><?= $arResult['CONTEST_FORM_TITLE'] ?></h2>

<? if (!empty($arResult['ACTION_ID'])) : ?>
    <input type="hidden" name="action_id" value="<?= $arResult['ACTION_ID'] ?>" />
<? endif; ?>
<? if (!empty($_REQUEST['btn_color'])) : ?>
    <input type="hidden" name="btn_color" value="<?= htmlspecialchars($_REQUEST['btn_color']) ?>" />
<? endif; ?>
<? if (!empty($_REQUEST['btn_text_color'])) : ?>
    <input type="hidden" name="btn_text_color" value="<?= htmlspecialchars($_REQUEST['btn_text_color']) ?>" />
<? endif; ?>

<?
foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
        if ($FIELD_SID == 'ACTION_ID') {
            print str_replace('value=""', 'value="' . $arResult['ACTION_ID'] . '"', $arQuestion["HTML_CODE"]);
        } elseif ($FIELD_SID == 'ACTION_NAME') {
            print str_replace('value=""', 'value="' . $arResult['ACTION_NAME'] . '"', $arQuestion["HTML_CODE"]);
        } else {
            print $arQuestion["HTML_CODE"];
        }
    }
}
?>
<div id="form_errors"></div>
<? if ($arResult["isFormErrors"] == "Y") : ?>
    <div class="alert alert--danger">
        <div class="alert-content">
            <i class="icon icon-exclamation-circle"></i><?= $arResult["FORM_ERRORS_TEXT"]; ?>
        </div>
    </div>
<? endif; ?>

<? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) : ?>
    <? if ($FIELD_SID == 'NAME' && $arResult['CONTEST_FIELDS_NAME']) : ?>
        <? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",'; ?>
        <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" required placeholder=" *' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
    <? elseif ($FIELD_SID == 'PHONE' && $arResult['CONTEST_FIELDS_PHONE']) : ?>
        <? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",'; ?>
        <? $jsPhoneSelector = '[name=\'form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '\']'; ?>
        <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" required placeholder=" *' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
    <? elseif ($FIELD_SID == 'EMAIL') : ?>
        <? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",'; ?>
        <? $jsEmailSelector = '[name=\'form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '\']'; ?>
        <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" required placeholder=" *' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
    <? elseif ($FIELD_SID == 'BIRTHDATE' && $arResult['CONTEST_FIELDS_BIRTHDATE']) : ?>
        <? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",'; ?>
        <? $jsBirthdateSelector = '[name=\'form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '\']'; ?>
        <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" required placeholder=" *' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
    <? elseif ($FIELD_SID == 'INSTAGRAM' && $arResult['CONTEST_FIELDS_INSTA']) : ?>
        <? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",'; ?>
        <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" required placeholder=" *' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
    <? elseif ($FIELD_SID == 'FORM_FILE' && $arResult['CONTEST_FIELDS_FILE_1']) : ?>
        <? $arResult['CONTEST_CHECK_FILE_1'] ? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",' : ''; ?>
        <div class="file-1-upload" id="form-file-1-div">
            <label>
                <input type="file" class="file-upload-1" name="form_file_<?= $arQuestion['STRUCTURE'][0]['FIELD_ID'] ?>" size="0" id="form-file-1"
                       placeholder="<?= ($arResult['CONTEST_CHECK_FILE_1'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_1'];?>"/>
                <span id="file_1_name"><?= ($arResult['CONTEST_CHECK_FILE_1'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_1'];?></span>
            </label>
        </div>
    <? elseif ($FIELD_SID == 'FORM_FILE_2' && $arResult['CONTEST_FIELDS_FILE_2']) :?>
        <? $arResult['CONTEST_CHECK_FILE_2'] ? $jsValidationFieldsListData .= '"form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . '":"",' : ''; ?>
        <div class="file-2-upload" id="form-file-2-div">
            <label>
                <input type="file" class="file-upload-2" name="form_file_<?= $arQuestion['STRUCTURE'][0]['FIELD_ID'] ?>" size="0" id="form-file-2"
                       placeholder="<?= ($arResult['CONTEST_CHECK_FILE_2'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_2'];?>"/>
                <span id="file_2_name"><?= ($arResult['CONTEST_CHECK_FILE_2'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_2'];?></span>
            </label>
        </div>
    <? elseif ($FIELD_SID != 'FORM_FILE' && $FIELD_SID != 'FORM_FILE_2'  && $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] != 'hidden') : ?>
        <?
        $hiddenInputField = $arQuestion["HTML_CODE"];
        $hiddenInputField = str_replace(array('inputtextarea"', 'inputtext"'), 'required" placeholder=" *' . $arQuestion["CAPTION"] . '"', $hiddenInputField);
        $hiddenInputField = str_replace('value=""', 'value="-"', $hiddenInputField);
        $hiddenInputField = preg_replace('/type="\w+"/', 'type="hidden"', $hiddenInputField);
        echo $hiddenInputField;
        ?>
    <? endif; ?>
<? endforeach; ?>

<div class="row captcha_block">
    <div class="col-md-6 captcha-code">
        <input id="captchaSid" type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHACode"] ?>" />
        <img id="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHACode"] ?>" width="180" height="40" alt="CAPTCHA" />
    </div>
    <div class="col-md-6">
        <a id="reloadCaptcha" class="reload-captcha">Обновить картинку</a>
    </div>
</div>
<div>
    <div class="captcha-word">
        <input type="text" class="required" name="captcha_word" maxlength="50" required value="" placeholder="*Код с картинки">
    </div>
</div>

<input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit" class="js-contest-form-btn" name="web_form_submit" value="<?= $arResult['CONTEST_FORM_BTN_TEXT'] ?>" <?= $arResult['BTN_STYLE_ATTR'] ?> />

<div id="form_checkbox_policy" class="col-xs-12">
    <input type="checkbox" id="form_checkbox_policy_checked" name="form_checkbox_policy" class="checkbox3" checked />
    <label for="form_checkbox_policy_checked" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a><? if ($arResult['CONTEST_RULES_SHOW']) : ?>
            и <a href="#insta_contest_rules" class="contest-rules-popup-open">правилами проведения конкурса</a>                                 <? endif; ?>.</label>
</div>

<?= $arResult["FORM_FOOTER"] ?>

<script>
    <? if (!empty($jsPhoneSelector)) : ?>
        // Маска для телефона
        var PHONE_MASK_EMPTY = '+7 (999) 999-99-99';
        $("<?= $jsPhoneSelector ?>").mask(PHONE_MASK_EMPTY, {
            autoclear: false
        }).click(function() {
            var phoneValue = $(this).val();
            var $phoneDOMEl = $(this)[0];
            if (phoneValue == '+7 (___) ___-__-__') {
                $phoneDOMEl.selectionStart = 4;
                $phoneDOMEl.selectionEnd = 4;
            }
        }).mouseover(function() {
            $(this).attr('placeholder', '+7 (___) ___-__-__');
        }).mouseout(function() {
            $(this).attr('placeholder', ' *Телефон');
        });
    <? endif; ?>

    <? if (!empty($jsBirthdateSelector)) : ?>
        // Маска для даты рождения
        var BIRTHDATE_MASK_EMPTY = '99.99.9999';
        $("<?= $jsBirthdateSelector ?>").mask(BIRTHDATE_MASK_EMPTY, {
            autoclear: false
        }).click(function() {
            var birthdateValue = $(this).val();
            var $birthdateDOMEl = $(this)[0];
            if (birthdateValue == '__.__.____') {
                $birthdateDOMEl.selectionStart = 0;
                $birthdateDOMEl.selectionEnd = 0;
            }
        }).mouseover(function() {
            $(this).attr('placeholder', '__.__.____');
        }).mouseout(function() {
            $(this).attr('placeholder', ' *Дата рождения');
        }).keyup(function() {
            let dateArr = $(this).val().split('.');
            let selectionIndex = -1;

            if(!dateArr || dateArr.length == 0) return;

            if(dateArr[0].indexOf('_') === -1 && (parseInt(dateArr[0]) < 1 || parseInt(dateArr[0]) > 31)) {
                if(dateArr[0][0] != '0' && dateArr[0][0] != '1' && dateArr[0][0] != '2' && dateArr[0][0] != '3') {
                    dateArr[0] = '__';
                    selectionIndex = 0;
                }
                else {
                    dateArr[0] = dateArr[0].slice(0, -1) + '_';
                    selectionIndex = 1;
                }
                dateArr[1] = '__';
                dateArr[2] = '____';
            }
            else if(dateArr[1].indexOf('_') === -1 && (parseInt(dateArr[1]) < 1 || parseInt(dateArr[1]) > 12)) {
                if(dateArr[1][0] != '0' && dateArr[1][0] != '1') {
                    dateArr[1] = '__';
                    selectionIndex = 3;
                }
                else {
                    dateArr[1] = dateArr[1].slice(0, -1) + '_';
                    selectionIndex = 4;
                }
                dateArr[2] = '____';
            }
            else if(dateArr[2].indexOf('_') === -1 && (parseInt(dateArr[2]) < 1900 || parseInt(dateArr[2]) > (new Date().getFullYear()))) {
                if((dateArr[2][0] != '1' || dateArr[2][1] != '9') && (dateArr[2][0] != '2' || dateArr[2][1] != '0')) {
                    dateArr[2] = '____';
                    selectionIndex = 6;
                }
                else {
                    dateArr[2] = dateArr[2].slice(0, -1) + '_';
                    selectionIndex = 9;
                }
            }

            $(this).val(dateArr.join('.')).mask(BIRTHDATE_MASK_EMPTY, {
                autoclear: false
            });
            if(selectionIndex >= 0) {
                $(this).prop('selectionStart', selectionIndex).prop('selectionEnd', selectionIndex);
            }
        });
    <? endif; ?>

        (function() {
            // Защищает от повторного выполнения JS
            if (window._is_insta_contest_form_js_included === undefined) {
                window._is_insta_contest_form_js_included = true;
            } else {
                return;
            }

            $(document).on("click", ".cls-mail-div", function() {
                $('.podlozhka').hide(0);
                $('.mail-div').hide(0);
                $('.auth-div-full').hide(0);
                $('.popup').hide(0);
                $('body').removeClass('with--popup');
            });

                $(document).on("click", 'form[name="CONTEST_FORM"] .reload-captcha', function() {
                    $.getJSON('/local/ajax/reload_captcha.php', function(data) {
                        $('form[name="CONTEST_FORM"] #captchaImg').attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + data);
                        $('form[name="CONTEST_FORM"] #captchaSid').val(data);
                    });
                    return false;
                });

            $(document).on("click", ".button--outline", function() {
                $('.podlozhka').hide(0);
                $('.mail-div').hide(0);
                $('.auth-div-full').hide(0);
                $('.popup').hide(0);
                $('body').removeClass('with--popup');
            });

            <? if ($arResult['CONTEST_FIELDS_FILE_1']) : ?>
                $(document).on("change", ".file-1-upload input[type=file]", function() {
                    var filename = $(this).val().replace(/.*\\/, "");
                    $("#file_1_name").html(filename);
                    $('.file-1-upload').css('background', '#034078');
                    $('.file-1-upload').css('color', '#fff');
                });
            <? endif; ?>

            <? if ($arResult['CONTEST_FIELDS_FILE_2']) : ?>
            $(document).on("change", ".file-2-upload input[type=file]", function() {
                var filename = $(this).val().replace(/.*\\/, "");
                $("#file_2_name").html(filename);
                $('.file-2-upload').css('background', '#034078');
                $('.file-2-upload').css('color', '#fff');
            });
            <? endif; ?>

            $(document).on('click', '.js-contest-form-btn', function(e) {
                var arr = {
                    "captcha_word": "",
                    <?= $jsValidationFieldsListData ?>
                };

                $('.field_error').remove();

                var cou_err = 0;
                var text_html = "";
                $.each(arr, function(key, value) {
                    if (
                        $("[name='" + key + "']").val() === undefined ||
                        $("[name='" + key + "']").val().trim() == "" ||
                        (("[name='" + key + "']") == "<?= $jsPhoneSelector ?>" && !/\+7\ \(\d{3}\)\ \d{3}-\d{2}-\d{2}/.test($("[name='" + key + "']").val())) ||
                        (("[name='" + key + "']") == "<?= $jsBirthdateSelector ?>" && !/\d{2}.\d{2}.\d{4}/.test($("[name='" + key + "']").val())) ||
                        (("[name='" + key + "']") == "<?= $jsEmailSelector ?>" && !/^(([^<>'\(\)\[\]\.,;:\s@"]+(\.[^<>'()\[\]\.,;:\s@\"]+)*))@(([^\'<>()[\]\.,;:\s@\"]+\.)+[^\'<>()[\]\.,;:\s@\"]{2,})$/i.test($("[name='" + key + "']").val()))
                    ) {
                        cou_err++;
                        if ($("[name='" + key + "']").attr('placeholder') != "" && $("[name='" + key + "']").attr('placeholder') != undefined) {
                            value = $("[name='" + key + "']").attr('placeholder');
                        }
                        text_html = "<p class='field_error'>Необходимо заполнить поле " + value + "</p>";

                        if($("[name='" + key + "']").attr('id') == 'form-file-1') {
                            $("#form-file-1-div").after(text_html);
                        }
                        else if($("[name='" + key + "']").attr('id') == 'form-file-2') {
                            $("#form-file-2-div").after(text_html);
                        }
                        else {
                        $("[name='" + key + "']").addClass("red_border").after(text_html);
                        }
                    } else {
                        $("[name='" + key + "']").removeClass("red_border");
                    }
                });

                <? if ($arResult['CONTEST_FIELDS_FILE_1']) : ?>
                    if (document.getElementById("form-file-1").files[0] !== undefined) {
                        var validExpansions = ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt'];
                        var file = document.getElementById("form-file-1").files[0];
                        var type = file.name.split('.').pop().toLowerCase();
                        var size = file.size;
                        if (validExpansions.indexOf(type) == -1 || size >= 5 * 1024 * 1024) {
                            cou_err++;
                            text_html = "<p class='field_error'>Файл не соответствует требованиям: " +
                                "<span>Размер файла не более 5 мб; допустимые форматы: jpg, jpeg, bmp, gif, png, doc, docx, xls, xlsx, pdf, ppt</span></p>";

                            document.getElementById("form-file-1").value = "";
                            $('#file_1_name').html("<?= ($arResult['CONTEST_CHECK_FILE_1'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_1'];?>");
                            $('#form-file-1-div').removeAttr("style").addClass("red_border").after(text_html);
                        } else {
                            $('#form-file-1-div').removeClass("red_border");
                        }
                    } else {
                        $('#form-file-1-div').removeClass("red_border");
                    }
                <? endif; ?>

                <? if ($arResult['CONTEST_FIELDS_FILE_2']) : ?>
                if (document.getElementById("form-file-2").files[0] !== undefined) {
                    var validExpansions = ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt'];
                    var file = document.getElementById("form-file-2").files[0];
                    var type = file.name.split('.').pop().toLowerCase();
                    var size = file.size;
                    if (validExpansions.indexOf(type) == -1 || size >= 5 * 1024 * 1024) {
                        cou_err++;
                        text_html = "<p class='field_error'>Файл не соответствует требованиям: " +
                            "<span>Размер файла не более 5 мб; допустимые форматы: jpg, jpeg, bmp, gif, png, doc, docx, xls, xlsx, pdf, ppt</span></p>";

                        document.getElementById("form-file-2").value = "";
                        $('#file_2_name').html("<?= ($arResult['CONTEST_CHECK_FILE_2'] ? '*' : '') . $arResult['CONTEST_BTN_FILE_2'];?>");
                        $('#form-file-2-div').removeAttr("style").addClass("red_border").after(text_html);
                    } else {
                        $('#form-file-2-div').removeClass("red_border");
                    }
                } else {
                    $('#form-file-2-div').removeClass("red_border");
                }
                <? endif; ?>

                if (!($("#form_checkbox_policy_checked").prop('checked'))) {
                    text_html = "<p class='field_error'>Необходимо согласие с политикой конфиденциальности</p>";
                    $('.js-contest-form-btn').after(text_html);
                    cou_err++;
                }

                if (cou_err > 0) {
                    return false;
                } else {
                    return true;
                }
            });
        })();
</script>