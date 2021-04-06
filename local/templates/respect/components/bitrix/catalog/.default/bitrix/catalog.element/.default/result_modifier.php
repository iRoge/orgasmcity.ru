<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $LOCATION;

$cp =& $this->__component;
if (strlen($cp->getCachePath())) {
    $GLOBALS['CACHE_MANAGER']->RegisterTag('catalogAll');
    if ($LOCATION->getName() == "Москва") {
        $GLOBALS['CACHE_MANAGER']->RegisterTag('elementMoscow');
    }
    $GLOBALS['CACHE_MANAGER']->RegisterTag('catalogElement');
}

\Likee\Site\Helpers\Catalog::checkElementResult($arResult);

// Кол-во товара на складе
$arSizes = array();
if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $value) {
        $arSizes[$value['ID']] = true;
        if ($value["PROPERTIES"]["SIZE"]["VALUE"]) {
            $arSizes[$value['ID']] = $value["PROPERTIES"]["SIZE"]["VALUE"];
        }
    }
}
//остатки по складам
$arRests = $LOCATION->getRests(array_keys($arSizes));
//остатки по типам
$arResult['RESTS'] = $LOCATION->getTypeSizes($arRests, $arSizes);
//обрабатываем свойство "запрет резервирования"
if (!empty($arResult['PROPERTIES']['NO_RESERVE']['VALUE_ENUM_ID'])) {
    $arResult['RESTS']['ALL'] = $arResult['RESTS']['DELIVERY'];
    $arResult['RESTS']['RESERVATION'] = array();
}
//флаг безразмерной номеннклатуры
$key = key($arResult['RESTS']['ALL']);
$arResult['SINGLE_SIZE'] = (count($arResult['RESTS']['ALL']) == 1 && $arResult['RESTS']['ALL'][$key] === true) ? $key : false;
//цена товара
$arResult['PRICE_PRODUCT'] = $LOCATION->getProductsPrices($arResult['ID']);

$arCatalog = CCatalogSKU::GetInfoByIBlock($arParams['IBLOCK_ID']);

$arParams['OFFER_IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];
$arResult['ARTICLE'] = trim($arResult['PROPERTIES']['ARTICLE']['VALUE']);

$arResult['ELEMENT_BREADCRUMB'] = $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
if (strlen($arResult['ARTICLE']) > 0) {
    $arResult['ELEMENT_BREADCRUMB'] = 'Артикул ' . $arResult['ARTICLE'];
}
$arResult["ONLINE_TRY_ON"] = $arResult['PROPERTIES']['ONLINE_TRY_ON']['VALUE'] === 'Y';
unset($arResult['PROPERTIES']['ONLINE_TRY_ON']);
unset($arResult['DISPLAY_PROPERTIES']['ONLINE_TRY_ON']);
$this->__component->setResultCacheKeys(['ELEMENT_BREADCRUMB', 'SINGLE_SIZE', 'ONLINE_TRY_ON', 'ARTICLE']);

/** DETAIL PICTURE */

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arImage = $arResult['DETAIL_PICTURE'];

    $arResult['PICTURE'] = [
        'ALT' => $arImage['ALT'] ?: $arResult['NAME'],
        'SRC' => \Likee\Site\Helper::getResizePath($arImage, 1280, 1024, true),
        'THUMB' => \Likee\Site\Helper::getResizePath($arImage, 65, 65, true)
    ];
} else {
    $arResult['PICTURE'] = [
        'ALT' => $arResult['NAME'],
        'SRC' => \Likee\Site\Helper::getEmptyImg(650, 650),
        'THUMB' => \Likee\Site\Helper::getEmptyImg(65, 65)
    ];
}
$arResult['PHOTOS'] = [];
/** PREVIEW_PICTURE */
if (!empty($arResult['PREVIEW_PICTURE'])) {
    $arImage = [
        'ALT' => $arResult['NAME'],
        'SRC' => \Likee\Site\Helper::getResizePath($arResult['PREVIEW_PICTURE'], 1280, 1024, true),
        'THUMB' => \Likee\Site\Helper::getResizePath($arResult['PREVIEW_PICTURE'], 65, 65, true)
    ];

    array_unshift($arResult['PHOTOS'], $arImage);
}

