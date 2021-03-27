<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Loader;
use Likee\Site\Helpers\Branch;

/**
 * Класс для работы с каталогоми. Содержит методы для фильтрации и валидации.
 *
 * @package Likee\Site\Helpers
 */
class Price
{
    const IBLOCK_CATALOG_ID = 16;
    const IBLOCK_OFFERS_ID = 17;

    const CATALOG_PRICE_ID = 7;
    const CATALOG_OLD_PRICE_ID = 8;

    public static function getOptimalPrice($productId)
    {
        if (!Loader::includeModule('likee.site')) {
            return true;
        }

        $priceParams = self::getProductPriceParamsByOfferId($productId);
        if ($priceParams) {
            return [
                'PRODUCT_ID' => $productId,
                'PRICE' => [
                    'ID' => $productId,
                    'CATALOG_GROUP_ID' => self::CATALOG_PRICE_ID,
                    'ELEMENT_IBLOCK_ID' => self::IBLOCK_OFFERS_ID,
                    'PRICE' => $priceParams['price'],
                    'CURRENCY' => 'RUB',
                    'VAT_INCLUDED' => 'Y',
                ],
            ];
        }
    }
    public static function getOptimalPriceResult(&$arResult)
    {
        if (!Loader::includeModule('likee.site')) {
            return true;
        }

        $priceParams = self::getProductPriceParamsByOfferId($arResult['PRODUCT_ID']);
        if ($priceParams) {
            $arResult['PRICE']['PRICE'] = $priceParams['price'];
            $arResult['DISCOUNT_PRICE'] = $priceParams['price'];
            $arResult['RESULT_PRICE']['BASE_PRICE'] = $priceParams['price'];
            $arResult['RESULT_PRICE']['DISCOUNT_PRICE'] = $priceParams['price'];
        }
    }
    public static function getItemPrices(&$arItem)
    {
        static $priceParamName = 'CATALOG_PRICE_'.self::CATALOG_PRICE_ID;

        if (!Loader::includeModule('likee.site')) {
            return;
        }
        if (self::IBLOCK_OFFERS_ID != $arItem['IBLOCK_ID'] || empty($arItem[$priceParamName]) || empty($arItem['PROPERTIES']['CML2_LINK']['VALUE'])) {
            return;
        }

        $priceParams = self::getProductPriceParams($arItem['PROPERTIES']['CML2_LINK']['VALUE']);
        if ($priceParams) {
            $arItem[$priceParamName] = $arItem['~'.$priceParamName] = $priceParams['price'];
        }
    }
    public static function onElementLoadPrices(&$prices)
    {
        if (!Loader::includeModule('likee.site')) {
            return;
        }

        foreach ($prices as $productId => $data) {
            if (!empty($data['SIMPLE'][self::CATALOG_PRICE_ID])) {
                $priceParams = self::getProductPriceParamsByOfferId($productId);
                if ($priceParams) {
                    $prices[$productId]['SIMPLE'][self::CATALOG_PRICE_ID]['PRICE'] = $priceParams['price'];
                }
            }
        }
    }

    public static function calculateSegmentPrice($price, $priceSegmentId, $maxDiscBp)
    {
        $price = (int) $price;
        $priceSegmentId = trim(strtolower($priceSegmentId));
        $maxDiscBp = (int) $maxDiscBp;

        if ('white' == $priceSegmentId && 0 < $maxDiscBp) {
            $price = intval($price - ($price * $maxDiscBp / 100));
        }

        return $price;
    }

    public static function getProductPriceParams($productId, $useSegmentLogic = true)
    {
        static $productsPriceParams = [];

        if (! isset($productsPriceParams[$productId]) && Loader::includeModule('catalog')) {
            $priceParams = Branch::getProductPrice($productId);

            if (! $priceParams) {
                $priceParams = [
                    'product_id' => $productId,
                    'price' => 0,
                    'price1' => 0,
                    'price_segment_id' => 'Red',
                    'max_disc_bp' => 0
                ];

                // характеристики
                $rsProduct = \CIBlockElement::GetList(
                    [],
                    [
                        'ID' => $productId,
                        'IBLOCK_ID' => self::IBLOCK_CATALOG_ID,
                    ],
                    false,
                    false,
                    ['ID', 'PROPERTY_PRICESEGMENTID', 'PROPERTY_MAXDISCBP']
                );
    
                while ($arFields = $rsProduct->GetNext()) {
                    $priceParams['price_segment_id'] = $arFields['PROPERTY_PRICESEGMENTID_VALUE'];
                    $priceParams['max_disc_bp'] = $arFields['PROPERTY_MAXDISCBP_VALUE'];
                }
                unset($rsProduct, $arFields);

                $offers = self::getProductOffersIds($productId);
                if ($offers) {
                    // цены
                    $rsPrice = \CPrice::GetList(
                        [],
                        [
                            "PRODUCT_ID" => $offers[0],
                            'CATALOG_GROUP_ID' => [
                                self::CATALOG_PRICE_ID,
                                self::CATALOG_OLD_PRICE_ID
                            ]
                        ]
                    );
                    while ($arPrice = $rsPrice->Fetch()) {
                        if (self::CATALOG_PRICE_ID == $arPrice['CATALOG_GROUP_ID']) {
                            $priceParams['price'] = (int) $arPrice['PRICE'];
                        } else {
                            $priceParams['price1'] = (int) $arPrice['PRICE'];
                        }
                    }
                    unset($rsPrice, $arPrice);
                }
            }

            if ($useSegmentLogic) {
                $priceParams['price_original'] = $priceParams['price'];
                $priceParams['price'] = self::calculateSegmentPrice($priceParams['price'], $priceParams['price_segment_id'], $priceParams['max_disc_bp']);
            }
            $productsPriceParams[$productId] = $priceParams;
        }

        return $productsPriceParams[$productId];
    }

    public static function getProductPriceParamsByOfferId($offerId)
    {
        static $productIdByOfferId = [];
        
        if (! isset($productIdByOfferId[$offerId]) && Loader::includeModule('catalog')) {
            $productIdByOfferId[$offerId] = false;

            $res = \CCatalogSKU::getProductList($offerId, self::IBLOCK_OFFERS_ID);
            if ($res) {
                $productId = (int) $res[$offerId]['ID'];

                foreach (self::getProductOffersIds($productId) as $oid) {
                    $productIdByOfferId[$oid] = $productId;
                }
            }
            unset($res);
        }

        return isset($productIdByOfferId[$offerId]) ? self::getProductPriceParams($productIdByOfferId[$offerId]) : false;
    }

    public static function getProductOffersIds($productId)
    {
        static $productOffersId = [];

        if (! isset($productOffersId[$productId]) && Loader::includeModule('catalog')) {
            $productOffersId[$productId] = [];

            $res = \CCatalogSKU::getOffersList($productId, self::IBLOCK_CATALOG_ID, ['ACTIVE' => 'Y']);
            if ($res) {
                $productOffersId[$productId] = array_keys($res[$productId]);
            }
            unset($res);
        }

        return $productOffersId[$productId];
    }
}
