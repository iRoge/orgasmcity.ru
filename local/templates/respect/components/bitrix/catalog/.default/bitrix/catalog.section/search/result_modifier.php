<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var CBitrixComponentTemplate $this */

//кешируем кол-во найденных элементов, чтобы использовать component_epilog.php
$arResult['RECORD_COUNT'] = $arResult['NAV_RESULT']->NavRecordCount;
$this->__component->setResultCacheKeys(['RECORD_COUNT']);

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