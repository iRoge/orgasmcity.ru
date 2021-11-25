<?php
include("config.php");
die;
$start_time = time();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
set_time_limit(0);
ini_set('max_execution_time', '3600');
ini_set('memory_limit', '2048M');
$c_elem = 0;
$c_sect = 0;
CModule::IncludeModule('iblock');
CModule::IncludeModule('blog');
CModule::IncludeModule('user');
$arFilterSect = array(
    "IBLOCK_ID" => [IBLOCK_CATALOG, IBLOCK_VENDORS],
);
$res = CIBlockElement::GetList(array("ID" => "ASC"), $arFilterSect);
while ($ar_fields = $res->GetNext()) {
    CIBlockElement::Delete($ar_fields["ID"]);
    echo $ar_fields["ID"] . ' deleted ' . PHP_EOL;
    $c_elem++;
}
$arFilter = array('IBLOCK_ID' => [IBLOCK_CATALOG, IBLOCK_VENDORS]);
$db_list = CIBlockSection::GetList(array(), $arFilter, true);
while ($ar_result = $db_list->GetNext()) {
    CIBlockSection::Delete($ar_result["ID"]);
    echo $ar_result["ID"] . ' deleted ' . PHP_EOL;
    $c_sect++;
}
echo 'Удалено элементов: ' . $c_elem . ', секций: ' . $c_sect;