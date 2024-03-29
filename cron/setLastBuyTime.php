<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}

// Переменная обозначает, сколько раз за день запускается крон скрипт
// для определения с какой вероятностью проставлять, что его купили
$mul = 96;
$res = CIBlockElement::GetList(
    [
        "ID" => "ASC"
    ],
    [
        'IBLOCK_ID' => IBLOCK_OFFERS,
        'ACTIVE' => 'Y',
        '!PROPERTY_CML2_LINK' => false,
    ],
    false,
    false,
    ['ID', 'PROPERTY_CML2_LINK', 'PROPERTY_BASEPRICE', 'PROPERTY_BASEWHOLEPRICE']
);

$offers = [];
while ($offer = $res->GetNext()) {
    $offers[$offer['ID']] = $offer;
}

$offers = Functions::filterOffersByRests($offers);
foreach ($offers as $offer) {
    $price = \Qsoft\Helpers\PriceUtils::getCachedPriceForUser($offer['ID'])['PRICE'];
    $needChange = false;
    // Получается, что в среднем будет такой расклад:
    // До 500р товары покупают раз в 5 дней
    // До 7500р товары покупают раз в 7 дней
    // До 20000р товары покупают раз в 15 дней
    // Более 20000р товары покупают раз в 30 дней
    if ($price < 500) {
        if (rand(1, 5*$mul) == 1) {
            $needChange = true;
        }
    } elseif ($price < 7500) {
        if (rand(1, 7*$mul) == 1) {
            $needChange = true;
        }
    } elseif ($price < 20000) {
        if (rand(1, 15*$mul) == 1) {
            $needChange = true;
        }
    } else {
        if (rand(1, 30*$mul) == 1) {
            $needChange = true;
        }
    }

    if ($needChange) {
        $property = CIBlockElement::GetProperty(IBLOCK_CATALOG, $offer['PROPERTY_CML2_LINK_VALUE'], "sort", "asc", ["CODE" => "LAST_BUY_DATE"])->GetNext();
        $timestamp = rand(time() - 60 * 15, time());
        $dateTime = \Bitrix\Main\Type\DateTime::createFromTimestamp($timestamp);
        $props['LAST_BUY_DATE'] = $dateTime->format('d.m.Y H:i:s');
        if ($property['VALUE'] < $props['LAST_BUY_DATE']) {
            CIBlockElement::SetPropertyValuesEx($offer['PROPERTY_CML2_LINK_VALUE'], IBLOCK_CATALOG, $props);
            echo 'Товару ' . $offer['PROPERTY_CML2_LINK_VALUE'] . ' проставлено время ' . $props['LAST_BUY_DATE'] . PHP_EOL;
        }
    }
}
