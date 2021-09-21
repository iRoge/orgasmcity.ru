<?php

use Bitrix\Iblock\InheritedProperty\IblockValues;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\FileTable;
use Qsoft\Helpers\ComponentHelper;

/**
 * Class RdevsBrands
 */
class RdevsBrands extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams): array
    {
        parent::onPrepareComponentParams($arParams);
        return $arParams;
    }

    public function executeComponent(): void
    {
        $offers = Functions::filterOffersByRests(Functions::getAllOffers());
        $products = $this->getProducts(array_unique(array_column($offers, 'PROPERTY_CML2_LINK_VALUE')));

        $brandsXmlIdsAvail = array_unique(array_column($products, 'PROPERTY_VENDOR_VALUE'));

        $brands = Functions::getAllBrands();

        foreach ($brands as $id => $brand) {
            if (!in_array($brand['XML_ID'], $brandsXmlIdsAvail)) {
                unset($brands[$id]);
            }
        }

        $this->getSEO();

        $this->arResult['BRANDS'] = $brands;

        $this->includeComponentTemplate();
    }

    private function getSEO()
    {
        global $APPLICATION;

        $cache = new CPHPCache;

        if ($cache->InitCache(86400, 'seo|' . $APPLICATION->GetCurPage(), 'seo')) {
            $seo = $cache->GetVars()['seo'];
        } elseif ($cache->StartDataCache()) {
            $ipropValues = new IblockValues(IBLOCK_VENDORS);
            $seo = $ipropValues->getValues();

            if (!empty($seo)) {
                $cache->EndDataCache(['seo' => $seo]);
            } else {
                $cache->AbortDataCache();
            }
        }

        if (!empty($seo['SECTION_META_TITLE'])) {
            $APPLICATION->SetPageProperty('title', $seo['SECTION_META_TITLE']);
        }

        if (!empty($seo['SECTION_META_KEYWORDS'])) {
            $APPLICATION->SetPageProperty("keywords", $seo['SECTION_META_KEYWORDS']);
        }

        if (!empty($seo['SECTION_META_DESCRIPTION'])) {
            $APPLICATION->SetPageProperty("description", $seo['SECTION_META_DESCRIPTION']);
        }

        if (!empty($seo['SECTION_PAGE_TITLE'])) {
            $this->arResult['TITLE'] = $seo['SECTION_PAGE_TITLE'];
        }
    }

    private function getProducts(array $arProdIds): array
    {
        $productsCache = new CPHPCache;
        global $CACHE_MANAGER;
        $arProducts = [];

        if ($productsCache->InitCache($this->arParams['CACHE_TIME'], 'brand_products')) {
            $arProducts = $productsCache->GetVars()['products'];
        } elseif ($productsCache->StartDataCache()) {
            $CACHE_MANAGER->StartTagCache('products');
            $CACHE_MANAGER->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "ID" => $arProdIds,
            ];

            $arSelect = [
                "ID",
                "IBLOCK_ID",
                "NAME",
                "PROPERTY_VENDOR",
            ];

            $resProducts = CIBlockElement::GetList(
                ["SORT" => "ASC"],
                $arFilter,
                false,
                false,
                $arSelect,
            );

            while ($product = $resProducts->Fetch()) {
                $arProducts[$product['ID']] = $product;
            }

            $CACHE_MANAGER->endTagCache();
            $productsCache->EndDataCache(['products' => $arProducts]);
        }

        return $arProducts;
    }
}
