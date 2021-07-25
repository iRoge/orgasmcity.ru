<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Управление рассылкой");
$APPLICATION->SetPageProperty("description", "Управление рассылкой");
$APPLICATION->SetPageProperty("keywords", "");
$APPLICATION->SetTitle("Управление рассылкой");
?>
<?$APPLICATION->IncludeComponent('qsoft:subscribe.manager', '');?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
