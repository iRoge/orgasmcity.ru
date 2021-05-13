<?php

namespace Qsoft\Helpers;

class PriceUtils
{
    public static function getPrice($basePrice, $rrcPrice)
    {
        $markupPercent = ($rrcPrice - $basePrice) * 100 / $rrcPrice;

        if ($markupPercent >= 55) {
            // Реальная скидка 9%
            $price = self::getTrickyPrice($rrcPrice, 30, 30);
        } elseif ($markupPercent < 55 && $markupPercent >= 50) {
            // Реальная скидка 6.25%
            $price = self::getTrickyPrice($rrcPrice, 25, 25);
        } elseif ($markupPercent < 50 && $markupPercent >= 45) {
            // Реальная скидка 4%
            $price = self::getTrickyPrice($rrcPrice, 20, 20);
        } elseif ($markupPercent < 45 && $markupPercent >= 40) {
            // Реальная скидка 2.8%
            $price = self::getTrickyPrice($rrcPrice, 8, 10);
        } else {
            $price = self::getTrickyPrice($rrcPrice, 0, 0);
        }

        return $price;
    }

    private static function getTrickyPrice($rrcPrice, $rrcMarkup, $discount) {
        $price = [];
        $price['OLD_PRICE'] = ceil(($rrcPrice+($rrcPrice * $rrcMarkup/100))/10)*10;
        $price['PRICE'] = $price['OLD_PRICE'] - ($price['OLD_PRICE'] * $discount/100);
        $price['PRICE'] = ceil(($price['PRICE']-6)/10)*10;
        $price['DISCOUNT'] = $discount;

        return $price;
    }
}