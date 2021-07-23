<?php

namespace Qsoft\Helpers;

class PriceUtils
{
    public static function getPrice($basePrice, $rrcPrice)
    {
        $markupPercent = ($rrcPrice - $basePrice) * 100 / $rrcPrice;

        if ($markupPercent >= 80) {
            // Реальная скидка 16%
            $price = self::getTrickyPrice($rrcPrice, 20, 30);
        } elseif ($markupPercent < 80 && $markupPercent >= 70) {
            // Реальная скидка 13.75%
            $price = self::getTrickyPrice($rrcPrice, 15, 25);
        } elseif ($markupPercent < 70 && $markupPercent >= 55) {
            // Реальная скидка 12%
            $price = self::getTrickyPrice($rrcPrice, 10, 20);
        } elseif ($markupPercent < 55 && $markupPercent >= 45) {
            // Реальная скидка 7.3%
            $price = self::getTrickyPrice($rrcPrice, 3, 10);
        } else {
            $price = self::getTrickyPrice($rrcPrice, 0, 0);
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