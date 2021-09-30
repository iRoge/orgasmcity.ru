<?php

namespace Qsoft\Helpers;

use CIBlockElement;
use CPHPCache;
use Functions;

class PriceUtils
{
    static private array $pricesCache;

    static private array $pricesExceptions = [
        596712 => [
            'OLD_PRICE' => 430,
            'PRICE' => 430,
            'DISCOUNT' => 0,
            'WHOLEPRICE' => null,
            'DISCOUNT_DATE_TO' => '15.10.2021',
        ]
    ];


    public static function getCachedPriceForUser($offerId)
    {
        if (empty(self::$pricesCache)) {
            global $CACHE_MANAGER;
            $cache = new CPHPCache();

            if ($cache->InitCache(86400, 'pricesCache', '/prices')) {
                self::$pricesCache = $cache->getVars();
            }
            if (empty($aMenuLinksNew)) {
                $arFilter = [
                    "IBLOCK_ID" => IBLOCK_OFFERS,
                    "ACTIVE" => "Y",
                ];

                $arSelect = [
                    "ID",
                    "IBLOCK_ID",
                    "PROPERTY_CUSTOM_PRICE",
                    "PROPERTY_CUSTOM_OLD_PRICE",
                    "PROPERTY_CUSTOM_DISCOUNT",
                    "PROPERTY_CUSTOM_DISCOUNT_DATE_TO",
                    "PROPERTY_BASEWHOLEPRICE"
                ];

                $resOffers = CIBlockElement::GetList(
                    ["SORT" => "ASC"],
                    $arFilter,
                    false,
                    false,
                    $arSelect,
                );

                while ($offer = $resOffers->Fetch()) {
                    self::$pricesCache[$offer['ID']] = [
                        'OLD_PRICE' => $offer['PROPERTY_CUSTOM_OLD_PRICE_VALUE'],
                        'PRICE' => $offer['PROPERTY_CUSTOM_PRICE_VALUE'],
                        'DISCOUNT' => $offer['PROPERTY_CUSTOM_DISCOUNT_VALUE'],
                        'WHOLEPRICE' => $offer['PROPERTY_BASEWHOLEPRICE_VALUE'],
                        'DISCOUNT_DATE_TO' => $offer['PROPERTY_CUSTOM_DISCOUNT_DATE_TO_VALUE'],
                    ];
                }
                $cache->StartDataCache();
                $CACHE_MANAGER->StartTagCache('/prices');
                $CACHE_MANAGER->RegisterTag("catalogAll");
                $CACHE_MANAGER->RegisterTag("pricesAll");
                $cache->EndDataCache(self::$pricesCache);
                $CACHE_MANAGER->EndTagCache();
            }
        }
        global $USER;
        // Достаем персональную скидку
        $userDiscount = 0;
        if ($USER->IsAuthorized()) {
            $bonusSystemHelper = new BonusSystem($USER->GetID());
            $userDiscount = $bonusSystemHelper->getCurrentBonus();
        }

        $exception = null;
        if (self::$pricesExceptions[$offerId]) {
            $exception = self::$pricesExceptions[$offerId];
        }

        if (isset(self::$pricesCache[$offerId]) && self::$pricesCache[$offerId]['PRICE']) {
            $resultArray = self::$pricesCache[$offerId];
            $resultArray['OLD_PRICE'] = $exception && $exception['OLD_PRICE'] !== null ? $exception['OLD_PRICE'] : self::$pricesCache[$offerId]['OLD_PRICE'];
            $resultArray['PRICE'] = $exception && $exception['PRICE'] !== null ? $exception['PRICE'] : self::calculatePrice($resultArray['PRICE'], $userDiscount);
            $resultArray['WHOLEPRICE'] = $exception && $exception['WHOLEPRICE'] !== null ? $exception['WHOLEPRICE'] : self::$pricesCache[$offerId]['WHOLEPRICE'];
            $resultArray['DISCOUNT_WITHOUT_BONUS'] = $exception && $exception['DISCOUNT'] !== null ? $exception['DISCOUNT'] : self::$pricesCache[$offerId]['DISCOUNT'];
            $resultArray['DISCOUNT'] = $exception && $exception['DISCOUNT'] !== null ? $exception['DISCOUNT'] : self::$pricesCache[$offerId]['DISCOUNT'] + $userDiscount;
        } else {
            $resultArray = null;
        }

        return $resultArray;
    }

