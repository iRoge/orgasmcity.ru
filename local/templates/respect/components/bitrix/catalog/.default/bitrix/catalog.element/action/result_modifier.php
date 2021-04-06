<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

$this->__component->setResultCacheKeys(['ELEMENT_BREADCRUMB', 'BODY_CLASS']);

$arCatalog = CCatalogSKU::GetInfoByIBlock($arParams['IBLOCK_ID']);

$arParams['OFFER_IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];
$arResult['ARTICLE'] = trim($arResult['PROPERTIES']['ARTICLE']['VALUE']);


$arResult['ELEMENT_BREADCRUMB'] = $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
if (strlen($arResult['ARTICLE']) > 0)
    $arResult['ELEMENT_BREADCRUMB'] = 'Артикул ' . $arResult['ARTICLE'];

/* класс для нескольких разделов */
$arSectionsCount = CIBlockElement::GetElementGroups($arResult['ID'], false, ['ID'])->SelectedRowsCount();
$arResult['BODY_CLASS'] = (1 < $arSectionsCount) ? 'bc-hide-second' : '';

/** DETAIL PICTURE */
if (!empty($arResult['PREVIEW_PICTURE']) || !empty($arResult['DETAIL_PICTURE'])) {
    $arImage = $arResult['PREVIEW_PICTURE'] ?: $arResult['DETAIL_PICTURE'];
    $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arResult['DETAIL_PICTURE'], 450, 400, false);

    $arResult['PICTURE'] = [
        'ALT' => $arImage['ALT'] ?: $arResult['NAME'],
        'SRC' => \Likee\Site\Helper::getResizePath($arImage, 1280, 1024, true),
        'THUMB' => \Likee\Site\Helper::getResizePath($arImage, 450, 400, true)
    ];
} else {
    $arResult['PICTURE'] = [
        'ALT' => $arResult['NAME'],
        'SRC' => \Likee\Site\Helper::getEmptyImg(650, 650),
        'THUMB' => \Likee\Site\Helper::getEmptyImg(450, 400)
    ];
}

/** MORE PHOTOS */
$arResult['PHOTOS'] = [$arResult['PICTURE']];
if (!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $iImage) {
        $arResult['PHOTOS'][] = [
            'ALT' => !empty($arResult['PICTURE']['ALT']) ? $arResult['PICTURE']['ALT'] : $arResult['NAME'],
            'SRC' => \Likee\Site\Helper::getResizePath($iImage, 1280, 1024),
            'THUMB' => \Likee\Site\Helper::getResizePath($iImage, 450, 400)
        ];
    }
}

$arResult['PHOTOS_COUNT'] = count($arResult['PHOTOS']);

/** MIN PRICE */
foreach ($arResult['OFFERS'] as $arOffer) {
    if ($arOffer['CATALOG_AVAILABLE'] == 'Y' && $arOffer['CAN_BUY']) {
        $arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];

        $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] = 'white';
        if (! empty($arResult['PROPERTIES']['PRICESEGMENTID']['VALUE']) && 'Red' == $arResult['PROPERTIES']['PRICESEGMENTID']['VALUE']) {
            $arOfferPrice1 = \Likee\Site\Helpers\Catalog::getProductPrice($arOffer['ID'], 8);

            if ($arOfferPrice1 && $arOfferPrice1['PRICE'] > $arResult['MIN_PRICE']['DISCOUNT_VALUE']) {
                $arResult['MIN_PRICE']['VALUE'] = $arOfferPrice1['PRICE'];
                $arResult['MIN_PRICE']['PRINT_VALUE'] = CurrencyFormat($arOfferPrice1['PRICE'], $arOfferPrice1['CURRENCY']);
            }
            $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] = 'red';
        }
        $arResult['MIN_PRICE']['DISCOUNT_PCT'] = \Likee\Site\Helpers\Catalog::getDiscountPercent($arResult['MIN_PRICE']);

        break;
    }
}

