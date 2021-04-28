<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Roistat импорт');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->IncludeComponent(
    "qsoft:csv.import",
    "",
    array(
    "IBLOCK_TYPE" => "test",
    "IBLOCK_ID" => 16,
    "IMPORT" => [
        "FILE_NAME" => 'roistat_import',
        "TEMP_FILE_DIR" => '/upload/roistat/import/',
        "DELIMITER" => ';',
    ]
    ),
    false
);
?>
<?php
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
