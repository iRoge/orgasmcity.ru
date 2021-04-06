<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

$arLocation = \Likee\Location\Location::getCurrent();
$arResult['LOCATION'] = $arLocation;

$arCatalog = CCatalogSKU::GetInfoByIBlock($arParams['IBLOCK_ID']);

$arParams['OFFER_IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];
$arResult['ARTICLE'] = $arResult['PROPERTIES']['ARTICLE']['VALUE'];

/** DETAIL PICTURE */
if (!empty($arResult['PREVIEW_PICTURE']) || !empty($arResult['DETAIL_PICTURE'])) {
    $arImage = $arResult['PREVIEW_PICTURE'] ?: $arResult['DETAIL_PICTURE'];
    $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arResult['DETAIL_PICTURE'], 100, 100, true);
    $arResult['PICTURE'] = $arImage;
} else {
    $arResult['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(100, 100);
}

/** OFFERS */
$arOffersFilter = [];
if (!empty($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'])) {
    $arOffersFilter['IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];
    $arOffersFilter['ACTIVE_DATE'] = 'Y';
    $arOffersFilter['ACTIVE'] = 'Y';

    if ($arParams['HIDE_NOT_AVAILABLE'] == 'Y')
        $arOffersFilter['CATALOG_AVAILABLE'] = 'Y';

    $arOffersFilter = array_merge($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'], $arOffersFilter);
}

/** AVAILABLE */
$arOffersFilter['ID'] = array_column($arResult['OFFERS'], 'ID');

$rsAvailable = CIBlockElement::GetList([], $arOffersFilter, false, false, ['ID']);
$arAvailableOffers = [];
while ($arAvailable = $rsAvailable->Fetch()) {
    $arAvailableOffers[] = $arAvailable['ID'];
}

/** STORES LIST */
$arResult['STORES'] = [];
$arResult['STORES_TITLES'] = [];
$arStoresProducts = [];

if (!empty($arLocation['STORES'])) {
    $rsStock = \Bitrix\Catalog\StoreProductTable::getList([
        'filter' => [
            'PRODUCT_ID' => $arAvailableOffers,
            '>AMOUNT' => 0,
            'STORE_ID' => array_column($arLocation['STORES'], 'ID'),
            '>STORE.GPS_N' => 0,
            '>STORE.GPS_S' => 0,
            'STORE.ACTIVE' => 'Y'
        ],
        'select' => ['ID', 'PRODUCT_ID', 'STORE_ID', 'STORE_NAME' => 'STORE.TITLE']
    ]);

    $arAvailableOffers = [];
    while ($arStock = $rsStock->Fetch()) {
        if (!in_array($arStock['STORE_ID'], $arResult['STORES'])) {
            $arResult['STORES'][] = $arStock['STORE_ID'];
            $arResult['STORES_NAME'][$arStock['STORE_ID']] = $arStock['STORE_NAME'];
        }

        $arAvailableOffers[] = $arStock['PRODUCT_ID'];

        $arStoresProducts[$arStock['STORE_ID']][] = $arStock['PRODUCT_ID'];
    }
} else {
    $arAvailableOffers = [];
}

/** SIZES */
$bModelAvailable = false;

$arResult['SIZES'] = [];
foreach ($arResult['OFFERS'] as $arOffer) {
    $arPropSize = $arOffer['PROPERTIES']['SIZE'];

    if (empty($arPropSize['VALUE']))
        continue;

    $bOfferAvailable = $arOffer['CATALOG_AVAILABLE'] == 'Y' && in_array($arOffer['ID'], $arAvailableOffers);

    if (!$bOfferAvailable)
        continue;

    $arResult['SIZES'][$arPropSize['VALUE']] = [
        'OFFER_ID' => $arOffer['ID'],
        'VALUE' => $arPropSize['VALUE']
    ];
}
ksort($arResult['SIZES'], SORT_NATURAL);

if ($arParams['JSON'] == 'Y') {
    if (in_array(ONLINE_STORE_ID, $arResult['STORES']))
        unset($arResult['STORES'][array_search(ONLINE_STORE_ID, $arResult['STORES'])]);

    $rsStores = \CCatalogStore::GetList(
        ['SORT' => 'ASC'],
        ['ID' => $arResult['STORES'] ?: -1],
        false,
        false,
        [
            'ID', 'TITLE', 'PHONE', 'ADDRESS', 'SCHEDULE', 'GPS_N', 'GPS_S', 'UF_METRO', 'UF_PHONES'
        ]
    );

    $arStores = [];
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

        $arStores[$arStore['ID']] = $arStore;
    }

    $arResult['JSON_RESULT'] = [
        'pageTitle' => $arResult['NAME'],
        'colors' => [],
        'sizes' => $arResult['SIZES'],
        'shops' => []
    ];

    $arLocation = \Likee\Location\Location::getCurrent();

    foreach ($arStores as $arStore) {
        $arStore['GPS_N'] = floatval(str_replace(',', '.', $arStore['GPS_N']));
        $arStore['GPS_S'] = floatval(str_replace(',', '.', $arStore['GPS_S']));

        $iDistance = 0;
        if (!empty($arLocation['LAT']) && !empty($arLocation['LON'])) {
            $iDistance = \Likee\Location\Location::distance($arStore['GPS_N'], $arStore['GPS_S'], $arLocation['LAT'], $arLocation['LON'], 'K');
            $iDistance = round($iDistance, 1);
        }

        $arResult['JSON_RESULT']['shops'][] = [
            'title' => $arStore['TITLE'],
            'address' => $arStore['ADDRESS'],
            'distance' => $iDistance,
            'subway' => $arStore['METRO'],
            'worktime' => $arStore['SCHEDULE'],
            'phone' => $arStore['PHONE'],
            'coordinates' => [
                'lat' => $arStore['GPS_N'],
                'lng' => $arStore['GPS_S']
            ],
            'sizes' => $arStoresProducts[$arStore['ID']] ?: []
        ];
    }

    header('Content-type: application/json');
    echo json_encode($arResult['JSON_RESULT']);
    exit;
}