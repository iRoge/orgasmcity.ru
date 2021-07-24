<? use Bitrix\Main\FileTable;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityRecommendedComponent extends CBitrixComponent
{
    private $cacheManager;

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        global $CACHE_MANAGER;
        $this->cacheManager = $CACHE_MANAGER;

        return $arParams;
    }

    public function executeComponent()
    {
        $this->arResult['ITEMS'] = $this->getItems();

        $this->includeComponentTemplate();
    }

    /**
     * Получение элементов инфоблока
     * @return array
     */
    public function getItems()
    {
        $arItems = [];
        if ($this->arParams['TYPE'] == 'similar') {
            $products = $this->getSimilarProducts($this->arParams['SECTION_ID']);
        } else {
            $products = $this->getBestsellerProducts();
        }

        $offers = Functions::filterOffersByRests($this->getOffersByProductIds(array_keys($products)));
        foreach ($offers as $offer) {
            $product = $products[$offer['PROPERTY_CML2_LINK_VALUE']];
            if (isset($arItems[$offer['PROPERTY_CML2_LINK_VALUE']])) {
                if ($offer['PRICE']['PRICE'] < $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['PRICE']) {
                    $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['PRICE'] = $offer['PRICE']['PRICE'];
                    $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['OLD_PRICE'] = $offer['PRICE']['OLD_PRICE'];
                    $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['DISCOUNT'] = $offer['PRICE']['DISCOUNT'];
                }
            } else {
                $arItems[$offer['PROPERTY_CML2_LINK_VALUE']] = $product;
                $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['PRICE'] = $offer['PRICE']['PRICE'];
                $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['OLD_PRICE'] = $offer['PRICE']['OLD_PRICE'];
                $arItems[$offer['PROPERTY_CML2_LINK_VALUE']]['DISCOUNT'] = $offer['PRICE']['DISCOUNT'];
            }
        }

        if ($GLOBALS['device_type'] == 'mobile') {
            $numImgs = 8;
        } else {
            $numImgs = 12;
        }
        if (count($arItems) > $numImgs) {
            $randKeys = array_rand($arItems, $numImgs);
            $arRandItems = [];
            foreach ($randKeys as $key) {
                $arRandItems[] = $arItems[$key];
            }
        } else {
            $arRandItems = $arItems;
        }

        return $arRandItems;
    }

    private function getOffersByProductIds($productIds): array
    {
        $offerCache = new CPHPCache;
        $arOffers = [];

        if ($offerCache->InitCache($this->arParams['CACHE_TIME'], 'allOffers', 'offers')) {
            $arOffers = $offerCache->GetVars()['allOffers'];
        } elseif ($offerCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('offers');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
                "PROPERTY_CML2_LINK" => $productIds,
            ];

            $arSelect = [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_BASEWHOLEPRICE",
                "PROPERTY_BASEPRICE",
            ];

            $resOffers = CIBlockElement::GetList(
                ["SORT" => "ASC"],
                $arFilter,
                false,
                false,
                $arSelect,
            );

            while ($offer = $resOffers->Fetch()) {
                $offer['PRICE'] = PriceUtils::getPrice($offer["PROPERTY_BASEWHOLEPRICE_VALUE"], $offer["PROPERTY_BASEPRICE_VALUE"]);
                $arOffers[$offer['ID']] = $offer;
            }

            $this->cacheManager->endTagCache();
            $offerCache->EndDataCache(['allOffers' => $arOffers]);
        }

        return $arOffers;
    }

    private function getSimilarProducts($sectionId): array
    {
        $productsCache = new CPHPCache;
        $arProducts = [];

        if ($productsCache->InitCache(86400, 'similar_products|' . $sectionId, 'products')) {
            $arProducts = $productsCache->GetVars()['products'];
        } elseif ($productsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('products');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "=PROPERTY_BESTSELLER_VALUE" => "1",
                "=SECTION_ID" => $sectionId,
            ];

            $arProducts = $this->getProductsByFilter($arFilter);
            $this->cacheManager->endTagCache();
            $productsCache->EndDataCache(['products' => $arProducts]);
        }

        return $arProducts;
    }

    private function getBestsellerProducts(): array
    {
        $productsCache = new CPHPCache;
        $arProducts = [];

        if ($productsCache->InitCache(86400, 'recommended_products', 'products')) {
            $arProducts = $productsCache->GetVars()['products'];
        } elseif ($productsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('products');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "=PROPERTY_BESTSELLER_VALUE" => "1",
            ];

            $arProducts = $this->getProductsByFilter($arFilter);
            $this->cacheManager->endTagCache();
            $productsCache->EndDataCache(['products' => $arProducts]);
        }

        return $arProducts;
    }

    private function getProductsByFilter($arFilter)
    {
        $arProducts = [];
        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "NAME",
            "CODE",
            "PROPERTY_BESTSELLER",
            "DETAIL_PICTURE"
        ];

        $resProducts = CIBlockElement::GetList(
            ["SORT" => "ASC"],
            $arFilter,
            false,
            false,
            $arSelect,
        );

        while ($product = $resProducts->Fetch()) {
            if (!$product["DETAIL_PICTURE"]) {
                continue;
            }

            $product["DETAIL_PAGE_URL"] = "/" . $product["CODE"] . "/";
            $arProducts[$product['ID']] = $product;
            $arImageIds[] = $product["DETAIL_PICTURE"];
        }

        if (!empty($arImageIds)) {
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
            while ($arItem = $res->Fetch()) {
                $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
                $image = new \Bitrix\Main\File\Image($_SERVER["DOCUMENT_ROOT"] . $src);
                $k = $image->getExifData()['COMPUTED']['Width'] / $image->getExifData()['COMPUTED']['Height'];
                $smallSizes = [
                    'width' => $k < 1 ? $k * CATALOG_SMALL_IMG_HEIGHT : CATALOG_SMALL_IMG_HEIGHT,
                    'height' => CATALOG_SMALL_IMG_HEIGHT,
                ];

                $resizeSrc = Functions::ResizeImageGet($arItem, $smallSizes);
                if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                    continue;
                }
                $arImages[$arItem["ID"]] = $resizeSrc['src'] ?: $src;
            }
            foreach ($arProducts as $id => &$arItem) {
                if (!empty($arImages[$arItem["DETAIL_PICTURE"]])) {
                    $arItem["DETAIL_PICTURE"] = $arImages[$arItem["DETAIL_PICTURE"]];
                }
            }
        }

        return $arProducts;
    }
}