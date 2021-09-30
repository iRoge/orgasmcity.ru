<?php

namespace Qsoft\Events;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use \CIBlockElement;
use \Bitrix\Main\Context;
use \Bitrix\Main\Event;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\Fuser;
use Bitrix\Catalog\StoreTable;
use \Likee\Exchange\Tables\ReserveStorageTable;
use Bitrix\Sale\Order as Sale;
use Qsoft\Helpers\PriceUtils;
use \Qsoft\Sailplay\Tasks\TaskManager;

class Order
{
    public static function OnGetOptimalPriceResultHandler(&$arFields)
    {
        // если нет ID ТП, то ничего не делаем
        if (!$arFields["PRODUCT_ID"]) {
            return;
        }
        $arOffer = \CIBlockElement::GetList(
            [],
            [
                "ID" => $arFields["PRODUCT_ID"],
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "nTopCount" => 1,
            ],
            [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                'PROPERTY_BASEWHOLEPRICE',
                'PROPERTY_BASEPRICE',
            ]
        )->Fetch();
        // если нет ID товара, то ничего не делаем
        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return;
        }
        $arPrice = PriceUtils::getCachedPriceForUser($arOffer['ID']);
        if (!$arPrice) {
            $arFields = [];
            return;
        }
        $arFields['PRICE']['PRICE'] = $arPrice['PRICE'];
        $arFields['DISCOUNT_PRICE'] = $arPrice['PRICE'];
        $arFields['RESULT_PRICE']['BASE_PRICE'] = $arPrice['PRICE'];
        $arFields['RESULT_PRICE']['DISCOUNT_PRICE'] = $arPrice['PRICE'];
    }

    protected static function log($message)
    {
        orgasm_logger($message, "orderEvents.txt");
    }
}
