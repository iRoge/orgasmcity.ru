<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arItem['PREVIEW_PICTURE'], 1650, 900);

    if(isset($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) && $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] != ''){
        $arItem['DETAIL_PAGE_URL'] = $arItem["PREVIEW_PICTURE"]['DESCRIPTION'];

        if(substr($arItem["PREVIEW_PICTURE"]['DESCRIPTION'], 0, 4) == 'http'){
            $arItem['TARGET'] = "_blank";
        }
    }
}
unset($arItem);



foreach ($arResult['ITEMS'] as &$arItem) {
	if (empty ($arItem['DISPLAY_PROPERTIES']['SLIDER_PICTURES']['VALUE']))
		continue;
	
	$arItem['SLIDER'] = [];	
	foreach ($arItem['DISPLAY_PROPERTIES']['SLIDER_PICTURES']['FILE_VALUE'] as $arFile) {
		$arFile['SRC'] = \Likee\Site\Helper::getResizePath($arFile, 1650, 900);
		$arItem['SLIDER'][] = $arFile;
	}		
}
unset($arItem);

$arResult['FIRST_ITEM'] = array_shift($arResult['ITEMS']);