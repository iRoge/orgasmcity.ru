<?php

namespace Qsoft\Helpers;

class BonusSystem
{
    private $user;
    const BONUSES = [
        5000 => 1,
        10000 => 3,
        20000 => 6,
        30000 => 10,
    ];

    public function __construct($userID)
    {
        global $USER;
        $this->user = $USER->GetByID($userID)->Fetch();
    }

    public function getCurrentBonus()
    {

        return $this->user['UF_DISCOUNT'] ?? 0;
    }

    public function recalcUserBonus()
    {
        global $USER;
        $sum = $this->getUsersPaidOrdersSum();
        $actualBonus = 0;
        foreach (self::BONUSES as $minSum => $bonus) {
            if ($sum > $minSum) {
                $actualBonus = $bonus;
            } else {
                break;
            }
        }
        $this->user['UF_DISCOUNT'] = $actualBonus;
        $USER->Update($this->user['ID'], array("UF_DISCOUNT" => $actualBonus));
    }

    public function getUsersPaidOrdersSum()
    {
        $calculator = new \Bitrix\Sale\Discount\CumulativeCalculator($this->user['ID'],SITE_ID);
        return $calculator->calculate();
    }
}