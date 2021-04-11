<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */


$temp = $arResult;
foreach ($temp as $arItem) {
    $arResult['ITEMS'][] = $arItem;
}
unset($temp);
foreach ($arResult['ITEMS'] as &$arItem) {
    \Likee\Site\Helpers\Catalog::checkElementResult($arItem);
    
    //echo '<pre>'.print_r($arItem,1).'</pre>';
    if (!empty($arItem['PREVIEW_PICTURE']) || !empty($arItem['DETAIL_PICTURE'])) {
        $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arItem['DETAIL_PICTURE'] ?: $arItem['PREVIEW_PICTURE'], 325, 325);

        $arItem['PICTURE'] = $arImage;
    } else {
        $arItem['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(325, 325);
    }

    $arItem['MORE_PICTURES'] = [];

    $arItem['CAN_BUY'] = false;
    $arItem['MIN_PRICE'] = \Likee\Site\Helpers\Catalog::getMinPriceByOffers($arItem['OFFERS']);

    $dbProps = CIBlockElement::GetProperty($arItem['IBLOCK_ID'], $arItem['PRODUCT_ID'], [], ['CODE' => 'PRICESEGMENTID']);
    if ($arProps = $dbProps->Fetch()) {
        $arItem['PROPERTIES']['PRICESEGMENTID'] = $arProps;
    }
    unset($dbProps, $arProps);

    $arItem['MIN_PRICE']['DISCOUNT_SEGMENT'] = 'white';
    if (! empty($arItem['PROPERTIES']['PRICESEGMENTID']['VALUE']) && 'Red' == $arItem['PROPERTIES']['PRICESEGMENTID']['VALUE']) {
        $arOfferPrice1 = \Likee\Site\Helpers\Catalog::getProductPrice($arItem['OFFERS'][0]['ID'], 8);

        if ($arOfferPrice1 && $arOfferPrice1['PRICE'] > $arItem['MIN_PRICE']['DISCOUNT_VALUE']) {
            $arItem['MIN_PRICE']['VALUE'] = $arOfferPrice1['PRICE'];
            $arItem['MIN_PRICE']['PRINT_VALUE'] = CurrencyFormat($arOfferPrice1['PRICE'], $arOfferPrice1['CURRENCY']);
        }
        $arItem['MIN_PRICE']['DISCOUNT_SEGMENT'] = 'red';
    }
    $arItem['MIN_PRICE']['DISCOUNT_PCT'] = \Likee\Site\Helpers\Catalog::getDiscountPercent($arItem['MIN_PRICE']);

    foreach ($arItem['OFFERS'] as $iKeyOffer => &$arOffer) {
        $arOffer['CAN_BUY'] &= \Likee\Site\Helpers\Catalog::productCanBuy($arOffer['ID'], $arOffer['PROPERTIES']['STORES']['VALUE']);

        $arItem['CAN_BUY'] |= $arOffer['CAN_BUY'];

        $arPropColor = $arOffer['PROPERTIES']['COLOR'];

        if ($arOffer['CAN_BUY']
            && (!empty($arOffer['PREVIEW_PICTURE']) || !empty($arOffer['DETAIL_PICTURE']))
            && !empty($arPropColor['VALUE'])
            && !array_key_exists($arPropColor['VALUE'], $arItem['MORE_PICTURES'])
        ) {
            $arImage = $arOffer['PREVIEW_PICTURE'] ?: $arOffer['DETAIL_PICTURE'];

            $arColorImage = \Likee\Site\Helpers\HL::getFileByProp($arPropColor);

            $arItem['MORE_PICTURES'][$arPropColor['VALUE']] = [
                'THUMB' => \Likee\Site\Helper::getResizePath($arColorImage, 20, 20),
                'FULL' => \Likee\Site\Helper::getResizePath($arImage, 325, 325),
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


//Дополнительный запрос на количество
$arResult['COUNT'] = 0;
$iFUser = intval(CSaleBasket::GetBasketUserID(true));

if ($iFUser > 0) {
    $dbViewed = \CSaleViewedProduct::GetList(
        array(
            'DATE_VISIT' => 'DESC',
        ),
        array(
            'LID' => SITE_ID,
            'FUSER_ID' => $iFUser
        ),
        false,
        array(
            'nTopCount' => $arParams['MAX_VIEWED_COUNT'],
        ),
        array('ID')
    );

    $arResult['COUNT'] = $dbViewed->SelectedRowsCount();
}