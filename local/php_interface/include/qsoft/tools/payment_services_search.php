<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Sale\Internals\PaySystemActionTable as PaymentTable;
use Qsoft\PaymentWays\WaysByPaymentServicesTable;

$sTableID = "tbl_delivery_services_popup";
$lAdmin = new CAdminList($sTableID);
CModule::IncludeModule("sale");

$arDeliveriesIds = WaysByPaymentServicesTable::getList([
   'select' => ['PAYMENT_ID'],
])->fetchAll();

$arRequestIds = explode(',', $_REQUEST['id']);
$arDeliveriesIds = array_column($arDeliveriesIds, 'PAYMENT_ID');
$arDeliveriesIds = array_merge($arDeliveriesIds, $arRequestIds);

$arFilter = [
    'ACTIVE' => 'Y',
    '!@ID' => $arDeliveriesIds
];

$res = PaymentTable::getList([
    'filter' => $arFilter,
    'select' => ['ID', 'NAME', 'DESCRIPTION']
]);

$lAdmin->AddHeaders(array(
    array(
        "id" => "ID",
        "content" => "ID",
        "sort" => "ID",
        "default" => true,
    ),
    array(
        "id" => "NAME",
        "content" => "Название",
        "sort" => "NAME",
        "default" => true,
    ),
    array(
        "id" => "DESCRIPTION",
        "content" => "Описание",
        "sort" => "DESCRIPTION",
        "default" => true,
    ),
));

while ($arRes = $res->Fetch()) {
    $row = $lAdmin->AddRow($arRes['ID'], $arRes);
    $arActions = array(
        array(
            "ICON" => "",
            "TEXT" => "Выбрать",
            "DEFAULT" => true,
            "ACTION" => "SetValue('". $arRes["ID"] . "', '". $arRes["NAME"] . "', '". $arRes["DESCRIPTION"] . "');"
        ),
    );
    $row->AddActions($arActions);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");?>

<script type="text/javascript">
    function SetValue(id, name, desc) {
        window.opener.document.getElementById('input_delivery_<?=$_REQUEST['num']?>').value = id;
        window.opener.document.getElementById('name<?=$_REQUEST['num']?>').innerHTML = name;
        window.opener.document.getElementById('desc<?=$_REQUEST['num']?>').innerHTML = desc;
        window.close();
    }
</script>

<?php $lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>
