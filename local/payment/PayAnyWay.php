<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
?>


<? $APPLICATION->IncludeComponent(
    "bitrix:sale.order.payment.receive",
    "",
    array(
        "PAY_SYSTEM_ID" => "14",
        "PAY_SYSTEM_ID_NEW​" => "14",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO"
    ),
    false
); ?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php"); ?>