<?
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../../../../");

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm("Доступ запрещён");
}
// обработка параметров
$find_id = intval($_GET["find_id"]) ?: "";
$find_region = intval($_GET["find_region"]) ?: "";
$find_code = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["find_code"]);
$find_name = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["find_name"]);
$field_name = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["field_name"]);
$field_num = intval($_GET["field_num"]);
$value_type = $_GET['value_type'] ?: '';
$by = in_array($_GET["by"], array("ID", "CODE", "NAME_RU")) ? $_GET["by"] : "ID";
$order = $_GET["order"] == "desc" ? "desc" : "asc";
// идентификатор таблицы
$sTableID = "tbl_location_popup";
// инициализация сортировки
$oSort = new CAdminSorting($sTableID, $by, $order);
// инициализация списка
$lAdmin = new CAdminList($sTableID, $oSort);
// подготовка к выборке
CModule::IncludeModule("sale");
// формируем фильтр для запроса
$arFilter = array(
    'ID' => $find_id,
    '=TYPE.ID' => $find_region,
    '%CODE' => $find_code,
    '%NAME.NAME' => $find_name,
    '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
);
// убираем ключи с пустыми значениями
foreach ($arFilter as $key => $value) {
    if (!$value || empty($value)) {
        unset($arFilter[$key]);
    }
}
// запрос
$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => $arFilter,
    'select' => array(
        'ID',
        'CODE',
        'NAME_RU' => 'NAME.NAME'
    ),
    'order' => array(
        $by => $order
    ),
));

// постраничная навигация
$rsData = new CAdminResult($res, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Местоположения"));
// добавляем заголовки
$lAdmin->AddHeaders(array(
    array(
        "id" => "ID",
        "content" => "ID",
        "sort" => "ID",
        "default" => true,
    ),
    array(
        "id" => "CODE",
        "content" => "Код местоположения",
        "sort" => "CODE",
        "default" => true,
    ),
    array(
        "id" => "NAME_RU",
        "content" => "Название",
        "sort" => "NAME_RU",
        "default" => true,
    ),
));
// рисуем таблицу с результатами выборки
while ($arRes = $rsData->Fetch()) {
    //если нам нужно найти регион для админки страницы магазинов
    if ($find_region) {
        switch ($arRes['NAME_RU']) {
            case 'Московская область':
                $arRes['NAME_RU'] = "Москва и область";
                break;
            case 'Ленинградская область':
                $arRes['NAME_RU'] = "Санкт-Петербург и область";
                break;
        }
    }
    $row = $lAdmin->AddRow($arRes['ID'], $arRes);
    $arActions = array(
        array(
            "ICON" => "",
            "TEXT" => "Выбрать",
            "DEFAULT" => true,
            "ACTION" => "SetValue('".$arRes["NAME_RU"]."', '".$arRes["CODE"]."', '".$arRes["ID"]."');"
        ),
    );
    $row->AddActions($arActions);
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php")
?>
<? if ($_GET['new_format'] != true) :?>
<script type="text/javascript">
function SetValue(name, code, id) {
    window.opener.document.getElementById('<?= $field_name ?>_view_<?= $field_num ?>').innerHTML = name;
    <? if ($find_region || $value_type === 'name') : ?>
    window.opener.document.getElementById('<?= $field_name ?>_loc_<?= $field_num ?>').value = name;
    <? else : ?>
    window.opener.document.getElementById('<?= $field_name ?>_loc_<?= $field_num ?>').value = code;
    <? endif ?>
    a = window.opener.document.getElementById('<?= $field_name ?>_id_<?= $field_num ?>');
    if (a) {
        a.value = id
    }
    window.close();
}
</script>
<? else :?>
    <script type="text/javascript">
        function SetValue(name, code, id) {
            window.opener.document.getElementById('loc_name').value = name;
            window.opener.document.getElementById('loc_code').value = code;
            window.opener.document.getElementById('loc_name_visible').innerText = name;
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            var el = window.opener.document.getElementById('loc_code');
            el.dispatchEvent(evt);
            window.close();
        }
    </script>
<? endif;?>
<form name="find_form" method="GET" action="<?= $APPLICATION->GetCurPage() ?>?">
<?
// рисуем табличку фильтра
$oFilter = new CAdminFilter($sTableID."_filter");
$oFilter->Begin();
?>
<tr>
    <td>ID:</td>
    <td><input type="text" name="find_id" size="40" value="<?= htmlspecialchars($find_id) ?>"></td>
</tr>
<tr>
    <td>Код&nbsp;местоположения:</td>
    <td><input type="text" name="find_code" size="40" value="<?= htmlspecialchars($find_code) ?>"></td>
</tr>
<tr>
    <td>Название:</td>
    <td><input type="text" name="find_name" size="40" value="<?= htmlspecialchars($find_name) ?>"></td>
</tr>
<? if ($find_region) : ?>
<input type="hidden" name="find_region" value="<?= htmlspecialchars($find_region) ?>">
<? endif ?>
<input type="hidden" name="field_name" value="<?= htmlspecialchars($field_name) ?>">
<input type="hidden" name="field_num" value="<?= htmlspecialchars($field_num) ?>">
<input type="hidden" name="value_type" value="<?= htmlspecialchars($value_type) ?>">
<?
if ($_GET['new_format']) {
    $oFilter->Buttons(
        array(
            "table_id" => $sTableID,
            "url" => $APPLICATION->GetCurPage() . '?new_format=' . $_GET['new_format'],
            "form" => "find_form"
        )
    );
} else {
    $oFilter->Buttons(
        array(
            "table_id" => $sTableID,
            "url" => $APPLICATION->GetCurPage(),
            "form" => "find_form"
        )
    );
}

$oFilter->End();
?>
</form>
<?
//Выводим список
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
