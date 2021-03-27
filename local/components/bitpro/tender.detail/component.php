<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);


if(CModule::IncludeModule("iblock")) {

    $obCFile = new CFile();
    $obCIBlockElement = new CIBlockElement();
    $obCIBlockSection = new CIBlockSection();
    $obCUser = new CUser();

    $arResult['ITEM'] = [];


    // Выбираем тендер по коду
    $arSortTender = [
        "SORT" => "ASC"
    ];
    $arFilterTender = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "CODE" => $arParams["ITEM_CODE"],
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    ];
    $arSelectTender = [
        "ID",
        "NAME",
        "DETAIL_TEXT",
        "CODE",
        "PROPERTY_ATTACH_FILES",
        "PROPERTY_EMAIL"
    ];


    $resTender = $obCIBlockElement->GetList(
        $arSortTender,
        $arFilterTender,
        false,
        false,
        $arSelectTender
    );
    if ($ar_resTender = $resTender->GetNext()){
        $arResult['ITEM'] = $ar_resTender;

        $props = $obCIBlockElement->GetProperty($arParams["IBLOCK_ID"], $arResult['ITEM']['ID'], "sort", "asc", array("CODE" => "ATTACH_FILES"));
        while ($obProps = $props->GetNext())
        {
            $arResult['ITEM']['FILES'][$obProps['VALUE']] = $obCFile->GetFileArray($obProps['VALUE']);
            $arResult['ITEM']['FILES'][$obProps['VALUE']]['FORMAT'] = array_pop(explode('.', $arResult['ITEM']['FILES'][$obProps['VALUE']]['FILE_NAME']));
        }
    }

    $APPLICATION->AddChainItem($arResult['ITEM']["NAME"], "");


    /*echo'<pre>';
    print_r($arResult['ITEM']);
    echo'</pre>';
*/
}

$this->IncludeComponentTemplate();
?>