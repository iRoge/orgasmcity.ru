<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (\Bitrix\Main\Loader::includeModule('likee.site')) {
    $arResult = \Likee\Site\Helpers\Menu::menuTreeBuild($arResult);
}

foreach ($arResult as &$arItem) {
    $arItem["MAX"] = 0;
    foreach ($arItem['ITEMS'] as &$arItem2Level) {
        $arItem2Level["MAX_L"] = 0;
        foreach ($arItem2Level['ITEMS'] as &$arItem3Level) {
            preg_match("/\/([a-zA-Z_\-]+)\/?$/", $arItem3Level["LINK"], $matches);
            if ($matches[1]) {
                $arItem3Level["CODE"] = $matches[1];
            }
            $length = mb_strlen($arItem3Level["TEXT"]);
            if ($arItem2Level["MAX_L"] < $length) {
                $arItem2Level["MAX_L"] = $length;
            }
        }
        $count = count($arItem2Level['ITEMS']);
        if ($arItem["MAX"] < $count) {
            $arItem["MAX"] = $count;
        }
    }
    if ($arItem["MAX"] > 7) {
        $arItem["MAX"] = ceil($arItem["MAX"]/2);
    }
}

try {
    $colorsIblockId = \Likee\Site\Helpers\IBlock::getIBlockId('COLORS');
    $arSelect = array("ID", "NAME", 'IBLOCK_ID','PROPERTY_COLOR');
    $arFilter = array("IBLOCK_ID"=>$colorsIblockId, "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize"=>5000), $arSelect);
    while ($ob = $res->Fetch()) {
        $arColors[$ob['ID']] = $ob;
    }
    foreach ($arResult as $key => $item) {
        if ($item['PARAMS']['PROPS']['UF_TEXT_C']) {
            $arResult[$key]['PARAMS']['PROPS']['UF_TEXT_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_TEXT_C']];
        }
        if ($item['PARAMS']['PROPS']['UF_BG_C']) {
            $arResult[$key]['PARAMS']['PROPS']['UF_BG_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_BG_C']];
        }
    }
} catch (\Exception $e) {
}
