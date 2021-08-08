<?php

if ($arResult["isFormErrors"] == 'N' && !empty($_REQUEST['formresult']) && $_REQUEST['formresult'] == 'addok') {
    ?>
<script>
    Popup.show('<div class="contest-popup-container">Ваша заявка отправлена в ближайшее время с Вами свяжется представитель компании.</div>' +
        '<button class="close-popup-btn">OK</button>' +
        '</div>');
</script>
    <?
    return;
}

$phoneSelector = '';
?>

<?= '<p style="margin-top: 20px; margin-bottom: 0px;">'.$arResult["FORM_ERRORS_TEXT"].'</p>'; ?>

<?= $arResult["FORM_HEADER"] ?>

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

<div class="fieldset">
    <? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) : ?>
        <? if ($FIELD_SID == 'PHONE') : ?>
            <? $phoneSelector = '"[name=form_' . $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] . "_" . $arQuestion['STRUCTURE'][0]['FIELD_ID'] . ']"'; ?>
            <?= str_replace(array('inputtextarea"', 'inputtext"'), 'about-franshise__input" required placeholder="+7 (___) ___-__-__"', $arQuestion["HTML_CODE"]); ?>
        <? elseif ($FIELD_SID == 'EMAIL') : ?>
                <?
                $arQuestion["HTML_CODE"] = str_replace('type="text"', 'type="email"', $arQuestion["HTML_CODE"]);
                ?>
            <?= str_replace(array('inputtextarea"', 'inputtext"'), 'about-franshise__input" required placeholder="ваша@почта.ru"', $arQuestion["HTML_CODE"]); ?>
        <? elseif ($FIELD_SID == 'NAME') : ?>
            <?= str_replace(array('inputtextarea"', 'inputtext"'), 'about-franshise__input" required placeholder="Фамилия Имя"', $arQuestion["HTML_CODE"]); ?>
        <? elseif ($FIELD_SID == 'CITY') : ?>
            <?= str_replace(array('inputtextarea"', 'inputtext"'), 'about-franshise__input" required placeholder="Введите город"', $arQuestion["HTML_CODE"]); ?>
        <? else : ?>
            <?= str_replace(array('inputtextarea"', 'inputtext"'), 'about-franshise__input" required placeholder="' . $arQuestion["CAPTION"] . '"', $arQuestion["HTML_CODE"]); ?>
        <? endif; ?>
    <? endforeach; ?>
</div>

<div class="fieldset">
    <input type="checkbox" class="visually-hidden about-franshise__agreement-checkbox" id="agreement-franshise" required>
    <label for="agreement-franshise" class="about-franshise__agreement">
        Я согласен с <a href="agreement.doc">политикой конфиденциальности</a> и даю согласие на
        обработку персональных данных
    </label>

    <input type="submit" name="web_form_submit" class="about-franshise__submit download-btn" id="franshise_submit_button" value="стать франчайзи">
</div>

<?= $arResult["FORM_FOOTER"] ?>

<script>
    $(<?= $phoneSelector ?>).mask("+7 (999) 999-99-99");
</script>