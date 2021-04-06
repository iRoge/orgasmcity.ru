<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */


foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['PREVIEW_PICTURE']) || !empty($arItem['DETAIL_PICTURE'])) {
        $arImage = $arItem['PREVIEW_PICTURE'] ?: $arItem['DETAIL_PICTURE'];
        $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arImage, 325, 325);

        $arItem['PICTURE'] = $arImage;
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
                'FULL' => \Likee\Site\Helper::getResizePath($arImage, 325, 325),
                'ALT' => $arItem['PICTURE']['ALT']
            ];
        }
    }
    unset($arOffer);
}
unset($arItem);

