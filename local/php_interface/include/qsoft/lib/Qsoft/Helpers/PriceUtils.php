<?php

namespace Qsoft\Helpers;

class PriceUtils
{
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