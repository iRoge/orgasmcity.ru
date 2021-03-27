<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<yml_catalog date="' . date('Y-m-d H:i') . '">';
echo '<shop>';
echo '<url>' . 'https://' . SITE_SERVER_NAME . '/' . '</url>';

echo '<categories>';
foreach ($arResult['CATEGORIES_IDS'] as $category) {
    echo '<category id="' . $category['ID'] . '"' . (!empty($category['PARENT_ID']) ? ' Parentid="' . $category['PARENT_ID'] . '"' : '') . '>' . htmlspecialchars($category['NAME'], ENT_XML1) . '</category>';
}
echo '</categories>';

echo '<offers>';
foreach ($arResult['ITEMS'] as $arItem) {
    foreach ($arItem['OFFERS'] as $offerId => $offer) {
        $availability = (count($arItem['SIZES'])) ? 'true' : 'false';
        echo '<offer id="' . htmlspecialchars($offerId, ENT_XML1) . '" type="vendor.model" available="' . $availability . '">';
        echo '<url>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PAGE_URL'] . '</url>';
        echo '<price>' . $arItem['VIEW_PRICE'] . '</price>';
        if ($arItem['SEGMENT'] != 'White' && !empty($arItem['OLD_PRICE']) && $arItem['OLD_PRICE'] > $arItem['VIEW_PRICE']) {
            echo '<oldprice>' . $arItem['OLD_PRICE'] . '</oldprice>';
        }
        echo '<currencyId>' . (SITE_SERVER_NAME == 'respect-shoes.ru' ? 'RUB' : 'BYN') . '</currencyId>';
        echo '<typePrefix>' . $arItem['PROPERTY_TYPEPRODUCT_VALUE'] . '</typePrefix>';
        echo '<categoryId>' . $arItem['IBLOCK_SECTION_ID'] . '</categoryId>';
        if (!empty($arItem['PROPERTY_BRAND_VALUE'])) {
            echo '<vendor>' . htmlspecialchars($arItem['PROPERTY_BRAND_VALUE'], ENT_XML1) . '</vendor>';
        }
        echo '<model>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</model>';
        echo '<name>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</name>';
        echo '<picture>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PICTURE'] . '</picture>';
        echo '<picture>' . 'https://' . SITE_SERVER_NAME . $arItem['PREVIEW_PICTURE'] . '</picture>';
    
        foreach ($arItem['MORE_PHOTO'] as $morePhotoUrl) {
            echo '<picture>' . 'https://' . SITE_SERVER_NAME . $morePhotoUrl . '</picture>';
        }

        if (!empty($arItem['PROPERTY_COUNTRY_VALUE'])) {
            echo '<country_of_origin>' . htmlspecialchars($arItem['PROPERTY_COUNTRY_VALUE'], ENT_XML1) . '</country_of_origin>';
        }
        if (!empty($offer['SIZE'])) {
            echo '<param name="Размер" unit="RU">' . $offer['SIZE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_SEASON_VALUE'])) {
            echo '<param name="Сезон">' . $arItem['PROPERTY_SEASON_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_COLORSFILTER_VALUE'])) {
            echo '<param name="Цвет">' . $arItem['PROPERTY_COLORSFILTER_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_UPPERMATERIAL_VALUE'])) {
            echo '<param name="Материал верха">' . $arItem['PROPERTY_UPPERMATERIAL_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_LININGMATERIAL_VALUE'])) {
            echo '<param name="Материал подкладки">' . $arItem['PROPERTY_LININGMATERIAL_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_RHODEPRODUCT_VALUE'])) {
            echo '<param name="Пол">' . $arItem['PROPERTY_RHODEPRODUCT_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_HEELHEIGHT_TYPE_VALUE'])) {
            echo '<param name="Высота каблука">' . $arItem['PROPERTY_HEELHEIGHT_TYPE_VALUE'] . '</param>';
        }
        if (!empty($arItem['PROPERTY_ZASTEGKA_VALUE'])) {
            echo '<param name="Тип застежки">' . $arItem['PROPERTY_ZASTEGKA_VALUE'] . '</param>';
        }
        echo '</offer>';
    }
}
echo '</offers>';
echo '</shop>';
echo '</yml_catalog>';
