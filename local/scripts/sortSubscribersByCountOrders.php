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
    $arAlreadyExists[$element['ID']] = mb_strtolower($element['PROPERTY_EMAIL_VALUE']);
}

$contacts = unserialize(file_get_contents('local/scripts/contacts.txt'));
$contactsByEmail = [];
foreach ($contacts as $contact) {
    $contactsByEmail[mb_strtolower($contact['email'])] = $contact;
}
$i = 0;
foreach ($arAlreadyExists as $id => $email) {
    $countOrders = 0;
    if (isset($contactsByEmail[$email])) {
        $countOrders = $contactsByEmail[$email]['countOrders'];
    } else {
        continue;
    }

    $el = new CIBlockElement;
    $arFields = [
        "SORT" => 400 - $countOrders,
    ];
    $el->Update($id, $arFields);
    echo 'Обработано ' . ++$i . PHP_EOL;
}

