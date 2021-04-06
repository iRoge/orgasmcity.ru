<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['PREVIEW_PICTURE']) || !empty($arItem['DETAIL_PICTURE'])) {
        $arImage = $arItem['PREVIEW_PICTURE'] ?: $arItem['DETAIL_PICTURE'];
        $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arImage, 325, 325);

        $arItem['PICTURE'] = $arImage;
    } else {
        $arItem['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(325, 325);
    }

    $arItem['MORE_PICTURES'] = [];

    $arItem['CAN_BUY'] = false;
    $arItem['MIN_PRICE'] = \Likee\Site\Helpers\Catalog::getMinPriceByOffers($arItem['OFFERS']);

    foreach ($arItem['OFFERS'] as $iKeyOffer => &$arOffer) {
        $arOffer['CAN_BUY'] &= \Likee\Site\Helpers\Catalog::productCanBuy($arOffer['ID'], $arOffer['PROPERTIES']['STORES']['VALUE']);

        $arItem['CAN_BUY'] |= $arOffer['CAN_BUY'];


        $arPropColor = $arOffer['PROPERTIES']['COLOR'];

        if (
            $arOffer['CAN_BUY']
            && (!empty($arOffer['PREVIEW_PICTURE']) || !empty($arOffer['DETAIL_PICTURE']))
            && !empty($arPropColor['VALUE'])
            && !array_key_exists($arPropColor['VALUE'], $arItem['MORE_PICTURES'])
        ) {
            $arImage = $arOffer['PREVIEW_PICTURE'] ?: $arOffer['DETAIL_PICTURE'];

            $arColorImage = \Likee\Site\Helpers\HL::getFileByProp($arPropColor);

            $arItem['MORE_PICTURES'][$arPropColor['VALUE']] = [
                'THUMB' => \Likee\Site\Helper::getResizePath($arColorImage, 20, 20),
                'FULL' => \Likee\Site\Helper::getResizePath($arImage, 650, 650),
                'ALT' => $arItem['PICTURE']['ALT']
            ];
        }
    }
    unset($arOffer);

    $arItem['LABELS'] = array();
    if (! empty($arItem['PROPERTIES']['MLT']['VALUE'])) {
        $arProp = $arItem['PROPERTIES']['MLT'];
        $arLabel = \Likee\Site\Helpers\Catalog::getProductLabels($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'], $arProp['VALUE']);
        if ($arLabel) {
            $arItem['LABELS']['mlt'] = $arLabel;
        }
        unset($arLabel, $arProp);
    }
    if (! empty($arItem['PROPERTIES']['MRT']['VALUE'])) {
        $arProp = $arItem['PROPERTIES']['MRT'];
        $arLabel = \Likee\Site\Helpers\Catalog::getProductLabels($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'], $arProp['VALUE']);
        if ($arLabel) {
            $arItem['LABELS']['mrt'] = $arLabel;
        }
        unset($arLabel, $arProp);
    }
}
unset($arItem);

$arResult['ACTION'] = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    [
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'IBLOCK_ID' => $arParams['ACTIONS_IBLOCK_ID'],
        'PROPERTY_SECTION' => $arResult['ID'],
        '!PREVIEW_PICTURE' => false
    ],
    false,
    ['nTopCount' => 1],
    ['ID', 'NAME', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'PROPERTY_LINK']
)->Fetch();

if ($arResult['ACTION']) {
    \Bitrix\Iblock\Component\Tools::getFieldImageData(
        $arResult['ACTION'],
        ['PREVIEW_PICTURE'],
        \Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT
    );

    if ($arResult['ACTION']['PREVIEW_PICTURE']) {
        $arResult['ACTION']['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arResult['ACTION']['PREVIEW_PICTURE'], 325, 325);
    }
}

$arFilter = [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => $arParams['BANNERS_IBLOCK_ID'],
    'PROPERTY_SECTION' => $arResult['ID'],
    '!DETAIL_PICTURE' => false
];

if($arParams['PROMO']['UF_CODE']) {
    unset($arFilter['PROPERTY_SECTION']);
    if($arParams['PROMO']['PROP_CODE'] == 'MLT')
        $arFilter['!PROPERTY_SPECIAL_MLT'] = false;
    elseif($arParams['PROMO']['PROP_CODE'] == 'MRT')
        $arFilter['!PROPERTY_SPECIAL_MRT'] = false;

}


$arResult['BANNER'] = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    $arFilter,
    false,
    false,
    [
        'ID',
        'NAME',
        'DETAIL_PICTURE',
        'PROPERTY_BUTTON_LINK',
        'PROPERTY_BUTTON_NAME',
        'PROPERTY_SPECIAL_POSITION',
    ]
)->Fetch();

if ($arResult['BANNER']) {
    if ($arParams['PROMO']['UF_CODE']) {
        if ($arResult['BANNER']['PROPERTY_SPECIAL_POSITION_VALUE']) {
            $arResult['BANNER'] = false;
        }
    }

    if($arResult['BANNER'])
        $arResult['BANNER']['DETAIL_PICTURE'] = \CFile::GetPath($arResult['BANNER']['DETAIL_PICTURE']);
}