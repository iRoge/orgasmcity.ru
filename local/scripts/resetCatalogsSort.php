<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

$result = CIBlockSection::GetList(
    [
        "ID" => "ASC"
    ],
    [
        'IBLOCK_ID' => IBLOCK_CATALOG,
        "ACTIVE" => "Y",
    ],
    false,
    ['ID', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL']
);

while ($arSection = $result->GetNext()) {
    $section = new CIBlockSection();
    $section->Update($arSection['ID'], ['SORT' => 100]);
}