/** MORE PHOTOS */
array_unshift($arResult['PHOTOS'], $arResult['PICTURE']);
if (!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $iImage) {
        $arResult['PHOTOS'][] = [
            'ALT' => !empty($arResult['PICTURE']['ALT']) ? $arResult['PICTURE']['ALT'] : $arResult['NAME'],
            'SRC' => \Likee\Site\Helper::getResizePath($iImage, 1280, 1024),
            'THUMB' => \Likee\Site\Helper::getResizePath($iImage, 65, 65)
        ];
    }
}

$arResult['PHOTOS_COUNT'] = count($arResult['PHOTOS']);

/** COLORS */
$arResult['COLORS'] = [];

// это свойства, которые одинаковые у одной модели
$arColorsProps = ['LINE', 'SHOE', 'MODEL', 'MANUFACTURER'];
$bHasAllColorProps = true;

foreach ($arColorsProps as $sProp) {
    $bHasAllColorProps &= !empty($arResult['PROPERTIES'][$sProp]['VALUE']);
}

if ($bHasAllColorProps) {
    $arColorsFilter = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
        '!DETAIL_PICTURE' => false,
        '!ID' => $arResult['ID'],
    ];
    foreach ($arColorsProps as $sProp) {
        $arColorsFilter['PROPERTY_' . $sProp] = $arResult['PROPERTIES'][$sProp]['VALUE'];
    }
} else {
    $arColorsFilter['=ID'] = -1;
}

$rsColors = \CIBlockElement::GetList(
    [
        'SORT' => 'ASC',
        'ID' => 'ASC'
    ],
    $arColorsFilter,
    false,
    false,
    ['ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'NAME']
);

while ($obColor = $rsColors->GetNextElement(true, false)) {
    $arColor = $obColor->GetFields();
    $arColor['PROPERTIES'] = $obColor->GetProperties([], ['CODE' => 'COLORSFILTER']);
    $arColor['CURRENT'] = $arColor['ID'] == $arResult['ID'] ? 'Y' : 'N';
    $arColor['COLOR'] = \Likee\Site\Helpers\HL::getFieldValueByProp($arColor['PROPERTIES']['COLORSFILTER'], 'UF_GRBCODE');
    $arColor['COLOR'] = \Likee\Site\Helper::rgb2hex($arColor['COLOR']);
    $arColor['FILE'] = \Likee\Site\Helper::getResizePath($arColor['DETAIL_PICTURE'], 62, 62, true);
    $arResult['COLORS'][] = $arColor;
}

/** DISPLAY PROPS */
foreach ($arResult['DISPLAY_PROPERTIES'] as $iKey => &$arProp) {
    if (empty($arProp['VALUE']) || is_array($arProp['VALUE'])) {
        unset($arResult['DISPLAY_PROPERTIES'][$iKey]);
        continue;
    }
    if ($arProp['PROPERTY_TYPE'] == 'S') {
        $arProp['VALUE'] = \Likee\Site\Helpers\HL::getFieldValueByProp($arProp, 'UF_NAME');
    }
}
unset($arProp);

if (! empty($arResult['SECTION']['PATH'][0]['ID'])) {
    $parentSect = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $arResult['IBLOCK_ID'], "ID" => $arResult['SECTION']['PATH'][0]['ID']), false, array("UF_*"));
    if ($parentSectInfo = $parentSect->GetNext()) {
        if (strlen($parentSectInfo["UF_SIZES_TAB_HTML"]) > 0) { //проверяем что поле заполнено
            $tabInfo = CIBlockElement::GetByID($parentSectInfo["UF_SIZES_TAB_HTML"]);
            if ($ar_resInfo = $tabInfo->GetNext()) {
                $arResult['SECTION_SIZES_TAB'] = $ar_resInfo['PREVIEW_TEXT'];
            }
        }
    }
}
