<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);


if(CModule::IncludeModule("iblock")) {

    /*$obCFile = new CFile();
    $obCIBlockSection = new CIBlockSection();
    $obCUser = new CUser();*/

    $obCIBlockElement = new CIBlockElement();

    $arResult['ITEMS'] = [];

    // Выбираем все статьи
    $arSortArticles = [
        "SORT" => "ASC"
    ];
    $arFilterArticles = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    ];
    $arSelectArticles = [
        "ID",
        "NAME",
        "PREVIEW_TEXT",
        "CODE",
        "IBLOCK_SECTION_ID",
        "DETAIL_PAGE_URL"
    ];


    $resArticles = $obCIBlockElement->GetList(
        $arSortArticles,
        $arFilterArticles,
        false,
        false,
        $arSelectArticles
    );
    while ($article = $resArticles->GetNext()){
        $arResult['ITEMS'][$article["ID"]] = $article;
    }


}

$this->IncludeComponentTemplate();
?>