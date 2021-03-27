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
    echo '<category id="' . $category['ID'] . '"' . (!empty($category['PARENT_ID']) ? ' parentId="' . $category['PARENT_ID'] . '"' : '') . '>' . htmlspecialchars($category['NAME'], ENT_XML1) . '</category>';
}
echo '</categories>';

echo '<offers>';
foreach ($arResult['ITEMS'] as $arItem) {
    foreach ($arItem['OFFERS'] as $offerId => $offer) {
        $availability = (count($arItem['SIZES'])) ? 'true' : 'false';
        echo '<offer id="' . htmlspecialchars($offerId, ENT_XML1) . '" available="' . $availability . '" group_id="' . $arItem['ID'] . '">';
        echo '<url>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PAGE_URL'] . '</url>';
        if (!empty($arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']])) {
            if ($arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']]['SEGMENT'] === 'White') {
                echo '<price>' . $arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']]['OLD_PRICE'] . '</price>';
            } else {
                echo '<price>' . $arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']]['PRICE'] . '</price>';
                if ($arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']]['OLD_PRICE'] > 0) {
                    echo '<oldprice>' . $arResult['UNIQUE_SHOWCASES']['DEFAULT_PRICE'][$arItem['ID']]['OLD_PRICE'] . '</oldprice>';
                }
            }
        }
        echo '<categoryId>' . $arItem['IBLOCK_SECTION_ID'] . '</categoryId>';
        if (!empty($arItem['PROPERTY_BRAND_VALUE'])) {
            echo '<vendor>' . htmlspecialchars($arItem['PROPERTY_BRAND_VALUE'], ENT_XML1) . '</vendor>';
        }
        echo '<model>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</model>';
        echo '<name>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</name>';
        echo '<picture>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PICTURE'] . '</picture>';
        echo '<param name="picture2">' . 'https://' . SITE_SERVER_NAME . $arItem['PREVIEW_PICTURE'] . '</param>';

        $i = 3;
        foreach ($arItem['MORE_PHOTO'] as $morePhotoUrl) {
            echo '<param name="picture' . $i . '">' . 'https://' . SITE_SERVER_NAME . $morePhotoUrl . '</param>';
            $i++;
        }

        if (!empty($arItem['PROPERTY_UPPERMATERIAL_VALUE'])) {
            echo '<param name="Материал верха">' . htmlspecialchars($arItem['PROPERTY_UPPERMATERIAL_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_LININGMATERIAL_VALUE'])) {
            echo '<param name="Материал подкладки">' . htmlspecialchars($arItem['PROPERTY_LININGMATERIAL_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_MATERIALSOLE_VALUE'])) {
            echo '<param name="Материал подошвы">' . htmlspecialchars($arItem['PROPERTY_MATERIALSOLE_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_COLORSFILTER_VALUE'])) {
            echo '<param name="Цвет">' . htmlspecialchars($arItem['PROPERTY_COLORSFILTER_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_SEASON_VALUE'])) {
            echo '<param name="Cезон">' . htmlspecialchars($arItem['PROPERTY_SEASON_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_BRAND_VALUE'])) {
            echo '<param name="Бренд">' . htmlspecialchars($arItem['PROPERTY_BRAND_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_COUNTRY_VALUE'])) {
            echo '<param name="Страна происхождения">' . htmlspecialchars($arItem['PROPERTY_COUNTRY_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_HEELHEIGHT_TYPE_VALUE'])) {
            echo '<param name="Высота каблука">' . htmlspecialchars($arItem['PROPERTY_HEELHEIGHT_TYPE_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_RHODEPRODUCT_VALUE'])) {
            echo '<param name="Пол">' . htmlspecialchars($arItem['PROPERTY_RHODEPRODUCT_VALUE'], ENT_XML1) . '</param>';
        }
        if (!empty($arItem['PROPERTY_ARTICLE_VALUE'])) {
            echo '<param name="Артикул">' . htmlspecialchars($arItem['PROPERTY_ARTICLE_VALUE'], ENT_XML1) . '</param>';
        }
        foreach ($arResult['UNIQUE_SHOWCASES'] as $showcaseKey => $showcase) {
            if (isset($showcase['OFFERS'][$offerId])) {
                echo '<stock id="' . $showcaseKey . '">';
                echo '<available>' . ($showcase['OFFERS'][$offerId]['AVAILABLE'] ? 'true' : 'false') . '</available>';
                echo '<price>' . $showcase['OFFERS'][$offerId]['PRICE'] . '</price>';
                if ($showcase['OFFERS'][$offerId]['OLD_PRICE']) {
                    echo '<oldprice>' . $showcase['OFFERS'][$offerId]['OLD_PRICE'] . '</oldprice>';
                }
                echo '</stock>';
            }
        }
        echo '</offer>';
    }
}
echo '</offers>';
echo '</shop>';
echo '</yml_catalog>';
