<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<div class="container">
    <div class="column-4 column-center">

        <? ShowError($arResult["strProfileError"]); ?>
        <? if ($arResult['DATA_SAVED'] == 'Y'): ?>
            <p><?= GetMessage('PROFILE_DATA_SAVED'); ?></p>
        <? endif; ?>
        <form method="post" class="form--splitted form--profile" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data">
            <?= $arResult["BX_SESSION_CHECK"] ?>
            <input type="hidden" name="lang" value="<?= LANG ?>"/>
            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
            <input type="hidden" name="LOGIN" value="<? echo $arResult["arUser"]["LOGIN"] ?>"/>
            <div class="container">
                <div class="column-5">
                    <fieldset>
                        <legend>Личные данные</legend>
                        <div class="input-group">
                            <label><input name="NAME" value="<?= $arResult["arUser"]["NAME"] ?>" type="text" placeholder="Имя"></label>
                        </div>
                        <div class="input-group">
                            <label><input name="LAST_NAME" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" type="text" placeholder="Фамилия"></label>
                        </div>
                        <div class="input-group">
                            <label><input name="SECOND_NAME" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>" type="text" placeholder="Отчество"></label>
                        </div>
                    </fieldset>
                </div>
                <div class="column-5">
                    <fieldset>
                        <legend>Адрес доставки</legend>
                        <div class="input-group"><select class="selectize">
                                <option>* Регион</option>
                                <option value="0">Московская область</option>
                            </select></div>
                        <div class="input-group"><select class="selectize">
                                <option>* Город</option>
                                <option value="0">Москва</option>
                            </select></div>
                        <div class="input-group"><label><input type="text" placeholder="Улица"></label></div>
                    </fieldset>
                </div>
            </div>
            <div class="container">
                <div class="column-5">
                    <div class="input-group input-group--padding">
                        <label class="radio">
                            <input type="radio" name="PERSONAL_GENDER" value="M"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'M' ? ' checked' : ''; ?>>
                            <span>Мужчина</span>
                        </label>
                        <label class="radio">
                            <input type="radio" name="PERSONAL_GENDER" value="F"<?= $arResult["arUser"]["PERSONAL_GENDER"] == 'F' ? ' checked' : ''; ?>>
                            <span>Женщина</span>
                        </label>
                    </div>
                    <div class="input-group">
                        <label>
                            <input name="PERSONAL_BIRTHDAY" value="<?= $arResult["arUser"]["PERSONAL_BIRTHDAY"] ?>" type="text" placeholder="Дата рождения">
                            <?
                            //@todo сделать календарем
                            /*
                            $APPLICATION->IncludeComponent(
                                'bitrix:main.calendar',
                                '',
                                array(
                                    'SHOW_INPUT' => 'Y',
                                    'FORM_NAME' => 'form1',
                                    'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
                                    'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
                                    'SHOW_TIME' => 'N'
                                ),
                                null,
                                array('HIDE_ICONS' => 'Y')
                            );
*/
                            ?>
                        </label>
                    </div>
                    <div class="input-group">
                        <label><input name="EMAIL" type="email" value="<? echo $arResult["arUser"]["EMAIL"] ?>" placeholder="E-mail"></label>
                    </div>
                    <div class="input-group">
                        <label><input name="PERSONAL_PHONE" value="<? echo $arResult["arUser"]["PERSONAL_PHONE"] ?>" type="text" placeholder="Телефон"></label>
                    </div>
                </div>
                <div class="column-5">
                    <div class="container">
                        <div class="column-10">
                            <div class="input-group"><label><input type="text" placeholder="* Дом"></label></div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="column-33">
                            <div class="input-group"><label><input type="text" placeholder="Строение"></label></div>
                        </div>
                        <div class="column-33">
                            <div class="input-group"><label><input type="text" placeholder="Корпус"></label></div>
                        </div>
                        <div class="column-33">
                            <div class="input-group"><label><input type="text" placeholder="Подъезд"></label></div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="column-33">
                            <div class="input-group"><label><input type="text" placeholder="Этаж"></label></div>
                        </div>
                        <div class="column-33">
                            <div class="input-group"><label><input type="text" placeholder="Кв/Офис"></label></div>
                        </div>
                        <div class="column-33">
                            <div class="input-group"><label></label><input type="text" placeholder="Домофон"></div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="column-10">
                            <div class="input-group"><select class="selectize">
                                    <option>Желаемое время доставки</option>
                                    <option value="00:00">00:00</option>
                                    <option value="00:30">00:30</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="column-5"><a class="button button--third button--l button--block">Изменить пароль</a></div>
                <div class="column-5">
                    <div class="auth__social"><p>Привяжите аккаунты социальных сетей</p>
                        <div class="social-icons">
                            <?
                            if ($arResult["SOCSERV_ENABLED"]) {
                                $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "personal", array(
                                    "SHOW_PROFILES" => "Y",
                                    "ALLOW_DELETE" => "Y"
                                ), $component);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="column-10">
                    <input class="button button--primary button--outline button--xl button--block" type="submit" name="save" value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">
                </div>
            </div>
        </form>
    </div>
</div>