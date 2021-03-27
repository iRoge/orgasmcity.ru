<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->SetTitle("Установка возможности предзаказа");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->IncludeComponent(
    "bitpro:import.no_reserve.by.article",
    "",
    array(
        "IBLOCK_TYPE" => "test",
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "PROPERTY_NAME" => "PREORDER",
        "IMPORT" => [
            "FILE_NAME" => 'import_file',
            "TEMP_FILE_DIR" => '/upload/csvimport/',
            "DELIMITER" => ';',
        ]
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
