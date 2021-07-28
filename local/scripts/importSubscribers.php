<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

ini_set('memory_limit', '-1');

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
    $arAlreadyExists[] = mb_strtolower($element['PROPERTY_EMAIL_VALUE']);
}

$contacts = unserialize(file_get_contents('local/scripts/contacts.txt'));
$validEmailsJson = json_decode(file_get_contents('local/scripts/validEmails.json'), true);

$validEmails = [];
foreach ($validEmailsJson['root']['row'] as $email) {
    $validEmails[] = mb_strtolower($email['Email']);
}

foreach ($contacts as $contact) {
    if (
        !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)
        || in_array($contact['email'], $arAlreadyExists)
        || !in_array($contact['email'], $validEmails)
    ) {
        continue;
    }
    $el = new CIBlockElement;

    $props = [];
    $props['NAME'] = $contact['name'];
    $props['SECOND_NAME'] = $contact['secondName'];
    $props['MIDDLE_NAME'] = $contact['middleName'];
    $props['EMAIL'] = $contact['email'];
    $props['PHONE'] = $contact['phone'];

    $arLoadProductArray = [
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => IBLOCK_SUBSCRIBERS,
        "PROPERTY_VALUES" => $props,
        "NAME" => $props['EMAIL'],
        "ACTIVE" => "Y",
    ];

    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        echo "New ID: " . $PRODUCT_ID . PHP_EOL;
    } else {
        echo "Error: " . $el->LAST_ERROR . PHP_EOL;
    }
}


