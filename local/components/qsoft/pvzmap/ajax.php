<?require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->RestartBuffer();
$GLOBALS['PVZ_IDS'] = json_decode($_REQUEST['pvz_ids'], JSON_OBJECT_AS_ARRAY);
$GLOBALS['PVZ_PRICES'] = json_decode($_REQUEST['pvz_prices'], JSON_OBJECT_AS_ARRAY);
$APPLICATION->IncludeComponent('qsoft:pvzmap', '', ['IS_AJAX' => true]);
