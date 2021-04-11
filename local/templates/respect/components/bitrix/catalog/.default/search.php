<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$sectionUrl = $APPLICATION->GetCurPage(false);
$APPLICATION->SetTitle('Результат поиска');

$APPLICATION->IncludeComponent(
    "qsoft:catalog.section",
    "",
    array(
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
    )
);
