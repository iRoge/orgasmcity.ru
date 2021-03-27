<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
echo '<?xml version="1.0" encoding="UTF-8"?><products>';
if (!empty($arResult['ITEMS'])) {
    foreach ($arResult['ITEMS'] as $arItem) :
        echo '<product guid="' . $arItem['GUID_1C'] . '">';
        echo '<productId>' . $arItem['ID'] . '</productId>';
        echo '<article>' . htmlspecialchars($arItem['PROPERTY_ARTICLE_VALUE'], ENT_NOQUOTES, ini_get("default_charset"), false) . '</article>';
        echo '<kod_1s>' . $arItem['PROPERTY_KOD_1S_VALUE'] . '</kod_1s>';
        echo '<url>https://' . SITE_SERVER_NAME . $arItem['DETAIL_PAGE_URL'] . '</url>';
        if (!empty($arItem['DETAIL_PICTURE'])) {
            echo '<picture num="1">' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PICTURE'] . '</picture>';
        }
        if (!empty($arItem['PREVIEW_PICTURE'])) {
            echo '<picture num="2">' . 'https://' . SITE_SERVER_NAME . $arItem['PREVIEW_PICTURE'] . '</picture>';
        }
        $i = 3;
        foreach ($arItem['MORE_PHOTO'] as $morePhotoUrl) {
            echo '<picture num="' . $i . '">' . 'https://' . SITE_SERVER_NAME . $morePhotoUrl . '</picture>';
            $i++;
        }
        foreach ($arItem['OFFERS'] as $offerId => $offer) :
            echo '<offer id="' . $offerId . '">' . (!empty($offer['SIZE']) ? $offer['SIZE'] : 'Без размера') . '</offer>';
        endforeach;
        echo '</product>';
    endforeach;
}
echo '</products>';
