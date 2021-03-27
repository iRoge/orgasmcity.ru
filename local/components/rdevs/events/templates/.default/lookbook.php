<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "",
    array(
        "SITE_ID" => "s1",
        "START_FROM" => "0"
    )
);

$APPLICATION->IncludeComponent(
    "rdevs:events.lookbook",
    "",
    [
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_DIR' => $arParams['CACHE_DIR'],
        'IBLOCK_CODE' => $arParams['IBLOCK_CODE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CURRENT_SECTION' => $arParams['CURRENT_SECTION'],
        'CURRENT_ELEMENT' => $arParams['CURRENT_ELEMENT'],
        'CURRENT_PAGE_NUM' => $arParams['CURRENT_PAGE_NUM'],
        'DEFAULT_SECTION' => $arParams['DEFAULT_SECTION'],
    ],
    $component
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
