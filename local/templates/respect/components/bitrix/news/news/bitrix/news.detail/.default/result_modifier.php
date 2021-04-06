<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

$arResult['PHOTOS'] = array();
//$arResult['DETAIL_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arResult['DETAIL_PICTURE'], 1300, 500, false);
if (!empty($arResult['DETAIL_PICTURE'])){
    $arResult['PHOTOS'][] = $arResult['DETAIL_PICTURE'];
}

if (! empty($arResult['DISPLAY_PROPERTIES']['GALLERY']['VALUE'])) {
    if (1 < count($arResult['DISPLAY_PROPERTIES']['GALLERY']['VALUE'])) {
        $arResult['PHOTOS'] = array_merge($arResult['PHOTOS'] , $arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']);
    } else {
        $arResult['PHOTOS'][] = $arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'];
    }
}

foreach($arResult['PHOTOS'] as &$photo){
    if(isset($photo['DESCRIPTION']) && $photo['DESCRIPTION'] != ''){
        $photo['LINK'] = $photo['DESCRIPTION'];

        if(substr($photo['LINK'], 0, 4) == 'http'){
            $photo['TARGET'] = "_blank";
        }
    }
}

$arOtherNews = [];

$rsNews = CIBlockElement::GetList(
    [
        $arParams['SORT_BY1'] => $arParams['SORT_ORDER1'],
        $arParams['SORT_BY2'] => $arParams['SORT_ORDER2']
    ],
    [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arResult['IBLOCK_ID']
    ],
    false,
    [
        'nElementID' => $arResult['ID'],
        'nPageSize' => 1
    ],
    [
        'ID',
        'DETAIL_PAGE_URL'
    ]
);

while ($arNews = $rsNews->GetNext()) {
    $arOtherNews[] = $arNews;
}

if (count($arOtherNews) == 3) {
    $arResult['NEXT'] = $arOtherNews[0]['DETAIL_PAGE_URL'];
} elseif (count($arOtherNews) == 2 && $arResult['ID'] == $arOtherNews[1]['ID']) {
    $arResult['NEXT'] = $arOtherNews[0]['DETAIL_PAGE_URL'];
}
