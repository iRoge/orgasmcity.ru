<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;?>
<?echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">';
echo '<channel>';
echo '<title>' . Loc::getMessage('TITLE') . '</title>';
echo '<link>' . Loc::getMessage('URL') . '</link>';
echo '<description>' . Loc::getMessage('DESCRIPTION') . '</description>';
foreach ($arResult['ITEMS'] as $arItem) {
    foreach ($arItem['OFFERS'] as $offerId => $offer) {
        foreach ($arResult['UNIQUE_SHOWCASES'] as $showcaseKey => $showcase) {
            if (isset($showcase['OFFERS'][$offerId])) {
                if (empty($showcase['OFFERS'][$offerId]['PRICE']) && empty($showcase['OFFERS'][$offerId]['OLD_PRICE'])) {
                    continue;
                }

                echo '<item>';
                echo '<g:id>' . sprintf('%s-%s', $offerId, $showcaseKey) . '</g:id>';
                echo '<g:title>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</g:title>';
                echo '<g:description>' . htmlspecialchars($arItem['NAME'], ENT_XML1) . '</g:description>';
                echo '<g:google_product_category>' . $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['ALTERNATIVE_NAME'] . '</g:google_product_category>';
                echo '<g:product_type>' . $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['PATH'] . '</g:product_type>';
                echo '<g:product_type_key>' . $arItem['IBLOCK_SECTION_ID'] . '</g:product_type_key>';
                echo '<g:item_group_id>' . sprintf('%s-%s', $arItem['ID'], $showcaseKey) . '</g:item_group_id>';
                echo '<g:region>' . $showcaseKey . '</g:region>';
                echo '<g:link>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PAGE_URL'] . '</g:link>';
                echo '<g:image_link>' . 'https://' . SITE_SERVER_NAME . $arItem['DETAIL_PICTURE'] . '</g:image_link>';
                echo '<g:availability>in stock</g:availability>';
                echo '<g:adult>no</g:adult>';
                echo '<g:condition>new</g:condition>';
                if (!empty($arItem['PROPERTY_BRAND_VALUE'])) {
                    echo '<g:brand>' . htmlspecialchars($arItem['PROPERTY_BRAND_VALUE'], ENT_XML1) . '</g:brand>';
                }

                if (!empty($arItem['PROPERTY_COLORSFILTER_VALUE'])) {
                    echo '<g:color>' . htmlspecialchars(
                        $arItem['PROPERTY_COLORSFILTER_VALUE'],
                        ENT_XML1
                    ) . '</g:color>';
                }

                if (!empty($arItem['PROPERTY_RHODEPRODUCT_VALUE'])) {
                    echo '<g:gender>' . htmlspecialchars(
                        $arItem['PROPERTY_RHODEPRODUCT_VALUE'],
                        ENT_XML1
                    ) . '</g:gender>';
                }

                if (!empty($arItem['PROPERTY_UPPERMATERIAL_VALUE'])) {
                    echo '<g:material>' . htmlspecialchars(
                        $arItem['PROPERTY_UPPERMATERIAL_VALUE'],
                        ENT_XML1
                    ) . '</g:material>';
                }

                if (!empty($offer['SIZE'])) {
                    echo '<g:size>' . htmlspecialchars($offer['SIZE'], ENT_XML1) . '</g:size>';
                }

                if (!empty($arItem['FILTER_STRING'])) {
                    echo '<g:filters>' . $arItem['FILTER_STRING'] . '</g:filters>';
                }

                if ($showcase['OFFERS'][$offerId]['SEGMENT'] == 'White' || $showcase['OFFERS'][$offerId]['SEGMENT']['PRICESEGMENT'] == 'Yellow') {
                    echo '<g:price>' . $showcase['OFFERS'][$offerId]['PRICE'] . '</g:price>';
                } else {
                    echo '<g:sale_price>' . $showcase['OFFERS'][$offerId]['PRICE'] . '</g:sale_price>';
                    if (!empty($showcase['OFFERS'][$offerId]['OLD_PRICE'])) {
                        echo '<g:price>' . $showcase['OFFERS'][$offerId]['OLD_PRICE'] . '</g:price>';
                    }
                }

                echo '</item>';
            }
        }
    }
}
echo '</channel>';
echo '</rss>';
