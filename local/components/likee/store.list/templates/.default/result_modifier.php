<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

global $APPLICATION;

\Bitrix\Main\Loader::includeModule('highloadblock');

$arShopsJson = [];

foreach ($arResult['STORES'] as &$arStore) {
    if (!empty($arStore['PICTURE']) && is_array($arStore['PICTURE'])) {
        $arStore['PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arStore['PICTURE'], 470, 470, true);
    } else {
        $arStore['PICTURE']['SRC'] = \Likee\Site\Helper::getEmptyImg(470, 470);
    }
}
unset($arStore);

//список доступных пунктов метро для фильтрации
$arResult['STATIONS'] = [];
foreach ($arResult['STORES'] as $arStore) {
    foreach ($arStore['METRO'] as $arMetro) {
        if (!array_key_exists($arMetro, $arResult['STATIONS'])) {
            $arResult['STATIONS'][$arMetro] = $arMetro;
        }
    }
}

ksort($arResult['STATIONS']);

$arResult['JSON_SHOPS'] = [];
/*foreach ($arResult['STORES'] as $arStore) {


    $arMetro = [];
    foreach ($arStore['METRO'] as $iMetro) {
        $arMetro[]=$iMetro['NAME'];
    }
    $arStore['METRO'] = implode(', ', $arMetro);


    $arResult['JSON_SHOPS'][] = [
        'title' => $arStore['TITLE'],
        'address' => $arStore['ADDRESS'],
        'distance' => $iDistance,
        'subway' => $arStore['METRO'],
        'subway_trans' => Cutil::translit($arStore['METRO'],"ru",[]),
        'worktime' => $arStore['SCHEDULE'],
        'phone' => $arStore['PHONE'],
        'coordinates' => [
            'lat' => $arStore['GPS_N'],
            'lng' => $arStore['GPS_S']
        ],
        'index' => intval($arStore['ID'])
    ];
}*/
