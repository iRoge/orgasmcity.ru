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
    $price = \Qsoft\Helpers\PriceUtils::getPrice($offer['PROPERTY_BASEPRICE_VALUE'], $offer['PROPERTY_BASEWHOLEPRICE_VALUE'])['PRICE'];
    $needChange = false;
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
        $timestamp = rand(time() - 60 * 15, time());
        $dateTime = \Bitrix\Main\Type\DateTime::createFromTimestamp($timestamp);
        $props['LAST_BUY_DATE'] = $dateTime->format('d.m.Y H:i:s');
        CIBlockElement::SetPropertyValuesEx($offer['PROPERTY_CML2_LINK_VALUE'], IBLOCK_CATALOG, $props);
        echo 'Товару ' . $offer['PROPERTY_CML2_LINK_VALUE'] . ' проставлено время ' . $props['LAST_BUY_DATE'] . PHP_EOL;
    }
}
