<? use Likee\Site\Helper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */


\Bitrix\Main\Loader::includeModule('likee.site');

foreach ($arResult['ITEMS'] as $iKey => &$arItem) {
    if (!empty($arItem['PROPERTIES']['YOU_TUBE_LINK']['VALUE'])) {
        $arVideo = Helper::getYouTubeOEmbed($arItem['PROPERTIES']['YOU_TUBE_LINK']['VALUE']);
        if ($arVideo !== false) {
            $arItem['VIDEO'] = $arVideo;
            $arItem['PROPERTIES']['SIZE']['VALUE_XML_ID'] = 'BIG';
        }
    }

    $arItem['SIZE'] = $arItem['PROPERTIES']['SIZE']['VALUE_XML_ID'];

    if (!empty($arItem['PREVIEW_PICTURE']) && is_array($arItem['PREVIEW_PICTURE'])) {
        if ($arItem['SIZE'] == 'BIG') {
            $arItem['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arItem['PREVIEW_PICTURE'], 975, 650, true);
        } elseif ($arItem['SIZE'] == 'MIDDLE') {
            $arItem['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arItem['PREVIEW_PICTURE'], 650, 650, true);
        } else {
            $arItem['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arItem['PREVIEW_PICTURE'], 325, 325, true);
        }
    }

    $arItem['MOBILE_IMAGE']['SRC'] = CFile::GetPath($arItem['PROPERTIES']['MOBILE_IMAGE']['VALUE']);
    if (!$arItem['MOBILE_IMAGE']['SRC']) {
        $arItem['MOBILE_IMAGE']['SRC'] = $arItem['PREVIEW_PICTURE']['SRC'];
    }


    $arItem['MOBILE_LINK'] = $arItem['PROPERTIES']['MOBILE_LINK']['VALUE'];
    if (!$arItem['MOBILE_LINK']) {
        $arItem['MOBILE_LINK'] = $arItem['PROPERTIES']['LINK']['VALUE'];
    }

    
    if (isset($arItem['DISPLAY_PROPERTIES']['VIDEO_FILE_MP4']['VALUE']) || isset($arItem['DISPLAY_PROPERTIES']['VIDEO_FILE_WEBM']['VALUE']) || isset($arItem['DISPLAY_PROPERTIES']['VIDEO_FILE_OGV']['VALUE']) || isset($arItem['DISPLAY_PROPERTIES']['VIDEO_FILE']['VALUE'])) {
        $arItem['VIDEO_FILE'] = true;
    }

    if (!$arItem['DISPLAY_PROPERTIES']['VIDEO_GAG']['FILE_VALUE']['SRC']) {
        $arItem['DISPLAY_PROPERTIES']['VIDEO_GAG']['FILE_VALUE']['SRC'] = $arItem['DISPLAY_PROPERTIES']['VIDEO_PREVIEW']['FILE_VALUE']['SRC'];
    }

    $arItem['USEMAP'] = false;
    if (preg_match('/map name=["\']([^"\']+)["\']/i', $arItem['PREVIEW_TEXT'], $m)) {
        $arItem['USEMAP'] = $m[1];

        $arItem['PROPERTIES']['LINK']['VALUE'] = false;
    }
}
unset($arItem);

$arResult['SLIDES'] = [];
$arItemsInSlides = [];

while ($arItem = array_shift($arResult['ITEMS'])) {
    //в одном слайдере может быть только один банер большого или среднего размера
    if ($arItem['SIZE'] == 'BIG' || $arItem['SIZE'] == 'MIDDLE') {
        $iLastIndex = 0;
        if (!empty($arResult['SLIDES'])) {
            end($arResult['SLIDES']);
            $iLastIndex = key($arResult['SLIDES']);
            $iLastIndex++;
        }


        $arResult['SLIDES'][$iLastIndex][] = $arItem;
        $arItemsInSlides[$iLastIndex] += $arItem['SIZE'] == 'BIG' ? 6 : 4;
    } else {
        $iCurIndex = 0;

        foreach (array_keys($arResult['SLIDES']) as $iKey) {
            //проверяем, что в слайд влезет еще один банер
            if ($arItemsInSlides[$iKey] < 6) {
                $iCurIndex = $iKey;
                break;
            }
        }

        $arResult['SLIDES'][$iCurIndex][] = $arItem;
        $arItemsInSlides[$iCurIndex] += 1;
    }
}

function getBannerLinkStyle($val, $w, $h)
{
    $val = explode(",", trim($val));
    return "left:" . (intval(100 * $val[0] / $w)) . "%;" .
        "top:" . (intval(100 * $val[1] / $h)) . "%;" .
        "right:" . (100 - intval(100 * $val[2] / $w)) . "%;" .
        "bottom:" . (100 - intval(100 * $val[3] / $h)) . "%";
}

foreach ($arResult['SLIDES'] as $numer => $arItem) {
    foreach ($arItem as $nums => $arBanner) {
        if ($arBanner['PROPERTIES']['ACTIVE_MULTIPLY_LINKS']['VALUE']) {
            foreach ($arBanner['PROPERTIES']['LINKS']['VALUE'] as $num => $coordinates) {
                $arResult['SLIDES'][$numer][$nums]['BANNER']['MULTIPLY_LINKS'][] = [
                    'LINK' => $arBanner['PROPERTIES']['LINKS']['DESCRIPTION'][$num],
                    'STYLE' => getBannerLinkStyle($coordinates, $arBanner['PREVIEW_PICTURE']['WIDTH'], $arBanner['PREVIEW_PICTURE']['HEIGHT']),
                ];
            }
        }
    }
}
