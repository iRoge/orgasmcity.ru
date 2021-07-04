<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
//Определяем порядок отображения
$arResult["SHOW_FIELDS"] = array(
    0 =>'NAME',
    1 =>'EMAIL',
    2 =>'PERSONAL_PHONE',
    3 =>'PASSWORD',
    4 =>'CONFIRM_PASSWORD',
    5 =>'LOGIN'
);
//$_REQUEST['REGISTER']['LOGIN'] = $_REQUEST['REGISTER']['EMAIL'];
?>

    <? if (!$USER->IsAuthorized()) { ?>
        <?if (count($arResult["ERRORS"]) > 0) :
            foreach ($arResult["ERRORS"] as $key => $error) {
                if (intval($key) == 0 && $key !== 0) {
                    $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;" . GetMessage("REGISTER_FIELD_" . $key) . "&quot;", $error);
                }
            }
        elseif ($arResult["USE_EMAIL_CONFIRMATION"] === "Y") :
            ?>
            <p><? echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT") ?></p>
        <? endif; ?>
    <form id="reg-form3" method="post" class="in-auth-form" action="<?= POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data">
        <div class="register-alert">
            <div id="after-cart-in-err-reg-form3">
                <?
                if (!empty($arResult["ERRORS"])) {
                    ShowError(implode("<br />", $arResult["ERRORS"]));
                }?>
            </div>
            <? if ($USER->IsAuthorized()) : ?>
            <div id="after-cart-in-err-full-reg-form3"></div>
            <?endif;?>
            <div id="after-cart-in-success-reg-form3">
                <? if ($USER->IsAuthorized()) : ?>
                    <p><? echo GetMessage("MAIN_REGISTER_AUTH") ?></p>
                <? endif; ?>
            </div>
        </div>
        <? if ($arResult["BACKURL"] <> '') :
            ?> <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
        <? endif; ?>
        <input id="reg_agreement_H1nPJH-reg-form3" type="checkbox" name="agreement" value="1" checked style="display: none;">
        <? foreach ($arResult["SHOW_FIELDS"] as $FIELD) :
            $class="";
            $name_end="";
            if (in_array($FIELD, $arResult["REQUIRED_FIELDS"])) {
                $class="required";
                $name_end="*";
            }
            ?>
            <? switch ($FIELD) {
                case "LOGIN":
                    ?>
                    <input type="hidden" name="REGISTER[LOGIN]" value="user">
                    <?
                       break; case "PASSWORD":
                    ?>
                    <input type="password" id="REGISTER[<?= $FIELD ?>]-reg-form3" name="REGISTER[<?= $FIELD ?>]" placeholder="<?=$name_end?>Придумайте пароль" value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" class="<?=$class?> registration_password"/>
                    <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                            <?
                       break; case "CONFIRM_PASSWORD":
                            ?>
                    <input type="password" id="REGISTER[<?= $FIELD ?>]-reg-form3" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" placeholder="<?=$name_end?>Повторите пароль" class="<?=$class?> registration_confirm_password"/>
                    <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                            <?
                       break; case "PERSONAL_PHONE":
                            ?>
                            
                        <div class="input-group--phone">
                        <input class="registration_phone <?=$class?>" id="REGISTER[<?= $FIELD ?>]-reg-form3" size="30" type="text" placeholder="<?=$name_end?><?= GetMessage("REGISTER_FIELD_".$FIELD) ?>" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" />
                            <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                        </div>
                            <?
                       break; default:
                            ?>
                            <?if ($FIELD=='NAME' || $FIELD=='LAST_NAME' || $FIELD=='SECOND_NAME') {
                                ?><input size="30" type="fio"  placeholder="<?=$name_end?><?= GetMessage("REGISTER_FIELD_".$FIELD) ?>" id="REGISTER[<?= $FIELD ?>]-reg-form3" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" class="fio <?=$class?>"/>
                                    <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                                <?
                            } elseif ($FIELD=='EMAIL') {?>
                                <input size="30" type="text" placeholder="<?=$name_end?><?= GetMessage("REGISTER_FIELD_".$FIELD) ?>" id="REGISTER[<?= $FIELD ?>]-reg-form3" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" class="<?=$class?>"/>
                                <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                                <?
                            } else {?>
                                <input size="30" type="text" placeholder="<?=$name_end?><?= GetMessage("REGISTER_FIELD_".$FIELD) ?>" id="REGISTER[<?= $FIELD ?>]-reg-form3" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" class="<?=$class?>"/>
                                <div id='err-REGISTER[<?= $FIELD ?>]-reg-form3' class="error-field"></div>
                            <?}?>
                            <?/*if ($FIELD == "PERSONAL_BIRTHDAY"){
                      $APPLICATION->IncludeComponent(
                          'bitrix:main.calendar',
                          '',
                          array(
                              'SHOW_INPUT' => 'N',
                              'FORM_NAME' => 'regform',
                              'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                              'SHOW_TIME' => 'N'
                          ),
                          null,
                          array("HIDE_ICONS" => "Y")
                      );
                }*/
            }?>
        <? endforeach ?>
        <?
        /* CAPTCHA */
        if ($arResult["USE_CAPTCHA"] == "Y") {
            ?>
        <div class="row captcha_block">
            <div class="col-md-6 captcha-code">
                <input  id="captchaSid-reg-form3" type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                <img  id="captchaImg-reg-form3" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
            </div>
            <div class="col-md-6">
                <a  id="reloadCaptcha-reg-form3">Обновить картинку</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 captcha-word">
            <input type="text" id="captcha_word-reg-form3" name="captcha_word" maxlength="50" value="" placeholder="*Код с картинки">
            <div id='err-captcha_word-reg-form3' class="error-field"></div>
        </div>
        </div>
            <?
         
         /* !CAPTCHA */
        }
        ?>
        <input type="submit" name="register_submit_button" value="Зарегистрироваться" class="col-xs-12 form-in-after-lk-sub"/>
        <div id="registration_checkbox_policy-reg-form3" class="col-xs-12">
            <div id='err-conf-reg-form3' class="error-field"></div>
            <input type="checkbox" id="regform_checked-reg-form3" name="registration_checkbox_policy" class="checkbox3" checked="checked"/>
            <label for="regform_checked-reg-form3" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a> и <a href="<?= OFERTA_FILENAME ?>">договором оферты</a>.</label>
        </div>
    </form>
    <? } else {
        LocalRedirect('/auth/');
    } ?>
<?if ($arResult["USE_CAPTCHA"] == "Y") :?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#reloadCaptcha-reg-form3').click(function(){
                $.getJSON('<?=$this->__folder?>/reload_captcha.php', function(data) {
                    $('#captchaImg-reg-form3').attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
                    $('#captchaSid-reg-form3').val(data);
                });
                return false;
            });
        });
    </script>
<?endif;?>
