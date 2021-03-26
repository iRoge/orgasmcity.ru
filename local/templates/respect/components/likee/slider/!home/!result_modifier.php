<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$cp = $this->__component;
if (is_object($cp)) {
    $css = '';

    foreach ($arResult['ITEMS'] as &$arItem) {
        $arItem['VIDEO'] = [];
        if (! empty($arItem['PROPS']['VIDEO']['VALUE'])) {
            foreach ($arItem['PROPS']['VIDEO']['VALUE'] as $iFile) {
                $sFileSrc = \CFile::GetPath($iFile);
                $sFileExt = pathinfo($sFileSrc, PATHINFO_EXTENSION);

                $arItem['VIDEO'][$sFileExt] = $sFileSrc;
            }
            $arItem['VIDEO_GAG_SRC'] = '';
            if (! empty($arItem['PROPS']['VIDEO_GAG']['VALUE'])) {
                $arItem['VIDEO_GAG_SRC'] = CFile::GetPath($arItem['PROPS']['VIDEO_GAG']['VALUE']);
            }
            else {
                $arItem['VIDEO_GAG_SRC'] = $arItem['MOBILE_SRC'];
            }

        } else {
            $arItem['MOBILE_SRC'] = '';


            if (! empty($arItem['PROPS']['MOBILE_IMAGE']['VALUE'])) {
                $arItem['MOBILE_SRC'] = CFile::GetPath($arItem['PROPS']['MOBILE_IMAGE']['VALUE']);
            }

            /*$css .= "#msi-{$arItem['ID']}{background-image: url('{$arItem['PREVIEW_PICTURE']['SRC']}');}\n";
            if (! empty($arItem['MOBILE_SRC'])) {
                $css .= "@media (max-width: 599px) { #msi-{$arItem['ID']}{background-image: url('{$arItem['MOBILE_SRC']}');} }\n";
            }*/
        }
    }

    $cp->arResult['CSS'] = $css;
}

$arResult['SLICK'] = [];
$autoplaySpeedInSeconds = COption::GetOptionInt("likee", "main_slider_autoplay", 0);

if (1 < count($arResult['ITEMS']) && 0 < $autoplaySpeedInSeconds) {
    $arResult['SLICK']['autoplay'] = true;
    $arResult['SLICK']['autoplaySpeed'] = $autoplaySpeedInSeconds*1000;
}


$arResult['HOME_BG_SRC'] = false;
$homeBgFileId = COption::GetOptionInt("likee", "main_slider_bg", 0);

if ($homeBgFileId) {
    $arHomeBg = CFile::GetFileArray($homeBgFileId);

    if ($arHomeBg) {
        $arResult['HOME_BG_SRC'] = $arHomeBg['SRC'];
    }
} else if (1 == count($arResult['ITEMS']) && is_array($arResult['ITEMS'][0]['DETAIL_PICTURE'])) {
    $arResult['HOME_BG_SRC'] = $arResult['ITEMS'][0]['DETAIL_PICTURE']['SRC'];
}