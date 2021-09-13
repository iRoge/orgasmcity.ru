<?php

namespace Qsoft\Helpers;

use CIBlockElement;
use CPHPCache;
use Functions;

class PriceUtils
{
    static private array $pricesCache;


    public static function getCachedPriceForUser($offersIds)
    {
        $resultArray = [];
        if (empty(self::$pricesCache)) {
            global $CACHE_MANAGER;
            $cache = new CPHPCache();

            if ($cache->InitCache(86400, 'pricesCache', '/')) {
                self::$pricesCache = $cache->getVars();
            }
            if (empty($aMenuLinksNew)) {
                $cache->StartDataCache();
                $CACHE_MANAGER->StartTagCache('/');
                $CACHE_MANAGER->RegisterTag("catalogAll");
                $CACHE_MANAGER->RegisterTag("pricesAll");

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
                    ];
                }

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
        foreach ($offersIds as $offerId) {
            if (isset(self::$pricesCache[$offerId]) && self::$pricesCache[$offerId]['PRICE']) {
                $resultArray[$offerId] = self::$pricesCache[$offerId];
                $resultArray['DISCOUNT_WITHOUT_BONUS'] = self::$pricesCache[$offerId]['DISCOUNT'];
                $resultArray[$offerId]['DISCOUNT'] = self::$pricesCache[$offerId]['DISCOUNT'] + $userDiscount;
            } else {
                $resultArray[$offerId] = null;
            }
        }
        return $resultArray;
    }

    public static function recalcPrices()
    {
        $offers = Functions::getAllOffers();
        $offersByProductID = [];
        foreach ($offers as $offer) {
            $offersByProductID[$offer['PROPERTY_CML2_LINK_VALUE']][] = $offer['ID'];
        }
        // TODO: 1) Достать все товары со свойствами
        // TODO: 2) Достать все группировки с IS_ACTION = Да
        // TODO: 3) В цикле по каждой акции с циклом по товарам определить подходящие к акции товары
        // TODO:  и проставить скидку с новой ценной по каждому ассортименту в отдельный массив, если эта скидка минимальная.
        // TODO: Если скидки нет, то проставить цену по старой логике, только без скидки
        // TODO: 4) После формирования массива с новыми ценами по ассортиментам, записать в базу
    }

    public static function getPrice($basePrice, $rrcPrice)
    {
        $markupPercent = ($rrcPrice - $basePrice) * 100 / $basePrice;

        if ($rrcPrice < 1000) {
            // Если рекомендованная розничная меньше 1000 рублей
            if ($markupPercent >= 135) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 20, 30);
            } else {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 0, 0);
            }
        } elseif ($rrcPrice > 40000) {
            // Если рекомендованная розничная больше 40000 рублей
            if ($markupPercent >= 40) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 20, 30);
            } else {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 0, 0);
            }
        } else {
            // Если рекомендованная розничная между 1000 и 40000 рублей
            if ($markupPercent >= 150) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 20, 40);
            } elseif ($markupPercent < 150 && $markupPercent >= 135) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 15, 35);
            } elseif ($markupPercent < 135 && $markupPercent >= 120) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, 15, 25);
            } elseif ($markupPercent < 120 && $markupPercent >= 85) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, -10, 0);
            } elseif ($markupPercent < 85 && $markupPercent >= 65) {
                // Реальная скидка
                $price = self::getTrickyPrice($rrcPrice, -5, 0);
            } elseif ($markupPercent < 65) {
                $price = self::getTrickyPrice($rrcPrice, 0, 0);
            }
        }

        $price['WHOLEPRICE'] = $basePrice;
        return $price;
    }

    private static function getTrickyPrice($rrcPrice, $rrcMarkup, $discount)
    {
        global $USER;
        // Достаем персональную скидку
        $userDiscount = 0;
        if ($USER->IsAuthorized()) {
            $bonusSystemHelper = new BonusSystem($USER->GetID());
            $userDiscount = $bonusSystemHelper->getCurrentBonus();
            $discount += $userDiscount;
        }
        $price = [];
        $price['OLD_PRICE'] = ceil(($rrcPrice+($rrcPrice * $rrcMarkup/100))/10)*10;
        $price['PRICE'] = $price['OLD_PRICE'] - ($price['OLD_PRICE'] * $discount/100);
        $price['PRICE'] = ceil(($price['PRICE']-6)/10)*10;
        $price['DISCOUNT'] = $discount;
        $price['DISCOUNT_WITHOUT_BONUS'] = $discount - $userDiscount;

        return $price;
    }
}