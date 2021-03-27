<?php

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Qsoft\Sailplay\SailPlayApi;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) {
        global $$f;
    }

    return count($lAdmin->arFilterErrors) == 0;
}

$nPageSize = intval($_REQUEST['SIZEN_1']) ?? 20;
$sTableID = "sub_edit_tbl";
$oSort = new CAdminSorting($sTableID, ['ID']);

$FilterArr = array(
    "filter_date_from",
    "filter_date_to",
    "status_sms",
    "status_email",
);

$lAdmin = new CAdminList($sTableID, $oSort);
$lAdmin->InitFilter($FilterArr);
$arFilter = [];

if (($arID = $lAdmin->GroupAction())) {
    $action = empty($_REQUEST['action']) ? $_REQUEST['action_button'] : $_REQUEST['action'];

    $arTempFilter = ['ID' => implode('|', $arID)];
    if ($_REQUEST['action_target'] == 'selected') {
        $arTempFilter = $arFilter;
    }
    $dataDb = CUser::GetList($by, $order, $arTempFilter, [
        'SELECT' => ['ID', 'EMAIL'],
    ]);
    while ($arRes = $dataDb->fetch()) {
        switch ($action) {
            case 'subscribeSms':
                $USER->Update($arRes['ID'], [
                    'UF_SUBSCRIBE_SMS' => 1,
                    'UF_SP_LAST_TAG' => 'Подписка на SMS: Админка сайта',
                    'UF_SP_SUB_TIME' => new DateTime(),
                ]);
                SailPlayApi::userSubscribe($arRes['EMAIL'], ['sms_all'], 'email');
                SailPlayApi::userAddTags($arRes['EMAIL'], ['Подписка на SMS: Админка сайта'], 'email');
                break;
            case 'unsubscribeSms':
                $USER->Update($arRes['ID'], [
                    'UF_SUBSCRIBE_SMS' => 0,
                    'UF_SP_LAST_TAG' => 'Отписка на SMS: Админка сайта',
                    'UF_SP_SUB_TIME' => new DateTime(),
                ]);
                SailPlayApi::userUnsubscribe($arRes['EMAIL'], ['sms_all'], 'email');
                SailPlayApi::userAddTags($arRes['EMAIL'], ['Отписка на SMS: Админка сайта'], 'email');
                break;
            case 'subscribeEmail':
                $USER->Update($arRes['ID'], [
                    'UF_SUBSCRIBE_EMAIL' => 1,
                    'UF_SP_LAST_TAG' => 'Подписка на E-mail: Админка сайта',
                    'UF_SP_SUB_TIME' => new DateTime(),
                ]);
                SailPlayApi::userSubscribe($arRes['EMAIL'], ['email_all'], 'email');
                SailPlayApi::userAddTags($arRes['EMAIL'], ['Подписка на E-mail: Админка сайта'], 'email');
                break;
            case 'unsubscribeEmail':
                $USER->Update($arRes['ID'], [
                    'UF_SUBSCRIBE_EMAIL' => 0,
                    'UF_SP_LAST_TAG' => 'Отписка на E-mail: Админка сайта',
                    'UF_SP_SUB_TIME' => new DateTime(),
                ]);
                SailPlayApi::userUnsubscribe($arRes['EMAIL'], ['email_all'], 'email');
                SailPlayApi::userAddTags($arRes['EMAIL'], ['Отписка на E-mail: Админка сайта'], 'email');
                break;
        }
    }
}
if (CheckFilter()) {
    if (strlen($filter_date_from) > 0) {
        $arFilter[">=UF_SP_SUB_TIME"] = trim($filter_date_from);
    }
    if (strlen($filter_date_to) > 0) {
        if ($arDate = ParseDateTime($filter_date_to, CSite::GetDateFormat("FULL", SITE_ID))) {
            if (strlen($filter_date_to) < 11) {
                $arDate["HH"] = 23;
                $arDate["MI"] = 59;
                $arDate["SS"] = 59;
            }

            $filter_date_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
            $arFilter["<=UF_SP_SUB_TIME"] = $filter_date_to;
        } else {
            $filter_date_to = "";
        }
    }
    if ($status_sms === 'Y') {
        $arFilter["=UF_SUBSCRIBE_SMS"] = 1;
    } elseif ($status_sms === 'N') {
        $arFilter[] = [
            "LOGIC" => "OR",
            [
                "=UF_SUBSCRIBE_SMS" => false,
            ],
            [
                "=UF_SUBSCRIBE_SMS" => 0,
            ],
        ];
    }
    if ($status_email === 'Y') {
        $arFilter["=UF_SUBSCRIBE_EMAIL"] = 1;
    } elseif ($status_email === 'N') {
        $arFilter[] = [
            "LOGIC" => "OR",
            [
                "=UF_SUBSCRIBE_EMAIL" => false,
            ],
            [
                "=UF_SUBSCRIBE_EMAIL" => 0,
            ],
        ];
    }
}


if (($arID = $lAdmin->GroupAction())) {
    $action = empty($_REQUEST['action']) ? $_REQUEST['action_button'] : $_REQUEST['action'];
}

