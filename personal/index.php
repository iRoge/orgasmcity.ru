<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Личный кабинет");
$APPLICATION->SetPageProperty("description", "Личный кабинет");
$APPLICATION->SetPageProperty("keywords", "");
$APPLICATION->SetTitle("Личный кабинет");
?>

<? if (\Likee\Site\User::isPartner()): ?>
    <? $APPLICATION->IncludeComponent(
        "likee:profile.edit",
        "partner",
        array(
            'GROUP_ID' => [3, 4]
        ),
        false
    ); ?>
<? else: ?>
    <? $APPLICATION->IncludeComponent(
        "likee:profile.edit",
        "",
        array(
            'GROUP_ID' => 2
        ),
        false
    ); ?>

    <? $APPLICATION->IncludeComponent(
        "bitrix:main.profile",
        "lk1",
        array(
            "COMPONENT_TEMPLATE" => ".default",
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
<? endif; ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>