<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><?$APPLICATION->IncludeComponent(
	"fire:sale.basket.basket", 
	"opt", 
	array(
		"GIFTS_IMAGE_WIDTH" => "480",
		"GIFTS_IMAGE_HEIGHT" => "600",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"PATH_TO_ORDER" => "/personal/order/make/",
		"QUANTITY_FLOAT" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"TEMPLATE_THEME" => "site",
		"SET_TITLE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"USE_PREPAYMENT" => "N",
		"ACTION_VARIABLE" => "action",
		"IMAGE_WIDTH" => "155",
		"IMAGE_HEIGHT" => "194",
		"CATALOG_LINK" => "/catalog/",
		"MAIN_LINK" => "/",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>