/** OFFERS */
$arOffersFilter = [
    'IBLOCK_ID' => $arCatalog['IBLOCK_ID'],
    'ACTIVE' => 'Y',
    'ACTIVE_DATE' => 'Y'
];

if ($arParams['HIDE_NOT_AVAILABLE'] == 'Y')
    $arOffersFilter['CATALOG_AVAILABLE'] = 'Y';

if (!empty($GLOBALS[$arParams['FILTER_NAME']]['OFFERS']) && is_array($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'])) {
    $arOffersFilter = array_merge($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'], $arOffersFilter);
}

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
        'CATALOG_AVAILABLE' => 'Y',
        '!DETAIL_PICTURE' => false,
        '!ID' => $arResult['ID']
    ];

    foreach ($arColorsProps as $sProp) {
        $arColorsFilter['PROPERTY_' . $sProp] = $arResult['PROPERTIES'][$sProp]['VALUE'];
    }

    $arColorsFilter['=ID'] = CIBlockElement::SubQuery('PROPERTY_' . $arCatalog['SKU_PROPERTY_ID'], $arOffersFilter);
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

    $arColor['FILE'] = \Likee\Site\Helper::getResizePath($arColor['DETAIL_PICTURE'], 185, 185, true);

    $arResult['COLORS'][] = $arColor;
}

/** SIZES */
$arAvailableFilter = $arOffersFilter;
$arAvailableFilter['ID'] = array_column($arResult['OFFERS'], 'ID');


$rsAvailable = CIBlockElement::GetList([], $arAvailableFilter, false, false, ['ID']);
$arAvailableOffers = [];
while ($arAvailable = $rsAvailable->Fetch()) {
    $arAvailableOffers[] = $arAvailable['ID'];
}

$bModelAvailable = false;
$productHasAvailableOffers = false;

$arResult['SIZES'] = [];
$arResult['NO_SIZES'] = false;
foreach ($arResult['OFFERS'] as $arOffer) {
    $arPropSize = $arOffer['PROPERTIES']['SIZE'];

    $arResult['NO_SIZES'] = (empty($arPropSize['VALUE']) && 1 == count($arResult['OFFERS']));

    if (empty($arPropSize['VALUE']) && !$arResult['NO_SIZES'])
        continue;

    $bOfferAvailable = $arOffer['CATALOG_AVAILABLE'] == 'Y' && in_array($arOffer['ID'], $arAvailableOffers);

    $bModelAvailable |= $bOfferAvailable;

    $arSize = [
        'VALUE' => $arPropSize['VALUE'],
        'OFFER_ID' => $arOffer['ID'],
        'PRICE' => CCurrencyLang::CurrencyFormat($arOffer['PRICES']['BASE']['VALUE'], $arOffer['PRICES']['BASE']['CURRENCY'], true),
        'CATALOG_AVAILABLE' => $bOfferAvailable ? 'Y' : 'N',
        'STORES' => $arOffer['PROPERTIES']['STORES']['VALUE']
    ];

    $iOfferID = intval($arSize['OFFER_ID']);
    $arSize['CAN_BUY'] = $iOfferID > 0 && $arSize['CATALOG_AVAILABLE'] == 'Y' && \Likee\Site\Helpers\Catalog::productCanBuy($iOfferID, $arSize['STORES']);
    $arSize['CAN_RESERVED'] = $iOfferID > 0 && $arSize['CATALOG_AVAILABLE'] == 'Y' && \Likee\Site\Helpers\Catalog::productCanBeReserved($iOfferID, $arSize['STORES']);

    $arResult['SIZES'][$arPropSize['VALUE']] = $arSize;

    $productHasAvailableOffers |= ($arSize['CAN_BUY'] || $arSize['CAN_RESERVED']);

    unset($iOfferID, $arSize);
}
ksort($arResult['SIZES'], SORT_NATURAL);

