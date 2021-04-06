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

    $arItem['CAN_BUY'] = 0;

    foreach ($arItem['OFFERS'] as $iKeyOffer => $arOffer) {
        if ($arOffer['CAN_BUY'] && empty($arItem['CAN_BUY'])) {
            $arItem['MIN_PRICE'] = $arOffer['MIN_PRICE'];
            $arItem['CAN_BUY'] = $arOffer['CAN_BUY'];
            $arItem['ADD_URL'] = $arOffer['ADD_URL'];
        }

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