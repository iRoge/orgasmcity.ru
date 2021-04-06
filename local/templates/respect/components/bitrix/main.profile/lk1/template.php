<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->addExternalJS("/local/templates/respect/js/jquery.suggestions.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css");
global $LOCATION;
?>
<? ShowError($arResult["strProfileError"]); ?>
<? if ($arResult['DATA_SAVED'] == 'Y') : ?>
    <div class="personal-result"><?= GetMessage('PROFILE_DATA_SAVED'); ?></div>
<? endif; ?>
<div id="after-cart-in-err"></div>
<form method="post" class="form-in-after-lk" id="lk_form" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>"/>
    <input type="hidden" id='userId' name="ID" value="<?= $arResult["ID"] ?>"/>
    <input type="hidden" name="LOGIN" value="<? echo $arResult["arUser"]["LOGIN"] ?>"/>
    <div class="col-sm-6 col-xs-12">
        <h3>Личные данные</h3>
        <input type="text" name="NAME" class="col-xs-12 imya fio" placeholder="Имя" value="<?= $arResult["arUser"]["NAME"] ?>">
        <div id="err-personal-name" class="error-personal-fields"></div>
        <input type="text" name="LAST_NAME" class="col-xs-12 familiya fio" placeholder="Фамилия" value="<?= $arResult["arUser"]["LAST_NAME"] ?>">
        <input type="text" name="SECOND_NAME" class="col-xs-12 otchestvo fio" placeholder="Отчество" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>">
        <div class="col-xs-12 lk-gender-field">
            <input type="radio" class="checkbox2" id="gender_m" name="PERSONAL_GENDER" value="M"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'M' ? ' checked' : ''; ?>>
            <label class="gender-type-label" for="gender_m">Мужчина</label>
            <input type="radio" class="checkbox2" id="gender_f" name="PERSONAL_GENDER" value="F"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'F' ? ' checked' : ''; ?>>
            <label class="gender-type-label" for="gender_f">Женщина</label>
        </div>
        <? if ($arResult["arUser"]["PERSONAL_BIRTHDAY"]) : ?>
            <span class="col-xs-12 js-profile-input-lk js-profile-input-date"><?= $arResult["arUser"]["PERSONAL_BIRTHDAY"] ?></span>
        <? else : ?>
            <input type="text" id="PERSONAL_BIRTHDAY" name="PERSONAL_BIRTHDAY" class="col-xs-12 reg_date" placeholder="Дата рождения">
            <div id="err-personal-birthday" class="error-personal-fields"></div>
        <? endif ?>
        <input type="email" id="EMAIL" name="EMAIL" class="col-xs-12" placeholder="E-mail" value="<?= $arResult["arUser"]["EMAIL"] ?>">
        <div id="err-personal-email-phone" class="error-personal-fields"></div>
        <input type="text" id="PERSONAL_PHONE" name="PERSONAL_PHONE" class="col-xs-12 profile_phone" placeholder="Телефон" value="<?= $arResult["arUser"]["PERSONAL_PHONE"] ?>">
        <div id="err-personal-phone" class="error-personal-fields"></div>
        <input type="password" id='personal-pass' name="NEW_PASSWORD" class="col-xs-12" placeholder="Новый пароль">
        <div id="err-personal-pass" class="error-personal-fields"></div>
        <input type="password" id='personal-pass-conf' name="NEW_PASSWORD_CONFIRM" class="col-xs-12" placeholder="Подтвердите пароль">
    </div>
    <div class="col-sm-6 col-xs-12 lk-form-wrapper">
        <h3>Адрес доставки</h3>
        <?
        $APPLICATION->IncludeComponent(
            "qsoft:geolocation",
            "lk",
            array(
                "CACHE_TIME" => "31536000",
                "CACHE_TYPE" => "A",
            )
        ); ?>
        <input id="street" type="text" name="PERSONAL_STREET" value="<? echo $arResult["arUser"]["PERSONAL_STREET"] ?>" placeholder="Улица" class="col-xs-12"/>

        <div id="wrap">
            <div  style="margin-right:3px;position: relative;float: left;width: 50.5%;">
                <input style="width:96.5%;" id="house" type="text" name="UF_HOUSE" value="<? echo $arResult["arUser"]["UF_HOUSE"] ?>" placeholder="Дом, корпус, строение"/>
            </div>
            <div  style="position: relative;float: left;width: 48%;">
                <input <?=$arResult['DADATA_STATUS'] && COption::GetOptionInt("likee", "dadata_active") ? 'readonly ' : ''?>id="postal_code" style="width:102%;" type="number" name="UF_POSTALCODE" value="<?= $arResult["arUser"]["UF_POSTALCODE"] ?>" placeholder="<?=$arResult['DADATA_STATUS'] && COption::GetOptionInt("likee", "dadata_active") ? 'Индекс, заполняется автоматически' : 'Индекс (не обязательно)'?>">
            </div>
            <input type="number" name="UF_APARTMENT" value="<? echo $arResult["arUser"]["UF_APARTMENT"] ?>" placeholder="Кв/офис" style="width:49%;"/>
            <input type="number" name="UF_ENTRANCE" value="<? echo $arResult["arUser"]["UF_ENTRANCE"] ?>" placeholder="Подъезд" style="width:49%;"/>
            <input type="number" name="UF_FLOOR" value="<? echo $arResult["arUser"]["UF_FLOOR"] ?>" placeholder="Этаж" style="width:49%;"/>
            <input type="number" name="UF_INTERCOM" value="<? echo $arResult["arUser"]["UF_INTERCOM"] ?>" placeholder="Домофон" style="width:49%;"/>
            <input hidden id="fias_code"  type="text" name="UF_FIASCODE" value="<?= $arResult["arUser"]["UF_FIASCODE"] ?>" placeholder="ФИАС код (заполняется автоматически)">
            <input hidden id="region_fias"  class="form__elem" type="text" name="UF_REGIONFIAS" value="<?= $arResult["arUser"]["UF_REGIONFIAS"] ?>" placeholder="Заполняется автоматически">
            <input hidden id="area_fias"  class="form__elem" type="text" name="UF_AREAFIAS" value="<?= $arResult["arUser"]["UF_AREAFIAS"] ?>" placeholder="Заполняется автоматически">
            <input hidden id="city_fias"  class="form__elem" type="text" name="UF_CITYFIAS" value="<?= $arResult["arUser"]["UF_CITYFIAS"] ?>" placeholder="Заполняется автоматически">
            <input hidden id="district_fias"  class="form__elem" type="text" name="UF_DISTRICTFIAS" value="<?= $arResult["arUser"]["UF_DISTRICTFIAS"] ?>" placeholder="Заполняется автоматически">
            <input hidden id="settlement_fias"  class="form__elem" type="text" name="UF_SETTLEMENTFIAS" value="<?= $arResult["arUser"]["UF_SETTLEMENTFIAS"] ?>" placeholder="Заполняется автоматически">
            <input hidden id="street_fias"  class="form__elem" type="text" name="UF_STREETFIAS" value="<?= $arResult["arUser"]["UF_STREETFIAS"] ?>" placeholder="Заполняется автоматически">
            <div class="clear-blocks"></div>
        </div>
        <? /*
        <select name="UF_TIME" class="col-xs-12">
            <option value="">Желаемое время доставки</option>
            <option value="с 08:00 до 12:00"<?if($arResult["arUser"]["UF_TIME"]=='с 08:00 до 12:00'):?> selected<?endif?>>с 08:00 до 12:00</option>
            <option value="с 12:00 до 16:00"<?if($arResult["arUser"]["UF_TIME"]=='с 12:00 до 16:00'):?> selected<?endif?>>с 12:00 до 16:00</option>
            <option value="с 16:00 до 20:00"<?if($arResult["arUser"]["UF_TIME"]=='с 16:00 до 20:00'):?> selected<?endif?>>с 16:00 до 20:00</option>


            <option value="1" <?if ($arResult["arUser"]["UF_TIME"]=='1') :
                ?> selected<?
                              endif?>>10-14</option>
            <option value="2" <?if ($arResult["arUser"]["UF_TIME"]=='2') :
                ?> selected<?
                              endif?>>14-18</option>
            <option value="3" <?if ($arResult["arUser"]["UF_TIME"]=='3') :
                ?> selected<?
                              endif?>>16-20</option>
        </select>
        */ ?>
        <? /*<p style="margin-top: 25px;" class="col-xs-12">Привяжите аккаунты из социальных сетей</p>
        <div class="col-xs-12 social">
            <?
            if ($arResult["SOCSERV_ENABLED"]) {
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "personal", array(
                    "SHOW_PROFILES" => "Y",
                    "ALLOW_DELETE" => "Y"
                ));
            }
            ?>
        </div>*/?>
        <input class="col-xs-12 form-in-after-lk-sub" type="submit" name="save" value="Сохранить информацию"/>
    </div>
