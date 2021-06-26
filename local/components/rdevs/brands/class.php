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
    private $location;
    private $cacheManager;
    //инфоблок брендов
    private int $brandsIB;

    public function onPrepareComponentParams($arParams): array
    {
        parent::onPrepareComponentParams($arParams);

        global $LOCATION, $CACHE_MANAGER;

        $this->location = $LOCATION;
        $this->cacheManager = $CACHE_MANAGER;
        $this->brandsIB = IBLOCK_VENDORS;

        return $arParams;
    }

    public function executeComponent(): void
    {
        $offers = Functions::filterOffersByRests(Functions::getAllOffers());
        $products = $this->getProducts(array_unique(array_column($offers, 'PROPERTY_CML2_LINK_VALUE')));

        $brandsXmlIdsAvail = array_unique(array_column($products, 'PROPERTY_VENDOR_VALUE'));

        $brands = $this->loadBrandsFromIB();

        foreach ($brands as $id => $brand) {
            if (!in_array($brand['XML_ID'], $brandsXmlIdsAvail)) {
                unset($brands[$id]);
            }
        }

        $this->getSEO();

        $this->arResult['BRANDS'] = $brands;

        $this->includeComponentTemplate();
    }

    private function loadBrandsFromIB()
    {
        $brandsCache = new CPHPCache();
        $arBrands = [];

        if ($brandsCache->initCache(360000, 'brands', 'brands')) {
            $arBrands = $brandsCache->getVars()['array_brands'];
        } elseif ($brandsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('brands');
            $this->cacheManager->RegisterTag('catalogAll');

            $arBrands = $this->getBrands();

            uasort($arBrands, function ($a, $b) {
                if ($a['PREVIEW_PICTURE'] && !$b['PREVIEW_PICTURE']) {
                    return -1;
                } elseif ($b['PREVIEW_PICTURE'] && !$a['PREVIEW_PICTURE']) {
                    return 1;
                } else {
                    return strnatcmp($a['NAME'], $b['NAME']);
                }
            });

            $this->cacheManager->endTagCache();
            $brandsCache->EndDataCache(['array_brands' => $arBrands]);
        }

        return $arBrands;
    }

    private function getBrands(): array
    {
        $brandCache = new CPHPCache();
        $arBrands = [];

        if ($brandCache->InitCache($this->arParams['CACHE_TIME'], 'allBrands', 'brands')) {
            $arBrands = $brandCache->GetVars()['allBrands'];
        } elseif ($brandCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('brands');
            $this->cacheManager->RegisterTag('catalogAll');

            $res = CIBlockElement::GetList(
                false,
                [
                    "IBLOCK_ID" => $this->brandsIB,
                    'GLOBAL_ACTIVE' => 'Y',
                    'ACTIVE' => 'Y',
                    'IBLOCK_ACTIVE' => 'Y',
                ],
                false,
                false,
                [
                    "ID",
                    "NAME",
                    "CODE",
                    "SORT",
                    "DESCRIPTION",
                    "PREVIEW_PICTURE",
                    "XML_ID"
                ]
            );

            while ($arBrand = $res->GetNext(true, false)) {
                $arBrand['SECTION_PAGE_URL'] = '/brands/' . $arBrand['CODE'] . '/';
                $arBrands[$arBrand["ID"]] = $arBrand;

                if (!empty($arBrand["PREVIEW_PICTURE"])) {
                    $arImageIds[] = $arBrand["PREVIEW_PICTURE"];
                }
            }

            if (!empty($arImageIds)) {
                $arImages = $this->getImages($arImageIds);

                foreach ($arBrands as $id => &$arBrand) {
                    if (!empty($arImages[$arBrand['PREVIEW_PICTURE']])) {
                        $arBrand['PREVIEW_PICTURE'] = $arImages[$arBrand['PREVIEW_PICTURE']];
                    }
                }
            }

            $this->cacheManager->endTagCache();
            $brandCache->EndDataCache(['allBrands' => $arBrands]);
        }

        return $arBrands;
    }

    private function getImages($arImageIds): array
    {
        $res = FileTable::getList([
            "select" => [
                "ID",
                "SUBDIR",
                "FILE_NAME",
                "WIDTH",
                "HEIGHT",
                "CONTENT_TYPE",
            ],
            "filter" => [
                "ID" => $arImageIds,
            ],
        ]);

        $arImages = [];

        while ($arImage = $res->Fetch()) {
            $src = "/upload/" . $arImage["SUBDIR"] . "/" . $arImage["FILE_NAME"];

            if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                continue;
            }

            $arImages[$arImage["ID"]] = $src;
        }

        return $arImages;
    }

    private function getSEO()
    {
        global $APPLICATION;

        $cache = new CPHPCache;

        if ($cache->InitCache(86400, 'seo|' . $APPLICATION->GetCurPage(), 'seo')) {
            $seo = $cache->GetVars()['seo'];
        } elseif ($cache->StartDataCache()) {
            $ipropValues = new IblockValues($this->brandsIB);
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
        $arProducts = [];

        if ($productsCache->InitCache($this->arParams['CACHE_TIME'], 'brand_products')) {
            $arProducts = $productsCache->GetVars()['products'];
        } elseif ($productsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('products');
            $this->cacheManager->RegisterTag('catalogAll');

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

            $this->cacheManager->endTagCache();
            $productsCache->EndDataCache(['products' => $arProducts]);
        }

        return $arProducts;
    }
}
