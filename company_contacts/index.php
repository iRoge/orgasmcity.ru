<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent(
    "qsoft:infopage",
    "",
    array(
        "IBLOCK_CODE" => 'contacts',
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400"
    ),
    false
);
$APPLICATION->SetPageProperty("title", 'Бонусная программа. Город Оргазма');
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о контактах в Городе Оргазма");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
