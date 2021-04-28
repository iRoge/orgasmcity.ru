<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
use Qsoft\PaymentWays\WaysPaymentTable;
use Bitrix\Main\Localization\Loc;
use Qsoft\PaymentWays\WaysByPaymentServicesTable;
?>

<?
$sTableID = "tbl_ways"; // ID таблицы
$link_field = 'PAYMENTS';
$lAdmin = new CAdminList($sTableID); // основной объект списка
$arHeaders = setHeadersArray($link_field); // Массив заголовков в таблице
$fields = WaysPaymentTable::getEntity()->getFields();

//Обработка действий над списком
if ($request->getQuery('action_button') == 'delete') {
    $ID = $request->getQuery('ID');
    WaysPaymentTable::Delete($ID);
    WaysByPaymentServicesTable::Delete(['WAY_ID' => $ID]);
}

$ways_collection = WaysPaymentTable::GetList([
    'select' => ['*', $link_field],
])->fetchCollection();

$ways_array = [];

foreach ($ways_collection as $key => $way) {
    foreach ($fields as $field) {
        if ($field->getName() != $link_field) {
            $ways_array[$key][$field->getName()] = $way->get($field->getName());
        } elseif ($field->getName() == $link_field) {
            $ar1CIds = WaysByPaymentServicesTable::getList([
                'filter' => ['WAY_ID' =>  $way->get('ID')],
                'select' => ['ID_1C', 'PAYMENT_ID']
            ])->fetchAll();
            $ways_array[$key][$link_field]['PAYMENTS'] = array_unique(array_column($ar1CIds, 'PAYMENT_ID'));
            $ways_array[$key][$link_field]['1C_ID'] = array_column($ar1CIds, 'ID_1C');
        }
    }
}

$rs = new CDBResult;
$rs->InitFromArray($ways_array);
$rsData = new CAdminResult($rs, $sTableID);

function setHeadersArray($link_field): array
{
    $fields = WaysPaymentTable::getEntity()->getFields();

    $arHeaders = [];

    foreach ($fields as $field) {
        if ($field->getName() == $link_field) {
            $arHeaders[] = [
                "id" => $field->getName(),
                "content" => Loc::getMessage("DEL_1C"),
                "sort" => strtolower($field->getName()),
                "default" => true,
            ];
        } else {
            $arHeaders[] = [
                "id" => $field->getName(),
                "content" => $field->getTitle(),
                "sort" => strtolower($field->getName()),
                "default" => true,
            ];
        }
    }

    return $arHeaders;
}

$aContext = array(
    array(
        "TEXT"=>Loc::getMessage("WAY_ADD"),
        "LINK"=>"payment_way_edit.php?lang=".LANG,
        "TITLE"=>Loc::getMessage("WAY_ADD_TITLE"),
        "ICON"=>"btn_new",
    ),
);
$lAdmin->AddAdminContextMenu($aContext, false, false);

while ($arRes = $rsData->NavNext()) {
    $row =& $lAdmin->AddRow($arRes['ID'], $arRes);
    $html = '';

    foreach ($arRes[$link_field]['PAYMENTS'] as $key => $payment_id) {
        $comma =  ($payment_id == end($arRes[$link_field]['PAYMENTS'])) ? '' : ', ';
        $html .= $arRes[$link_field]['1C_ID'][$key] . '/<a href="/bitrix/admin/sale_payment_service_edit.php?ID=' . $payment_id . '">' . $payment_id . '</a>' . $comma;
        $active = $arRes['ACTIVE'] ? 'Y' : 'N';
        $local = $arRes['LOCAL'] ? 'Y' : 'N';
        $prepayment = $arRes['PREPAYMENT'] ? 'Y' : 'N';
    }

    $arActions[0] = [
        "ICON"=>"delete",
        "TEXT"=>Loc::getMessage("DELETE"),
        "ACTION"=>"if(confirm('".Loc::getMessage("DELETE_Q")."')) ".$lAdmin->ActionDoGroup($arRes['ID'], "delete")
    ];

    $row->AddActions($arActions);
    $row->AddViewField("ACTIVE", $active);
    $row->AddViewField("LOCAL", $local);
    $row->AddViewField("PREPAYMENT", $prepayment);
    $row->AddViewField("NAME", '<a href="payment_way_edit.php?WAY_ID='. $arRes['ID'] . '&lang=' . LANG . '">' . $arRes['NAME'] . '</a>');
    $row->AddViewField("PAYMENTS", $html);
}

$lAdmin->AddHeaders($arHeaders);
$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle(Loc::getMessage("TITLE"));
$lAdmin->DisplayList();

?>
<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
