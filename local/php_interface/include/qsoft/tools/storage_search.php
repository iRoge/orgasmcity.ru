<?
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../../../../");

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm("Доступ запрещён");
}
// обработка параметров
$find_id = intval($_GET["find_id"]) ?: "";
$find_title = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["find_title"]);
$find_addr = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["find_addr"]);
$field_name = preg_replace("/[^а-яёa-z0-9_\\[\\]: ]/iu", "", $_GET["field_name"]);
$field_num = intval($_GET["field_num"]);
$by = in_array($_GET["by"], array("ID", "TITLE", "ADDRESS")) ? $_GET["by"] : "ID";
$order = $_GET["order"] == "desc" ? "desc" : "asc";
// идентификатор таблицы
$sTableID = "tbl_storage_popup";
// инициализация сортировки
$oSort = new CAdminSorting($sTableID, $by, $order);
// инициализация списка
$lAdmin = new CAdminList($sTableID, $oSort);
// подготовка к выборке
CModule::IncludeModule("catalog");
// формируем фильтр для запроса
$arFilter = array(
    'ID' => $find_id,
    '%TITLE' => $find_title,
    '%ADDRESS' => $find_addr,
);
// убираем ключи с пустыми значениями
foreach ($arFilter as $key => $value) {
    if (!$value || empty($value)) {
        unset($arFilter[$key]);
    }
}
// запрос
$res = \Bitrix\Catalog\StoreTable::getList(array(
    'filter' => $arFilter,
    'select' => array(
        'ID',
        'TITLE',
        'ADDRESS',
    ),
    'order' => array(
        $by => $order
    ),
));

// постраничная навигация
$rsData = new CAdminResult($res, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Склады"));
// добавляем заголовки
$lAdmin->AddHeaders(array(
    array(
        "id" => "ID",
        "content" => "ID",
        "sort" => "ID",
        "default" => true,
    ),
    array(
        "id" => "TITLE",
        "content" => "Название",
        "sort" => "TITLE",
        "default" => true,
    ),
    array(
        "id" => "ADDRESS",
        "content" => "Адрес",
        "sort" => "ADDRESS",
        "default" => true,
    ),
));
// рисуем таблицу с результатами выборки
while ($arRes = $rsData->Fetch()) {
    $row = $lAdmin->AddRow($arRes['ID'], $arRes);
    $arActions = array(
        array(
            "ICON" => "",
            "TEXT" => "Выбрать",
            "DEFAULT" => true,
            "ACTION" => "SetValue('".$arRes["TITLE"]."', '".$arRes["ID"]."');"
        ),
    );
    $row->AddActions($arActions);
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php")
?>
<script type="text/javascript">
function SetValue(name, id) {
    window.opener.document.getElementById('<?= $field_name ?>_view_<?= $field_num ?>').innerHTML = name;
    window.opener.document.getElementById('<?= $field_name ?>_id_<?= $field_num ?>').value = id;
    window.close();
}
</script>
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
    <td>Название:</td>
    <td><input type="text" name="find_title" size="40" value="<?= htmlspecialchars($find_title) ?>"></td>
</tr>
<tr>
    <td>Адрес:</td>
    <td><input type="text" name="find_addr" size="40" value="<?= htmlspecialchars($find_addr) ?>"></td>
</tr>
<input type="hidden" name="field_name" value="<?= htmlspecialchars($field_name) ?>">
<input type="hidden" name="field_num" value="<?= htmlspecialchars($field_num) ?>">
<?
$oFilter->Buttons(
    array(
        "table_id" => $sTableID,
        "url" => $APPLICATION->GetCurPage(),
        "form" => "find_form"
    )
);
$oFilter->End();
?>
</form>
<?
//Выводим список
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