$rsData = UserTable::getList([
    'select' => [
        'ID',
        'EMAIL',
        'PERSONAL_PHONE',
        'UF_SP_LAST_TAG',
        'UF_SP_SUB_TIME',
        'UF_SUBSCRIBE_SMS',
        'UF_SUBSCRIBE_EMAIL',
    ],
    'filter' => $arFilter,
]);

$rsData = new CAdminResult($rsData, $sTableID);

$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Адреса"));

$lAdmin->AddHeaders(array(
    array(
        "id" => "NAME",
        "content" => "ФИО",
        "sort" => "NAME",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "EMAIL",
        "content" => "e-mail",
        "sort" => "EMAIL",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "PHONE",
        "content" => "телефон",
        "sort" => "PHONE",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "SUB_SMS",
        "content" => "Статус подписки на sms",
        "sort" => "SUB_SMS",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "SUB_EMAIL",
        "content" => "Статус подписки на e-mail",
        "sort" => "SUB_EMAIL",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "TIME",
        "content" => "Последнее изменение подписки",
        "sort" => "TIME",
        "align" => "left",
        "default" => true,
    ),
    array(
        "id" => "TAG",
        "content" => "Последний тег подписки",
        "sort" => "TAG",
        "align" => "left",
        "default" => true,
    ),
));
while ($arRes = $rsData->NavNext()) {
    $row =& $lAdmin->AddRow($arRes['ID'], $arRes);
    $row->AddViewField("NAME", GetFormatedUserName($arRes['ID'], false));
    $row->AddViewField("EMAIL", $arRes['EMAIL']);
    $row->AddViewField("PHONE", $arRes['PERSONAL_PHONE']);
    $row->AddViewField("SUB_SMS", $arRes['UF_SUBSCRIBE_SMS'] ? 'Подписан' : 'Не подписан');
    $row->AddViewField("SUB_EMAIL", $arRes['UF_SUBSCRIBE_EMAIL'] ? 'Подписан' : 'Не подписан');
    $row->AddViewField("TAG", $arRes['UF_SP_LAST_TAG']);
    $row->AddViewField("TIME", $arRes['UF_SP_SUB_TIME']);

    $arActions = [];
    if ($arRes['UF_SUBSCRIBE_SMS']) {
        $arActions[] = array(
            "ICON" => "delete",
            "DEFAULT" => true,
            "TEXT" => "Отписать от sms",
            "ACTION" => "if(confirm('Отписать от получения рассылки?')) " . $lAdmin->ActionDoGroup($arRes['ID'], "unsubscribeSms"),
        );
    } else {
        $arActions[] = array(
            "ICON" => "edit",
            "DEFAULT" => true,
            "TEXT" => "Подписать на sms",
            "ACTION" => "if(confirm('Подписать к получению рассылок?')) " . $lAdmin->ActionDoGroup($arRes['ID'], "subscribeSms"),
        );
    }

    if ($arRes['UF_SUBSCRIBE_EMAIL']) {
        $arActions[] = array(
            "ICON" => "delete",
            "DEFAULT" => true,
            "TEXT" => "Отписать от e-mail",
            "ACTION" => "if(confirm('Отписать от получения рассылки?')) " . $lAdmin->ActionDoGroup($arRes['ID'], "unsubscribeEmail"),
        );
    } else {
        $arActions[] = array(
            "ICON" => "edit",
            "DEFAULT" => true,
            "TEXT" => "Подписать на e-mail",
            "ACTION" => "if(confirm('Подписать к получению рассылок?')) " . $lAdmin->ActionDoGroup($arRes['ID'], "subscribeEmail"),
        );
    }

    $row->AddActions($arActions);
}
$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);
$lAdmin->AddGroupActionTable(array(
    "subscribeSms" => 'Подписать на sms',
    "subscribeEmail" => 'Подписать на e-mail',
    "unsubscribeSms" => 'Отписать от sms',
    "unsubscribeEmail" => 'Отписать от e-mail',
));

$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();
$APPLICATION->SetTitle('Управление подписчиками');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
    $sTableID . "_filter",
    array(
        "Дата последнего изменения подписки",
        "Статус подписки на смс",
        "Статус подписки на e-mail",
    )
);

$oFilter->SetDefaultRows(array("time"));

?>
<form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
    <? $oFilter->Begin(); ?>
    <tr>
        <td><b>Дата последнего изменения подписки:</b></td>
        <td>
            <? echo CalendarPeriod("filter_date_from", $filter_date_from, "filter_date_to", $filter_date_to, "find_form", "Y") ?>
        </td>
    </tr>
    <tr>
        <td><b>Статус подписки на смс</b></td>
        <td>
            <? echo SelectBoxFromArray("status_sms", ['REFERENCE' => ['Подписан', 'Не подписан'], 'REFERENCE_ID' => ['Y', 'N']], $status_sms ?? "NOT_REF", 'Не выбрано') ?>
        </td>
    </tr>
    <tr>
        <td><b>Статус подписки на e-mail</b></td>
        <td>
            <? echo SelectBoxFromArray("status_email", ['REFERENCE' => ['Подписан', 'Не подписан'], 'REFERENCE_ID' => ['Y', 'N']], $status_email ?? "NOT_REF", 'Не выбрано') ?>
        </td>
    </tr>
    <?
    $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
    $oFilter->End();
    ?>
</form>
<? $lAdmin->DisplayList(); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
