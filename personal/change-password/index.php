<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Изменить пароль");
?>
<? $APPLICATION->IncludeComponent(
        "bitrix:main.profile",
        "change_password",
        array(
            "COMPONENT_TEMPLATE" => "change_password",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => "undefined",
            "SET_TITLE" => "N",
            "USER_PROPERTY" => array(),
            "SEND_INFO" => "N",
            "CHECK_RIGHTS" => "N",
            "USER_PROPERTY_NAME" => ""
        ),
        false
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>