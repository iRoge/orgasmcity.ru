<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>

<? $APPLICATION->IncludeComponent(
    "likee:store.list",
    "",
    array(
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "PHONE" => $arParams["PHONE"],
        "SCHEDULE" => $arParams["SCHEDULE"],
        "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
        "TITLE" => $arParams["TITLE"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
        "PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
        "MAP_TYPE" => $arParams["MAP_TYPE"],
        'METRO_ID' => intval($_REQUEST['metro_id']),
        'STORE_NAME' => $_REQUEST['store_name'],
        'LOCATION_CITY' => $_REQUEST['location'],
    ),
    $component
);
