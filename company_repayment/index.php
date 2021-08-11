<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о совершении возврата в городе оргазма. Если у Вас имеются претензии к качеству товара, купленного в нашем интернет-магазине, или возникла необходимость его возврата/обмена по каким-либо причинам, вы можете написать нам на почту return@orgasmcity.ru");

$APPLICATION->IncludeComponent(
    "qsoft:infopage",
    "",
    array(
        "IBLOCK_CODE" => 'refundNew',
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400"
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
