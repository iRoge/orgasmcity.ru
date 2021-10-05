<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

$result = CIBlockElement::GetList(
    [
        "ID" => "ASC"
    ],
    [
        'IBLOCK_ID' => IBLOCK_FEEDBACK,
    ],
    false,
    false,
    ['ID']
);
$nowStamp = time();
$fromStamp = $nowStamp - 60 * 60 * 24 * 7;
while ($arElement = $result->GetNext()) {
    $randStamp = rand($fromStamp, $nowStamp);
    $element = new CIBlockElement();
    $element->Update($arElement['ID'], ['DATE_ACTIVE_FROM' => date('d.m.Y H:i:s', $randStamp)]);
}