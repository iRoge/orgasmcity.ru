<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->IncludeComponent(
    "qsoft:catalog.section",
    "",
    array(
        "STORE_ID" => $arParams['STORE_ID'],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"]
    )
);
