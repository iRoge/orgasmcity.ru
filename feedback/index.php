<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Форма отправки отзыва");
$APPLICATION->SetPageProperty("keywords", 'город, оргазм, отзывы, секс, шоп');
$APPLICATION->SetPageProperty("description", "Вы можете посмотреть все отзывы о секс шопе Город Оргазма");
$APPLICATION->SetPageProperty("title", 'Отзывы. Город Оргазма');
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
