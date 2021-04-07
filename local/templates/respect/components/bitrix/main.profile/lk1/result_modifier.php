<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (substr($arResult["arUser"]["EMAIL"], -10) == '@rshoes.ru') {
    unset($arResult["arUser"]["EMAIL"], $arResult["arUser"]["~EMAIL"]);
}

$birthday = explode(".", $arResult["arUser"]["PERSONAL_BIRTHDAY"]);
if (!checkdate($birthday[1], $birthday[0], $birthday[2]) || $birthday[2] < 1900 || $birthday[2] > 2100) {
    $arResult["arUser"]["PERSONAL_BIRTHDAY"] = "";
}
global $LOCATION;
// устанавливаем стандартизированное название региона для dadata
$arResult['DADATA_REGION_NAME'] = $LOCATION->getDadataStandartRegionNameFromLocation();
$arResult['DADATA_CITY_NAME'] = $LOCATION->getDadataStandartCityNameFromLocation();
// проверяем работоспособность дадаты и остаток запросов на день
$arResult['DADATA_STATUS'] = $LOCATION->getDadataStatus();
