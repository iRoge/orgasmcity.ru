<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arResult['PROPS'] = [
    'COLOR' => 'COLOR',
    'ARTICLE' => 'ARTICLE',
    'SIZE' => 'SIZE',
];

$arOrderProps = array();
foreach ($arResult['ORDER_PROPS'] as $iKey => $arProp) {
    $arOrderProps[$arProp['CODE']] = $arProp;
}
$arResult['ORDER_PROPS'] = $arOrderProps;

$arOffersId = array_column($arResult['BASKET'], 'PRODUCT_ID');

$rsOffers = \CIBlockElement::GetList(
    array(),
    array('ID' => $arOffersId),
    false,
    false,
    array(
        'ID',
        'IBLOCK_ID',
        'PROPERTY_CML2_LINK',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PROPERTY_ARTICLE',
        'PROPERTY_SIZE',
        'PROPERTY_COLOR'
    )
);

while ($arOffer = $rsOffers->GetNextElement()) {
    $arOffer = $arOffer->GetFields();
    $arOffers[$arOffer['ID']] = $arOffer;
}

$arColors = $arOffers;

CIBlockElement::GetPropertyValuesArray(
    $arColors,
    reset($arOffers)['IBLOCK_ID'],
    [],
    array('CODE' => 'COLOR')
);

foreach ($arResult['BASKET'] as &$arItem) {

    if (empty($arItem['PICTURE'])) {
        $arItem['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(325, 325);
    }

    if (array_key_exists($arItem['PRODUCT_ID'], $arOffers)) {
        $arOffer = $arOffers[$arItem['PRODUCT_ID']];

        if (array_key_exists($arOffer['ID'], $arColors)) {
            $arPropColor = $arColors[$arOffer['ID']]['COLOR'];

            $arItem['COLOR'] = [
                'VALUE' => '',//$arPropColor['VALUE'],
                //'FILE' => \Likee\Site\Helpers\HL::getFileByProp($arPropColor),
            ];
        }

        $obModel = CIBlockElement::GetList(
            array(),
            array(
                '=ID' => intval($arOffer['PROPERTY_CML2_LINK_VALUE']),
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'PROPERTY_COLOR',
                'DETAIL_PAGE_URL',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
            )
        )->GetNextElement();

        $arModel = $obModel->GetFields();
        $arModel['PROPS'] = $obModel->GetProperties();

        if (!empty($arOffer['PROPERTY_ARTICLE_VALUE'])) {
            $arItem['ARTICLE'] = $arOffer['PROPERTY_ARTICLE_VALUE'];
        } elseif (!empty($arModel['PROPS']['ARTICLE']['VALUE'])) {
            $arItem['ARTICLE'] = $arModel['PROPS']['ARTICLE']['VALUE'];
        } else {
            $arItem['ARTICLE'] = '';
        }

        if (empty($arItem['COLOR']['VALUE'])) {
            $arItem['COLOR']['VALUE'] = \Likee\Site\Helpers\HL::getFieldValueByProp($arModel['PROPS']['COLORSFILTER'], 'UF_GRBCODE');
            $arItem['COLOR']['VALUE'] = \Likee\Site\Helper::rgb2hex($arItem['COLOR']['VALUE']);
        }


        $arItem['DETAIL_PAGE_URL'] = $arModel['DETAIL_PAGE_URL'];
    }
}
unset($arItem);

$arStoresId = array_filter(array_column($arResult['SHIPMENT'], 'STORE_ID'));

$arResult['STORES'] = [];

if ($arStoresId) {
    $rsStores = \Bitrix\Catalog\StoreTable::getList([
        'filter' => [
            'ID' => $arStoresId
        ]
    ]);

    while ($arStore = $rsStores->fetch()) {
        $arResult['STORES'][] = $arStore;
    }
}