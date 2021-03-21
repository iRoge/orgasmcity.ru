<?
define('NOT_NEED_H1', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Каталог");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	".default", 
	array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/include_areas/catalog_main.php",
	),
	false,
	array("HIDE_ICONS"=>"Y")
);?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>