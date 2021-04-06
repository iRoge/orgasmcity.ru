<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$arDetParams = array(
    "PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
    "PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
    "PATH_TO_COPY" => $arResult["PATH_TO_LIST"] . '?ID=#ID#',
    "PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
    "SET_TITLE" => $arParams["SET_TITLE"],
    "ID" => $arResult["VARIABLES"]["ID"],
    "ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],

    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

    "CUSTOM_SELECT_PROPS" => $arParams["CUSTOM_SELECT_PROPS"]
);

foreach ($arParams as $key => $val) {
    if (strpos($key, "PROP_") !== false)
        $arDetParams[$key] = $val;
}

$sTemplate = '';
if (isset($_REQUEST['from_order']))
    $sTemplate = 'success';

$APPLICATION->IncludeComponent(
    "bitrix:sale.personal.order.detail",
    $sTemplate,
    $arDetParams,
    $component
); ?>

<div class="spacer--3"></div>

<style>
    .sale-paysystem-wrapper,
    .sale-paysystem-yandex-button-descrition,
    .sale-paysystem-description {
        border: none;
        color: #fff;
    }
</style>