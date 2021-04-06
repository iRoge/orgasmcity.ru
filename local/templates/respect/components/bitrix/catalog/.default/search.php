<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$sectionUrl = $APPLICATION->GetCurPage(false);
$APPLICATION->SetTitle('Результат поиска');
$sSearch = htmlentities(trim($_REQUEST['q']));
if (strlen($sSearch) > 0) {
    echo "<script>fbq('track', 'Search');</script>";
    $APPLICATION->SetTitle('Товары по запросу &laquo;' . $sSearch . '&raquo;');
}

$APPLICATION->IncludeComponent(
    "qsoft:catalog.section",
    "",
    array(
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SECTION_TYPE" => 'search',
        "SEARCH" => $sSearch
    )
);
