<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;

\Likee\Site\Helpers\Catalog::checkElementResult($arResult);
$arResult['ARTICLE'] = $arResult['PROPERTIES']['ARTICLE']['VALUE'];

// Кол-во товара на складе
$arSizes = array();
if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $value) {
        $arSizes[$value['ID']] = true;
        if ($value["PROPERTIES"]["SIZE"]["VALUE"]) {
            $arSizes[$value['ID']] = $value["PROPERTIES"]["SIZE"]["VALUE"];
        }
    }
}
//остатки по складам (только на резерв)
$arRests = $LOCATION->getRests(array_keys($arSizes), 2);
//остатки по типам
$arResult['RESTS'] = $LOCATION->getTypeSizes($arRests, $arSizes, 2);
//остатки по магазинам
$arStores = $LOCATION->getStoreSizes($arRests, $arSizes);
//флаг безразмерной номеннклатуры
$key = key($arResult['RESTS']['ALL']);
$arResult['SINGLE_SIZE'] = (count($arResult['RESTS']['ALL']) == 1 && $arResult['RESTS']['ALL'][$key] === true) ? $key : false;
//цена товара
$arResult['PRICE_PRODUCT'] = $LOCATION->getProductsPrices($arResult['ID']);
//название города
$arResult['CITY_NAME'] = $LOCATION->getName();
/** DETAIL PICTURE */
if (!empty($arResult['PREVIEW_PICTURE']) || !empty($arResult['DETAIL_PICTURE'])) {
    $arImage = $arResult['PREVIEW_PICTURE'] ?: $arResult['DETAIL_PICTURE'];
    $arImage['SRC'] = \Likee\Site\Helper::getResizePath($arResult['DETAIL_PICTURE'], 650, 650, true);
    $arResult['PICTURE'] = $arImage;
} else {
    $arResult['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(650, 650);
}
/** STORES */
if (!empty($arStores)) {
    $arStoresIds = array_keys($arStores);
    $res = \CCatalogStore::GetList(
        array(
            'TITLE' => 'ASC',
        ),
        array(
            'ID' => $arStoresIds,
        ),
        false,
        false,
        array(
            'ID',
            'TITLE',
            'PHONE',
            'ADDRESS',
            'SCHEDULE',
            'GPS_N',
            'GPS_S',
            'UF_METRO',
            'UF_PHONES',
            'UF_METRO_DADATA'
        )
    );

    $arResult['SHOPS'] = [];
    while ($arStore = $res->Fetch()) {
        if (empty($arStore['PHONE'])) {
            $arPhones = unserialize($arStore['UF_PHONES']);
            $arStore['PHONE'] = reset($arPhones);
        }

        $arStore['METRO'] = [];
        foreach (unserialize($arStore['UF_METRO']) as $iMetro) {
            $obEntity = \Likee\Site\Helpers\HL::getEntityClassByTableName('b_1c_dict_metro');
            if (!empty($obEntity) && is_object($obEntity)) {
                $sClass = $obEntity->getDataClass();
                $arMetro = $sClass::getRowById($iMetro);
                $arStore['METRO'][] = $arMetro['UF_NAME'];
            }
        }
        $arStore['METRO'] = implode(', ', $arStore['METRO']);
        $arStore['GPS_N'] = floatval(str_replace(',', '.', $arStore['GPS_N']));
        $arStore['GPS_S'] = floatval(str_replace(',', '.', $arStore['GPS_S']));
        $arResult['SHOPS'][] = array(
            'index' => intval($arStore['ID']),
            'title' => $arStore['TITLE'],
            'address' => $arStore['ADDRESS'],
            'subway' => $arStore['METRO'],
            'subway_trans' => Cutil::translit($arStore['METRO'], "ru", []),
            'worktime' => $arStore['SCHEDULE'],
            'phone' => $arStore['PHONE'],
            'coordinates' => [
                'lat' => $arStore['GPS_N'],
                'lng' => $arStore['GPS_S']
            ],
            'sizes' => $arStores[$arStore['ID']] ?: [],
            'metro' => json_decode($arStore['UF_METRO_DADATA']),
        );
    }
}
