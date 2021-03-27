<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Loader;
use Likee\Location\Location;
use Likee\Site\Tables\City2BranchTable;
use Likee\Exchange\Tables\BranchProductPricesTable;

class Branch
{
    public static function getProductPrice($productId)
    {
        $branchId = self::getBranchId();

        if ($branchId && Loader::includeModule('likee.exchange')) {
            $rsProductPrice = BranchProductPricesTable::getList([
                'filter' => [
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                ]
            ]);
            return $rsProductPrice->fetch();
        }

        return false;
    }

    public static function getBranchId()
    {
        static $branchId = null;

        if (is_null($branchId)) {
            $branchId = false;

            if (Loader::includeModule('likee.location')) {
                $arLocation = Location::getCurrent();
                
                $rsBranchCity = City2BranchTable::getList([
                    'filter' => [
                        'name' => $arLocation['CITY_NAME']
                    ]
                ]);
                while ($arBranchCity = $rsBranchCity->fetch()) {
                    $branchId = (int) $arBranchCity['branch_id'];
                }
                unset($rsBranchCity, $arBranchCity, $arLocation);
            }
        }

        return $branchId;
    }
}