</form>
<? if ($arResult['DADATA_STATUS'] && COption::GetOptionInt("likee", "dadata_active")) : ?>
<script>
    var token = "<?=COption::GetOptionString('likee', 'dadata_token', '')?>";

    var type = "ADDRESS";
    var $street = $("#street");
    var $house = $("#house");

    function showCodes(suggestion) {
        $("#postal_code").val(suggestion.data.postal_code);
        $("#fias_code").val(suggestion.data.fias_id);
        $("#region_fias").val(suggestion.data.region_fias_id);
        $("#area_fias").val(suggestion.data.area_fias_id);
        $("#city_fias").val(suggestion.data.city_fias_id);
        $("#district_fias").val(suggestion.data.city_district_fias_id);
        $("#settlement_fias").val(suggestion.data.settlement_fias_id);
        $("#street_fias").val(suggestion.data.street_fias_id);
    }

    function clearCodes() {
        $("#postal_code").val("");
        $("#fias_code").val("");
        $("#region_fias").val("");
        $("#area_fias").val("");
        $("#city_fias").val("");
        $("#district_fias").val("");
        $("#settlement_fias").val("");
        $("#street_fias").val("");
    }

    // улица
    $street.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        // ограничиваем поиск
        constraints: {
            label: false,
            locations: {
                region: "<?=$arResult['DADATA_REGION_NAME']?>",
                city: "<?=$arResult['DADATA_CITY_NAME']?>",
            },
            deletable: false
        },
        onSelect: showCodes,
        onSelectNothing: clearCodes
    });

    // дом
    $house.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "house",
        // ограничиваем поиск
        constraints: $street,
        onSelect: showCodes,
        onSelectNothing: clearCodes
    });
</script>
<? endif; ?>
