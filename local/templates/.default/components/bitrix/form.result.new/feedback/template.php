<?php

use Bitrix\Highloadblock as HL;

?>
<div class="cls-mail-div"></div>
<?php
if ($arResult["isFormNote"] == "Y") : ?>
    <div class="product-preorder-success">
        <header>
            <?= $arResult["FORM_NOTE"]; ?>
        </header>
        <footer class="form-footer">
            <button class="js-popup-close button button--xxl button--primary button--outline">ОК</button>
        </footer>
    </div>
<?php else : ?>
    <h2>Обратная связь</h2>

    <?=$arResult["FORM_HEADER"]?>


    <?/*form name="<?= $arResult["WEB_FORM_NAME"] ?>" action="/local/ajax/feedback.php" method="POST"
          enctype="multipart/form-data" class="contact-form" novalidate="novalidate">
        <input type="hidden" name="WEB_FORM_ID" value="<?= $arParams["WEB_FORM_ID"] ?>">
        <?= bitrix_sessid_post() */?>

        <?
        foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
            if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
                print $arQuestion["HTML_CODE"];
            }
        }
        ?>
        <div id="after-feedback-in-err" ></div>
        <? if ($arResult["isFormErrors"] == "Y") : ?>
            <div class="alert alert--danger">
                <div class="alert-content">
                    <i class="icon icon-exclamation-circle"></i><?= $arResult["FORM_ERRORS_TEXT"]; ?>
                </div>
            </div>
        <? endif; ?>
        <?

        // subjects
        $arSubjectList = [];

        $hlblockId = COption::GetOptionInt('respect.feedback', "hlblock_id");
        if ($hlblockId) {
            CModule::IncludeModule("highloadblock");

            $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
            $obEntity = HL\HighloadBlockTable::compileEntity($hlblock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList([
                'select' => ['*'],
                'order' => ['UF_SORT' => 'ASC']
            ]);
            while ($arItem = $rsData->Fetch()) {
                if (!empty($arItem['UF_NAME'])) {
                    $arSubjectList[] = $arItem['UF_NAME'];
                }
            }

            unset($hlblock, $obEntity, $strEntityDataClass);
        }
        foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) :
            $label = ($arQuestion["REQUIRED"] == "Y" ? ' *' : '') . $arQuestion["CAPTION"];
            ?>
            <? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'text' || $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'email') :
                if ('FEEDBACK_SUBJECT' == $FIELD_SID && $arSubjectList) :
                    $htmlNameValue = 'form_text_' . $arQuestion["STRUCTURE"][0]["ID"];
                    $selectedValue = isset($_REQUEST[$htmlNameValue]) ? $_REQUEST[$htmlNameValue] : '';
                    ?>
                <select class="feedback-selectize" name="<?= $htmlNameValue ?>" style="background-color: #fff5f7">
                    <option value=""><?= $label ?></option>
                    <? foreach ($arSubjectList as $subject) :?>
                        <option value="<?= $subject ?>"<?= ($subject == $selectedValue ? ' selected' : '') ?>><?= $subject ?></option>
                    <? endforeach; ?>
                </select>
                <? else :?>
                    <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" placeholder="' . $label . '"', $arQuestion["HTML_CODE"]); ?>
                <? endif; ?>
            <? elseif ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea') : ?>
                <?= str_replace(array('inputtextarea"', 'inputtext"'), 'required" placeholder="' . $label . '"', $arQuestion["HTML_CODE"]); ?>
            <? else :?>
                <?if ($arQuestion["CAPTION"]=='Тема сообщения') :?>
                <label><?= $arQuestion["CAPTION"] ?><? if ($arQuestion["REQUIRED"] == "Y") :
                    ?><?= $arResult["REQUIRED_SIGN"]; ?><?
                       endif; ?></label>
                    <?= $arQuestion["HTML_CODE"] ?>
                <?endif;?>
            <? endif; ?>
        <? endforeach; ?>
        <div class="file-upload" id="feedback-file-div">
            <label>
                <input type="file" name="form_file_14" size="0" id="feedback-file" />
                <span id="filename">Прикрепить файл</span>
            </label>
        </div>
        <?
        /* CAPTCHA */
        if ($arResult["isUseCaptcha"] == "Y") :
            ?>
        <div class="row captcha_block">
            <div class="col-md-6 captcha-code">
                <input id="captchaSid" type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHACode"]?>" />
                <img id="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHACode"]?>" width="180" height="40" alt="CAPTCHA" />
            </div>
            <div class="col-md-6">
                   <a id="reloadCaptcha" class="reload-captcha">Обновить картинку</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 captcha-word">
                <input type="text" name="captcha_word" maxlength="50" value="" placeholder="*Код с картинки">
            </div>
         </div>
        <? endif; ?>

        <input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit"
         class="js-feedback-btn"
         name="web_form_submit"
         value="Отправить сообщение"/>

        <div id="feedback_checkbox_policy" class="col-xs-12">
            <input type="checkbox" id="feedback_checkbox_policy_checked" name="feedback_checkbox_policy" class="checkbox3" checked/>
            <label for="feedback_checkbox_policy_checked" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a>.</label>
        </div>
    <?=$arResult["FORM_FOOTER"]?>
<?php endif; ?>