<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о доставке в городе оргазма. В городе оргазма широкий выбор доставки. Здесь вы можете заказать товары для взрослых с доставкой на дом курьером, забрать в пунктах самовывоза PickPoint или СДЭК, или выбрать доставку в отделение почты России");

$APPLICATION->IncludeComponent(
    "qsoft:infopage",
    "",
    array(
        "IBLOCK_CODE" => 'delivery',
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400"
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