    public static function recalcPrices()
    {
        $offers = Functions::getAllOffers();
        $offersByProductID = [];
        foreach ($offers as $offer) {
            $offersByProductID[$offer['PROPERTY_CML2_LINK_VALUE']][] = $offer;
        }
        $products = Functions::getAllProducts();
        $assortmentPrices = [];
        $res = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_GROUPS,
                'ACTIVE' => 'Y',
                'PROPERTY_IS_ACTION' => true,
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'DETAIL_TEXT',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'DATE_ACTIVE_TO',
                'NAME',
                'PROPERTY_BESTSELLER',
                'PROPERTY_NEW',
                'PROPERTY_LENGTH_FROM',
                'PROPERTY_LENGTH_TO',
                'PROPERTY_DIAMETER_FROM',
                'PROPERTY_DIAMETER_TO',
                'PROPERTY_VENDOR',
                'PROPERTY_VIBRATION',
                'PROPERTY_YEAR',
                'PROPERTY_SECTION',
                'PROPERTY_VOLUME',
                'PROPERTY_PRODUCT',
                'PROPERTY_MATERIAL',
                'PROPERTY_PRICE_FROM',
                'PROPERTY_PRICE_TO',
                'PROPERTY_DISCOUNT_50',
                'PROPERTY_DISCOUNT_50_75',
                'PROPERTY_DISCOUNT_75_100',
                'PROPERTY_DISCOUNT_100_125',
                'PROPERTY_DISCOUNT_125',
            ]
        );
        while ($action = $res->GetNext()) {
            foreach ($products as $product) {
                if (
                    (!empty($action['PROPERTY_BESTSELLER_VALUE']) && !in_array($product['PROPERTY_BESTSELLER_VALUE'], $action['PROPERTY_BESTSELLER_VALUE']))
                    || (!empty($action['PROPERTY_NEW_VALUE']) && !in_array($product['PROPERTY_NEW_VALUE'], $action['PROPERTY_NEW_VALUE']))
                    || (!empty($action['PROPERTY_LENGTH_FROM_VALUE']) && $product['PROPERTY_LENGTH_VALUE'] < $action['PROPERTY_LENGTH_FROM_VALUE'])
                    || (!empty($action['PROPERTY_LENGTH_TO_VALUE']) && $product['PROPERTY_LENGTH_VALUE'] > $action['PROPERTY_LENGTH_TO_VALUE'])
                    || (!empty($action['PROPERTY_DIAMETER_FROM_VALUE']) && $product['PROPERTY_DIAMETER_VALUE'] < $action['PROPERTY_DIAMETER_FROM_VALUE'])
                    || (!empty($action['PROPERTY_DIAMETER_TO_VALUE']) && $product['PROPERTY_DIAMETER_VALUE'] > $action['PROPERTY_DIAMETER_TO_VALUE'])
                    || (!empty($action['PROPERTY_VENDOR_VALUE']) && !in_array($product['PROPERTY_VENDOR_VALUE'], $action['PROPERTY_VENDOR_VALUE']))
                    || (!empty($action['PROPERTY_VIBRATION_VALUE']) && !in_array($product['PROPERTY_VIBRATION_VALUE'], $action['PROPERTY_VIBRATION_VALUE']))
                    || (!empty($action['PROPERTY_YEAR_VALUE']) && !in_array($product['PROPERTY_YEAR_VALUE'], $action['PROPERTY_YEAR_VALUE']))
                    || (!empty($action['PROPERTY_PRODUCT_VALUE']) && !in_array($product['XML_ID'], $action['PROPERTY_PRODUCT_VALUE']))
                    || (!empty($action['PROPERTY_SECTION_VALUE']) && !in_array($product['IBLOCK_SECTION_ID'], $action['PROPERTY_SECTION_VALUE']))
                    || (!empty($action['PROPERTY_MATERIAL_VALUE']) && !in_array($product['PROPERTY_MATERIAL_VALUE'], $action['PROPERTY_MATERIAL_VALUE']))
                ) {
                    continue;
                }
                foreach ($offersByProductID[$product['ID']] as $assortment) {
                    if (
                        !empty($action['PROPERTY_PRICE_FROM_VALUE']) && $assortment["PROPERTY_BASEPRICE_VALUE"] < $action['PROPERTY_PRICE_FROM_VALUE']
                        || !empty($action['PROPERTY_PRICE_TO_VALUE']) && $assortment["PROPERTY_BASEPRICE_VALUE"] > $action['PROPERTY_PRICE_TO_VALUE']
                    ) {
                        continue;
                    }
                    $basePrice = $assortment["PROPERTY_BASEPRICE_VALUE"];
                    $wholePrice = $assortment["PROPERTY_BASEWHOLEPRICE_VALUE"];
                    $oldPrice = self::getReducedPrice($wholePrice, $basePrice);
                    if (!$oldPrice) {
                        continue;
                    }
                    $markupPercent = ($oldPrice - $wholePrice) * 100 / $wholePrice;

                    if ($markupPercent < 50) {
                        $discount = $action['PROPERTY_DISCOUNT_50_VALUE'];
                    } elseif ($markupPercent < 75) {
                        $discount = $action['PROPERTY_DISCOUNT_50_75_VALUE'];
                    } elseif ($markupPercent < 100) {
                        $discount = $action['PROPERTY_DISCOUNT_75_100_VALUE'];
                    } elseif ($markupPercent < 125) {
                        $discount = $action['PROPERTY_DISCOUNT_100_125_VALUE'];
                    } else {
                        $discount = $action['PROPERTY_DISCOUNT_125_VALUE'];
                    }

                    if (
                        empty($assortmentPrices[$assortment['ID']])
                        || $assortmentPrices[$assortment['ID']]['DISCOUNT'] < $discount
                    ) {
                        $price = self::calculatePrice($oldPrice, $discount);
                        $assortmentPrices[$assortment['ID']] = [
                            'DISCOUNT' => $discount,
                            'PRICE' => $price,
                            'OLD_PRICE' => $oldPrice > $price ? $oldPrice : $price,
                            'CUSTOM_DISCOUNT_DATE_TO' => $action['DATE_ACTIVE_TO']
                        ];
                    }
                }
            }
        }

        foreach ($offers as $id => $offer) {
            if (!empty($assortmentPrices[$id])) {
                $assortmentPrices[$id]['WHOLEPRICE'] = $offer['PROPERTY_BASEWHOLEPRICE_VALUE'];
            } else {
                $price = self::getReducedPrice($offer["PROPERTY_BASEWHOLEPRICE_VALUE"], $offer["PROPERTY_BASEPRICE_VALUE"]);
                $assortmentPrices[$id] = [
                    'DISCOUNT' => 0,
                    'PRICE' => $price,
                    'OLD_PRICE' => $price,
                    'WHOLEPRICE' => $offer['PROPERTY_BASEWHOLEPRICE_VALUE'],
                    'CUSTOM_DISCOUNT_DATE_TO' => ''
                ];
            }
        }

        foreach ($assortmentPrices as $offerId => $arPrice) {
            $props = [];
            if (isset($arPrice['DISCOUNT'])) {
                $props['CUSTOM_DISCOUNT'] = $arPrice['DISCOUNT'];
            }
            if (isset($arPrice['PRICE'])) {
                $props['CUSTOM_PRICE'] = $arPrice['PRICE'];
            }
            if (isset($arPrice['OLD_PRICE'])) {
                $props['CUSTOM_OLD_PRICE'] = $arPrice['OLD_PRICE'];
            }
            if (isset($arPrice['WHOLEPRICE'])) {
                $props['BASEWHOLEPRICE'] = $arPrice['WHOLEPRICE'];
            }
            if (isset($arPrice['CUSTOM_DISCOUNT_DATE_TO'])) {
                $props['CUSTOM_DISCOUNT_DATE_TO'] = $arPrice['CUSTOM_DISCOUNT_DATE_TO'];
            }

            CIBlockElement::SetPropertyValuesEx($offerId, IBLOCK_OFFERS, $props);
        }
    }

    public static function getReducedPrice($basePrice, $rrcPrice)
    {
        $markupPercent = ($rrcPrice - $basePrice) * 100 / $basePrice;

        $price = null;
        if ($markupPercent >= 150) {
            $price = self::calculatePrice($rrcPrice, 25);
        } elseif ($markupPercent >= 135) {
            $price = self::calculatePrice($rrcPrice, 20);
        } elseif ($markupPercent >= 120) {
            $price = self::calculatePrice($rrcPrice, 15);
        } elseif ($markupPercent >= 85) {
            $price = self::calculatePrice($rrcPrice, 10);
        } elseif ($markupPercent >= 65) {
            $price = self::calculatePrice($rrcPrice, 5);
        } else {
            $price = self::calculatePrice($rrcPrice, 0);
        }

        return $price;
    }

    private static function calculatePrice($price, $reductionPercent)
    {
        return ceil(($price - ($price * $reductionPercent / 100) - 6) / 10) * 10;
    }
}