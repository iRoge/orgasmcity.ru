<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

$arCatalog = CCatalogSKU::GetInfoByIBlock($arParams['IBLOCK_ID']);
$arParams['OFFER_IBLOCK_ID'] = $arCatalog['IBLOCK_ID'];


/** OFFERS */
$arOffersFilter = [
    'IBLOCK_ID' => $arCatalog['IBLOCK_ID'],
    'ACTIVE' => 'Y',
    'ACTIVE_DATE' => 'Y'
];

if ($arParams['HIDE_NOT_AVAILABLE'] == 'Y') {
    $arOffersFilter['CATALOG_AVAILABLE'] = 'Y';
}

if (!empty($GLOBALS[$arParams['FILTER_NAME']]['OFFERS']) && is_array($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'])) {
    $arOffersFilter = array_merge($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'], $arOffersFilter);
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

$arResult['SIZES'] = [];
foreach ($arResult['OFFERS'] as $arOffer) {
    $arPropSize = $arOffer['PROPERTIES']['SIZE'];

    if (empty($arPropSize['VALUE'])) {
        continue;
    }

    $bOfferAvailable = $arOffer['CATALOG_AVAILABLE'] == 'Y' && in_array($arOffer['ID'], $arAvailableOffers);

    $bModelAvailable |= $bOfferAvailable;

    $arResult['SIZES'][$arPropSize['VALUE']] = [
        'VALUE' => $arPropSize['VALUE'],
        'OFFER_ID' => $arOffer['ID'],
        'PRICE' => CCurrencyLang::CurrencyFormat($arOffer['PRICES']['BASE']['VALUE'], $arOffer['PRICES']['BASE']['CURRENCY'], true),
        'CATALOG_AVAILABLE' => $bOfferAvailable ? 'Y' : 'N',
        'STORES' => $arOffer['PROPERTIES']['STORES']['VALUE']
    ];
}
ksort($arResult['SIZES'], SORT_NATURAL);

$arResult['CATALOG_AVAILABLE'] = $bModelAvailable ? 'Y' : 'N';
