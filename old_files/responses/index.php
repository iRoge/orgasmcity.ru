<?
define('NOT_NEED_H1', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Отзывы");
?><?$APPLICATION->IncludeComponent(
	"fire:responses.list", 
	"opt", 
	array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DATE_FORMAT" => "j M Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_PANEL" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"IBLOCK_ID" => "10",
		"IBLOCK_TYPE" => "responses",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "",
		"PAGE_ELEMENT_COUNT" => "5",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITH_STUB"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>