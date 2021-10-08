<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

$order = array('sort' => 'asc');
$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
$rsUsers = CUser::GetList($order, $tmp);
$arEmails = [];
while ($arUser = $rsUsers->Fetch()) {
    $arEmails[] = $arUser["EMAIL"];
}

$result = CIBlockElement::GetList(
    [
        "ID" => "ASC"
    ],
    [
        'IBLOCK_ID' => IBLOCK_SUBSCRIBERS,
    ],
    false,
    false,
    ['ID', 'PROPERTY_EMAIL']
);

$arAlreadyExists = [];
while ($element = $result->Fetch()) {
    if (!in_array($element['PROPERTY_EMAIL_VALUE'], $arEmails)) {
        CIBlockElement::Delete($element['ID']);
    }
}