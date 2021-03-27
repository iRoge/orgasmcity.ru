<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<yml_catalog date="<?= date('Y-m-d H:i') ?>">
    <shop>
        <name>Respect-Shoes</name>
        <company><?= $arResult['FEED_SETTINGS']['PROPERTY_LEGAL_ENTITY_VALUE'] ?></company>
        <currencies>
            <currency id="<?= (SITE_SERVER_NAME == 'respect-shoes.ru' ? 'RUB' : 'BYN') ?>" rate="1" />
        </currencies>
        <url>https://<?= SITE_SERVER_NAME ?>/</url>
        <categories>
        <? foreach ($arResult['CATEGORIES_IDS'] as $category) { ?>
            <category id="<?= $category['ID'] ?>" <?= (!empty($category['PARENT_ID']) ? ' parentId="' . $category['PARENT_ID'] . '"' : '') ?>><?= htmlspecialchars($category['NAME'], ENT_XML1) ?></category>
        <? } ?>
        </categories>
        <offers>
        <? foreach ($arResult['ITEMS'] as $arItem) {
            foreach ($arItem['OFFERS'] as $offerId => $offer) {
                $availability = (count($arItem['SIZES'])) ? 'true' : 'false'; ?>
                <offer id="<?= $offer['GUID_1C'] ?>" type="vendor.model" available="<?= $availability ?>" group_id="<?= $arItem['GUID_1C'] ?>">
                <url>https://<?= SITE_SERVER_NAME . $arItem['DETAIL_PAGE_URL'] ?></url>
                <price><?= $arItem['VIEW_PRICE'] ?></price>
                <? if ($arItem['SEGMENT'] != 'White' && !empty($arItem['OLD_PRICE']) && $arItem['OLD_PRICE'] > $arItem['VIEW_PRICE']) { ?>
                    <oldprice><?= $arItem['OLD_PRICE'] ?></oldprice>
                <? } ?>
                <currencyId><?= (SITE_SERVER_NAME == 'respect-shoes.ru' ? 'RUB' : 'BYN') ?></currencyId>
                <typePrefix><?= $arItem['PROPERTY_TYPEPRODUCT_VALUE'] ?></typePrefix>
                <categoryId><?= $arItem['IBLOCK_SECTION_ID'] ?></categoryId>
                <? if (!empty($arItem['PROPERTY_BRAND_VALUE'])) { ?>
                    <vendor><?= htmlspecialchars($arItem['PROPERTY_BRAND_VALUE'], ENT_XML1) ?></vendor>
                <? } ?>
                <model><?= htmlspecialchars($arItem['NAME'], ENT_XML1) ?></model>
                <picture>https://<?= SITE_SERVER_NAME . $arItem['DETAIL_PICTURE'] ?></picture>
                <? if (!empty($arItem['PROPERTY_KOD_1S_VALUE'])) { ?>
                    <param name="Код 1с"><?= $arItem['PROPERTY_KOD_1S_VALUE'] ?></param>
                <? }
                if (!empty($arItem['SEGMENT'])) { ?>
                    <param name="Сегмент цен"><?= $arItem['SEGMENT'] ?></param>
                <? } ?>
                    <param name="Доставка"><?= $offer['DELIVERY'] ? 'true' : 'false' ?></param>
                    <param name="Резерв"><?= $offer['RESERV'] ? 'true' : 'false' ?></param>
                </offer>
            <? }
        } ?>
        </offers>
    </shop>
</yml_catalog>
