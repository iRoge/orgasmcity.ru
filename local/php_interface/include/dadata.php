<?php

function getDadataStandartRegionNameFromLocation()
{
    global $LOCATION;
    $region = $LOCATION->getRegion();
    $city = $LOCATION->getName();
    $vocRegion = [
        'Республика Саха (Якутия)' => 'Саха /Якутия/',
        'Ханты-Мансийский автономный округ' => 'Ханты-Мансийский Автономный округ - Югра',
        'Чувашская Республика' => 'Чувашская республика',
        'Республика Северная Осетия-Алания' => 'Северная Осетия - Алания',
        'Кемеровская область' => 'Кемеровская область - Кузбасс',
     ];
    $exepCity = [
        'Севастополь' => 'Севастополь',
        'Инкерман' => 'Севастополь',
        'Санкт-Петербург' => 'Санкт-Петербург',
        'Зеленогорск' => 'Санкт-Петербург',
        'Кронштадт' => 'Санкт-Петербург',
        'Красное Село' => 'Санкт-Петербург',
        'Колпино' => 'Санкт-Петербург',
        'Сестрорецк' => 'Санкт-Петербург',
        'Павловск' => 'Санкт-Петербург',
        'Петергоф' => 'Санкт-Петербург',
        'Ломоносов' => 'Санкт-Петербург',
        'Пушкин' => 'Санкт-Петербург',
    ];
    $newMoscowCities = [
        'Московский',
        'Троицк',
        'Зеленоград',
        'Щербинка',
    ];
    if ($region == 'Московская область') {
        if (in_array($city, $newMoscowCities)) {
            return 'Москва';
        }
    }
    if (key_exists($region, $vocRegion)) {
        return $vocRegion[$region];
    }
    if (key_exists($city, $exepCity)) {
        return $exepCity[$city];
    }
    $region = str_ireplace('автономная область', '', $region);
    $region = str_ireplace('автономный округ', '', $region);
    $region = str_ireplace('Республика', '', $region);
    $region = str_ireplace('область', '', $region);
    $region = str_ireplace('край', '', $region);
    $region = trim($region);
    return $region;
};

function getDadataStandartCityNameFromLocation()
{
    global $LOCATION;

    $city = $LOCATION->getName();
    $vocCity = [
        'Железнодорожный' => 'Балашиха',
        'Юбилейный' => 'Королев',
        'Городской округ Черноголовка' => 'Черноголовка',
        'Снегири' => 'Истра',
        'Ожерелье' => 'Кашира',
        'Урус-Мартан' => '',
        'Алупка' => 'Ялта',
    ];
    if (key_exists($city, $vocCity)) {
        return $vocCity[$city];
    }
    return $city;
};

function getDadataStatus()
{
    $ch = curl_init('https://dadata.ru/api/v2/stat/daily');
    $headers = array('Authorization: Token ' . COption::GetOptionString('likee', 'dadata_token', ''), 'X-Secret: ' . COption::GetOptionString('likee', 'dadata_xsecret_token', ''));

    curl_setopt($ch, CURLOPT_URL, 'https://dadata.ru/api/v2/stat/daily');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result) {
        $arStatus = json_decode($result);
        if ((COption::GetOptionInt('likee', 'dadata_maxspd', '') - $arStatus->services->suggestions > 50) && !isset($arStatus->detail) && COption::GetOptionInt("likee", "dadata_active")) {
            return true;
        }
        return false;
    }
    return false;
}
