<?php

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
    //главная секция
    private array $mainSection;
    //уникальная витрина
    private $uniqShowcaseId;

    public function onPrepareComponentParams($arParams): array
    {
        parent::onPrepareComponentParams($arParams);

        global $LOCATION, $CACHE_MANAGER;

        $this->location = $LOCATION;
        $this->cacheManager = $CACHE_MANAGER;
        $this->brandsIB = Functions::getEnvKey('IBLOCK_BRANDS');
        $this->uniqShowcaseId = $this->location->getUserShowcase();

        return $arParams;
    }

    public function executeComponent(): void
    {
        $brands = $this->loadBrandsFromIB();

        $this->getSEO();

        $this->arResult['BRANDS'] = $brands;

        $this->includeComponentTemplate();
    }

    private function loadBrandsFromIB()
    {
        $brandsCache = new CPHPCache();
        $arRestBrands = array();

        if ($brandsCache->initCache(1200, 'brands|' . $this->uniqShowcaseId, 'brands')) {
            $arRestBrands = $brandsCache->getVars()['array_brands'];
        } elseif ($brandsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('brands');
            $this->cacheManager->RegisterTag('catalogAll');

            $arAllOffers = $this->getOffers();
            $arProducts = $this->getProducts($arAllOffers['PROD_IDS']);
            $arBrands = $this->getBrands();
            $arRestBrands['MAIN_SECTION'] = $arBrands['MAIN_SECTION'];
            $restOffers = $this->getRestsOfferIds($arAllOffers['OFFERS']);

            foreach ($restOffers as $offerId) {
                $brand = $arBrands['BRANDS'][$arBrands['XML'][$arProducts[$arAllOffers['OFFERS'][$offerId]['PROPERTY_CML2_LINK_VALUE']]['PROPERTY_BRAND_VALUE']]];

                if (!empty($brand)) {
                    $arRestBrands['BRANDS'][$brand['ID']] = $brand;
                }
            }

            uasort($arRestBrands['BRANDS'], function ($a, $b) {
                return strnatcmp($a['NAME'], $b['NAME']);
            });

            $this->cacheManager->endTagCache();
            $brandsCache->EndDataCache(['array_brands' => $arRestBrands]);
        }

        $this->mainSection = $arRestBrands['MAIN_SECTION'];

        return $arRestBrands['BRANDS'];
    }

    private function getBrands(): array
    {

        $brandCache = new CPHPCache();
        $arBrands = array();

        if ($brandCache->InitCache($this->arParams['CACHE_TIME'], 'allBrands', 'brands')) {
            $arBrands = $brandCache->GetVars()['allBrands'];
        } elseif ($brandCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('brands');
            $this->cacheManager->RegisterTag('catalogAll');

            $res = CIBlockSection::GetList(
                [
                    'NAME' => 'ASC',
                ],
                [
                    "IBLOCK_ID" => $this->brandsIB,
                    'GLOBAL_ACTIVE' => 'Y',
                    'ACTIVE' => 'Y',
                    'IBLOCK_ACTIVE' => 'Y',
                ],
                false,
                [
                    "ID",
                    "NAME",
                    "CODE",
                    "SORT",
                    "DESCRIPTION",
                    "PICTURE",
                    "DETAIL_PICTURE",
                    "SECTION_PAGE_URL",
                    "UF_XML_BRANDS",
                    'DEPTH_LEVEL',
                ]
            );

            while ($arBrand = $res->GetNext(true, false)) {
                if ($arBrand['DEPTH_LEVEL'] == 1) {
                    $arBrands['MAIN_SECTION'] = $arBrand;
                } else {
                    $arBrands['BRANDS'][$arBrand["ID"]] = $arBrand;

                    foreach ($arBrand['UF_XML_BRANDS'] as $xml) {
                        $arBrands['XML'][$xml] = $arBrand["ID"];
                    }

                    if (!empty($arBrand["PICTURE"])) {
                        $arImageIds[] = $arBrand["PICTURE"];
                    }

                    if (!empty($arBrand["DETAIL_PICTURE"])) {
                        $arImageIds[] = $arBrand["DETAIL_PICTURE"];
                    }
                }
            }

            if (!empty($arImageIds)) {
                $arImages = $this->getImages($arImageIds);

                foreach ($arBrands['BRANDS'] as $id => &$arBrand) {
                    if (!empty($arImages[$arBrand['PICTURE']])) {
                        $arBrand['PICTURE'] = $arImages[$arBrand['PICTURE']];
                    }

                    if (!empty($arImages[$arBrand['DETAIL_PICTURE']])) {
                        $arBrand['DETAIL_PICTURE'] = $arImages[$arBrand['DETAIL_PICTURE']];
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
        $res = FileTable::getList(array(
            "select" => array(
                "ID",
                "SUBDIR",
                "FILE_NAME",
                "WIDTH",
                "HEIGHT",
                "CONTENT_TYPE",
            ),
            "filter" => array(
                "ID" => $arImageIds,
            ),
        ));

        $arImages = array();

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

        $mainSection = $this->mainSection;
        $cache = new CPHPCache();

        if ($cache->InitCache(86400, 'seo|' . $APPLICATION->GetCurPage(), 'seo')) {
            $seo = $cache->GetVars()['seo'];
        } elseif ($cache->StartDataCache()) {
            $ipropValues = new SectionValues($this->brandsIB, $mainSection['ID']);
            $seo = $ipropValues->getValues();

            if (!empty($seo)) {
                $cache->EndDataCache(['seo' => $seo]);
            } else {
                $cache->AbortDataCache();
            }
        }

        $seo['DESCRIPTION'] = $this->mainSection['DESCRIPTION'];

        if (!empty($seo['SECTION_META_TITLE'])) {
            $APPLICATION->SetPageProperty('title', $seo['SECTION_META_TITLE']);
        }

        if (!empty($seo['SECTION_META_KEYWORDS'])) {
            $APPLICATION->SetPageProperty("keywords", $seo['SECTION_META_KEYWORDS']);
        }

        if (!empty($seo['SECTION_META_DESCRIPTION'])) {
            $APPLICATION->SetPageProperty("description", $seo['SECTION_META_DESCRIPTION']);
        }

        if (!empty($seo['DESCRIPTION'])) {
            $sDescription = '<div class="catalog-section-description">' . $seo['DESCRIPTION'] . '</div>';
            $APPLICATION->AddViewContent('under_instagram', $sDescription);
        }

        if (!empty($seo['SECTION_PAGE_TITLE'])) {
            $this->arResult['TITLE'] = $seo['SECTION_PAGE_TITLE'];
        }
    }

    private function getOffers(): array
    {
        $offerCache = new CPHPCache();
        $arOffers = array();

        if ($offerCache->InitCache($this->arParams['CACHE_TIME'], 'allOffers', 'offers')) {
            $arOffers = $offerCache->GetVars()['allOffers'];
        } elseif ($offerCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('offers');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ];

            $arSelect = [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
            ];

            $resOffers = CIBlockElement::GetList(
                ["SORT" => "ASC"],
                $arFilter,
                false,
                false,
                $arSelect,
            );

            while ($offer = $resOffers->Fetch()) {
                $arOffers['OFFERS'][$offer['ID']] = $offer;
                $arOffers['PROD_IDS'][$offer['PROPERTY_CML2_LINK_VALUE']] = $offer['PROPERTY_CML2_LINK_VALUE'];
            }

            $this->cacheManager->endTagCache();
            $offerCache->EndDataCache(['allOffers' => $arOffers]);
        }

        return $arOffers;
    }

    private function getRestsOfferIds(array $arAllOffers): array
    {
        return array_keys($this->location->getRests(array_keys($arAllOffers)));
    }

    private function getProducts(array $arProdIds): array
    {
        $productsCache = new CPHPCache();
        $arProducts = array();

        if ($productsCache->InitCache($this->arParams['CACHE_TIME'], 'products', 'products')) {
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
                "PROPERTY_BRAND",
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
