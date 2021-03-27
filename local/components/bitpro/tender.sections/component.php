<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);


if(CModule::IncludeModule("iblock")) {

    $obCFile = new CFile();
    $obCIBlockElement = new CIBlockElement();
    $obCIBlockSection = new CIBlockSection();
    $obCUser = new CUser();

    $arResult['ITEMS'] = [];
    $arrSections = [];
    $arrTenders = [];

    // Выбираем все категории
    $arSortSection = [
        "SORT" => "ASC"
    ];
    $arFilterSection = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y"
    ];
    $arSelectSection = [
        "ID",
        "NAME",
        'DESCRIPTION',
        "UF_*"
    ];

    $resSection = $obCIBlockSection->GetList(
        $arSortSection,
        $arFilterSection,
        false,
        $arSelectSection
    );
    while ($ar_resSection = $resSection->GetNext()){
        $arrSections[$ar_resSection["ID"]] = $ar_resSection;
    }

    // Выбираем все тендеры
    $arSortTender = [
        "SORT" => "ASC"
    ];
    $arFilterTender = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    ];
    $arSelectTender = [
        "ID",
        "NAME",
        "CODE",
        "IBLOCK_SECTION_ID",
        "DETAIL_PAGE_URL"
    ];


    $resTender = $obCIBlockElement->GetList(
        $arSortTender,
        $arFilterTender,
        false,
        false,
        $arSelectTender
    );
    while ($ar_resTender = $resTender->GetNext()){
        $arrTenders[$ar_resTender["ID"]] = $ar_resTender;
    }

    foreach($arrSections as $section){
        $arResult['ITEMS'][$section['ID']] = $section;
        foreach($arrTenders as $tender){
            if($tender['IBLOCK_SECTION_ID'] == $section['ID']){
                $arResult['ITEMS'][$section['ID']]['LIST'][$tender['ID']] = $tender;
            }
        }
    }


}

$this->IncludeComponentTemplate();
?>