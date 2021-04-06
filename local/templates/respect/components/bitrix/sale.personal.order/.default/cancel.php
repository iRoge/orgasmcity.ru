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

$APPLICATION->IncludeComponent(
    "bitrix:sale.personal.order.cancel",
    "",
    array(
        "PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
        "PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "ID" => $arResult["VARIABLES"]["ID"],
    ),
    $component
);