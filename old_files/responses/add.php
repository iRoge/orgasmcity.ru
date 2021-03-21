<?
define('NOT_NEED_H1', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оставьте свой отзыв");
?><?$APPLICATION->IncludeComponent(
	"fire:responses.add", 
	"opt", 
	array(
		"DISPLAY_PANEL" => "Y",
		"IBLOCK_ID" => "10",
		"IBLOCK_TYPE" => "responses",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "N",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>