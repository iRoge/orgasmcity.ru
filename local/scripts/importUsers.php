<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

ini_set('memory_limit', '-1');

$result = CIBlockElement::GetList
(
    ["ID" => "ASC"],
    [
        'IBLOCK_ID' => IBLOCK_MAILING,
        'SECTION_ID' => 0,
        'INCLUDE_SUBSECTIONS' => 'N'
    ]
);

while ($element = $result->Fetch()) {
    CIBlockElement::Delete($element['ID']);
}

$contacts = unserialize(file_get_contents('local/scripts/contacts.txt'));

foreach ($contacts as $contact) {
    $el = new CIBlockElement;

    $props = [];
    $props['NAME'] = $contact['name'];
    $props['SECOND_NAME'] = $contact['secondName'];
    $props['MIDDLE_NAME'] = $contact['middleName'];
    $props['EMAIL'] = $contact['email'];
    $props['PHONE'] = $contact['phone'];
    $props['SUBSCRIBE'] = 1;

    $arLoadProductArray = [
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => IBLOCK_MAILING,
        "PROPERTY_VALUES" => $props,
        "NAME" => $contact['secondName'] . ' ' . $contact['name'] . ' ' . $contact['middleName'],
        "ACTIVE" => "Y",
    ];

    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        echo "New ID: " . $PRODUCT_ID . PHP_EOL;
    } else {
        echo "Error: " . $el->LAST_ERROR . PHP_EOL;
    }
}


