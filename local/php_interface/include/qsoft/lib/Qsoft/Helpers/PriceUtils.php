<?php

namespace Qsoft\Helpers;

class PriceUtils
{
    public static function getPrice($basePrice, $rrcPrice)
    {
        $rrcPrice *= 0.85;
        $markupPercent = ($rrcPrice - $basePrice) * 100 / $basePrice;

        if ($markupPercent >= 180) {
            // Реальная скидка 31%
            $price = self::getTrickyPrice($rrcPrice, 15, 40);
        } elseif ($markupPercent < 180 && $markupPercent >= 160) {
            // Реальная скидка 23%
            $price = self::getTrickyPrice($rrcPrice, 10, 30);
        } elseif ($markupPercent < 160 && $markupPercent >= 105) {
            // Реальная скидка 16%
            $price = self::getTrickyPrice($rrcPrice, 5, 20);
        } elseif ($markupPercent < 105 && $markupPercent >= 95) {
            // Реальная скидка 0%
            $price = self::getTrickyPrice($rrcPrice, 0, 0);
        } else {
            return false;
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