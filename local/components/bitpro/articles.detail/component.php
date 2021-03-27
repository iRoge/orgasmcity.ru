<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);


if(CModule::IncludeModule("iblock")) {

    /*$obCFile = new CFile();
    $obCIBlockElement = new CIBlockElement();
    $obCIBlockSection = new CIBlockSection();
    $obCUser = new CUser();*/

    $obCIBlockElement = new CIBlockElement();

    $arResult['ITEM'] = [];


    // Выбираем тендер по коду
    $arSortArticle = [
        "SORT" => "ASC"
    ];
    $arFilterArticle = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "CODE" => $arParams["ITEM_CODE"],
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    ];
    $arSelectArticle = [
        "ID",
        "NAME",
        "DETAIL_TEXT",
        "CODE",
    ];


    $resArticle = $obCIBlockElement->GetList(
        $arSortArticle,
        $arFilterArticle,
        false,
        false,
        $arSelectArticle
    );
    if ($Article = $resArticle->GetNext()){
        $arResult['ITEM'] = $Article;

        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arParams["IBLOCK_ID"], $Article['ID']);
        $arResult['ITEM']['SEO'] = $ipropValues->getValues();

    }


    /*echo'<pre>';
    print_r($arResult['ITEM']);
    echo'</pre>';*/

}

$this->IncludeComponentTemplate();
?>