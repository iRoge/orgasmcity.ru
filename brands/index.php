<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Список брендов в Городе Оргазма. В Городе Оргазма более 700 производителей и брендов товаров для взрослых. Lelo, Calexotics, Baile, Doc Johnson, Dream Toys, Erolanta, Le Frivole, Leg Avenue, Lola toys, Lovetoys, Obsessive, Orion, Pipedream, Passion, NS Novelties, Livia Corsetti, Leg Avenue");
$APPLICATION->SetPageProperty("title", 'Список брендов в Городе Оргазма');

$APPLICATION->IncludeComponent(
    "rdevs:brands",
    ".default",
    array(
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
    ),
    false
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>