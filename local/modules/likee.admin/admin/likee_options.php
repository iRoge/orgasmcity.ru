<?php

use \Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Qsoft\Pvzmap\PVZTable;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$aTabs = array(
//    array(
//        "DIV" => "options_popup_fo",
//        "TAB" => 'Диалоговое окно быстрого заказа',
//        "ICON" => "main_user_edit",
//        "TITLE" => 'Настройка параметров отображения окна для пользователей',
//    ),
    array(
        "DIV" => "options_order_resriction",
        "TAB" => 'Ограничения при заказе',
        "ICON" => "main_user_edit",
        "TITLE" => 'Настройка параметров ограничений при заказе',
    ),
//    array(
//        "DIV" => "options_catalo_view",
//        "TAB" => 'Отображение каталога',
//        "ICON" => "main_user_edit",
//        "TITLE" => 'Настройка параметров отображения каталога',
//    ),
//    array(
//        "DIV" => "global_opts",
//        "TAB" => 'Общие настройки',
//        "ICON" => "main_user_edit",
//        "TITLE" => 'Глобальные настройки отображения',
//    ),
//    array(
//        "DIV" => "global_popup_b",
//        "TAB" => 'Всплывающие окна',
//        "ICON" => "main_user_edit",
//        "TITLE" => 'Настройки для всплывающих окон',
//    ),
    array(
        'DIV' => 'whatsapp_options',
        'TAB' => 'WhatsApp',
        'ICON' => 'main_user_edit',
        'TITLE' => 'Настройки взаимодействия с WhatsApp',
    ),
    array(
        'DIV' => 'pvz_options',
        'TAB' => 'ПВЗ',
        'ICON' => 'main_user_edit',
        'TITLE' => 'Настройки ПВЗ',
    )
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
$returnUrl = $_GET["return_url"] ? "&return_url=" . urlencode($_GET["return_url"]) : "";

$mainBgFileId = COption::GetOptionInt("likee", "main_slider_bg", 0);


//Получение ПВЗ
Loader::includeModule('qsoft.pvzmap');
$res = PVZTable::getList([]);

if ($_SERVER['REQUEST_METHOD'] == "POST"
    && check_bitrix_sessid()
) {
    COption::SetOptionInt("likee", "popup_fo_active", intval(@$_POST["popup_fo_active"]));
    COption::SetOptionInt("likee", "popup_fo_page", intval($_POST["popup_fo_page"]));
    COption::SetOptionInt("likee", "popup_fo_catalog", intval($_POST["popup_fo_catalog"]));
    COption::SetOptionInt("likee", "popup_fo_once", intval(@$_POST["popup_fo_once"]));
    COption::SetOptionInt("likee", "home_slider_autoplay_1", intval(@$_POST["home_slider_autoplay_1"]));
    COption::SetOptionInt("likee", "home_slider_autoplay_2", intval($_POST["home_slider_autoplay_2"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_1", intval($_POST["home_slider_mobile_autoplay_1"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_2", intval($_POST["home_slider_mobile_autoplay_2"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_3", intval($_POST["home_slider_mobile_autoplay_3"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_4", intval($_POST["home_slider_mobile_autoplay_4"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_5", intval($_POST["home_slider_mobile_autoplay_5"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_6", intval($_POST["home_slider_mobile_autoplay_6"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_7", intval($_POST["home_slider_mobile_autoplay_7"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_8", intval($_POST["home_slider_mobile_autoplay_8"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_9", intval($_POST["home_slider_mobile_autoplay_9"]));
    COption::SetOptionInt("likee", "home_slider_mobile_autoplay_10", intval($_POST["home_slider_mobile_autoplay_10"]));
    COption::SetOptionInt("likee", "catalog_mlt", intval(@$_POST["catalog_mlt"]));
    COption::SetOptionInt("likee", "catalog_mrt", intval(@$_POST["catalog_mrt"]));
    COption::SetOptionInt("likee", "might_like", intval(@$_POST["might_like"]));
    COption::SetOptionInt("likee", "watch_history", intval(@$_POST["watch_history"]));
    COption::SetOptionInt("likee", "catalog_filter", intval(@$_POST["catalog_filter"]));
    COption::SetOptionInt("likee", "dadata_active", intval(@$_POST["dadata_active"]));
    COption::SetOptionInt("likee", "dadata_maxspd", intval(@$_POST["dadata_maxspd"]));
    COption::SetOptionInt("likee", "sailplay_anon_user_id", intval(@$_POST["sailplay_anon_user_id"]));
    COption::SetOptionInt("likee", "night_import", trim(@$_POST["night_import"]));
    COption::SetOptionInt("likee", "fiveMinuts_import", trim(@$_POST["fiveMinuts_import"]));
    COption::SetOptionString("likee", "catalog_label", trim(@$_POST["catalog_label"]));
    COption::SetOptionString("likee", "rshoes_redirect_url", trim(@$_POST["rshoes_redirect_url"]));
    COption::SetOptionString("likee", "popup_b_utm", trim(@$_POST["popup_b_utm"]));
    COption::SetOptionString("likee", "instashopping_token", trim($_POST["instashopping_token"]));
    COption::SetOptionString("likee", "dadata_token", trim(@$_POST["dadata_token"]));
    COption::SetOptionString("likee", "dadata_xsecret_token", trim(@$_POST["dadata_xsecret_token"]));

    //Сохранение СДЕК
    COption::SetOptionString("likee", "login_cdek", trim(@$_POST["login_cdek"]));
    COption::SetOptionString("likee", "password_cdek", trim(@$_POST["password_cdek"]));
    //Сохранение 5POST
    COption::SetOptionString("likee", "apikey_5post", trim(@$_POST["apikey_5post"]));

    // сохраняем настройки ограничений
    $names = array(
        'prepayment_min_summ' => 'int',
        "order_max_num" => "int",
        "order_max_num_text" => "text",
        "basket_min_num" => "int",
        "basket_min_num_text" => "text",
        "one_click_min" => "int",
        "favorites_max_num_text" => "text",
        "basket_max_art_num" => "int",
        "basket_max_art_num_text" => "text",
        "text_for_double_basket" => "text",
        "text_for_basket_without_local_products" => "text",
        "text_for_popup_basket_with_local_products" => "text",
        "text_for_popup_basket_without_local_products" => "text",
        "disabled_payment_click_text"  => "text",
        "order_success_text" => "html",
        "order_success_text_reservation" => "html",
        "product_cart_donors_text" => "text",
    );
    foreach ($names as $key => $val) {
        if (isset($_POST[$key])) {
            if ($val != "html") {
                if ($val == "int") {
                    $_POST[$key] = intval($_POST[$key]);
                } else {
                    $_POST[$key] = htmlspecialchars($_POST[$key]);
                }
            }
            if (isset($_POST[$key])) {
                Option::set("respect", $key, $_POST[$key]);
            }
        }
    }

    $waPhone = preg_replace('/\D+/', '', trim(@$_POST['whatsapp_phone']));

    if (empty($waPhone) || preg_match('/^7[0-9]{10}$/', $waPhone)) {
        COption::SetOptionString('respect', 'whatsapp_phone', $waPhone);
    }

    COption::SetOptionString('respect', 'whatsapp_text', urlencode(@$_POST['whatsapp_text']));
    COption::SetOptionString('respect', 'whatsapp_text_reserv', urlencode(@$_POST['whatsapp_text_reserv']));
    COption::SetOptionInt("respect", "whatsapp_allowShow", intval($_POST["whatsapp_allowShow"]));
    COption::SetOptionInt("respect", "mango_show", intval($_POST["mango_show"]));

    // обработка файла
    $arMainBg = $mainBgFileId ? CFile::getFileArray($mainBgFileId) : [];
    $mainBgNeedSave = false;

    if (!empty($_POST["MAINBG_del"]) && $_POST["MAINBG_del"] == 'Y') {
        $arMainBg["del"] = 'Y';
        $arMainBg["old_file"] = $arMainBg['ID'];
        $mainBgNeedSave = true;
    } elseif (!empty($_FILES["MAINBG"]) && is_array($_FILES["MAINBG"]) && $_FILES["MAINBG"]["error"] == 0) {
        $imageFileError = CFile::CheckImageFile($_FILES["MAINBG"]);

        if (is_null($imageFileError)) {
            $arMainBg = $_FILES["MAINBG"];
            $mainBgNeedSave = true;
        }
    }

    if ($mainBgNeedSave) {
        $arMainBg["MODULE_ID"] = "likee";
        $mainBgFileId = CFile::SaveFile($arMainBg, "likee");
        COption::SetOptionInt("likee", "main_slider_bg", $mainBgFileId);
        CBitrixComponent::clearComponentCache('likee:slider');
    }
    unset($arMainBg);

    //Сохранение ПВЗ
    $arIDsActive = array_keys($_POST['PVZ']);
    $arIDsHidePostamat = array_keys($_POST['PVZ_hide_postamat']);
    $arIDsHideOnlyPrepayment = array_keys($_POST['PVZ_hide_only_prepayment']);
    $arIDsHidePost = array_keys($_POST['PVZ_hide_post']);
    while ($arPVZ = $res->fetch()) {
        if (in_array($arPVZ['ID'], $arIDsActive)) {
            $active = 'Y';
        } else {
            $active = 'N';
        }
        if (in_array($arPVZ['ID'], $arIDsHidePostamat)) {
            $hidePostamat = 'Y';
        } else {
            $hidePostamat = 'N';
        }
        if (in_array($arPVZ['ID'], $arIDsHideOnlyPrepayment)) {
            $hideOnlyPrepayment = 'Y';
        } else {
            $hideOnlyPrepayment = 'N';
        }
        if (in_array($arPVZ['ID'], $arIDsHidePost)) {
            $hidePost = 'Y';
        } else {
            $hidePost = 'N';
        }
        PVZTable::update($arPVZ['ID'], [
            'ACTIVE' => $active,
            'HIDE_POSTAMAT' => $hidePostamat,
            'HIDE_ONLY_PREPAYMENT' => $hideOnlyPrepayment,
            'HIDE_POST' => $hidePost,
        ]);
    }

    $CACHE_MANAGER->ClearByTag("likee_options");
    $CACHE_MANAGER->ClearByTag("PVZ");

    LocalRedirect("/bitrix/admin/likee_options.php?lang=" . LANGUAGE_ID . $returnUrl . "&" . $tabControl->ActiveTabParam());
}

$APPLICATION->SetTitle('Настройки для сайта Orgasmcity');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <script src="/local/templates/respect/lib/jquery.js"></script>
    <script src="/local/templates/respect/js/inputs/jquery.maskedinput.min.js"></script>
    <form method="POST" action="likee_options.php?lang=<?= LANGUAGE_ID ?><?= $returnUrl ?>"
          enctype="multipart/form-data" name="editform">
        <?
        $tabControl->Begin();
        /*
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">Активно:</td>
            <td width="60%"><input type="checkbox" name="popup_fo_active"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "likee",
                                       "popup_fo_active",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Время открытия - страница (сек):</td>
            <td width="60%"><input type="text" name="popup_fo_page" size="6"
                                   value="<? echo COption::GetOptionInt("likee", "popup_fo_page", 40) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Время открытия - каталог (сек):</td>
            <td width="60%"><input type="text" name="popup_fo_catalog" size="6"
                                   value="<? echo COption::GetOptionInt("likee", "popup_fo_catalog", 120) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Показывать 1 раз за сессию:</td>
            <td width="60%"><input type="checkbox" name="popup_fo_once"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "likee",
                                       "popup_fo_once",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>
        <? */
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">Сумма заказа для бесплатной доставке по предоплате:</td>
            <td width="60%"><input type="number" min="0" name="prepayment_min_summ"
                                   value="<?= Option::get("respect", "prepayment_min_summ") ?>"/>
            </td>
        </tr>
        <tr>
            <td width="40%">Максимальное количество заказов в день:</td>
            <td width="60%"><input type="number" min="0" name="order_max_num"
                                   value="<?= Option::get("respect", "order_max_num") ?>"/>
                – укажите <u>%NUM%</u> в поле ниже, чтобы передать туда это значение
            </td>
        </tr>
        <tr>
            <td width="40%">Текст ошибки:</td>
            <td width="60%"><textarea name="order_max_num_text"><?= Option::get(
                "respect",
                "order_max_num_text"
            ) ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Минимальная сумма заказа через корзину:</td>
            <td width="60%"><input type="number" min="0" name="basket_min_num"
                                   value="<?= Option::get("respect", "basket_min_num") ?>"/>
                – укажите <u>%NUM%</u> в поле ниже, чтобы передать туда это значение
            </td>
        </tr>
        <tr>
            <td width="40%">Текст ошибки:</td>
            <td width="60%"><textarea name="basket_min_num_text"><?= Option::get(
                "respect",
                "basket_min_num_text"
            ) ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Минимальная сумма заказа в 1 клик:</td>
            <td width="60%"><input type="number" min="0" name="one_click_min"
                                   value="<?= Option::get("respect", "one_click_min") ?>"/>
        </tr>
        <tr>
            <td width="40%">Максимальное количество позиций (артикулов) в корзине:</td>
            <td width="60%"><input type="number" min="0" name="basket_max_art_num"
                                   value="<?= Option::get("respect", "basket_max_art_num") ?>"/>
                – укажите <u>%NUM%</u> в поле ниже, чтобы передать туда это значение
            </td>
        </tr>
        <tr>
            <td width="40%">Текст ошибки:</td>
            <td width="60%"><textarea name="basket_max_art_num_text"><?= Option::get(
                "respect",
                "basket_max_art_num_text"
            ) ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст для двойной корзины:</td>
            <td width="60%"><textarea name="text_for_double_basket"><?= Option::get("respect", "text_for_double_basket") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст для корзины с не местным товаром:</td>
            <td width="60%"><textarea name="text_for_basket_without_local_products"><?= Option::get("respect", "text_for_basket_without_local_products") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст для раскрытия Корзины с местным товаром:</td>
            <td width="60%"><textarea name="text_for_popup_basket_with_local_products"><?= Option::get("respect", "text_for_popup_basket_with_local_products") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст для раскрытия Корзины с не местным товаром:</td>
            <td width="60%"><textarea name="text_for_popup_basket_without_local_products"><?= Option::get("respect", "text_for_popup_basket_without_local_products") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст ошибки при добавлении 100го товара в избранное:</td>
            <td width="60%"><textarea name="favorites_max_num_text"><?= Option::get("respect", "favorites_max_num_text") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст ошибки при клике на неактивный способ оплаты:</td>
            <td width="60%"><textarea name="disabled_payment_click_text"><?= Option::get("respect", "disabled_payment_click_text") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст на странице успешного заказа для предоплаты:</td>
            <td width="60%"><textarea style="height: 100px; width: 600px" name="order_success_text"><?= Option::get("respect", "order_success_text") ?></textarea></td>
        </tr>
        <tr>
            <td width="40%">Текст на странице успешного заказа с резервированием:</td>
            <td width="60%"><textarea style="height: 100px; width: 600px" name="order_success_text_reservation"><?= Option::get("respect", "order_success_text_reservation") ?></textarea></td>
        </tr>

        <?/*
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">Выводить в каталоге элемены из MLT:</td>
            <td width="60%"><input type="checkbox" name="catalog_mlt" value="1" <? (1 == COption::GetOptionInt(
                "likee",
                "catalog_mlt",
                1
            )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Выводить в каталоге элемены из MRT:</td>
            <td width="60%"><input type="checkbox" name="catalog_mrt" value="1" <? (1 == COption::GetOptionInt(
                "likee",
                "catalog_mrt",
                1
            )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Отобрать в общем каталоге иконки спецраздела:</td>
            <td width="60%">
                <select name="catalog_label">
                    <option value="mlt" <? if ('mlt' == COption::GetOptionString("likee", "catalog_label", 'mlt')) {
                        echo " selected";
                                        } ?>>MLT
                    </option>
                    <option value="mrt" <? if ('mrt' == COption::GetOptionString("likee", "catalog_label", 'mrt')) {
                        echo " selected";
                                        } ?>>MRT
                    </option>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%">Отображать в каталоге фильтр:</td>
            <td width="60%"><input type="checkbox" name="catalog_filter"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "likee",
                                       "catalog_filter",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Отображать блок "Вам может понравится":</td>
            <td width="60%"><input type="checkbox" name="might_like" value="1" <? (1 == COption::GetOptionInt(
                "likee",
                "might_like",
                1
            )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Отображать блок "История просмотров":</td>
            <td width="60%"><input type="checkbox" name="watch_history"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "likee",
                                       "watch_history",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Текст для карточки товара на территории с двойной корзиной:</td>
            <td width="60%"><textarea name="product_cart_donors_text"><?= Option::get("respect", "product_cart_donors_text") ?></textarea></td>
        </tr>
        <tr>
        <?
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">Ночной импорт</td>
            <td width="60%"><input type="checkbox" name="night_import"
                                   value="1"<? (1 == COption::GetOptionInt(
                                       "likee",
                                       "night_import",
                                       0
                                   )) and print ' checked' ?>>
            </td>
        </tr>
        <tr>
            <td width="40%">5-минутный импорт</td>
            <td width="60%"><input type="checkbox" name="fiveMinuts_import"
                                   value="1"<? (1 == COption::GetOptionInt(
                                       "likee",
                                       "fiveMinuts_import",
                                       0
                                   )) and print ' checked' ?>>
            </td>
        </tr>
        <tr>
            <td width="40%">Время смены баннеров в первом слайдере на главной странице (сек):</td>
            <td width="60%"><input type="text" name="home_slider_autoplay_1" size="6"
                                   value="<? echo COption::GetOptionInt("likee", "home_slider_autoplay_1", 0) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Время смены баннеров во втором слайдере на главной странице (сек):</td>
            <td width="60%"><input type="text" name="home_slider_autoplay_2" size="6"
                                   value="<? echo COption::GetOptionInt("likee", "home_slider_autoplay_2", 8) ?>"></td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top">Фон под слайдером на главной странице:</td>
            <td width="60%">
                <div><input type="file" name="MAINBG"></div>
                <? if (isset($mainBgFileId) && intval($mainBgFileId) > 0) : ?>
                    <br>
                    <?
                    $arMainBg = CFile::GetFileArray($mainBgFileId);
                    echo CFile::ShowImage($arMainBg, 150, 150, "border=0", "", false);
                    ?>
                    <br/>
                    <div>
                        <input type="checkbox" name="MAINBG_del" value="Y" id="MAINBG_del">
                        <label for="MAINBG_del">Удалить файл</label>
                    </div>
                <? endif; ?>
            </td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 1</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_1" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_1",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 2</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_2" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_2",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 3</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_3" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_3",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 4</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_4" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_4",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 5</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_5" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_5",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 6</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_6" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_6",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 7</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_7" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_7",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 8</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_8" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_8",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 9</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_9" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_9",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Слайдер на главной мобильный 10</td>
            <td width="60%"><input type="text" name="home_slider_mobile_autoplay_10" size="6"
                                   value="<? echo COption::GetOptionInt(
                                       "likee",
                                       "home_slider_mobile_autoplay_10",
                                       3
                                   ) ?>"></td>
        </tr>
        <tr>
            <td width="40%">Токен для Instashopping:</td>
            <td width="60%"><input type="text" name="instashopping_token" size="40"
                                   value="<? echo COption::GetOptionString("likee", "instashopping_token", '') ?>"></td>
        </tr>
        <tr>
            <td width="40%">Нажми для получения токена</td>
            <td width="60%">
                <button type='button'><a target='_blank'
                                         href='https://api.instagram.com/oauth/authorize/?client_id=cd6af13c3954406b9dfc2ad52e545608&redirect_uri=https://respect-shoes.ru&response_type=token'>Получить
                        токен</a></button>
            </td>
        </tr>
        <tr>
            <td width="40%">Инструкция:</td>
            <td width="60%">
                <button type='button'><a target='_blank'
                                         href='https://respect-shoes.ru/upload/files/Instashopping.docx'>Инструкция</a>
                </button>
            </td>
        </tr>
        <tr>
            <td width="40%">Использовать dadata для подсказок в полях адреса</td>
            <td width="60%"><input type="checkbox" name="dadata_active" value="1"<?
                (1 == COption::GetOptionInt("likee", "dadata_active", 1)) and print ' checked' ?>></td>
        </tr>
        <tr>
            <td width="40%">API token для dadata (обязателен для работоспособности, берется из личного кабинета
                dadata):
            </td>
            <td width="60%"><input type="text" name="dadata_token"
                                   value="<?= COption::GetOptionString("likee", "dadata_token", '') ?>"></td>
        </tr>
        <tr>
            <td width="40%">X-Secret token для dadata (обязателен для работоспособности, берется из личного кабинета
                dadata):
            </td>
            <td width="60%"><input type="text" name="dadata_xsecret_token"
                                   value="<?= COption::GetOptionString("likee", "dadata_xsecret_token", '') ?>"></td>
        </tr>
        <tr>
            <td width="40%">Максимум запросов в день на dadata (обязателен для работоспособности, берется из личного
                кабинета dadata):
            </td>
            <td width="60%"><input type="number" name="dadata_maxspd"
                                   value="<?= COption::GetOptionInt("likee", "dadata_maxspd", '') ?>"></td>
        </tr>
        <tr>
            <td width="40%">ID анонимного пользователя для SailPlay:</td>
            <td width="60%"><input type="text" name="sailplay_anon_user_id" readonly size="40"
                                   value="<? echo COption::GetOptionInt("likee", "sailplay_anon_user_id", '') ?>"></td>
        </tr>
        <?
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">UTM метки для глобальных окон:</td>
            <td width="60%"><input type="text" name="popup_b_utm" size="40"
                                   value="<? echo COption::GetOptionString("likee", "popup_b_utm", '') ?>"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <div class="adm-info-message-wrap" align="center">
                    <div class="adm-info-message">Проверка будет вестись по полному соответствию значения. Можно
                        указать, как только название <strong>utm_source</strong>, так и вместе с требуемым значением
                        <strong>utm_source=sp</strong>.<br/>Для нескольких значений разделяйте их запятыми. Пример: <i>utm_source=sp,utm_referer</i>
                    </div>
                </div>
            </td>
        </tr>
        <?php */
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%">Контактный номер телефона:</td>
            <td width="60%">
                <input type="text" id="whatsapp_phone" name="whatsapp_phone" placeholder="+7 (___) ___-__-__"
                       value="<?= COption::GetOptionString('respect', 'whatsapp_phone') ?>">
            </td>
        </tr>
        <tr>
            <td width="40%">Текст сообщения:</td>
            <td width="60%">
                <textarea name="whatsapp_text"><?= urldecode(COption::GetOptionString(
                    'respect',
                    'whatsapp_text'
                )) ?></textarea>
            </td>
        </tr>
        <tr>
            <td width="40%">Включить Whatsapp в мобильной версии</td>
            <td width="60%"><input type="checkbox" name="whatsapp_allowShow"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "respect",
                                       "whatsapp_allowShow",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>
        <tr>
            <td width="40%">Текст обращения клиента в форме резервирования:</td>
            <td width="60%">
                <textarea name="whatsapp_text_reserv"><?= urldecode(COption::GetOptionString(
                    'respect',
                    'whatsapp_text_reserv'
                )) ?></textarea><br>
                <b>#ARTICLE_NAME#</b> - переменная "Артикул и Наименование"
            </td>
        </tr>
        <tr>
            <td width="40%">Включить кнопку обратного звонка Mango</td>
            <td width="60%"><input type="checkbox" name="mango_show"
                                   value="1" <? (1 == COption::GetOptionInt(
                                       "respect",
                                       "mango_show",
                                       1
                                   )) and print ' checked' ?> /></td>
        </tr>

        <?php
        $tabControl->BeginNextTab();
        ?>

        <tr>
            <td width="40%">Логин СДЕК</td>
            <td width="60%"><input type="text" name="login_cdek" size=30
                                   value="<? echo COption::GetOptionString(
                                       "likee",
                                       "login_cdek",
                                       ''
                                   ) ?>"></td>
        </tr>

        <tr>
            <td width="40%">Пароль СДЕК</td>
            <td width="60%"><input type="text" name="password_cdek" size="30"
                                   value="<? echo COption::GetOptionString(
                                       "likee",
                                       "password_cdek",
                                       ''
                                   ) ?>"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                 <input type="button" onclick="
                     $('#cdek-result').html('Обновление');
                     BX.ajax.post('/local/scripts/getCDEKpvz.php', false, function(response) {
                        $('#cdek-result').html(response);
                     });" value="Обновить CDEK через API" >
                <span id="cdek-result" style="margin-left: 40px">
                    <?= "Обновлено: " . date("d-m-Y H:i:s", filemtime($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/CDEK.pvz'));
                    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="button" onclick="
                     $('#pickpoint-result').html('Обновление');
                     BX.ajax.post('/local/scripts/getPickPointPVZ.php', false, function(response) {
                        $('#pickpoint-result').html(response);
                     });" value="Обновить PickPoint через API" >
                <span id="pickpoint-result" style="margin-left: 40px">
                    <?= "Обновлено: " . date("d-m-Y H:i:s", filemtime($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/PickPoint.pvz'));
                    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <table>
                        <tr style="margin-top: 30px" align="center">
                            <td width="130px">Название</td>
                            <td width="100px">Активность</td>
                            <td width="100px">Скрыть постаматы</td>
                            <td width="100px">Скрыть только предоплатные</td>
                            <td width="100px">Скрыть IML/Почта</td>
                        </tr>
                        <? while ($arPVZ = $res->fetch()) : ?>
                            <tr align="center">
                                <td width="130px"><?= $arPVZ['NAME'] ?></td>
                                <td width="100px">
                                    <input type="checkbox"
                                           name="PVZ[<?= $arPVZ['ID'] ?>]"
                                           value="Y"
                                        <? if ($arPVZ['ACTIVE'] == 'Y') : ?>
                                            checked="checked"
                                        <? endif; ?>
                                    >
                                </td>
                                <td width="100px">
                                    <input type="checkbox"
                                           name="PVZ_hide_postamat[<?= $arPVZ['ID'] ?>]"
                                           value="Y"
                                        <? if ($arPVZ['HIDE_POSTAMAT'] == 'Y') : ?>
                                            checked="checked"
                                        <? endif; ?>
                                    >
                                </td>
                                <td width="100px">
                                    <input type="checkbox"
                                           name="PVZ_hide_only_prepayment[<?= $arPVZ['ID'] ?>]"
                                           value="Y"
                                        <? if ($arPVZ['HIDE_ONLY_PREPAYMENT'] == 'Y') : ?>
                                            checked="checked"
                                        <? endif; ?>
                                    >
                                </td>
                                <td width="100px">
                                    <input type="checkbox"
                                           name="PVZ_hide_post[<?= $arPVZ['ID'] ?>]"
                                           value="Y"
                                        <? if ($arPVZ['HIDE_POST'] == 'Y') : ?>
                                           checked="checked"
                                        <? endif; ?>
                                    >
                                </td>
                            </tr>
                        <? endwhile; ?>
                </table>
            </td>
        </tr>

        <?
        $tabControl->Buttons(
            array(
                "disabled" => false,
                "back_url" => $_GET["return_url"] ? $_GET["return_url"] : "likee_options.php?lang=" . LANG,
            )
        );
        ?>
        <? echo bitrix_sessid_post(); ?>
        <input type="hidden" name="lang" value="<? echo LANG ?>">
        <?
        $tabControl->End();
        ?>
    </form>
    <style>
        textarea {
            width: 450px;
            height: 45px;
            resize: none
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#whatsapp_phone').mask('+7 (999) 999-99-99', {
                autoclear: false
            });
            $('#whatsapp_phone').click(function () {
                if ($('#whatsapp_phone').val() == '+7 (___) ___-__-__') {
                    $(this)[0].selectionStart = 4;
                    $(this)[0].selectionEnd = 4;
                }
            });
        });

        function addRedirect() {
            var html = '<td><input type="text" name="rshoes[path][]" value="" style="width:100%; -moz-box-sizing: border-box; box-sizing: border-box;"></td><td><input type="text" name="rshoes[url][]" value="" style="width:100%; -moz-box-sizing: border-box; box-sizing: border-box;"></td>';

            var tr = document.createElement('tr');
            tr.innerHTML = html;

            var list = document.getElementById('rshoes-list');
            list.appendChild(tr);
        }
    </script>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
