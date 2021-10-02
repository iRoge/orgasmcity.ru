<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Форма отправки отзыва");

$APPLICATION->IncludeComponent(
    "orgasmcity:feedback.form",
    "default",
    [
        "IBLOCK_CODE" => 'feedback',
        "FILTERS" => [
            "ACTIVE" => 'Y',
            'PROPERTY_PRODUCT_ID' => false,
        ]
    ],
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
