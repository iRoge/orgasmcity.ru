<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Форма отправки отзыва");

$APPLICATION->IncludeComponent(
    "orgasmcity:feedback.form",
    "default",
    array(
        "IBLOCK_CODE" => 'feedback',
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400"
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
