<?
foreach ($arResult['ITEMS'] as $arItem) {
    foreach ($arItem['PROPERTIES']['STYLE_FRANCHISE']['VALUE_XML_ID'] as $styleFranchise) {
        $arResult[$styleFranchise][] = $arItem['DETAIL_PICTURE']['SRC'];
    }
}
