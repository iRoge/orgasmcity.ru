<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<? ShowError($arResult["strProfileError"]); ?>
<? if ($arResult['DATA_SAVED'] == 'Y'): ?>
    <p><?= GetMessage('PROFILE_DATA_SAVED'); ?></p>
<? endif; ?>
<form method="post" class="col-xs-12 form-in-after-lk" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>"/>
    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
    <input type="hidden" name="LOGIN" value="<? echo $arResult["arUser"]["LOGIN"] ?>"/>

    <div class="col-xs-6">
        <h3>Личные данные</h3>
        <input name="NAME" value="<?= $arResult["arUser"]["NAME"] ?>" class="col-xs-12" type="text" placeholder="Имя">
        <input name="LAST_NAME" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" class="col-xs-12" type="text" placeholder="Фамилия">
        <input name="SECOND_NAME" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>" class="col-xs-12" type="text" placeholder="Отчество">
        <div class="col-xs-6 top-minus">
            <input type="radio" class="checkbox2" id="gender_m" name="PERSONAL_GENDER" value="M"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'M' ? ' checked' : ''; ?>>
            <label for="gender_m">Мужчина</label>
        </div>
        <div class="col-xs-6 top-minus">
            <input type="radio" class="checkbox2" id="gender_f" name="PERSONAL_GENDER" value="F"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'F' ? ' checked' : ''; ?>>
            <label for="gender_f">Женщина</label>
        </div>

        <input name="PERSONAL_BIRTHDAY" value="<?= $arResult["arUser"]["PERSONAL_BIRTHDAY"] ?>" type="date" placeholder="Дата рождения" class="col-xs-12">
        <input name="EMAIL" type="email" value="<? echo $arResult["arUser"]["EMAIL"] ?>" placeholder="E-mail" class="col-xs-12">
        <input name="PERSONAL_PHONE" value="<? echo $arResult["arUser"]["PERSONAL_PHONE"] ?>" type="number" placeholder="Телефон" class="col-xs-12">

        <input type="password" name="NEW_PASSWORD" placeholder="Новый пароль" class="col-xs-12"/>
        <input type="password" name="NEW_PASSWORD_CONFIRM" placeholder="Подтвердите пароль" class="col-xs-12"/>

        <input class="col-xs-12 form-in-after-lk-sub" type="submit" name="save" value="Сохранить информацию"/>
    </div>
    <div class="col-xs-6">
        <h3>Адрес доставки</h3>

        <select name="name" class="col-xs-12">
            <option value="0">Регион</option>
            <option value="Москва и МО">Москва и МО</option>
            <option value="Ленинградская область">Ленинградская область</option>
            <option value="Республика Татарстан">Республика Татарстан</option>
        </select>
        <input type="text" name="PERSONAL_CITY" value="<? echo $arResult["arUser"]["PERSONAL_CITY"] ?>" placeholder="Город" class="col-xs-12"/>
        <input type="text" name="PERSONAL_STREET" value="<? echo $arResult["arUser"]["PERSONAL_STREET"] ?>" placeholder="Улица" class="col-xs-12"/>
        <input type="text" name="UF_HOUSE" value="<? echo $arResult["arUser"]["UF_HOUSE"] ?>" placeholder="Улица" class="col-xs-12"/>

        <div id="wrap">
            <input type="number" name="UF_ST" value="<? echo $arResult["arUser"]["UF_ST"] ?>" placeholder="Строение" style="width:30%;"/>
            <input type="number" name="UF_HOUSING" value="<? echo $arResult["arUser"]["UF_HOUSING"] ?>" placeholder="Корпус" style="width:30%;"/>
            <input type="number" name="UF_ENTRANCE" value="<? echo $arResult["arUser"]["UF_ENTRANCE"] ?>" placeholder="Подъезд" style="width:30%;"/>
            <div class="clear-blocks"></div>
        </div>
        <div id="wrap">
            <input type="number" name="UF_FLOOR" value="<? echo $arResult["arUser"]["UF_FLOOR"] ?>" placeholder="Этаж" style="width:30%;"/>
            <input type="number" name="UF_APARTMENT" value="<? echo $arResult["arUser"]["UF_APARTMENT"] ?>" placeholder="Кв/офис" style="width:30%;"/>
            <input type="number" name="UF_INTERCOM" value="<? echo $arResult["arUser"]["UF_INTERCOM"] ?>" placeholder="Домофон" style="width:30%;"/>
            <div class="clear-blocks"></div>
        </div>
        <select name="UF_TIME" class="col-xs-12">
            <option value="0">Желаемое время доставки</option>
            <option value="с 08:00 до 12:00"<?if($arResult["arUser"]["UF_TIME"]=='с 08:00 до 12:00'):?> selected<?endif?>>с 08:00 до 12:00</option>
            <option value="с 12:00 до 16:00"<?if($arResult["arUser"]["UF_TIME"]=='с 12:00 до 16:00'):?> selected<?endif?>>с 12:00 до 16:00</option>
            <option value="с 16:00 до 20:00"<?if($arResult["arUser"]["UF_TIME"]=='с 16:00 до 20:00'):?> selected<?endif?>>с 16:00 до 20:00</option>
        </select>

        <p style="margin-top: 25px;" class="col-xs-12">Привяжите аккаунты из социальных сетей</p>
        <div class="col-xs-12 social">
            <?
            if ($arResult["SOCSERV_ENABLED"]) {
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "personal", array(
                    "SHOW_PROFILES" => "Y",
                    "ALLOW_DELETE" => "Y"
                ));
            }
            ?>
        </div>
    </div>
</form>

