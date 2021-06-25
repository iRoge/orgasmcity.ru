<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");
?>

<?
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