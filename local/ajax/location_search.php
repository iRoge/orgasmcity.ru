<?php

use Bitrix\Sale\Location\LocationTable;
use Qsoft\Helpers\TextHelper;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

// удаляем стоп слова
$search = trim(preg_replace(array('/ город[^ ]*/iu', '/ г /iu', '/\s\s+/'), ' ', mb_strtolower(" ".$_GET["q"]." ")));
// проверяем не верную раскладку
$searchMap = TextHelper::restoreText($search);
// удаляем спец символы
$search = trim(preg_replace(array('/\W+/iu', '/\s\s+/'), ' ', $search));
$searchMap = trim(preg_replace(array('/\W+/iu', '/\s\s+/'), ' ', $searchMap));
// делим по пробелам
$search = explode(" ", $search);
$searchMap = explode(" ", $searchMap);

// Получаем города вместе с их регионами
$cache = new CPHPCache();
$cities = [];
if ($cache->InitCache(31536000, 'geo_cities')) {
    $cities = $cache->GetVars()['cities'];
} elseif ($cache->StartDataCache()) {
    $locationList = LocationTable::getList([
        'filter' => [
            '=TYPE_ID' => 5,
        ],
        'select' => [
            'ID',
            'CODE',
            'CITY_NAME' => 'NAME.NAME',
            'PARENT_NAME' => 'PARENT.NAME.NAME',
            'PARENT_TYPE_ID' => 'PARENT.TYPE_ID',
        ],
    ]);
    $cities = [];
    while ($location = $locationList->fetch()) {
        $cities[$location['ID']] = [
            'id' => $location['ID'],
            'code' => $location['CODE'],
            'name' => $location['CITY_NAME'],
        ];
        if ($location['PARENT_TYPE_ID'] == 3) {
            $cities[$location['ID']]['region'] = $location['PARENT_NAME'];
            continue;
        }
    }

    $cache->EndDataCache(['cities' => $cities]);
}

// Ищем города, названия которых содержат хотя бы одно слово из поискового запроса
$result = [];
$searchWords = array_unique(array_merge($search, $searchMap));
foreach ($cities as $city) {
    foreach ($searchWords as $word) {
        if (mb_stristr($city['name'], $word) !== false) {
            $city['text'] = $city['region'] ? $city['name'] . ', ' . $city['region'] : $city['name'];
            $result[] = $city;
            continue 2;
        }
    }
}

echo json_encode(['results' => $result], JSON_UNESCAPED_UNICODE);
