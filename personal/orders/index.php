<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle('Мои заказы');
global $USER;
if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?back_url=/personal/orders/');
}
?>

<? if ($USER->IsAuthorized()) : ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:sale.personal.order",
        ".default",
        array(
            "COMPONENT_TEMPLATE" => ".default",
            "PROP_1" => array(),
            "PROP_2" => array(),
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SEF_MODE" => "Y",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_GROUPS" => "Y",
            "ORDERS_PER_PAGE" => "10",
            "PATH_TO_PAYMENT" => "/order/payment/",
            "PATH_TO_BASKET" => "/cart/",
            "SET_TITLE" => "Y",
            "SAVE_IN_SESSION" => "Y",
            "NAV_TEMPLATE" => "modern",
            "CUSTOM_SELECT_PROPS" => array(),
            "HISTORIC_STATUSES" => array(),
            "SEF_FOLDER" => "/personal/orders/",
            "SEF_URL_TEMPLATES" => array(
                "list" => "",
                "detail" => "#ID#/",
                "cancel" => "cancel/#ID#/",
            )
        ),
        false
    ); ?>
<? endif; ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");