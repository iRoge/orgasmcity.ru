<?php

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\FileTable;
use Bitrix\Main\Loader;
use Likee\Site\Helper;
use Likee\Site\Helpers\HL;
use Qsoft\Helpers\ComponentHelper;
use Qsoft\Helpers\PriceUtils;

/**
 * Class QsoftCatalogSection
 */
class QsoftCatalogSection extends ComponentHelper
{
    private const TYPE_ALL_CATALOG = 'all';
    private const TYPE_SECTION = 'section';
    private const TYPE_SEARCH = 'search';
    private const TYPE_SALES = 'sales';
    private const TYPE_FAVORITES = 'favorites';
    private const TYPE_GROUP = 'group';
    private const SORT_PRICE_DESC = 'price_desc';
    private const SORT_PRICE_ASC = 'price';
    private const SORT_POPULAR = 'popular';
    private const SORT_NEW = 'new';
    private const SORT_DEFAULT = 'default';

    /**
     * свойства, которые для одного продукта могут принимать несколько значений
     */
    private const MULTIPLE_VALUE_PROPERTIES = [
        'SIZES',
        'COLORS',
    ];

    private const PRODUCT_PROPERTIES = [
        'max_price' => 'MAX_PRICE',
        'max_diameter' => 'MAX_DIAMETER',
        'max_length' => 'MAX_LENGTH',
        'min_price' => 'MIN_PRICE',
        'min_diameter' => 'MIN_DIAMETER',
        'min_length' => 'MIN_LENGTH',
        'diameter' => 'DIAMETER',
        'length' => 'LENGTH',
        'color' => 'COLORS',
        'vendor' => 'VENDOR',
        'sizes' => 'SIZES',
        'material' => 'MATERIAL',
        'volume' => 'VOLUME',
        'collection' => 'COLLECTION',
    ];

    private const PRODUCT_PROPERTIES_MAP = [
        'VENDOR' => 'PROPERTY_VENDOR_VALUE',
        'MATERIAL' => 'PROPERTY_MATERIAL_VALUE',
        'VOLUME' => 'PROPERTY_VOLUME_VALUE',
        'COLLECTION' => 'PROPERTY_COLLECTION_VALUE',
        'COLORS' => 'COLORS',
        'SIZES' => 'SIZES',
    ];

    private const NUMBER_FILTERS = [
        'max_price' => 'MAX_PRICE',
        'max_diameter' => 'MAX_DIAMETER',
        'max_length' => 'MAX_LENGTH',
        'min_price' => 'MIN_PRICE',
        'min_diameter' => 'MIN_DIAMETER',
        'min_length' => 'MIN_LENGTH',
    ];

    private const DEFAULT_PRODUCT_FIELDS_TO_SELECT = [
        "ID",
        "IBLOCK_ID",
        "IBLOCK_SECTION_ID",
        "XML_ID",
        "NAME",
        "CODE",
        "DETAIL_PICTURE",
        "PREVIEW_PICTURE",
        "SORT",
        "PROPERTY_ARTICLE",
        "PROPERTY_DIAMETER",
        "PROPERTY_LENGTH",
        "PROPERTY_BESTSELLER",
        "PROPERTY_VENDOR",
        "PROPERTY_VOLUME",
        "PROPERTY_MATERIAL",
        "PROPERTY_COLLECTION",
        "SHOW_COUNTER",
    ];

    private const DEFAULT_OFFER_FIELDS_TO_SELECT = [
        "ID",
        "IBLOCK_ID",
        "PROPERTY_CML2_LINK",
        "PROPERTY_SIZE",
        "PROPERTY_COLOR",
        "PROPERTY_BASEPRICE",
        "PROPERTY_BASEWHOLEPRICE",
    ];

    /**
     * порядок вывода фильтра
     */
    private const FILTER_KEYS = [
        'MATERIAL',
        'PRICE',
        'DIAMETER',
        'LENGTH',
        'VOLUME',
        'SIZES',
        'COLORS',
        'COLLECTION',
        'VENDOR'
    ];

    protected string $relativePath = '/qsoft/catalog.section';
    /**
     * @var string
     */
    private string $type = self::TYPE_SECTION;
    /**
     * @var string
     */
    private string $code;
    /**
     * @var array
     */
    private array $products;
    /**
     * @var array
     */
    private array $offers;
    /**
     * @var array
     */
    private array $items;
    /**
     * @var bool|string
     */
    private $sort = null;
    /**
     * @var array
     */
    private $group;
    /**
     * @var array
     */
    private $section;
    /**
     * @var array
     */
    private $ibFilter;
    private $urlFilter;
    private $filterArray = [];
    private $disabledOptions;
    private $props = [];
    private $isBrand = false;
    private $isBrandTagCode = null;
    private $resultItems;


    private $filtersScopes = [
        'MAX_PRICE' => 0,
        'MAX_DIAMETER' => 0,
        'MAX_LENGTH' => 0,
        'MIN_PRICE' => 9999999,
        'MIN_DIAMETER' => 9999999,
        'MIN_LENGTH' => 9999999,
    ];

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        global $APPLICATION;
        parent::onPrepareComponentParams($arParams);

        $this->tryParseString($arParams['SECTION_CODE']);
        $this->tryParseInt($arParams['CACHE_TIME'], 36000000);
        $this->tryParseString($arParams['SECTION_TYPE'], self::TYPE_SECTION);

        $type = $arParams['SECTION_TYPE'];
        $sectionUrl = $APPLICATION->GetCurPage(false);
        $code = $arParams['SECTION_CODE'];

        if ($sectionUrl == '/catalog/') {
            //Ассортимент магазина
            $type = self::TYPE_ALL_CATALOG;
        } elseif ($sectionUrl == '/catalog/search/') {
            $type = self::TYPE_SEARCH;
            $arParams['SEARCH'] = $this->getSearchParam();
        } elseif ($type !== self::TYPE_SEARCH && !$code) {
            //Остальные типы
            $type = $this->checkGroupOrTag($sectionUrl);
            $sectionCode = rtrim($sectionUrl, '/');
            $code = $this->getCode($sectionCode);
        }

        $arParams['SECTION_TYPE'] = $type;
        $arParams['SECTION_URL'] = $sectionUrl;
        $arParams['SECTION_CODE'] = $code;

