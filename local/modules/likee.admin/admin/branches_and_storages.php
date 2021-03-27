<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;
use \Bitrix\Catalog\StoreTable;

// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
if (isset($_POST["default_location"]) || isset($_POST["default_storages"]) || isset($_POST["default_branch"]) || isset($_POST["default_abs_branch"])) {
    if (isset($_POST["default_location"])) {
        try {
            $temp = json_decode($_POST["default_location"], true);
            if ($temp["ID"] && $temp["CODE"] && $temp["NAME_RU"]) {
                Option::set("respect", "default_location", json_encode(array(
                    "ID" => $temp["ID"],
                    "CODE" => $temp["CODE"],
                    "NAME_RU" => $temp["NAME_RU"],
                ), JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
        }
    }
    if (isset($_POST["donors_targets"])) {
        try {
            $temp = json_decode($_POST["donors_targets"], true);
            $arLocations = array();
            if (!empty($temp)) {
                foreach ($temp as $id => $location) {
                    $id = intval($id);
                    if (!$id || empty($location)) {
                        continue;
                    }
                    $arLocations[$id] = array(
                        'location_id' => $location['location_id'],
                        'id' => $location['id'],
                        'name' => $location['name'],
                    );
                }
            }
            if (!empty($arLocations)) {
                Option::set("respect", "donors_targets", json_encode($arLocations, JSON_UNESCAPED_UNICODE));
            } else {
                Option::set("respect", "donors_targets", json_encode(array(), JSON_UNESCAPED_UNICODE));
            }
            Application::getInstance()->getTaggedCache()->clearByTag('unique_showcases');
            Application::getInstance()->getTaggedCache()->clearByTag('unique_showcasesWO');
            unset($arLocations);
        } catch (\Exception $e) {
        }
    }
    if (isset($_POST["default_storages"])) {
        try {
            $temp = json_decode($_POST["default_storages"], true);
            $arStorages = array();
            foreach ($temp as $id => $val) {
                $id = intval($id);
                if (!$id || empty($val)) {
                    continue;
                }
                $arStorages[$id] = array(
                    0 => $val[0] == 1 ? 1 : 0,
                    1 => $val[1] == 1 ? 1 : 0,
                );
            }
            if (!empty($arStorages)) {
                Option::set("respect", "default_storages", json_encode($arStorages, JSON_UNESCAPED_UNICODE));
            }
            unset($arStorages);
        } catch (\Exception $e) {
        }
    }
    $_POST["default_branch"] = intval($_POST["default_branch"]);
    if ($_POST["default_branch"]) {
        Option::set("respect", "default_branch", $_POST["default_branch"]);
    }
    $_POST["default_abs_branch"] = intval($_POST["default_abs_branch"]);
    if ($_POST["default_abs_branch"]) {
        Option::set("respect", "default_abs_branch", $_POST["default_abs_branch"]);
    }
    $ok = true;
}

if (isset($_POST['geolocation_list'])) {
    $arGeolocations = json_decode($_POST['geolocation_list'], true);

    if (!empty($arGeolocations)) {
        $arValues = [];

        foreach ($arGeolocations as $geolocation) {
            $locationCode = $DB->ForSQL(trim($geolocation['location_code']));

            if (!$locationCode) {
                continue;
            }

            $name = $DB->ForSQL(trim($geolocation['name']));
            $sort = intval($geolocation['sort']);

            $arValues[] = "('$locationCode', '$name', $sort)";
        }

        if (!empty($arValues)) {
            $DB->Query("
                TRUNCATE TABLE b_popular_locality;
            ");
            $DB->Query("
                INSERT INTO b_popular_locality (location_code, name, sort)
                VALUES " . implode(',', array_unique($arValues)) . ";
            ");

            $ok = true;
            Application::getInstance()->getTaggedCache()->clearByTag('popular_localities');
            Application::getInstance()->getTaggedCache()->clearByTag('unique_showcases');
            Application::getInstance()->getTaggedCache()->clearByTag('unique_showcasesWO');
        }
    }
}

if (is_set($_POST["default_discount"])) {
    $defaultDiscount = intval($_POST["default_discount"]) ?? 0;
    Option::set("respect", "default_discount", $defaultDiscount);
}

Loader::includeModule("sale");
Loader::includeModule("catalog");

$aTabs = array(
    array(
        "DIV" => "qsoft_default",
        "TAB" => 'Настройка значений по умолчанию',
        "ICON"=> "main_user_edit",
        "TITLE"=> 'Настройка значений по умолчанию для складов, филиалов, местоположений',
    ),
    array(
        'DIV' => 'select_geolocation_list',
        'TAB' => 'Задать список городов для выбора посетителем',
        'TITLE' => 'Список городов, который будет выведен пользователю для выбора региона доставки в шапке сайта',
        'ICON' => 'main_user_edit',
    ),
    array(
        'DIV' => 'select_locations_for_donors',
        'TAB' => 'Задать список местоположений для донорства',
        'TITLE' => 'Список местоположений (и всех дочерних), на витрину которых будут добавлены товары на доставку складов по умолчанию',
        'ICON' => 'main_user_edit',
    ),
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs, true, true);
// создаем подключение
$connection = Application::getConnection();
// получаем все филиалы
$res = $connection->query("SELECT id, name FROM b_respect_branch");
$arBranches = array();
while ($arItem = $res->Fetch()) {
    $arBranches[$arItem["id"]] = $arItem["name"];
}
// получаем все склады
$res = StoreTable::getList(array(
    'select' => array(
        "ID",
        "TITLE",
    ),
    'filter' => array(
        "ACTIVE" => "Y",
    ),
));
$arStorages = array(
    0 => "Не выбраны",
);
while ($arItem = $res->Fetch()) {
    $arStorages[$arItem["ID"]] = $arItem["TITLE"];
}

$rsGeolocationList = $DB->Query(
    'SELECT location_code, name, sort FROM b_popular_locality ORDER BY sort'
);

while ($arLocality = $rsGeolocationList->Fetch()) {
    $arLocalities[] = $arLocality;
}

$APPLICATION->SetTitle('Настройки филалов и складов');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID ?>"  enctype="multipart/form-data" id="editform" name="editform">
<?= bitrix_sessid_post() ?>
<input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
<?
if ($ok) {
    echo CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Данные успешно обновлены",
        "TYPE"=> "OK",
    ));
}
$tabControl->Begin();
$tabControl->BeginNextTab();
$default_discount = Option::get("respect", "default_discount", 20);
try {
    $default_location = json_decode(Option::get("respect", "default_location"), true);
    if (empty($default_location)) {
        $default_location = array("NAME_RU" => "Не выбрано");
    }
    $default_storages = json_decode(Option::get("respect", "default_storages"), true);
    if (empty($default_storages)) {
        $default_storages = array();
    }
    $donors_targets = json_decode(Option::get("respect", "donors_targets"), true);
    if (empty($donors_targets)) {
        $donors_targets = array();
    }
} catch (\Exception $e) {
    $default_location = array("NAME_RU" => "Не выбрано");
    $default_storages = array();
}
?>
<tr>
    <td>
        <table>
            <tr>
                <td>Местоположение по умолчанию: </td>
                <td>
                    <span id="loc_view_0" name="loc_view_0"><?= $default_location["NAME_RU"] ?></span>
                    <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'loc', 0)" value="...">
                    <input type="hidden" id="loc_loc_0" name="loc_loc_0" value="<?= $default_location["CODE"] ?>">
                    <input type="hidden" id="loc_id_0" name="loc_id_0" value="<?= $default_location["ID"] ?>">
                </td>
            </tr>
            <tr>
                <td>Скидка по умолчанию</td>
                <td>
                    <input type="number" min="0" max="100" value="<?=$default_discount?>" id="default_discount" name="default_discount">
                </td>
            </tr>
            <tr>
                <td>Филиал, который является страховочной ценой: </td>
                <td><? getBranchSelect("default_branch", Option::get("respect", "default_branch")) ?></td>
            </tr>
            <tr>
                <td>Филиал, который является безусловной ценой: </td>
                <td><? getBranchSelect("default_abs_branch", Option::get("respect", "default_abs_branch")) ?></td>
            </tr>
            <tr>
                <td>Склады по умолчанию: </td>
                <td>
                    <table class="location_table">
                        <tr>
                            <td class="align_left" colspan="2">Склад</td>
                            <td>Доставка</td>
                            <td>Резерв</td>
                            <td></td>
                        </tr>
                    <? $i = 0 ?>
                    <? foreach ($default_storages as $id => $storage) : ?>
                        <tr class="str_row" data-num="<?= $i ?>">
                            <td id="str_view_<?= $i ?>" class="align_left"><?= $arStorages[$id] ?></td>
                            <td><input class="tablebodybutton" type="button" OnClick="open_win('storage_search', 'str', <?= $i ?>)" value="...">
                                <input type="hidden" id="str_id_<?= $i ?>" value="<?= $id ?>"></td>
                            <td><input type="checkbox" id="str_del_<?= $i ?>" <?= $storage[0] ? "checked" : "" ?>></td>
                            <td><input type="checkbox" id="str_res_<?= $i ?>" <?= $storage[1] ? "checked" : "" ?>></td>
                            <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                        </tr>
                        <? $i++; ?>
                    <? endforeach ?>
                        <tr class="str_row" data-num="<?= $i ?>">
                            <td id="str_view_<?= $i ?>" class="align_left">Выберите склад:</td>
                            <td><input class="tablebodybutton" type="button" onClick="open_win('storage_search', 'str', <?= $i ?>)" value="...">
                                <input type="hidden" id="str_id_<?= $i ?>"></td>
                            <td><input type="checkbox" id="str_del_<?= $i ?>"></td>
                            <td><input type="checkbox" id="str_res_<?= $i ?>"></td>
                            <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                        </tr>
                    </table>
                    <input type="button" id="str_but" data-next="<?= $i + 1 ?>" value="Ещё" onClick="loc_link_next('str')">
                </td>
            </tr>
        </table>
        <script type="text/javascript">
        $(document).ready(function(){
            $("#editform").submit(function(){
                // генерируем JSON для местоположения по умолчанию
                var value = {
                    ID: $('#loc_id_0').val(),
                    CODE: $('#loc_loc_0').val(),
                    NAME_RU: $('#loc_view_0').html()
                };
                value = JSON.stringify(value);
                $('<input>').attr('type', 'hidden')
                            .attr('name', 'default_location')
                            .attr('value', value)
                            .appendTo('#editform');
                // генерируем JSON для складов по умолчанию
                var value = [];
                $(".str_row").each(function(){
                    var i = $(this).data("num");
                    if($("#str_id_" + i).val().trim() != "") {
                        value[$("#str_id_" + i).val()] = [
                            llc("#str_del_" + i),
                            llc("#str_res_" + i)
                        ];
                    }
                });
                value = JSON.stringify(value);
                $('<input>').attr('type', 'hidden')
                            .attr('name', 'default_storages')
                            .attr('value', value)
                            .appendTo('#editform');
            });
        });
        function loc_link_next() {
            var el = $("#str_but");
            var num = el.data("next");
            //тут мы сразу добавляем label, потому что битрикс их генерит, чтобы поменять отображение
            el.prev("table").append(`<tr class="str_row" data-num="` + num + `">
                <td id="str_view_` + num + `" class="align_left">Выберите склад:</td>
                <td><input class="tablebodybutton" type="button" onClick="open_win('storage_search', 'str', ` + num + `)" value="...">
                    <input type="hidden" id="str_id_` + num + `"></td>
                <td><input type="checkbox" id="str_del_` + num + `" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="str_del_` + num + `"></label></td>
                <td><input type="checkbox" id="str_res_` + num + `" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="str_res_` + num + `"></label></td>
                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
            </tr>`);
            el.data("next", num + 1);
        }
        function llc(el) {return $(el).prop("checked") === true ? 1 : 0}
        function open_win(page, name, num){ window.open('/local/php_interface/include/qsoft/tools/'+page+'.php?lang=ru&field_name='+name+'&field_num='+num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
        </script>
        <style>.location_table td{text-align:center}.str_row td:first-child{min-width:171px}.location_table .align_left{text-align:left!important}</style>
    </td>
</tr>

<?php
$tabControl->BeginNextTab()
?>

<tr>
    <td>
        <table id="geolocation_table">
        <?php foreach ($arLocalities as $index => $arLocality) : ?>
            <tr data-index="<?= $index ?>">
                <td id="geolocation_list_view_<?= $index ?>">
                    <?= $arLocality['name'] ?>
                </td>
                <td>
                    <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'geolocation_list', <?= $index ?>)" value="Изменить">
                    <input type="hidden" id="geolocation_list_loc_<?= $index ?>" name="geolocation_list_loc_<?= $index ?>" value="<?= $arLocality['location_code'] ?>">
                </td>
                <td>
                    <input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()">
                </td>
                <td>
                    <input type="text" id="geolocation_list_sort_<?= $index ?>" name="geolocation_list_sort_<?= $index ?>" value="<?= $arLocality['sort'] ?>">
                </td>
            </tr>
        <?php endforeach; ?>
        </table>

        <?php
        $index = $index ?? -1;
        ?>

        <br>
        <input type="button" id="add_locality" data-next="<?= $index + 1 ?>" value="Добавить" onClick="addLocalityRow(this.id, 'geolocation_table')">

        <script type="text/javascript">
            $(document).ready(function() {
                $('#editform').submit(function(){
                    var geolocationList = getGeolocationJSON();

                    $('<input>').attr('type', 'hidden')
                                .attr('name', 'geolocation_list')
                                .attr('value', geolocationList)
                                .appendTo('#editform');
                });
            });

            function getGeolocationJSON() {
                var geolocationList = [];
                var type = null;
                var index = null;

                $('[id^=geolocation_list_]').each(function() {
                    type = this.id.split('_')[2];
                    index = this.id.split('_')[3];
                    
                    if (geolocationList[index] == undefined) {
                        geolocationList[index] = {};
                    }

                    switch (type) {
                        case 'loc':
                            geolocationList[index].location_code = $(this).val();
                            break;

                        case 'view':
                            geolocationList[index].name = $(this).text();
                            break;

                        case 'sort':
                            geolocationList[index].sort = $(this).val();
                            break;
                    }
                });

                return JSON.stringify(geolocationList);
            }

            function addLocalityRow(buttonID, tableID) {
                var button = $('#' + buttonID);
                var table = $('#' + tableID);
                var index = button.data('next');

                table.append(`
                    <tr>
                        <td id="geolocation_list_view_` + index + `">
                            Выберите город
                        </td>
                        <td>
                            <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'geolocation_list', ` + index + `)" value="Выбрать">
                            <input type="hidden" id="geolocation_list_loc_` + index + `" name="geolocation_list_loc_` + index + `" value="">
                        </td>
                        <td>
                            <input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()">
                        </td>
                        <td>
                            <input type="text" id="geolocation_list_sort_` + index + `" name="geolocation_list_sort_` + index + `" placeholder="Сортировка">
                        </td>
                    </tr>
                `);

                button.data('next', index + 1);
            }
        </script>

        <style type="text/css">
            #geolocation_table
            {
                width: 100%;
            }

            #geolocation_table td, #add_locality
            {
                text-align: center;
                width: 25%;
            }
        </style>
    </td>
</tr>

<?php
$tabControl->BeginNextTab()
?>
<tr>
    <td>
        <table>
            <tr>
                <td style="width: 300px">Местоположения для донорства: </td>
                <td>
                    <table class="location_table">
                        <tr>
                            <td class="align_left" colspan="2">Местоположение</td>
                            <td></td>
                        </tr>
                        <? $k = 0; foreach ($donors_targets as $id => $location) : ?>
                            <tr class="target_row" data-num="<?=$k?>">
                                <td class="align_left" style="min-width: 250px;">
                                    <span id="tar_view_<?=$k?>" name="tar_view_<?=$k?>"><?=$location['name']?></span>
                                </td>
                                <td>
                                    <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'tar', <?=$k?>)" value="...">
                                    <input type="hidden" id="tar_loc_<?=$k?>" name="tar_loc_<?=$k?>" value="<?=$location['location_id']?>">
                                    <input type="hidden" id="tar_id_<?=$k?>" name="tar_id_<?=$k?>" value="<?=$location['id']?>">
                                </td>
                                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                            </tr>
                            <? $k++; ?>
                        <? endforeach; ?>
                        <tr class="target_row" data-num="<?=$k?>">
                            <td class="align_left" style="min-width: 250px;">
                                <span id="tar_view_<?=$k?>" name="tar_view_<?=$k?>">Выберите местоположение:</span>
                            </td>
                            <td>
                                <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'tar', <?=$k?>)" value="...">
                                <input type="hidden" id="tar_loc_<?=$k?>" name="tar_loc_<?=$k?>" value="">
                                <input type="hidden" id="tar_id_<?=$k?>" name="tar_id_<?=$k?>" value="">
                            </td>
                            <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                        </tr>
                    </table>
                    <input type="button" id="target_btn" data-next="<?=$k + 1?>" value="Ещё" onClick="loc_link_next_donor('str')">
                </td>
            </tr>
        </table>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#editform").submit(function(){
                    // генерируем JSON для местоположений-целей для доноров
                    let locs = {};

                    $(".target_row").each(function(){
                        let i = $(this).data("num");
                        if($("#tar_id_" + i).val() != "") {
                            if (locs[$("#tar_id_" + i).val()] == undefined) {
                                locs[$("#tar_id_" + i).val()] = {};
                            }
                            locs[$("#tar_id_" + i).val()].location_id = $("#tar_loc_" + i).val();
                            locs[$("#tar_id_" + i).val()].id = $("#tar_id_" + i).val();
                            locs[$("#tar_id_" + i).val()].name = $("#tar_view_" + i).text().trim();
                        }
                    });
                    locs = JSON.stringify(locs);
                    $('<input>').attr('type', 'hidden')
                        .attr('name', 'donors_targets')
                        .attr('value', locs)
                        .appendTo('#editform');
                });
            });
            function loc_link_next_donor() {
                var el = $("#target_btn");
                var num = el.data("next");
                el.prev("table").append(`<tr class="target_row" data-num="` + num + `">
                    <td class="align_left" style="min-width: 250px;">
                        <span id="tar_view_` + num + `" name="tar_view_` + num + `">Выберите местоположение:</span>
                    </td>
                    <td>
                        <input class="tablebodybutton" type="button" onClick="open_win('location_search', 'tar', ` + num + `)" value="...">
                        <input type="hidden" id="tar_loc_` + num + `" name="tar_loc_` + num + `" value="">
                        <input type="hidden" id="tar_id_` + num + `" name="tar_id_` + num + `" value="">
                    </td>
                    <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                </tr>`);
                el.data("next", num + 1);
            }
            function open_win(page, name, num){
                window.open('/local/php_interface/include/qsoft/tools/'+page+'.php?lang=ru&field_name='+name+'&field_num='+num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));
            }
        </script>
    </td>
</tr>
<?
$tabControl->Buttons(
    array(
        "disabled" => false,
        "back_url" => $APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID,
    )
);
$tabControl->End();
?>
</form>
<?
function getBranchSelect($select_name, $value)
{
    global $arBranches;
    ?>
    <select name="<?= $select_name ?>">
        <option value="0">Не выбран</option>
        <? foreach ($arBranches as $id => $name) : ?>
        <option value="<?= $id ?>" <?= $id == $value ? "selected" : "" ?>><?= $name ?></option>
        <? endforeach ?>
    </select>
    <?
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
