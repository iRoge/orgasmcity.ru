<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("");?><?$APPLICATION->IncludeComponent(
	"fire:sale.basket.basket.small",
	"opt",
	Array(
		"IMAGE_HEIGHT" => "194",
		"IMAGE_WIDTH" => "155",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_ORDER" => "/personal/order/make/",
		"PATH_TO_WISHLIST" => "/personal/wishlist/"
	)
);?>