<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();



foreach ($arResult['ITEMS'] as $key=>$arItem) {
    if ($arItem['PROPS']['PRODUCT_IMG']['VALUE']) {
        $arResult['ITEMS'][$key]['PRODUCT_IMG'] = CFile::GetFileArray($arItem['PROPS']['PRODUCT_IMG']['VALUE']);

        if ($arHomeBg) {
            $arResult['HOME_BG_SRC'] = $arHomeBg['SRC'];
        }

    }
}

$arResult['SLICK'] = [];
$autoplaySpeedInSeconds = COption::GetOptionInt("likee", "main_slider_autoplay", 0);

if (1 < count($arResult['ITEMS']) && 0 < $autoplaySpeedInSeconds) {
    $arResult['SLICK']['autoplay'] = true;
    $arResult['SLICK']['autoplaySpeed'] = $autoplaySpeedInSeconds*1000;
}


$arResult['HOME_BG_SRC'] = false;
$homeBgFileId = COption::GetOptionInt("likee", "main_slider_bg", 0);