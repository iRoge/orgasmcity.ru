<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->IncludeComponent(
    "qsoft:infopage",
    "",
    array(
        "IBLOCK_CODE" => 'payment',
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400"
    ),
    false
);
$APPLICATION->SetPageProperty("title", 'Оплата. Город Оргазма');
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о способах оплаты в городе оргазма . В городе оргазма вы можете заказать и оплатить товары для взрослых одним из следующих способов: при получении товара или онлайн банковской картой на сайте");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