        return $arParams;
    }

    private function checkGroupOrTag(&$sectionUrl): string
    {
        $isSpecial = strpos($sectionUrl, 'catalog') !== false;
        if ($isSpecial) {
            if (strpos($sectionUrl, '/hits/') !== false) {
                $sectionUrl = str_replace('/catalog/hits', '', $sectionUrl);
                return 'hits';
            } elseif (strpos($sectionUrl, '/sales/') !== false) {
                $sectionUrl = str_replace('/catalog/sales', '', $sectionUrl);
                return 'sales';
            } elseif (strpos($sectionUrl, '/favorites/') !== false) {
                $sectionUrl = str_replace('/catalog/favorites', '', $sectionUrl);
                return 'favorites';
            }
        } elseif (strpos($sectionUrl, '/brands/') !== false) {
            $this->isBrand = true;
            $brandUrl = explode('/', rtrim($sectionUrl, '/'));

            return 'group';
        }

        return 'section';
    }

    private function getCode($sectionUrl)
    {
        $arSections = explode('/', $sectionUrl);
        $code = array_pop($arSections);
        if (strpos($code, 'tag_') !== false) {
            $code = substr($code, 4);
        }
        return $code;
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        Loader::includeModule('highloadblock');
        $this->init();
        //Загружаем фильтры из URL заранее, чтобы можно было считать для остатков по складам
        $this->getFilterFromUrl();

        $this->arResult['FAVORITES_PROD_IDS'] = $this->getFavorites(); //загружаем избранное

        if ($this->checkActionFavorite()) {
            Functions::exitJson($this->addOrDelFavorite());
        }

        if (!$this->loadProductsAndOffers()) {
            return false;
        }

        $this->getUserViewSettings();
        $this->prepareCatalogResult();

        if (isset($_REQUEST['getFilters'])) {
            $this->prepareFilterResult();
            $this->includeComponentTemplate();
            return false;
        }

        $this->getSeo();
        $this->buildNavChain();
        if ($this->type === self::TYPE_SECTION && !empty($this->section['ID'])) {
            $this->arResult['SECTION_ID'] = $this->section['ID'];
        } elseif ($this->type === self::TYPE_ALL_CATALOG) {
            $this->arResult['SECTION_ID'] = $this->getSecondLevelSections();
        }
        $this->arResult['SECTION_TYPE'] = $this->type;
        $this->arResult['PROPS'] = $this->props;
        $this->includeComponentTemplate();

        $GLOBALS['CATALOG_SECTION_ID'] = $this->arResult['SECTION_ID'];
        $GLOBALS['CATALOG_ELEMENT_IDS'] = $this->arResult['CATALOG_ELEMENT_IDS'];
    }

    public function init()
    {
        $this->type = $this->arParams['SECTION_TYPE'];
        $this->code = $this->arParams['SECTION_CODE'];
    }

    private function getUserViewSettings()
    {
        global $LOCATION;
        $this->arResult['CURRENT_HOST'] = $LOCATION->getCurrentHost(); //передаем текущий хост для установки кук настроек
        if (isset($_COOKIE['user_settings'])) {
            $userSettings = explode('~', $_COOKIE['user_settings']);
            $this->arResult['USER_SETTINGS']['SORT'] = $userSettings[0];
            $this->arResult['USER_SETTINGS']['GRID'] = $userSettings[1];
            $this->arResult['USER_SETTINGS']['LOCATION_FILTER'] = $userSettings[2];
        }
    }

    private function getFavorites()
    {
        $arFavoritesIds = [];
        global $USER;
        if ($USER->IsAuthorized()) { // Для авторизованного получаем из User
            $arUser = $USER->GetByID($USER->GetID())->Fetch();
            $arFavoritesIds = array_flip($arUser['UF_FAVORITES']);
        } else {
            if (isset($_COOKIE['favorites'])) {
                $arFavoritesIds = unserialize($_COOKIE['favorites']);
            }
        }
        return $arFavoritesIds;
    }

    private function loadProductsAndOffers(): bool
    {
        switch ($this->type) {
            case self::TYPE_SECTION:
                $this->loadProducts();
                if (empty($this->section)) {
                    return Functions::abort404();
                }
                $this->loadOffers();
                break;
            case self::TYPE_GROUP:
                if ($this->isBrand) {
                    if (!$this->getBrand()) {
                        return false;
                    }
                } else {
                    return Functions::abort404();
                    if (!$this->getGroup()) {
                        return false;
                    }
                }
                $this->getGroupFilters();
                $this->getGroupProducts();
                $this->getGroupOffers();
                break;
            case self::TYPE_SEARCH:
                $this->getSearchProducts();
                $this->getSearchOffers();
                break;
            case self::TYPE_FAVORITES:
                if (empty($this->arResult['FAVORITES_PROD_IDS'])) {
                    $this->getSeo();
                    $this->includeComponentTemplate();
                    return false;
                }
                $this->loadProducts(true);
                $this->loadOffers();
                if (!isset($_REQUEST['getFilters'])) {
                    $this->delNonActualFavorites();
                }
                $this->getFavoritesProductsAndOffers();
                break;
            case self::TYPE_SALES:
            default:
                $this->loadProducts();
                $this->loadOffers();
                break;
        }

        return true;
    }

    /**
     * load products for current and related sections
     * products are stored in @var $this->products
     */
    private function loadProducts($all = false): void
    {
        if (!empty($this->products)) {
            return;
        }
        if (empty($this->props)) {
            $this->props = $this->getPropertyValues();
        }
        $arProducts = [];
        if ($this->initCache('products_' . $this->type)) {
            if ($all) {
                $arProducts = $this->getCachedVars('products_' . $this->type);
            } else {
                list($arProducts, $this->section) = $this->getCachedVars('products_section_' . $this->type);
            }
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            ];

            if (!$all) {
                $currentSection = $this->getCurrentSection($this->code);

                if (!empty($currentSection)) {
                    $currentSection['SAME_SECTIONS'] = $this->getSameSections($currentSection);
                    $this->section = $currentSection;
                    $relatedSections = $this->loadRelatedSections($currentSection);
                    if (!empty($relatedSections)) {
                        $arFilter['IBLOCK_SECTION_ID'] = $relatedSections;
                    }
                }
            }

            $arSelectFields = self::DEFAULT_PRODUCT_FIELDS_TO_SELECT;
            $res = CIBlockElement::GetList(
                ["ID" => "ASC"],
                $arFilter,
                false,
                false,
                $arSelectFields
            );
            $arProducts = $this->processProducts($res);
            if (!empty($arProducts)) {
                $this->endTagCache();
                if ($all) {
                    $this->saveToCache('products_' . $this->type, $arProducts);
                } else {
                    $this->saveToCache('products_section_' . $this->type, [$arProducts, $currentSection]);
                }
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }
        $this->products = $arProducts;
    }

    /**
     * @param $code
     * @return array
     */
    private function getCurrentSection($code): array
    {
        $arSection = [];

        if (empty($code)) {
            return $arSection;
        }
        $res = CIBlockSection::GetList(
            [],
            [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "CODE" => $code,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "ID",
                "SECTION_PAGE_URL",
                "LEFT_MARGIN",
                "RIGHT_MARGIN",
                "DESCRIPTION",
                "NAME",
                "DEPTH_LEVEL",
                "IBLOCK_SECTION_ID"
            ],
            false
        );

        while ($arItem = $res->GetNext(true, false)) {
            if (strpos($this->arParams['SECTION_URL'], $arItem["SECTION_PAGE_URL"]) !== false) {
                if ($this->arParams['SECTION_URL'] == $arItem["SECTION_PAGE_URL"]) {
                    $arSection = $arItem;
                    break;
                }
                $arSection = $arItem;
            }
        }

        return $arSection;
    }

    /**
     * @return array
     */
    private function getSecondLevelSections(): array
    {
        $arSections = [];

        $res = CIBlockSection::GetList(
            [],
            [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
                "DEPTH_LEVEL" => 1,
            ],
            false,
            [
                "ID",
            ],
            false
        );

        while ($arItem = $res->Fetch()) {
            $arSections[] = $arItem['ID'];
        }

        return $arSections;
    }

    /**
     * load sections related to current section
     * @param $arSection
     * @return array
     */
    private function loadRelatedSections($arSection): array
    {
        $arSectionIds = [];

        if (is_array($arSection) && array_key_exists("LEFT_MARGIN", $arSection) && array_key_exists("RIGHT_MARGIN", $arSection)) {
            $res = CIBlockSection::GetList(
                [
                    "SORT" => "ASC",
                ],
                [
                    "IBLOCK_ID" => IBLOCK_CATALOG,
                    ">LEFT_MARGIN" => $arSection["LEFT_MARGIN"],
                    "<RIGHT_MARGIN" => $arSection["RIGHT_MARGIN"],
                ],
                false,
                [
                    "ID",
                ]
            );

            while ($arItem = $res->Fetch()) {
                $arSectionIds[] = $arItem["ID"];
            }
        }

        return $arSectionIds;
    }

    private function processProducts($res): array
    {
        if (empty($this->props)) {
            $this->props = $this->getPropertyValues();
        }

        $arProducts = [];
        $arImageIds = [];
        while ($arItem = $res->Fetch()) {
            if (!$arItem["DETAIL_PICTURE"]) {
                continue;
            }
            $arProducts[$arItem["ID"]] = [
                "ID" => $arItem["ID"],
                "NAME" => $arItem['NAME'],
                "XML_ID" => $arItem['XML_ID'],
                "DETAIL_PICTURE" => $arItem["DETAIL_PICTURE"],
                "PREVIEW_PICTURE" => $arItem["PREVIEW_PICTURE"],
                "PROPERTY_ARTICLE_VALUE" => $arItem["PROPERTY_ARTICLE_VALUE"],
                "PROPERTY_DIAMETER_VALUE" => $arItem["PROPERTY_DIAMETER_VALUE"],
                "PROPERTY_LENGTH_VALUE" => $arItem["PROPERTY_LENGTH_VALUE"],
                "PROPERTY_BESTSELLER_VALUE" => $arItem["PROPERTY_BESTSELLER_VALUE"],
                "PROPERTY_VENDOR_VALUE" => $arItem["PROPERTY_VENDOR_VALUE"],
                "PROPERTY_VOLUME_VALUE" => $arItem["PROPERTY_VOLUME_VALUE"],
                "PROPERTY_MATERIAL_VALUE" => $arItem["PROPERTY_MATERIAL_VALUE"],
                "PROPERTY_COLLECTION_VALUE" => $arItem["PROPERTY_COLLECTION_VALUE"],
                "SORT" => $arItem["SORT"],
                "SHOW_COUNTER" => $arItem["SHOW_COUNTER"],
                "DETAIL_PAGE_URL" => "/" . $arItem["CODE"] . "/",
                "IBLOCK_SECTION_ID" => $arItem["IBLOCK_SECTION_ID"],
                "RND_SORT" => rand(0, 1),
            ];

            $arImageIds[] = $arItem["DETAIL_PICTURE"];
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
            $arImagesBig = [];
            while ($arItem = $res->Fetch()) {
                $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
                $image = new \Bitrix\Main\File\Image($_SERVER["DOCUMENT_ROOT"] . $src);
                $k = $image->getExifData()['COMPUTED']['Width'] / $image->getExifData()['COMPUTED']['Height'];
                $smallSizes = [
                    'width' => $k < 1 ? $k * CATALOG_SMALL_IMG_HEIGHT : CATALOG_SMALL_IMG_HEIGHT,
                    'height' => CATALOG_SMALL_IMG_HEIGHT,
                ];
                $bigSizes = [
                    'width' => $k < 1 ? $k * CATALOG_BIG_IMG_HEIGHT : CATALOG_BIG_IMG_HEIGHT,
                    'height' => CATALOG_BIG_IMG_HEIGHT,
                ];
                $resizeSrc = Functions::ResizeImageGet($arItem, $smallSizes);
                $resizeSrcBig = Functions::ResizeImageGet($arItem, $bigSizes);
                if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                    continue;
                }
                $arImages[$arItem["ID"]] = $resizeSrc['src'] ?: $src;
                $arImagesBig[$arItem["ID"]] = $resizeSrcBig['src'] ?: $src;
            }
            foreach ($arProducts as $id => &$arItem) {
                if (!empty($arImages[$arItem["DETAIL_PICTURE"]])) {
                    $arItem["DETAIL_PICTURE_BIG"] = $arImagesBig[$arItem["DETAIL_PICTURE"]];
                    $arItem["DETAIL_PICTURE"] = $arImages[$arItem["DETAIL_PICTURE"]];
                }
            }
        }
        return $arProducts;
    }

    /**
     * load offers for @var $this ->products
     * offers are stored in @var $this ->offers
     */
    private function loadOffers(): void
    {
        if (!empty($this->offers)) {
            return;
        }
        $arOffers = [];
        if ($this->initCache('offers_' . $this->type)) {
            $arOffers = $this->getCachedVars('offers_' . $this->type);
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");

            $arSelectFields = self::DEFAULT_OFFER_FIELDS_TO_SELECT;
            $arFilter = [
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ];
            $res = CIBlockElement::GetList(
                [
                    "SORT" => "ASC",
                ],
                $arFilter,
                false,
                false,
                $arSelectFields
            );

            $arOffers = $this->processOffers($res);

            if (!empty($arOffers)) {
                $this->endTagCache();
                $this->saveToCache('offers_' . $this->type, $arOffers);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->offers = $arOffers;
    }

    private function processOffers($res): array
    {
        $arOffers = [];
        while ($arItem = $res->Fetch()) {
            if (!$this->products[$arItem["PROPERTY_CML2_LINK_VALUE"]]) {
                continue;
            }
            if (!isset($arItem['PROPERTY_BASEPRICE_VALUE']) || !isset($arItem['PROPERTY_BASEWHOLEPRICE_VALUE'])) {
                continue;
            }
            $sizeRes = CIBlockElement::GetProperty(IBLOCK_OFFERS, $arItem["ID"], "sort", "asc", ['CODE' => 'SIZE']);
            $size = $sizeRes->GetNext();
            $arOffers[$arItem["ID"]] = [
                'PROPERTY_CML2_LINK_VALUE' => $arItem['PROPERTY_CML2_LINK_VALUE'],
                'PROPERTY_SIZE_VALUE' => $size['VALUE'],
                'PROPERTY_COLOR_VALUE' => $arItem['PROPERTY_COLOR_VALUE'],
                'PROPERTY_BASEPRICE_VALUE' => $arItem['PROPERTY_BASEPRICE_VALUE'],
                'PROPERTY_BASEWHOLEPRICE_VALUE' => $arItem['PROPERTY_BASEWHOLEPRICE_VALUE'],
            ];
        }

        return $arOffers;
    }

    private function getFavoritesProductsAndOffers()
    {
        foreach ($this->offers as $offerID => $offer) {
            if (isset($this->arResult['FAVORITES_PROD_IDS'][$offer['PROPERTY_CML2_LINK_VALUE']])) {
                $favOffers[$offerID] = $offer;
            }
        }
        $this->offers = $favOffers;
        $this->arResult['FAVORITES_OFFERS_IDS'] = implode(',', array_keys($this->offers));
        $this->products = array_intersect_key($this->products, $this->arResult['FAVORITES_PROD_IDS']);
        setcookie("favorites_count", count($this->products), 0, '/');
    }

    private function checkActionFavorite()
    {
        if (isset($_REQUEST['changeFavourite'])) {
            return true;
        }

        return false;
    }

    private function addOrDelFavorite()
    {
        $arFavoritesIds = $this->arResult['FAVORITES_PROD_IDS'];
        if (isset($arFavoritesIds[$_REQUEST['ID']])) {
            unset($arFavoritesIds[$_REQUEST['ID']]);
            $response['res'] = 'delete';
            $this->setFavorites($arFavoritesIds);
        } else {
            if (count($arFavoritesIds) >= 99) {
                $response['text'] = Option::get(
                    "respect",
                    "favorites_max_num_text",
                    "Максимальное количество позиций в избранном - 99 моделей."
                );
                $response['res'] = 'error';
            } else {
                $arFavoritesIds[$_REQUEST['ID']] = $_REQUEST['ID'];
                $this->setFavorites($arFavoritesIds);
                $response['res'] = 'add';
            }
        }
        return $response;
    }

    private function setFavorites($arFavoritesIds)
    {
        global $USER;

        $this->arResult['FAVORITES_PROD_IDS'] = $arFavoritesIds;
        if ($USER->IsAuthorized()) { // Для авторизованного пишем в User
            $arFavoritesIds = array_flip($arFavoritesIds);
            $USER->Update($USER->GetID(), array("UF_FAVORITES" => $arFavoritesIds));
        } else {
            setcookie("favorites", serialize($arFavoritesIds), 0, '/');
        }
        setcookie("favorites_count", count($arFavoritesIds), 0, '/');
    }

    private function delNonActualFavorites()
    {
        $this->loadProducts(true);
        $this->loadOffers();
        $rsStoreOffers = \Bitrix\Catalog\ProductTable::getList(
            [
                'select' => ['ID', 'QUANTITY']
            ],
        );
        $availableOffers = [];
        while ($arStoreOffer = $rsStoreOffers->fetch()) {
            if ($arStoreOffer['QUANTITY'] > 0) {
                $availableOffers[$arStoreOffer['ID']] = $arStoreOffer['ID'];
            }
        }
        $actualOffers = array_intersect_key($this->offers, $availableOffers);
        $arProducts = [];
        foreach ($actualOffers as $offerId => $arItem) {
            $arProducts[$arItem["PROPERTY_CML2_LINK_VALUE"]][] = $offerId;
        }
        $arActualProducts = [];
        foreach ($arProducts as $productId => $arOffersId) {
            foreach ($arOffersId as $offerId) {
                if (isset($actualOffers[$offerId])) {
                    $arActualProducts[$productId][] = $offerId;
                    break;
                }
            }
        }
        $this->setFavorites(array_intersect_key($this->arResult['FAVORITES_PROD_IDS'], $arActualProducts));
        return array_intersect_key($this->arResult['FAVORITES_PROD_IDS'], $arActualProducts);
    }

    private function getGroup()
    {
        if (!empty($this->group)) {
            return true;
        }
        $group = false;
        if ($this->initCache('group_' . $this->code)) {
            $group = $this->getCachedVars('group');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");
            $group = $this->loadGroup();
            if (empty($group)) {
                $this->abortTagCache();
                $this->abortCache();
            } else {
                $this->endTagCache();
                $this->saveToCache('group', $group);
            }
        }

        if (empty($group)) {
            return Functions::abort404();
        }
        $this->group = $group;
        $this->arResult["BANNER"] = $group["BANNER"] ?? array();

        return true;
    }

    private function loadGroup()
    {
        $group = [];

        if (!empty($this->code)) {
            $group = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => IBLOCK_GROUPS,
                    'CODE' => $this->code,
                    'ACTIVE' => 'Y',
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'DETAIL_TEXT',
                    'PREVIEW_PICTURE',
                    'DETAIL_PICTURE',
                    'NAME',
                    'PROPERTY_SECTION',
                    'PROPERTY_PRICE_FROM',
                    'PROPERTY_PRICE_TO',
                    'PROPERTY_ARTICLE',
                    'PROPERTY_OFFERS_SIZE',
                    'PROPERTY_LININGMATERIAL',
                    'PROPERTY_UPPERMATERIAL',
                    'PROPERTY_RHODEPRODUCT',
                    'PROPERTY_SEASON',
                    'PROPERTY_COLOR',
                    'PROPERTY_MLT',
                    'PROPERTY_MRT',
                    'PROPERTY_SUBTYPEPRODUCT',
                    'PROPERTY_COLLECTION',
                    'PROPERTY_COUNTRY',
                    'PROPERTY_HEELHEIGHT',
                    'PROPERTY_PRICESEGMENTID',
                    'PROPERTY_SEGMENT_FROM',
                    'PROPERTY_SEGMENT_TO',
                    'PROPERTY_SKU_ADDITIONAL',
                    'PROPERTY_SKU_EXCLUDE',
                    'PROPERTY_F_STORES_O',
                    'PROPERTY_F_STORES_R',
                    'PROPERTY_IS_ACTION',
                    'PROPERTY_BRAND',
                    'PROPERTY_COLORS',
                    'PROPERTY_ONLINE_TRY_ON',
                    'PROPERTY_IS_BRAND',
                    'PROPERTY_STYLE',
                    'PROPERTY_ZASTEGKA',
                    'PROPERTY_HEELHEIGHT_TYPE',
                    'PROPERTY_MATERIALSTELKI',
                    'PROPERTY_VIDKABLUKA',
                    'PROPERTY_TOP_ARTICLES',
                ]
            )->Fetch();
            // Проверяем группу на галочку о брендовой группировке
            if (strpos($this->arParams['SECTION_URL'], '/brands/') === false && $group['PROPERTY_IS_BRAND_VALUE'] == 'Y' || strpos($this->arParams['SECTION_URL'], '/brands/') !== false && $group['PROPERTY_IS_BRAND_VALUE'] !== 'Y') {
                return false;
            }
            if ($group['PROPERTY_IS_ACTION_VALUE'] === 'Да') {
                if ($group['PREVIEW_PICTURE']) {
                    $temp = CFile::GetFileArray($group['PREVIEW_PICTURE']);
                    $res = CIBlockElement::GetProperty(
                        IBLOCK_GROUPS,
                        $group["ID"],
                        array(),
                        array(
                            "CODE" => "ACTION_LINKS_DESKTOP",
                        )
                    );
                    while ($arItem = $res->Fetch()) {
                        if (!$arItem["VALUE"]) {
                            continue;
                        }
                        $group["BANNER"]["DESKTOP_LINKS"][] = array(
                            "LINK" => $arItem["DESCRIPTION"],
                            "STYLE" => $this->getBannerLinkStyle($arItem["VALUE"], $temp["WIDTH"], $temp["HEIGHT"]),
                        );
                    }
                    if (!empty($group["BANNER"]["DESKTOP_LINKS"])) {
                        $group["BANNER"]["DESKTOP"] = $temp["SRC"];
                    } else {
                        $group["BANNER"]["SINGLE"] = $temp["SRC"];
                    }
                }
                if ($group['DETAIL_PICTURE']) {
                    $temp = CFile::GetFileArray($group['DETAIL_PICTURE']);
                    $res = CIBlockElement::GetProperty(
                        IBLOCK_GROUPS,
                        $group["ID"],
                        array(),
                        array(
                            "CODE" => "ACTION_LINKS_MOBILE",
                        )
                    );
                    while ($arItem = $res->Fetch()) {
                        if (!$arItem["VALUE"]) {
                            continue;
                        }
                        $group["BANNER"]["MOBILE_LINKS"][] = array(
                            "LINK" => $arItem["DESCRIPTION"],
                            "STYLE" => $this->getBannerLinkStyle($arItem["VALUE"], $temp["WIDTH"], $temp["HEIGHT"]),
                        );
                    }
                    if (!empty($group["BANNER"]["MOBILE_LINKS"])) {
                        $group["BANNER"]["MOBILE"] = $temp["SRC"];
                    }
                }
            }
        }

        return $group ?: [];
    }

    private function getBannerLinkStyle($val, $w, $h)
    {
        $val = explode(",", trim($val));
        return "left:" . (intval(100 * $val[0] / $w)) . "%;" .
            "top:" . (intval(100 * $val[1] / $h)) . "%;" .
            "right:" . (100 - intval(100 * $val[2] / $w)) . "%;" .
            "bottom:" . (100 - intval(100 * $val[3] / $h)) . "%";
    }

    private function getGroupFilters()
    {
        list($productPropertiesMap, $offersPropertiesMap) = array_values($this->getPropertiesMap());

        $filter = [
            'PRODUCT' => ['IBLOCK_ID' => IBLOCK_CATALOG, 'ACTIVE' => 'Y'],
            'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS, 'ACTIVE' => 'Y'],
            'RESULT' => [],
        ];

        foreach ($productPropertiesMap as $property) {
            if (!empty($this->group[$property . '_VALUE'])) {
                $propertyName = ($property === 'PROPERTY_SECTION') ? 'IBLOCK_SECTION_ID' : $property;
                $filter['PRODUCT'][$propertyName] = $this->group[$property . '_VALUE'];
            }
        }

        foreach ($offersPropertiesMap as $key => $value) {
            if (!empty($this->group[$key . '_VALUE'])) {
                $filter['OFFER'][$value] = $this->group[$key . '_VALUE'];
            }
        }

//        /**
//         * артикулы для фильтра
//         * TODO - файл с артикулами
//         */
//        $this->getFilterArticles($filter);
//
//        /**
//         * обработка размеров
//         */
//        $this->getFilterSizes($filter);
//
//        /**
//         * обработка секций
//         */
//        $this->getFilterSections($filter);
//
//        $this->getFilterPrices($filter, $this->group);

        $this->ibFilter = $filter;
    }

    private function getPropertiesMap(): array
    {
        return [
            'PRODUCT' => [
                'PROPERTY_VENDOR'
            ],
            'OFFER' => [
            ],
        ];
    }

    private function getFilterArticles(array &$filter)
    {
        if (!empty($filter['PRODUCT']['PROPERTY_ARTICLE'])) {
            $filter['PRODUCT']['PROPERTY_ARTICLE'] = $this->strToArray($filter['PRODUCT']['PROPERTY_ARTICLE']);
        } else {
            $filter['PRODUCT']['PROPERTY_ARTICLE'] = [];
        }
        if (!empty($filter['PRODUCT']['PROPERTY_SKU_ADDITIONAL'])) {
            $filter['ADDITIONAL'] = $this->strToArray($filter['PRODUCT']['PROPERTY_SKU_ADDITIONAL']);
        }
        if (!empty($filter['PRODUCT']['PROPERTY_SKU_EXCLUDE'])) {
            $filter['EXCLUDE'] = $this->strToArray($filter['PRODUCT']['PROPERTY_SKU_EXCLUDE']);
        }
        if (!empty($filter['PRODUCT']['PROPERTY_TOP_ARTICLES'])) {
            $filter['TOP_ARTICLES'] = array_flip($this->strToArray($filter['PRODUCT']['PROPERTY_TOP_ARTICLES']));
        }
        unset($filter['PRODUCT']['PROPERTY_SKU_ADDITIONAL']);
        unset($filter['PRODUCT']['PROPERTY_SKU_EXCLUDE']);
        unset($filter['PRODUCT']['PROPERTY_TOP_ARTICLES']);

        if (empty($filter['PRODUCT']['PROPERTY_ARTICLE'])) {
            unset($filter['PRODUCT']['PROPERTY_ARTICLE']);
        }
    }

    private function strToArray(string $str): array
    {
        $array = explode(',', $str);

        return array_map(function ($item) {
            return trim($item);
        }, $array);
    }

    private function getFilterSizes(array &$filter)
    {
        if (!empty($filter['OFFER']['PROPERTY_SIZE'])) {
            $filter['OFFER']['PROPERTY_SIZE_VALUE'] = $this->strToArray($filter['OFFER']['PROPERTY_SIZE']);
        }
        unset($filter['OFFER']['PROPERTY_SIZE']);
    }

    private function getFilterSections(array &$filter)
    {
        $arSections = [];
        foreach ($filter['PRODUCT']['IBLOCK_SECTION_ID'] as $sectionId) {
            $section = $this->getSectionById($sectionId);
            if (!empty($section)) {
                $relatedSections = $this->loadRelatedSections($section);
                $arSections = array_merge($arSections, $relatedSections);
            }
        }
        $filter['PRODUCT']['IBLOCK_SECTION_ID'] = array_values(array_unique($arSections));
    }

    private function getSectionById($id)
    {
        if (!$id) {
            return [];
        }
        $arSection = [];
        $res = CIBlockSection::GetList(
            [],
            [
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ID" => $id,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "ID",
                "LEFT_MARGIN",
                "RIGHT_MARGIN",
            ],
            false
        );

        while ($arItem = $res->Fetch()) {
            $arSection = $arItem;
        }

        return $arSection;
    }

    private function getFilterPrices(array &$filter, array $data)
    {
        if (!empty($data['PROPERTY_PRICE_FROM_VALUE'])) {
            $filter['RESULT']['MIN_PRICE'] = intval($data['PROPERTY_PRICE_FROM_VALUE']);
        }

        if (!empty($data['PROPERTY_PRICE_TO_VALUE'])) {
            $filter['RESULT']['MAX_PRICE'] = intval($data['PROPERTY_PRICE_TO_VALUE']);
        }
    }

    private function getGroupProducts()
    {
        $products = [];

        if (empty($this->props)) {
            $this->props = $this->getPropertyValues();
        }
        $cacheTag = 'products_group_' . $this->code;
        if ($this->initCache($cacheTag)) {
            $products = $this->getCachedVars('products');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");

            $products = $this->loadProductsByFilter();
            if (!empty($products)) {
                $this->endTagCache();
                $this->saveToCache('products', $products);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->products = $products;
    }

    private function loadProductsByFilter()
    {
        $arSelectFields = self::DEFAULT_PRODUCT_FIELDS_TO_SELECT;

        $res = CIBlockElement::GetList(
            [],
            $this->ibFilter['PRODUCT'],
            false,
            false,
            $arSelectFields
        );

        return $this->processProducts($res);
    }

    private function getGroupOffers()
    {
        $offers = [];
        $cacheTag = 'offers_group_' . $this->code;
        if ($this->initCache($cacheTag)) {
            $offers = $this->getCachedVars('offers');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");

            $offers = $this->loadOffersByFilter();

            if (!empty($offers)) {
                $this->endTagCache();
                $this->saveToCache('offers', $offers);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->offers = $offers;
    }

    private function loadOffersByFilter()
    {
        $res = CIBlockElement::GetList(
            [
                "SORT" => "ASC",
            ],
            $this->ibFilter['OFFER'],
            false,
            false,
            self::DEFAULT_OFFER_FIELDS_TO_SELECT
        );

        return $this->processOffers($res);
    }

    private function buildSearchFilter()
    {
        $brandCodes = array_filter($this->props['VENDOR'], function ($v) {
            return mb_stristr($v, $this->arParams['SEARCH']);
        });

        $this->ibFilter = [
            'PRODUCT' => [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'ACTIVE' => 'Y',
                [
                    'LOGIC' => 'OR',
                    ['NAME' => '%' . $this->arParams['SEARCH'] . '%'],
                    ['PROPERTY_ARTICLE' => '%' . $this->arParams['SEARCH'] . '%'],
                    ['PROPERTY_VENDOR' => array_keys($brandCodes)],
                ],
            ],
            'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS, 'ACTIVE' => 'Y'],
            'RESULT' => [],
        ];
    }

    private function getSearchParam()
    {
        $query = $this->request->getPostList();
        $searchParam = $query->get('q');
        if (!$searchParam) {
            $query = $this->request->getQueryList();
            $searchParam = $query->get('q');
        }

        return $searchParam;
    }

    private function getSearchProducts()
    {
        $products = [];

        if (empty($this->props)) {
            $this->props = $this->getPropertyValues();
        }

        $this->buildSearchFilter();

        if ($this->initCache('search_products')) {
            $products = $this->getCachedVars('products');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");
            $this->registerTag('catalogSearch');

            $products = $this->loadProductsByFilter();

            if (!empty($products)) {
                $this->endTagCache();
                $this->saveToCache('products', $products);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->products = $products;
    }

    private function getSearchOffers()
    {
        $offers = [];

        if ($this->initCache('search_offers')) {
            $offers = $this->getCachedVars('offers');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");
            $this->registerTag('catalogSearch');

            $offers = $this->loadOffersByFilter();

            if (!empty($offers)) {
                $this->endTagCache();
                $this->saveToCache('offers', $offers);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->offers = $offers;
    }

    private function prepareCatalogResult(): void
    {
        if (isset($_REQUEST['getFilters'])) {
            if ($this->initCache('resultItems', 3)) {
                $this->items = $this->getCachedVars('items');
            } else {
                $this->getResultItems();
            }
        } else {
            $this->getResultItems();
            $this->initCache('resultItems', 3);
            $this->startCache();
            $this->saveToCache('items', $this->items);
        }
        $resultItems = $this->items;
        $this->resultItems = $this->filter($resultItems);
        $this->resultItems = $this->sort($this->resultItems);

        $pageSize = intval($this->request->get('SIZEN_' . $this->getNavNum()));
        if (!$pageSize) {
            $pageSize = $_COOKIE["CATALOG_SORT_TO"];
        }
        $pageSize = in_array($pageSize, [36, 48, 72, 96]) ? $pageSize : 48;
        $dbResult = new CDBResult();
        // костыль для того, что бы номер страницы всегда брался из URL
        CPageOption::SetOptionString("main", "nav_page_in_session", "N");
        $dbResult->InitFromArray($this->resultItems);
        $dbResult->NavStart($pageSize);
        $this->setNavNum($dbResult->NavNum);
        $this->arResult['NAV_STRING'] = $dbResult->GetPageNavString(
            'Товары',
            'show_full',
            true,
            $this
        );
        $this->arResult['CURRENT_PAGE_NOM'] = $dbResult->NavPageNomer;
        $this->arResult['ITEMS'] = $dbResult;
        $this->arResult['IS_AJAX'] = $this->request->isAjaxRequest();
        $this->arResult['MODELS_COUNT'] = $this->arResult['ITEMS']->nSelectedCount . ' ' . $this->correctEndingModels($this->arResult['ITEMS']->nSelectedCount, 'модель', 'модели', 'моделей');
    }

    private function correctEndingModels($num, $form1, $form2, $form3)
    {
        $num = abs($num) % 100;
        $num1 = $num % 10;
        if ($num > 10 && $num < 20) {
            return $form3;
        }
        if ($num1 > 1 && $num1 < 5) {
            return $form2;
        }
        if ($num1 == 1) {
            return $form1;
        }
        return $form3;
    }

    /**
     * builds resultItems @return array
     */
    private function getResultItems(): array
    {
        if (!empty($this->items)) {
            return $this->items;
        }

        $isFavoritesCatalog = false;
        if ($this->type == self::TYPE_FAVORITES) {
            $isFavoritesCatalog = true;
        }
        $rests = [];
        if (!$isFavoritesCatalog) {
            $rests = Functions::getRests(array_keys($this->offers));
        }

        $items = [];
        foreach ($this->offers as $offerId => $value) {
            if (!$isFavoritesCatalog && (!isset($rests[$offerId]) || $rests[$offerId] < 1)) {
                continue;
            }

            $price = PriceUtils::getPrice($value["PROPERTY_BASEWHOLEPRICE_VALUE"], $value["PROPERTY_BASEPRICE_VALUE"]);

            if ($this->type === self::TYPE_SALES && !$price['DISCOUNT']) {
                continue;
            }

            $pid = $value["PROPERTY_CML2_LINK_VALUE"];

            if (!$items[$pid]) {
                $items[$pid] = $this->products[$pid];
            }

            if (
                (
                    !isset($items[$pid]['PRICE'])
                    || isset($items[$pid]['PRICE'])
                    && $price['PRICE'] < $items[$pid]['PRICE']
                )
            ) {
                $items[$pid]['PRICE'] = $price['PRICE'];
                $items[$pid]['OLD_PRICE'] = $price['OLD_PRICE'];
                $items[$pid]['PERCENT'] = $price['DISCOUNT'];
            }
            $items[$pid]["SIZES"][] = $value["PROPERTY_SIZE_VALUE"];
            $items[$pid]["COLORS"][] = $value["PROPERTY_COLOR_VALUE"];
            $items[$pid]["ASSORTMENTS"][] = $value;
        }

        foreach ($items as $key => &$item) {
            $item["SIZES"] = array_unique($item["SIZES"]);
            $item["COLORS"] = array_unique($item["COLORS"]);
        }

        $this->items = $items;
        return $this->items;
    }

    private function getFilterFromUrl()
    {
        $filter = [];
        $query = $this->request->getQueryList();
        if ($query->get('set_filter') === 'Y') {
            foreach (self::PRODUCT_PROPERTIES as $urlKey => $filterKey) {
                if ($params = $query->get($urlKey)) {
                    $params = explode(';', $params);
                    if (in_array($urlKey, array_keys(self::NUMBER_FILTERS))) {
                        $params = floatval(array_pop($params));
                    }
                    $filter[$filterKey] = $params;
                }
            }
        }
        $this->urlFilter = $filter;
    }

    private function filter(array $items, array $filter = []): array
    {
        $arFilter = empty($filter) ? $this->urlFilter : $filter;

        $items = array_filter($items, function (&$item) use ($arFilter) {
            $comp = true;

            if (isset($arFilter['MIN_PRICE'])) {
                $comp = $comp && isset($item['PRICE']) && $item['PRICE'] >= $arFilter['MIN_PRICE'];
            }
            if (isset($arFilter['MAX_PRICE'])) {
                $comp = $comp && isset($item['PRICE']) && $item['PRICE'] <= $arFilter['MAX_PRICE'];
            }
            if (isset($arFilter['MIN_LENGTH'])) {
                $comp = $comp && isset($item['PROPERTY_LENGTH_VALUE']) && $item['PROPERTY_LENGTH_VALUE'] >= $arFilter['MIN_LENGTH'];
            }
            if (isset($arFilter['MAX_LENGTH'])) {
                $comp = $comp && isset($item['PROPERTY_LENGTH_VALUE']) && $item['PROPERTY_LENGTH_VALUE'] <= $arFilter['MAX_LENGTH'];
            }
            if (isset($arFilter['MIN_DIAMETER'])) {
                $comp = $comp && isset($item['PROPERTY_DIAMETER_VALUE']) && $item['PROPERTY_DIAMETER_VALUE'] >= $arFilter['MIN_DIAMETER'];
            }
            if (isset($arFilter['MAX_DIAMETER'])) {
                $comp = $comp && isset($item['PROPERTY_DIAMETER_VALUE']) && $item['PROPERTY_DIAMETER_VALUE'] <= $arFilter['MAX_DIAMETER'];
            }

            foreach ($arFilter as $key => $value) {
                if (array_key_exists($key, self::PRODUCT_PROPERTIES_MAP)) {
                    $comp = $this->applyFilter($value, $item[self::PRODUCT_PROPERTIES_MAP[$key]]);
                }
                if (!$comp) {
                    return false;
                }
            }

            if ($item['PRICE'] > $this->filtersScopes['MAX_PRICE']) {
                $this->filtersScopes['MAX_PRICE'] = $item['PRICE'];
            }
            if ($item['PRICE'] < $this->filtersScopes['MIN_PRICE']) {
                $this->filtersScopes['MIN_PRICE'] = $item['PRICE'];
            }
            if (isset($item['PROPERTY_LENGTH_VALUE'])) {
                if ($item['PROPERTY_LENGTH_VALUE'] > $this->filtersScopes['MAX_LENGTH']) {
                    $this->filtersScopes['MAX_LENGTH'] = $item['PROPERTY_LENGTH_VALUE'];
                }
            }
            if (isset($item['PROPERTY_LENGTH_VALUE'])) {
                if ($item['PROPERTY_LENGTH_VALUE'] < $this->filtersScopes['MIN_LENGTH']) {
                    $this->filtersScopes['MIN_LENGTH'] = $item['PROPERTY_LENGTH_VALUE'];
                }
            }
            if (isset($item['PROPERTY_DIAMETER_VALUE'])) {
                if ($item['PROPERTY_DIAMETER_VALUE'] > $this->filtersScopes['MAX_DIAMETER']) {
                    $this->filtersScopes['MAX_DIAMETER'] = $item['PROPERTY_DIAMETER_VALUE'];
                }
            }
            if (isset($item['PROPERTY_DIAMETER_VALUE'])) {
                if ($item['PROPERTY_DIAMETER_VALUE'] < $this->filtersScopes['MIN_DIAMETER']) {
                    $this->filtersScopes['MIN_DIAMETER'] = $item['PROPERTY_DIAMETER_VALUE'];
                }
            }
            return $comp;
        });
        return $items;
    }

    private function applyFilter($filter, $data)
    {
        if (is_array($data)) {
            return !empty(array_intersect($filter, $data));
        }

        return in_array($data, $filter);
    }

    private function getDisabledOptions()
    {
        $availableOptions = $this->getAvailableFilterOptions();

        foreach ($this->filterArray as $key => $options) {
            if (!array_key_exists($key, $availableOptions)) {
                $this->disabledOptions[$key] = $options;
            } else {
                $this->disabledOptions[$key] = array_diff($options, $availableOptions[$key]);
            }
        }
        $this->disabledOptions = array_filter($this->disabledOptions, function ($item) {
            return !empty($item);
        });
    }

    public function getFilterArray(): array
    {
        if (!empty($this->filterArray)) {
            return $this->filterArray;
        }

        $filter = [];
        if (empty($this->offers) || empty($this->products)) {
            if (!$this->loadProductsAndOffers()) {
                return $filter;
            }
        }
        if ($this->type != 'favorites') {
            if ($this->initCache('arFilters')) {
                $filter = $this->getCachedVars('arFilters');
            } elseif ($this->startCache()) {
                $this->startTagCache();
                $this->registerTag("catalogAll");
                $filter = $this->getFilterList();
                $this->endTagCache();
                $this->saveToCache('arFilters', $filter);
            }
        } else {
            $filter = $this->getFilterList();
        }
        $this->filterArray = $filter;
        return $filter;
    }

    private function getFilterList(): array
    {
        if (empty($this->items)) {
            $this->getResultItems();
        }
        $filter = [];
        $items = $this->items;
        foreach ($items as $item) {
            foreach (self::PRODUCT_PROPERTIES_MAP as $filterKey => $itemKey) {
                if (empty($item[$itemKey])) {
                    continue;
                }
                if (in_array($filterKey, self::MULTIPLE_VALUE_PROPERTIES) && isset($item[$itemKey])) {
                    if (empty($filter[$filterKey])) {
                        $filter[$filterKey] = $item[$itemKey];
                    } else {
                        $filter[$filterKey] = array_merge($filter[$filterKey], $item[$itemKey]);
                    }
                } else {
                    $filter[$filterKey][] = $item[$itemKey];
                }
            }
        }
        foreach (array_keys(self::PRODUCT_PROPERTIES_MAP) as $filterKey) {
            $value = &$filter[$filterKey];
            if (is_array($value)) {
                $value = array_filter(array_values(array_unique($value)), function ($item) {
                    return !empty($item);
                });
                if (count($value) <= 1) {
                    unset($value);
                }
            }
        }

        return $filter;
    }

    public function getAvailableFilterOptions(): array
    {
        if (!$this->loadProductsAndOffers()) {
            return [];
        }
        if (empty($this->items)) {
            $this->getResultItems();
        }
        $filter = [];

        $currentFilter = $this->urlFilter;
        $filterOptions = $this->getFilterArray();
        $items = $this->items;

        foreach ($filterOptions as $filterKey => $filterArray) {
            foreach ($filterArray as $option) {
                $tmpFilter = $currentFilter;
                $tmpFilter[$filterKey] = [];
                $tmpFilter[$filterKey][] = $option;
                if ($this->checkActiveCheckbox($items, $tmpFilter)) {
                    $filter[$filterKey][] = $option;
                }
            }
        }

        return $filter;
    }

    private function checkActiveCheckbox(array $items, array $filter): bool
    {
        $arFilter = empty($filter) ? $this->urlFilter : $filter;

        $comp = false;
        foreach ($items as $item) {
            $comp = true;

            if (isset($arFilter['MIN_PRICE'])) {
                $comp = $item['PRICE'] >= $arFilter['MIN_PRICE'];
            }
            if (isset($arFilter['MAX_PRICE'])) {
                $comp = $comp && $item['PRICE'] <= $arFilter['MAX_PRICE'];
            }
            if (isset($arFilter['MAX_DIAMETER'])) {
                $comp = $comp && $item['PROPERTY_DIAMETER_VALUE'] <= $arFilter['MAX_DIAMETER'];
            }
            if (isset($arFilter['MIN_DIAMETER'])) {
                $comp = $comp && $item['PROPERTY_DIAMETER_VALUE'] >= $arFilter['MIN_DIAMETER'];
            }
            if (isset($arFilter['MAX_LENGTH'])) {
                $comp = $comp && $item['PROPERTY_LENGTH_VALUE'] <= $arFilter['MAX_LENGTH'];
            }
            if (isset($arFilter['MIN_LENGTH'])) {
                $comp = $comp && $item['PROPERTY_LENGTH_VALUE'] >= $arFilter['MIN_LENGTH'];
            }

            if (!$comp) {
                continue;
            }

            foreach ($arFilter as $key => $value) {
                if (array_key_exists($key, self::PRODUCT_PROPERTIES_MAP)) {
                    $comp = $this->applyFilter($value, $item[self::PRODUCT_PROPERTIES_MAP[$key]]);
                    if (!$comp) {
                        break;
                    }
                }
            }
            if ($comp) {
                break;
            }
        }

        return $comp;
    }

    private function sort(array $items): array
    {
        $this->getSortFromRequest();
        switch ($this->sort) {
            case self::SORT_PRICE_DESC:
                $this->sortByPrice($items, 'desc');
                break;
            case self::SORT_PRICE_ASC:
                $this->sortByPrice($items);
                break;
            case self::SORT_NEW:
                $this->sortByNew($items);
                break;
            case self::SORT_POPULAR:
            case self::SORT_DEFAULT:
            default:
                $this->sortByPopular($items);
                break;
        }

        return $items;
    }

    private function getSortFromRequest()
    {
        $sort = self::SORT_DEFAULT;

        $this->arResult['SORT_ARRAY'] = [
            self::SORT_PRICE_DESC => 'Цена по убыванию',
            self::SORT_PRICE_ASC => 'Цена по возрастанию',
//            self::SORT_POPULAR => 'По популярности',
            self::SORT_NEW => 'По новизне',
            self::SORT_DEFAULT => 'По умолчанию',
        ];

        if (!empty($this->arResult['USER_SETTINGS']['SORT']) && array_key_exists($this->arResult['USER_SETTINGS']['SORT'], $this->arResult['SORT_ARRAY'])) {
            $sort = $this->arResult['USER_SETTINGS']['SORT'];
        }

        $urlSort = $this->request->get('sort');
        if (!empty($urlSort) && array_key_exists($urlSort, $this->arResult['SORT_ARRAY'])) {
            $sort = $urlSort;
        }

        if (!empty($sort)) {
            $this->sort = $sort;
        }

        $this->arResult['SELECTED_SORT'] = $this->sort;
    }

    private function sortByPrice(array &$items, string $order = 'asc'): void
    {
        $sortParams = [
            [
                'sort_key' => 'PRICE',
                'order' => $order
            ]
        ];

        uasort($items, function ($a, $b) use ($sortParams) {
            return $this->compareBySortParams($a, $b, $sortParams);
        });
    }

    private function sortByPopular(array &$items): void
    {
        $sortParams = [
            [
                'sort_key' => 'SHOW_COUNTER',
                'order' => 'desc'
            ]
        ];

        uasort($items, function ($a, $b) use ($sortParams) {
            return $this->compareBySortParams($a, $b, $sortParams);
        });
    }

    private function sortByNew(array &$items): void
    {
        $sortParams = [
            [
                'sort_key' => 'XML_ID',
                'sort' => 100,
                'rnd_sort' => 0,
                'order' => 'desc'
            ],
        ];

        uasort($items, function ($a, $b) use ($sortParams) {
            return $this->compareBySortParams($a, $b, $sortParams);
        });
    }

    private function compareBySortParams(&$a, &$b, &$sortParams)
    {
        $lastNotEqualSortParam = false;

        foreach ($sortParams as $sortParam) {
            if (intval($a[$sortParam['sort_key']]) != intval($b[$sortParam['sort_key']])) {
                $lastNotEqualSortParam = $sortParam;
                break;
            }
        }

        if ($lastNotEqualSortParam === false) {
            // Если элементы с одинаковой сортировкой, то сортирует по XML_ID по убыванию
            return $b['XML_ID'] <=> $a['XML_ID'];
        }

        if ($lastNotEqualSortParam['order'] == 'desc') {
            // Направление сортировки "По убыванию"
            return (intval($a[$lastNotEqualSortParam['sort_key']]) > intval($b[$lastNotEqualSortParam['sort_key']])) ? -1 : 1;
        } else {
            // Направление сортировки "По возрастанию"
            return (intval($a[$lastNotEqualSortParam['sort_key']]) < intval($b[$lastNotEqualSortParam['sort_key']])) ? -1 : 1;
        }
    }

    private function getNavNum(): ?int
    {
        $navNum = null;
        if (!empty($_SESSION['CATALOG_SECTION_NAV_NUM'])) {
            return $_SESSION['CATALOG_SECTION_NAV_NUM'];
        }

        return $navNum;
    }

    private function setNavNum(int $navNum)
    {
        $_SESSION['CATALOG_SECTION_NAV_NUM'] = $navNum;
    }

    private function prepareFilterResult()
    {
        if (empty($this->props)) {
            $this->props = $this->getPropertyValues();
        }
        $this->getDisabledOptions();
        $this->prepareFilter();
        $FILTER = [];
        if ($this->type != 'favorites') {
            if ($this->initCache('filter_result')) {
                $FILTER = $this->getCachedVars('filter_result');
            } elseif ($this->startCache()) {
                $this->startTagCache();
                $this->registerTag("catalogAll");
                $FILTER = $this->getFilterValues();
                $this->endTagCache();
                $this->saveToCache('filter_result', $FILTER);
            }
        } else {
            $FILTER = $this->getFilterValues();
        }

        // Сортируем цвета в алфавитном порядке
        uasort($FILTER['COLORS'], function ($a, $b) {
            return strcmp($a["VALUE"]["UF_NAME"], $b["VALUE"]["UF_NAME"]);
        });
        $this->arResult['FILTER'] = $FILTER;
        foreach ($this->disabledOptions as $key => $options) {
            foreach ($options as $option) {
                $this->arResult['FILTER'][$key][$option]['DISABLED'] = true;
            }
        }

        $this->getCheckedOptions();

        $this->arResult['FILTER']['MIN_PRICE'] = $this->filtersScopes['MIN_PRICE'];
        $this->arResult['FILTER']['MAX_PRICE'] = $this->filtersScopes['MAX_PRICE'];
        $this->arResult['FILTER']['MIN_DIAMETER'] = $this->filtersScopes['MIN_DIAMETER'];
        $this->arResult['FILTER']['MAX_DIAMETER'] = $this->filtersScopes['MAX_DIAMETER'];
        $this->arResult['FILTER']['MIN_LENGTH'] = $this->filtersScopes['MIN_LENGTH'];
        $this->arResult['FILTER']['MAX_LENGTH'] = $this->filtersScopes['MAX_LENGTH'];

        $this->arResult['JS_KEYS'] = array_flip(self::PRODUCT_PROPERTIES);
        $this->arResult['FILTER_KEYS'] = self::FILTER_KEYS;
        
        if ($this->type === self::TYPE_SECTION) {
            if ($this->section['DEPTH_LEVEL'] > 1) {
                $this->arResult['SAME_SECTIONS'] = $this->section['SAME_SECTIONS'];
            }
        }
    }

    private function getFilterValues()
    {
        $arFiltersWithValues = [];

        foreach ($this->arResult['FILTER'] as $key => $xml_ids) {
            if (array_key_exists($key, $this->props)) {
                foreach ($xml_ids as $xml_id) {
                    $value = $this->props[$key][$xml_id];
                    if (!empty($value)) {
                        $arFiltersWithValues[$key][$xml_id] = ['VALUE' => $value];
                    }
                }
            } else {
                if (is_array($xml_ids)) {
                    foreach ($xml_ids as $index => $xml_id) {
                        $arFiltersWithValues[$key][$xml_id] = ['VALUE' => $xml_id];
                    }
                } else {
                    $arFiltersWithValues[$key] = $xml_ids;
                }
            }
        }

        return $arFiltersWithValues;
    }

    private function prepareFilter()
    {
        $this->arResult['FILTER'] = $this->getFilterArray();

        foreach ($this->arResult['FILTER'] as $filterKey => $options) {
            if (is_array($options) && count($options) <= 1) {
                unset($this->arResult['FILTER'][$filterKey]);
            }
        }

        sort($this->arResult['FILTER']['SIZES']);
    }

    private function getPropertyValues()
    {
        if ($this->initCache('properties')) {
            $props = $this->getCachedVars('properties')['props'];
        }

        if (empty($props)) {
            $this->startCache();
            $this->startTagCache();
            $this->registerTag("catalogAll");
            $props = [];

            $props['SIZES'] = $this->getEnumProps(IBLOCK_OFFERS, 'SIZE');
            $props['COLORS'] = $this->getColorsFilter();
            $props['MATERIAL'] = $this->getEnumProps(IBLOCK_CATALOG, 'MATERIAL');
            $props['VENDOR'] = $this->getVendorFilter();

            $this->endTagCache();
            $this->saveToCache('properties', [
                'props' => $props,
            ]);
        }

        return $props;
    }

    private function getColorsFilter()
    {
        $arColors = [];
        $obEntity = HL::getEntityClassByHLName('Firecolorreference');

        if ($obEntity && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();
            $rsColors = $sClass::getList(['select' => ['UF_NAME', 'UF_XML_ID', 'UF_FILE']]);

            while ($arColor = $rsColors->fetch()) {
                $arColor['IMG_SRC'] = CFile::GetPath($arColor["UF_FILE"]);
                $arColors[$arColor['UF_XML_ID']] = $arColor;
                unset($arColors[$arColor['UF_XML_ID']]['UF_XML_ID']);
                unset($arColors[$arColor['UF_XML_ID']]['UF_FILE']);
            }
        }
        return $arColors;
    }

    private function getEnumProps($iblockID, $code)
    {
        $arPropsValues = [];
        $propertyEnums = CIBlockPropertyEnum::GetList(
            [
                "SORT" => "ASC"
            ],
            [
                "IBLOCK_ID" => $iblockID,
                "CODE" => $code
            ]
        );

        while($arEnum = $propertyEnums->GetNext())
        {
            $arPropsValues[$arEnum['ID']] = $arEnum['VALUE'];
        }


        return $arPropsValues;
    }

    private function getVendorFilter()
    {
        $arBrands = [];
        $arFilter = [
            'IBLOCK_ID' => IBLOCK_VENDORS,
            'ACTIVE' => 'Y'
        ];

        $rBrands = CIBlockElement::GetList(
            [],
            $arFilter,
            false,
            false,
            [
                'XML_ID',
                'NAME',
            ]
        );

        while ($arBrand = $rBrands->GetNext()) {
            $arBrands[$arBrand['XML_ID']] = $arBrand['NAME'];
        }

        return $arBrands;
    }

    private function needJson(): bool
    {
        return $this->request->isAjaxRequest() && $this->request->get('json') === 'Y';
    }

    private function getCheckedOptions()
    {
        foreach (self::PRODUCT_PROPERTIES as $urlKey => $filterKey) {
            if ($params = $this->urlFilter[$filterKey]) {
                if (self::NUMBER_FILTERS[$urlKey]) {
                    if ($urlKey === 'max_price' || $urlKey === 'min_price') {
                        $this->arResult['FILTER']['CHECKED']['PRICE'] = true;
                    } elseif ($urlKey === 'min_diameter' || $urlKey === 'max_diameter') {
                        $this->arResult['FILTER']['CHECKED']['DIAMETER'] = true;
                    } elseif ($urlKey === 'min_length' || $urlKey === 'max_length') {
                        $this->arResult['FILTER']['CHECKED']['LENGTH'] = true;
                    }
                    $this->arResult['FILTER']['CHECKED'][$filterKey] = $params;
                } else {
                    $this->arResult['FILTER']['CHECKED'][$filterKey] = true;
                    foreach ($params as $param) {
                        $this->arResult['FILTER'][$filterKey][$param]['CHECKED'] = true;
                    }
                }
            }
        }
    }

    private function getSeo()
    {
        global $APPLICATION;
        $seo = [];
        $cache = new CPHPCache();
        if ($cache->InitCache(86400, 'seo|' . $APPLICATION->GetCurPage(), 'seo')) {
            $seo = $cache->GetVars()['seo'];
        } elseif ($cache->StartDataCache()) {
            switch ($this->type) {
                case self::TYPE_SECTION:
                    $ipropValues = new SectionValues(IBLOCK_CATALOG, $this->section['ID']);
                    //$seo['DESCRIPTION'] = $this->section['DESCRIPTION'];
                    //TODO Надо избравиться от этого запроса. Сделано временно.
                    $seo['DESCRIPTION'] = CIBlockSection::GetList([], ['ID' => $this->section['ID']], false, ['DESCRIPTION'], false)->Fetch()['DESCRIPTION'];
                    break;
                case self::TYPE_GROUP:
                    if ($this->isBrand) {
                        $ipropValues = new ElementValues(IBLOCK_VENDORS, $this->group['ID']);
                        $seo['DESCRIPTION'] = $this->group['DESCRIPTION'];
                    }
                    break;
                case self::TYPE_SEARCH:
                    $cache->AbortDataCache();
                    $seo['DESCRIPTION'] = $seo['SECTION_META_TITLE'] = $seo['SECTION_PAGE_TITLE'] = substr(
                        'Поиск: ' . $this->arParams['SEARCH'],
                        0,
                        80
                    );
                    break;
                default:
                    break;
            }

            if (!isset($ipropValues) && !in_array($this->type, [self::TYPE_SEARCH, self::TYPE_SALES, self::TYPE_FAVORITES])) {
                $cache->AbortDataCache();
                return;
            }
            if (isset($ipropValues)) {
                $seo = array_merge($seo, $ipropValues->getValues());
            }

            if (empty($seo['ELEMENT_PAGE_TITLE'])) {
                switch ($this->type) {
                    case self::TYPE_GROUP:
                        $seo['ELEMENT_PAGE_TITLE'] = $this->group['NAME'];
                        break;
                    case self::TYPE_SECTION:
                        $seo['ELEMENT_PAGE_TITLE'] = $this->section['NAME'];
                        break;
                    case self::TYPE_SALES:
                        $seo['ELEMENT_PAGE_TITLE'] = 'Скидки: ' . $this->section['NAME'];
                        break;
                    case self::TYPE_FAVORITES:
                        $seo['ELEMENT_PAGE_TITLE'] = 'Избранное';
                        break;
                    default:
                        break;
                }
            }

            if (!empty($seo)) {
                $cache->EndDataCache(['seo' => $seo]);
            } else {
                $cache->AbortDataCache();
            }
        }

        if (!empty($seo['SECTION_META_KEYWORDS'])) {
            $APPLICATION->SetPageProperty("keywords", $seo['SECTION_META_KEYWORDS']);
        } elseif (!empty($seo['ELEMENT_META_KEYWORDS'])) {
            $APPLICATION->SetPageProperty("keywords", $seo['ELEMENT_META_KEYWORDS']);
        } else {
            $APPLICATION->SetPageProperty("keywords",
                'Купить секс-товары,секс-шоп,скидки,низкой цене, ' . ($this->isBrand ? $this->group['NAME'] : $this->section['NAME'])
            );
        }

        if (!empty($seo['SECTION_META_DESCRIPTION'])) {
            $APPLICATION->SetPageProperty("description", $seo['SECTION_META_DESCRIPTION']);
        } elseif (!empty($seo['ELEMENT_META_DESCRIPTION'])) {
            $APPLICATION->SetPageProperty("description", $seo['ELEMENT_META_DESCRIPTION']);
        } else {
            $APPLICATION->SetPageProperty("description",
                'В Городе Оргазма вы можете не дорого со скидкой купить на выбор любой товар '
                . ($this->isBrand ? 'бренда ' . $this->group['NAME'] : 'из каталога ' . $this->section['NAME'])
            );
        }

        if (!empty($seo['SECTION_PAGE_TITLE'])) {
            $this->arResult['TITLE'] = $seo['SECTION_PAGE_TITLE'];
        } elseif (!empty($seo['ELEMENT_PAGE_TITLE'])) {
            $this->arResult['TITLE'] = $seo['ELEMENT_PAGE_TITLE'];
        }
    }

    private function buildNavChain()
    {
        global $APPLICATION;
        $chain = [];
        $cacheTag = 'nav_chain';
        if ($this->initCache($cacheTag)) {
            $chain = $this->getCachedVars('nav_chain');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");

            $chain = $this->getNavChain();
            if (!empty($chain)) {
                $this->endTagCache();
                $this->saveToCache('nav_chain', $chain);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $title = 'Купить ';
        foreach ($chain as $item) {
            $APPLICATION->AddChainItem($item['title'], $item['url']);
        }
        $title .= mb_strtolower(implode(' ', array_column($chain, 'title')));
        if ($this->isBrand) {
            $title .= ' ' . $this->group['NAME'];
        }
        $title .= ' в интернет магазине orgasmcity.ru';
        $APPLICATION->SetPageProperty('title', $title);
    }

    private function getNavChain()
    {
        $chain = [];
        switch ($this->type) {
            case self::TYPE_SECTION:
            case self::TYPE_SALES:
                $chain = $this->getSectionChain();
                break;
        }

        return $chain;
    }

    private function getSectionChain()
    {
        $chain = [];
        $rsPath = CIBlockSection::GetNavChain(IBLOCK_CATALOG, $this->section['ID'], ['NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL']);
        while ($arPath = $rsPath->GetNext(true, false)) {
            if ($arPath['DEPTH_LEVEL'] == 1) {
                continue;
            }
//            $ipropValues = new SectionValues(IBLOCK_CATALOG, $arPath["ID"]);
            $chain[] = [
                'title' => $this->type == self::TYPE_SALES ? 'Скидки: ' . $arPath['NAME'] : $arPath['NAME'],
                'url' => $this->type == self::TYPE_SALES ? '/catalog/sales' . $arPath['SECTION_PAGE_URL'] : $arPath['SECTION_PAGE_URL'],
            ];
        }
        return $chain;
    }

    private function getSameSections($section): array
    {
        $arSections = [];
        $arFilter = [
            '!ID' => $section['ID'],
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'ACTIVE' => 'Y',
            'SECTION_ID' => $section['IBLOCK_SECTION_ID']
        ];
        $res = CIBlockSection::GetList(false, $arFilter, true, ['ID', 'NAME', 'SECTION_PAGE_URL']);
        while($arResult = $res->GetNext())
        {
            $arSections[] = $arResult;
        }

        return $arSections;
    }

    private function getBrand()
    {
        $brand = false;
        $cacheTag = 'brand_' . $this->code;
        if ($this->initCache($cacheTag)) {
            $brand = $this->getCachedVars('brand');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag("catalogAll");
            $brand = $this->loadBrand();
            if (empty($brand)) {
                $this->abortTagCache();
                $this->abortCache();
            } else {
                $this->endTagCache();
                $this->saveToCache('brand', $brand);
            }
        }

        if (empty($brand)) {
            return Functions::abort404();
        }

        $this->group = $brand;

        return $brand;
    }

    private function loadBrand()
    {
        $brand = CIBlockElement::GetList(
            [],
            [
                "IBLOCK_ID" => IBLOCK_VENDORS,
                'ACTIVE' => 'Y',
                'CODE' => $this->code,
            ],
            false,
            [
                "ID",
                "NAME",
                "CODE",
                "SORT",
                "XML_ID",
                "DESCRIPTION",
                "PICTURE",
                "DETAIL_PICTURE",
                "SECTION_PAGE_URL",
            ]
        )->GetNext(true, false);

        if (empty($brand)) {
            return false;
        }

        $brand['PROPERTY_VENDOR_VALUE'] = $brand['XML_ID'];

        return $brand;
    }
}
