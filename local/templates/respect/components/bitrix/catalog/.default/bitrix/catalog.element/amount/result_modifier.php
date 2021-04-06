<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

$sPropColor = 'COLOR';
$sPropSize = 'SIZE';
$sPropSizeClothes = 'SIZES_CLOTHES';
$sPropArticle = 'ARTICLE';

$arResult['LOCATION'] = \Likee\Location\Location::getCurrent();

$arCatalog = CCatalogSKU::GetInfoByIBlock($arParams['IBLOCK_ID']);

$arParams['OFFER_IBLOCK_ID'] = \Likee\Site\Helpers\IBlock::getIBlockId('OFFERS');
$arResult['ARTICLE'] = $arResult['PROPERTIES'][$sPropArticle]['VALUE'];


/** DETAIL PICTURE */
if (!empty($arResult['PREVIEW_PICTURE']) || !empty($arResult['DETAIL_PICTURE'])) {
    $arImage = $arResult['PREVIEW_PICTURE'] ?: $arResult['DETAIL_PICTURE'];
    $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arResult['DETAIL_PICTURE'], 650, 650, true);
    $arResult['PICTURE'] = $arImage;
} else {
    $arResult['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(650, 650);
}


/** MORE PHOTOS */
if ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $iImage) {
        $arResult['PHOTOS'][] = [
            'ALT' => $arResult['PICTURE']['ALT'],
            'SRC' => \Likee\Site\Helper::getResizePath($iImage, 1280, 1024, true),
            'THUMB' => \Likee\Site\Helper::getResizePath($iImage, 645, 385, true)
        ];
    }

    $arResult['PHOTOS_COUNT'] = count($arResult['PHOTOS']);
}


/** SIZES */
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

$arOffersFilter['ID'] = array_column($arResult['OFFERS'], 'ID');

$rsAvailable = CIBlockElement::GetList([], $arOffersFilter, false, false, ['ID']);
$arAvailableOffers = [];
while ($arAvailable = $rsAvailable->Fetch()) {
    $arAvailableOffers[] = $arAvailable['ID'];
}

$bModelAvailable = false;

$arResult['SIZES'] = [];
foreach ($arResult['OFFERS'] as $arOffer) {
    $arPropSize = $arOffer['PROPERTIES']['SIZE'];

    if (empty($arPropSize['VALUE']))
        continue;

    $bOfferAvailable = $arOffer['CATALOG_AVAILABLE'] == 'Y' && in_array($arOffer['ID'], $arAvailableOffers);

    $bModelAvailable |= $bOfferAvailable;

    $arResult['SIZES'][$arPropSize['VALUE']] = [
        'VALUE' => $arPropSize['VALUE'],
        'OFFER_ID' => $arOffer['ID'],
        'PRICE' => CCurrencyLang::CurrencyFormat($arOffer['PRICES']['BASE']['VALUE'], $arOffer['PRICES']['BASE']['CURRENCY'], true),
        'CATALOG_AVAILABLE' => $bOfferAvailable ? 'Y' : 'N',
        'STORES' => $arOffer['PROPERTIES']['STORES']['VALUE'],
        'QUANTITY' => $arOffer['CATALOG_QUANTITY'],
    ];
}
ksort($arResult['SIZES'], SORT_NATURAL);

$arResult['CATALOG_AVAILABLE'] = $bModelAvailable ? 'Y' : 'N';


/*if (empty($arResult['MIN_PRICE'])) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        if ($arOffer['CAN_BUY']) {
            $arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];
            break;
        }
    }
}*/
foreach ($arResult['OFFERS'] as $arOffer) {
    if ($arOffer['CAN_BUY']) {
        $arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];
        break;
    }
}

/** BONUSES */
$arResult['BONUS'] = \Likee\Site\SailPlay::getProductBonuses($arResult['ID']);

/** STORES */

$arStores = $arStoresProducts = [];
foreach ($arResult['SIZES'] as $sSize => $arSize) {

    $arStores = array_merge($arStores, $arSize['STORES']);
    foreach ($arSize['STORES'] as $iStore) {
        //Дополнительная проверка на возможность резервирования

        if (\Likee\Site\Helpers\Catalog::productCanBeReservedByShop($arSize['OFFER_ID'], $iStore)) {
            $arStoresProducts[$iStore][] = $sSize;
        }
    }
}

$arStores = array_intersect(array_column($arResult['LOCATION']['STORES'], 'ID'), array_unique($arStores));
if (in_array(ONLINE_STORE_ID, $arStores))
    unset($arStores[array_search(ONLINE_STORE_ID, $arStores)]);

$rsStores = \CCatalogStore::GetList(
    ['SORT' => 'ASC'],
    ['ID' => $arStores],
    false,
    false,
    [
        'ID', 'TITLE', 'PHONE', 'ADDRESS', 'SCHEDULE', 'GPS_N', 'GPS_S', 'UF_METRO', 'UF_PHONES'
    ]
);

$iIndex = 0;
$arResult['JSON_SHOPS'] = [];

while ($arStore = $rsStores->Fetch()) {
    if (empty($arStore['PHONE'])) {
        $arPhones = unserialize($arStore['UF_PHONES']);
        $arStore['PHONE'] = reset($arPhones);
    }

    $arStore['METRO'] = [];
    foreach (unserialize($arStore['UF_METRO']) as $iMetro) {
        $obEntity = \Likee\Site\Helpers\HL::getEntityClassByTableName('b_1c_dict_metro');
        if (!empty($obEntity) && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();
            $arMetro = $sClass::getRowById($iMetro);
            $arStore['METRO'][] = $arMetro['UF_NAME'];
        }
    }

    $arStore['METRO'] = implode(', ', $arStore['METRO']);

    $arStore['GPS_N'] = floatval(str_replace(',', '.', $arStore['GPS_N']));
    $arStore['GPS_S'] = floatval(str_replace(',', '.', $arStore['GPS_S']));

    $iDistance = 0;
    if (!empty($arLocation['LAT']) && !empty($arLocation['LON'])) {
        $iDistance = \Likee\Location\Location::distance($arStore['GPS_N'], $arStore['GPS_S'], $arLocation['LAT'], $arLocation['LON'], 'K');
        $iDistance = round($iDistance, 1);
    }

    $arResult['JSON_SHOPS'][] = [
        'title' => $arStore['TITLE'],
        'address' => $arStore['ADDRESS'],
        'distance' => $iDistance,
        'subway' => $arStore['METRO'],
        'subway_trans' => Cutil::translit($arStore['METRO'], "ru", []),
        'worktime' => $arStore['SCHEDULE'],
        'phone' => $arStore['PHONE'],
        'coordinates' => [
            'lat' => $arStore['GPS_N'],
            'lng' => $arStore['GPS_S']
        ],
        'sizes' => $arStoresProducts[$arStore['ID']] ?: [],
        'index' => intval($arStore['ID'])
    ];
}