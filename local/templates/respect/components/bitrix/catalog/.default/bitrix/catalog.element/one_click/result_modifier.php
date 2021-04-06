<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

$sPropColor = 'COLOR';
$sPropSize = 'SIZE';
$sPropSizeClothes = 'SIZES_CLOTHES';
$sPropArticle = 'ARTICLE';

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
$arMatrix = [];
$arResult['SIZES'] = [];

foreach ($arResult['OFFERS'] as $arOffer) {
    $arPropColor = $arOffer['PROPERTIES']['COLOR'];
    $arPropSize = $arOffer['PROPERTIES']['SIZE'];

    if (empty($arPropColor['VALUE']) || empty($arPropSize['VALUE']))
        continue;

    if (!in_array($arPropSize['VALUE'], $arResult['SIZES']))
        $arResult['SIZES'][] = $arPropSize['VALUE'];

    if (!array_key_exists($arPropColor['VALUE'], $arMatrix)) {
        $arMatrix[$arPropColor['VALUE']] = [
            'FILE' => \Likee\Site\Helpers\HL::getFileByProp($arPropColor),
            'ARTICLE' => '',
            'SIZES' => []
        ];
    }

    if (empty($arMatrix[$arPropColor['VALUE']]['ARTICLE']) && !empty($arOffer['PROPERTIES']['ARTICLE']['VALUE']))
        $arMatrix[$arPropColor['VALUE']]['ARTICLE'] = $arOffer['PROPERTIES']['ARTICLE']['VALUE'];

    $arAvailableFilter = array_merge($GLOBALS[$arParams['FILTER_NAME']]['OFFERS'], [
        'IBLOCK_ID' => $arOffer['IBLOCK_ID'],
        'ID' => $arOffer['ID'],
    ]);

    $rsAvailable = CIBlockElement::GetList([], $arAvailableFilter, false, false, ['ID']);
    if ($rsAvailable->SelectedRowsCount() <= 0)
        $arOffer['CATALOG_AVAILABLE'] = 'N';

    $arSize = [
        'VALUE' => $arPropSize['VALUE'],
        'QUANTITY' => 0,
        'PRICE' => CCurrencyLang::CurrencyFormat($arOffer['PRICES']['BASE']['VALUE'], $arOffer['PRICES']['BASE']['CURRENCY'], true),
        'BASKET_ID' => 0,
        'OFFER_ID' => $arOffer['ID'],
        'CATALOG_QUANTITY' => intval($arOffer['CATALOG_QUANTITY']),
        'CATALOG_AVAILABLE' => $arOffer['CATALOG_AVAILABLE']
    ];

    $arMatrix[$arPropColor['VALUE']]['SIZES'][$arPropSize['VALUE']] = $arSize;
}
$arResult['MATRIX'] = $arMatrix;

sort($arResult['SIZES']);
$arParams['COLOR'] = key($arResult['MATRIX']);

if (empty($arResult['MIN_PRICE'])) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        if ($arOffer['CAN_BUY']) {
            $arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];
            break;
        }
    }
}

/** BONUSES */
$arResult['BONUS'] = \Likee\Site\SailPlay::getProductBonuses($arResult['ID']);