$arResult['CATALOG_AVAILABLE'] = $bModelAvailable ? 'Y' : 'N';
$arResult['AVAILABILITY_IN_REGION'] = $productHasAvailableOffers ? 'Y' : 'N';

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

/** BONUSES */
$arResult['BONUS'] = \Likee\Site\SailPlay::getProductBonuses($arResult['ID']);


/** DELIVERY PRICE */
$arResult['DELIVERY_PRICE'] = \Likee\Site\Helpers\Delivery::getMinDeliveryPriceForCity($arParams['CITY_CODE'], $arResult['MIN_PRICE']['CURRENCY']);


/** STORES */
$arResult['AVAILABILITY_IN_SHOPS'] = 'N';
if ($arResult['CATALOG_AVAILABLE'] == 'Y' && !empty($arResult['SIZES'])) {
    $arLocation = \Likee\Location\Location::getCurrent();
    $arStores = array_column($arLocation['STORES'], 'ID');

    $rsStock = \Bitrix\Catalog\StoreProductTable::getList([
        'filter' => [
            'PRODUCT_ID' => $arAvailableOffers,
            '>AMOUNT' => 0,
            'STORE_ID' => $arStores,
            '>STORE.GPS_N' => 0,
            '>STORE.GPS_S' => 0,
            'STORE.ACTIVE' => 'Y'
        ],
        'select' => ['ID'],
        'limit' => 1
    ]);

    $arResult['AVAILABILITY_IN_SHOPS'] = $rsStock->getSelectedRowsCount() > 0 ? 'Y' : 'N';
}

$arResult['LABELS'] = array();
if (! empty($arResult['PROPERTIES']['MLT']['VALUE'])) {
    $arProp = $arResult['PROPERTIES']['MLT'];
    $arLabel = \Likee\Site\Helpers\Catalog::getProductLabels($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'], $arProp['VALUE']);
    if ($arLabel) {
        $arResult['LABELS']['mlt'] = $arLabel;
    }
    unset($arLabel, $arProp);
}
if (! empty($arResult['PROPERTIES']['MRT']['VALUE'])) {
    $arProp = $arResult['PROPERTIES']['MRT'];
    $arLabel = \Likee\Site\Helpers\Catalog::getProductLabels($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'], $arProp['VALUE']);
    if ($arLabel) {
        $arResult['LABELS']['mrt'] = $arLabel;
    }
    unset($arLabel, $arProp);
}
// группировки
if (! empty($arResult['PROPERTIES']['SHOW_IN_GROUPS']['VALUE'])) {
    $arLabel = \Likee\Site\Helpers\Groups::getGroupLabel($arResult['PROPERTIES']['SHOW_IN_GROUPS']['VALUE']);
    if ($arLabel) {
        $arResult['LABELS'] = ['group' => $arLabel];
    }
    unset($arLabel, $arProp);
}

if(! empty($arResult['SECTION']['PATH'][0]['ID'])){
    $parentSect = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => $arResult['IBLOCK_ID'], "ID" => $arResult['SECTION']['PATH'][0]['ID']), false, array("UF_*"));
    if($parentSectInfo = $parentSect->GetNext()) {
        if(strlen($parentSectInfo["UF_SIZES_TAB_HTML"]) > 0) { //проверяем что поле заполнено

            $tabInfo = CIBlockElement::GetByID($parentSectInfo["UF_SIZES_TAB_HTML"]);
            if($ar_resInfo = $tabInfo->GetNext()){
                $arResult['SECTION_SIZES_TAB'] = $ar_resInfo['PREVIEW_TEXT'];
            }
        }
    }
}

// возможность резервирования
$arResult['RESERVE_NOT_ALLOWED'] = false;
if (! empty($arResult['PROPERTIES']['NO_RESERVE']['VALUE_ENUM_ID'])) {
    $arResult['RESERVE_NOT_ALLOWED'] = true;
}