<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle("Импорт сортировки по актикулам");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->IncludeComponent(
    "bitpro:import.sort.by.article",
    "",
    array(
        "IBLOCK_TYPE" => "test",
        "IBLOCK_ID" => 16,
        "IMPORT" => [
            "FILE_NAME" => 'inport_file',
            "TEMP_FILE_DIR" => '/upload/csvimport/',
            "DELIMITER" => ';',
        ]
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
