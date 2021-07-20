<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\Component\Tools;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use Bitrix\Main\Type\Collection;
use Bitrix\Main\UserTable;
use Likee\Site\Helper;
use Likee\Site\Helpers\HL;
use Qsoft\Helpers\BonusSystem;
use Qsoft\Helpers\ComponentHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Qsoft\Helpers\PriceUtils;

/**
 * Class QsoftCatalogElement
 *
 *
 *   ████──███─████─███─███
 *   █──█──█───█──█─█────█
 *   █─██──███─█──█─███──█
 *   █──█────█─█──█─█────█
 *   █████─███─████─█────█
 *
 * Небольшая справка:
 *
 * Методы с префиксом load тянут что либо из базы если нужно их закэшировать
 * нужно использовать метод getEntity.
 * Передаются ключ для кэша и метод который вызывается если нету валидного кэша.
 *
 */
class QsoftCatalogElement extends ComponentHelper
{
    protected string $relativePath = '/qsoft/catalog.element';
    private $cacheTag = 'catalogAll';
    private $srcThousand = ['width' => 1000, 'height' => 1000];
    private $srcSizeMedium = ['width' => 600, 'height' => 600];
    private $thumbSize = ['width' => 96, 'height' => 96];
    private $uploadDir;
    private $forSEO;

    public function executeComponent()
    {
        if ($_REQUEST['action'] == 'subscribe') {
            return false;
        }
        $this->checkElement();
        $this->setUploadDir();
        $this->loadModules();
        $this->prepareResult();
        $this->beforeTemplate();
        $this->includeComponentTemplate();
        $this->setMeta();
        return $this->returnElementID();
    }

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        $this->tryParseString($arParams['ELEMENT_CODE']);
        $this->tryParseInt($arParams['CACHE_TIME'], 36000000);
        $arParams["SET_TITLE"] = $arParams["SET_TITLE"] != "N";
        $arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
        $arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"] != "N"; //Turn on by default
        $arParams["ADD_ELEMENT_CHAIN"] = (isset($arParams["ADD_ELEMENT_CHAIN"]) && $arParams["ADD_ELEMENT_CHAIN"] == "Y");
        return $arParams;
    }

    private function checkElement()
    {
        $arItem = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => IBLOCK_CATALOG, 'CODE' => $this->arParams['ELEMENT_CODE']],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'DETAIL_TEXT', 'NAME', 'ACTIVE']
        )->Fetch();
        $this->forSEO['DETAIL_TEXT'] = $arItem['DETAIL_TEXT'];
        $this->forSEO['NAME'] = $arItem['NAME'];

        if (!$arItem['ID'] || ($arItem['ACTIVE'] == 'N')) {
            Tools::process404("", true, true, true, "");
        }
    }

    private function getEntity($key, $method): array
    {
        $arEntity = [];
        if ($this->initCache($key)) {
            $arEntity = $this->getCachedVars($key);
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag($this->cacheTag);
            if ($key == 'iprops') {
                $this->registerTag('SEO_META');
            }
            $arEntity = $this->$method();
            if (!empty($arEntity)) {
                $this->endTagCache();
                $this->saveToCache($key, $arEntity);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }
        return $arEntity;
    }

    private function setUploadDir(): void
    {
        $this->uploadDir = '/' . COption::GetOptionString("main", "upload_dir", "upload") . '/';
    }

    private function loadModules(): void
    {
        try {
            Loader::includeModule('iblock');
        } catch (LoaderException $e) {
            ShowError(Loc::getMessage('MODULE_INCLUDE_ERROR', ["#MODULE#" => Loc::getMessage('MODULE_IBLOCK')]));
        }
        try {
            Loader::includeModule('catalog');
        } catch (LoaderException $e) {
            ShowError(Loc::getMessage('MODULE_INCLUDE_ERROR', ["#MODULE#" => Loc::getMessage('MODULE_CATALOG')]));
        }
    }

    private function loadProduct(): array
    {
        $arOrder = [];
        $arFilter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'CODE' => $this->arParams['ELEMENT_CODE'],
        ];
        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "NAME",
            "XML_ID",
            "DETAIL_PICTURE",
            "PREVIEW_PICTURE",
            "DETAIL_TEXT",
            "SORT",
            "IBLOCK_SECTION_ID",
            "SHOW_COUNTER"
        ];

        $rsElement = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        $oElement = $rsElement->GetNextElement();
        $arElement = $oElement->GetFields();
        $arElement['PROPERTIES'] = $oElement->GetProperties();
        $arElement['BRAND_PAGE'] = $this->getBrandPage($arElement['PROPERTIES']['vendor']['VALUE']);
        return $arElement;
    }

    private function loadOffers(): array
    {
        $arOffers = [];
        $arOrder = [];
        $arFilter = [
            'IBLOCK_ID' => IBLOCK_OFFERS,
            'ACTIVE' => 'Y',
            'PROPERTY_CML2_LINK' => $this->arResult['ID'],
        ];
        $arSelect = [
            'ID',
            'NAME',
            'IBLOCK_ID',
            'XML_ID'
        ];
        $rsOffers = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($objOffer = $rsOffers->GetNextElement()) {
            $basePrice = $objOffer->GetProperties(
                [],
                ['CODE' => 'BasePrice']
            )['BasePrice'];
            $baseWholePrice = $objOffer->GetProperties(
                [],
                ['CODE' => 'BasewholePrice']
            )['BasewholePrice'];
            if (!$basePrice['VALUE'] || !$baseWholePrice['VALUE']) {
                continue;
            }
            $price = PriceUtils::getPrice($baseWholePrice['VALUE'], $basePrice['VALUE']);
            $basePrice['OLD_VALUE'] = $price['OLD_PRICE'];
            $basePrice['VALUE'] = $price['PRICE'];
            $basePrice['PERCENT'] = $price['DISCOUNT'];
            $offerFields = $objOffer->GetFields();
            $arOffers[$offerFields['ID']] = $offerFields;
            $arOffers[$offerFields['ID']]['PROPERTIES']['PRICE'] = $basePrice;
            $arOffers[$offerFields['ID']]['PROPERTIES']['COLOR'] = $objOffer->GetProperties(
                [],
                ['CODE' => 'color']
            )['color'];
            $arOffers[$offerFields['ID']]['PROPERTIES']['SIZE'] = $objOffer->GetProperties(
                [],
                ['CODE' => 'size']
            )['size'];
        }
        return $arOffers;
    }

    private function loadInheritedProperties(): array
    {
        $ipropValues = new ElementValues($this->arParams["IBLOCK_ID"], $this->arResult["ID"]);
        $arIProps = $ipropValues->getValues();
        return $arIProps;
    }

    private function getDisplayProperties(): array
    {
        $arProps = [];

        $arPropsToShow = [
            "article",
            "diameter",
            "length",
            "bestseller",
            "vendor",
            "volume",
            "material",
            "collection",
            "batteries",
            'material',
            "function",
            "vibration",
            "year",
        ];

        foreach ($arPropsToShow as $prop_code) {
            if (!empty($this->arResult['PROPERTIES'][$prop_code]['VALUE'])) {
                $arProps[$prop_code] = $this->arResult['PROPERTIES'][$prop_code];
            }
        }

        foreach ($arProps as $iKey => &$arProp) {
            if (empty($arProp['VALUE'])) {
                unset($arProps[$iKey]);
                continue;
            }

            if (is_array($arProp['VALUE'])) {
                $arProp['VALUE'] = $arProp['VALUE'][0];
            }

            if (in_array($arProp['CODE'], ['length', 'diameter'])) {
                $arProp['VALUE'] = (float)$arProp['VALUE'] . 'см';
            }

            if ($arProp['CODE'] == 'vibration') {
                $arProp['VALUE'] = $arProp['VALUE'] ? 'Да' : 'Нет';
            }

            if ($arProp['CODE'] == 'vendor') {
                $vendor = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => IBLOCK_VENDORS,
                        'ACTIVE' => 'Y',
                        'XML_ID' =>  $arProp['VALUE'],
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'NAME',
                        'IBLOCK_ID',
                        'CODE'
                    ])->GetNext();
                $arProp['VALUE'] = $vendor['NAME'];

                $arProp['CODE_VALUE'] = $vendor['CODE'];
            }
        }

        return $arProps;
    }

    private function prepareResult(): void
    {
        $this->arResult = $this->getEntity('product', 'loadProduct');
        $this->arResult['NAME'] = $this->forSEO['NAME'];
        $this->arResult['DETAIL_TEXT'] = $this->forSEO['DETAIL_TEXT'];
        $this->arResult['OFFERS'] = $this->loadOffers();
        $this->arResult['AVAILABLE_OFFER_PROPS'] = $this->getAvailableProps();
        $this->arResult['OFFERS'] = $this->filterOffersByRests($this->arResult['OFFERS']);
        $this->arResult['IPROPERTY_VALUES'] = $this->getEntity('iprops', 'loadInheritedProperties');
        $this->arResult['PHOTOS'] = $this->getEntity('images', 'loadImages');
        $this->arResult['DISPLAY_PROPERTIES'] = $this->getDisplayProperties();
        $this->arResult['PATH'] = $this->getEntity('path', 'loadSectionsPath');
//        $this->arResult['SECTION_SIZES_TAB'] = reset($this->getEntity('size_table', 'loadSizesTable'));
        $this->arResult['USER'] = $this->loadUserData();
        $this->arResult['ARTICLE'] = trim($this->arResult['PROPERTIES']['article']['VALUE']);
        $this->arResult['PICTURES_COUNT'] = count($this->arResult['PICTURES']);

        $this->arResult['SINGLE_SIZE'] = (count($this->arResult['OFFERS']) === 1)
        && !reset($this->arResult['OFFERS'])['PROPERTIES']['SIZE']['VALUE']
        && !reset($this->arResult['OFFERS'])['PROPERTIES']['COLOR']['VALUE'] ? key($this->arResult['OFFERS']) : false;
        // минимальная цена товара по офферам
        $this->arResult['MIN_PRICE_OFFER'] = $this->getMinPriceOffer();

        $this->arResult['SHOW_ONE_CLICK'] = $this->getShowOneClick();
        $this->arParams['OFFER_IBLOCK_ID'] = IBLOCK_OFFERS;

        $this->arResult['ELEMENT_BREADCRUMB'] = $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
        if (strlen($this->arResult['ARTICLE']) > 0) {
            $this->arResult['ELEMENT_BREADCRUMB'] = Loc::getMessage("ARTICLE") . $this->arResult['ARTICLE'];
        }

        $this->arResult["FAVORITES"] = $this->checkFavorites($this->arResult['ID']);

        $this->arResult['USER_DISCOUNT'] = 0;
        global $USER;
        if ($USER->IsAuthorized()) {
            $bonusSystemHelper = new BonusSystem($USER->GetID());
            $this->arResult['USER_DISCOUNT'] = $bonusSystemHelper->getCurrentBonus();
        }
    }

    private function beforeTemplate(): void
    {
        $GLOBALS['ELEMENT_BREADCRUMB'] = $this->arResult['ELEMENT_BREADCRUMB'];
        $GLOBALS['CATALOG_ELEMENT_ID'] = sprintf('%s-%s', $this->arResult['ID'], $this->arResult['BRANCH_ID']);
        Helper::addBodyClass('page--product');
        $this->includeStyles();
        $this->includeScripts();
        $this->checkAndSetNoIndex();
    }

    private function getImageArray(array $arImage): array
    {
        $k = $arImage['WIDTH'] / $arImage['HEIGHT'];
        $srcSizeMedium = [
            'width' => $k < 1 ? $k * $this->srcSizeMedium['height'] : $this->srcSizeMedium['height'],
            'height' => $this->srcSizeMedium['height'],
        ];
        $srcThousand = [
            'width' => $k < 1 ? $k * $this->srcThousand['height'] : $this->srcThousand['height'],
            'height' => $this->srcThousand['height'],
        ];
        $arSRCMedium = Functions::ResizeImageGet($arImage, $srcSizeMedium);
        $arTHUMB = Functions::ResizeImageGet($arImage, $this->thumbSize);
        $arSRCThousand = Functions::ResizeImageGet($arImage, $srcThousand);

        $arImage['ALT'] = $this->arResult['NAME']; //TODO Собрать нормальный ALT
        $arImage['SRC_ORIGINAL'] = $arSRCThousand['src'] ?: $this->uploadDir . $arImage["SUBDIR"] . "/" . $arImage["FILE_NAME"];
        $arImage['SRC_MEDIUM'] = $arSRCMedium['src'] ?: $this->uploadDir . $arImage["SUBDIR"] . "/" . $arImage["FILE_NAME"];
        $arImage['THUMB'] = $arTHUMB['src'];
        return $arImage;
    }

    private function loadImages(): array
    {
        $arImages = [];
        if ($this->arResult['PROPERTIES']['pics']['VALUE'] != '') {
            $arImagesIDs = $this->arResult['PROPERTIES']['pics']['VALUE'];
        } else {
            $arImagesIDs = [$this->arResult['DETAIL_PICTURE'], $this->arResult['PREVIEW_PICTURE']];
        }
        if (!empty($arImagesIDs)) {
            $rsImages = CFile::GetList([], ['@ID' => $arImagesIDs]);
            while ($arImage = $rsImages->Fetch()) {
                $arImages[$arImage['ID']] = $this->getImageArray($arImage);
            }
        } else {
            $arImages['SRC'] = Helper::getEmptyImg(650, 650);
            $arImages['ALT'] = $this->arResult['NAME']; //TODO Собрать нормальный ALT
        }
        ksort($arImages, SORT_NATURAL);
        return $arImages;
    }

    private function includeStyles(): void
    {
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slider-pro.css');
        Asset::getInstance()->addCss('//api.fittin.ru/admin/api/lumen/public/css/style.css');
    }

    private function includeScripts(): void
    {
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.sliderPro.js');
    }

    private function checkAndSetNoIndex(): void
    {
        if (empty($this->arResult['PROPERTIES']['NAME_FOR_INTERNET_SHOP']['VALUE'])
            || empty($this->arResult['DETAIL_PICTURE'])
            && empty($this->arResult['PREVIEW_PICTURE'])) {
            $GLOBALS['NO_INDEX'] = 'noindex';
        }
    }

    private function loadSectionsPath(): array
    {
        $arSectionPath = [];
        $rsPath = CIBlockSection::GetNavChain(
            IBLOCK_CATALOG,
            $this->arResult['IBLOCK_SECTION_ID'],
            ['NAME', 'SECTION_PAGE_URL', 'ID', 'DEPTH_LEVEL']
        );
        while ($arPath = $rsPath->GetNext()) {
            $ipropValues = new SectionValues($this->arParams["IBLOCK_ID"], $arPath["ID"]);
            $arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
            $arSectionPath[] = $arPath;
        }
        return $arSectionPath;
    }

    private function loadSizesTable(): array
    {
        if (!empty(reset($this->arResult['PATH'])['ID'])) {
            $sizes_tab = '';
            $arOrder = [];
            $arFilter = [
                'ID' => reset($this->arResult['PATH'])['ID'],
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
            ];
            $arSelect = ['UF_SIZES_TAB_HTML'];
            $rsSection = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect, false);
            $arSection = $rsSection->Fetch();

            if (strlen($arSection["UF_SIZES_TAB_HTML"]) > 0) { //проверяем что поле заполнено
                $tabInfo = CIBlockElement::GetByID($arSection["UF_SIZES_TAB_HTML"]);
                if ($ar_resInfo = $tabInfo->GetNext()) {
                    $sizes_tab = $ar_resInfo['PREVIEW_TEXT'];
                }
            }
        }

        return [$sizes_tab];
    }

    private function loadUserData(): array
    {
        global $USER;
        $arUser = [];
        if ($USER->IsAuthorized()) {
            $arUser = UserTable::getList(array(
                "select" => array(
                    "PERSONAL_PHONE",
                    "LAST_NAME",
                    "SECOND_NAME",
                    "NAME",
                    'EMAIL',
                ),
                "filter" => array(
                    "ID" => $USER->GetID(),
                ),
                "limit" => 1,
            ))->Fetch();
            $arUser['FIO'] = str_replace(
                '  ',
                ' ',
                $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'] . ' ' . $arUser['LAST_NAME']
            );
        }
        return $arUser;
    }

    private function checkFavorites($productId)
    {
        global $USER;
        if ($USER->IsAuthorized()) { // Для авторизованного получаем из User
            $arUser = $USER->GetByID($USER->GetID())->Fetch();
            $favoritesId = array_flip($arUser['UF_FAVORITES']);
        } else {
            if (isset($_COOKIE['favorites'])) {
                $favoritesId = unserialize($_COOKIE['favorites']);
            }
        }
        return isset($favoritesId[$productId]) ? true : false;
    }

    private function setMeta(): void
    {
        global $APPLICATION;
        $section = $this->arResult['PATH'];
        $section = array_pop($section);
        $GLOBALS['SEO_PAGE_ELEMENT'] = true;
        $GLOBALS['SEO_ELEMENT_ARTICLE'] = $this->arResult['ARTICLE'];
        $GLOBALS['SEO_ELEMENT_NAME'] = $this->arResult['NAME'];
        $GLOBALS['SEO_CURRENT_PAGE'] = $section['SECTION_PAGE_URL'];

        if ($this->arParams["SET_TITLE"]
            || $this->arParams["ADD_ELEMENT_CHAIN"]
            || $this->arParams["SET_BROWSER_TITLE"] === 'Y'
            || $this->arParams["SET_META_KEYWORDS"] === 'Y'
            || $this->arParams["SET_META_DESCRIPTION"] === 'Y'
        ) {
            $this->arResult["META_TAGS"] = [];

            if ($this->arParams["SET_TITLE"]) {
                $this->arResult["META_TAGS"]["TITLE"] =
                    $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                    ?: "Купить " . mb_strtolower($this->arResult["NAME"]);
            }

            if ($this->arParams["ADD_ELEMENT_CHAIN"]) {
                $this->arResult["META_TAGS"]["ELEMENT_CHAIN"] = $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] ?: $this->arResult["NAME"];
            }

            if ($this->arParams["SET_BROWSER_TITLE"] === 'Y') {
                $browserTitle = Collection::firstNotEmpty(
                    $this->arResult["PROPERTIES"],
                    array($this->arParams["BROWSER_TITLE"], "VALUE"),
                    $this->arResult,
                    $this->arParams["BROWSER_TITLE"],
                    $this->arResult["IPROPERTY_VALUES"],
                    "ELEMENT_META_TITLE"
                );
                $this->arResult["META_TAGS"]["BROWSER_TITLE"] = (
                is_array($browserTitle)
                    ? implode(" ", $browserTitle)
                    : $browserTitle
                );
                unset($browserTitle);
            }
            if ($this->arParams["SET_META_KEYWORDS"] === 'Y') {
                $metaKeywords = Collection::firstNotEmpty(
                    $this->arResult["PROPERTIES"],
                    array($this->arParams["META_KEYWORDS"], "VALUE"),
                    $this->arResult["IPROPERTY_VALUES"],
                    "ELEMENT_META_KEYWORDS"
                );
                $this->arResult["META_TAGS"]["KEYWORDS"] = (
                is_array($metaKeywords)
                    ? implode(" ", $metaKeywords)
                    : $metaKeywords
                );
                unset($metaKeywords);
            }
            if ($this->arParams["SET_META_DESCRIPTION"] === 'Y') {
                $metaDescription = Collection::firstNotEmpty(
                    $this->arResult["PROPERTIES"],
                    array($this->arParams["META_DESCRIPTION"], "VALUE"),
                    $this->arResult["IPROPERTY_VALUES"],
                    "ELEMENT_META_DESCRIPTION"
                );
                $this->arResult["META_TAGS"]["DESCRIPTION"] = (
                is_array($metaDescription)
                    ? implode(" ", $metaDescription)
                    : $metaDescription
                );
                unset($metaDescription);
            }
        }

        if ($this->arParams["SET_TITLE"]) {
            $APPLICATION->SetTitle($this->arResult["META_TAGS"]["TITLE"]);
        }

        if ($this->arParams["SET_BROWSER_TITLE"] === 'Y') {
            if ($this->arResult["META_TAGS"]["BROWSER_TITLE"]) {
                $APPLICATION->SetPageProperty("title", $this->arResult["META_TAGS"]["BROWSER_TITLE"]);
            }
        }

        pre($this->getKeywordsByString($this->arResult['NAME']));
        if ($this->arParams["SET_META_KEYWORDS"] === 'Y') {
            if ($this->arResult["META_TAGS"]["KEYWORDS"]) {
                $APPLICATION->SetPageProperty("keywords", $this->arResult["META_TAGS"]["KEYWORDS"]);
            } else {
                $APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS . ', ' . $this->getKeywordsByString($this->arResult['NAME']));
            }
        }

        if ($this->arParams["SET_META_DESCRIPTION"] === 'Y') {
            if ($this->arResult["META_TAGS"]["DESCRIPTION"]) {
                $APPLICATION->SetPageProperty("description", $this->arResult["META_TAGS"]["DESCRIPTION"]);
            } else {
                $APPLICATION->SetPageProperty("description", mb_strimwidth($this->arResult['DETAIL_TEXT'], 0, 150, "..."));
            }
        }

        if (!empty($seo['ELEMENT_PAGE_TITLE'])) {
            $APPLICATION->SetTitle($seo['ELEMENT_PAGE_TITLE']);
        }
        if (!empty($seo['ELEMENT_META_TITLE']) || !empty($seo['SECTION_META_TITLE'])) {
            $APPLICATION->SetPageProperty("title", $seo['ELEMENT_META_TITLE'] ?: $seo['SECTION_META_TITLE']);
        }
        if (!empty($seo['ELEMENT_META_KEYWORDS']) || !empty($seo['SECTION_META_KEYWORDS'])) {
            $APPLICATION->SetPageProperty("keywords", $seo['ELEMENT_META_KEYWORDS'] ?: $seo['SECTION_META_KEYWORDS']);
        }

        if (!empty($seo['ELEMENT_META_DESCRIPTION']) || !empty($seo['SECTION_META_DESCRIPTION'])) {
            $APPLICATION->SetPageProperty(
                "description",
                $seo['ELEMENT_META_DESCRIPTION'] ?: $seo['SECTION_META_DESCRIPTION']
            );
        }

        if ($this->arParams["ADD_SECTIONS_CHAIN"] && !empty($this->arResult["PATH"]) && is_array($this->arResult["PATH"])) {
            foreach ($this->arResult["PATH"] as $arPath) {
                if ($arPath['DEPTH_LEVEL'] == 1) {
                    continue;
                }
                if ($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "") {
                    $APPLICATION->AddChainItem($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arPath["~SECTION_PAGE_URL"]);
                } else {
                    $APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
                }
            }
        }
        if ($this->arParams["ADD_ELEMENT_CHAIN"]) {
            $APPLICATION->AddChainItem($this->arResult["META_TAGS"]["ELEMENT_CHAIN"]);
        }
    }

    private function getKeywordsByString($string)
    {
        $keywords = '';
        $explode = explode(' ', $string);
        $additional = '';
        foreach ($explode as $key => $item) {
            if ($item == '-' || preg_match("/[\d]+/im", $item)) {
                continue;
            }
            if (!in_array($item, ['без', 'на', 'из', 'к', 'в', 'с'])) {
                $keywords .= ($additional ? $additional . ' ' : '') . $item . ', ';
                $additional = '';
            } else {
                $additional = $item;
            }
        }

        return rtrim($keywords, ', ');
    }

    private function returnElementID(): int
    {
        if (isset($this->arResult['ID'])) {
            return $this->arResult['ID'];
        } else {
            return 0;
        }
    }

    private function getShowOneClick()
    {
        return false;
    }

    private function getMinPriceOffer()
    {
        $minPrice = 10000000;
        $offerWithMinPrice = null;
        foreach ($this->arResult['OFFERS'] as $offer) {
            if (isset($offer['PROPERTIES']['PRICE']['VALUE']) && $offer['PROPERTIES']['PRICE']['VALUE'] < $minPrice) {
                $minPrice = $offer['PROPERTIES']['PRICE']['VALUE'];
                $offerWithMinPrice = $offer;
            }
        }

        if ($minPrice === 10000000) {
            return null;
        }

        return $offerWithMinPrice;
    }

    private function getBrandPage($brandXml)
    {
        $res = CIBlockElement::GetList(
            [
                'NAME' => 'ASC',
            ],
            [
                "IBLOCK_ID" => IBLOCK_VENDORS,
                'ACTIVE' => 'Y',
                'XML_ID' => $brandXml,
            ],
            false,
            [
                "ID",
                "NAME",
                "CODE",
            ]
        );

        if ($brand = $res->GetNext(true, false)) {
            return '/brands/' . $brand["CODE"] . '/';
        } else {
            return false;
        }
    }

    private function loadColors()
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

    private function getAvailableProps()
    {
        $colors = $this->getEntity('colors', 'loadColors');
        $props = [
            'SIZES' => [],
            'COLORS' => [],
        ];
        foreach ($this->arResult['OFFERS'] as $offer) {
            if ($offer['PROPERTIES']['SIZE']['VALUE'] && !in_array($offer['PROPERTIES']['SIZE']['VALUE'], $props['SIZES'])) {
                $props['SIZES'][] = $offer['PROPERTIES']['SIZE']['VALUE'];
            }
            if ($offer['PROPERTIES']['COLOR']['VALUE'] && !isset($props['COLORS'][$offer['PROPERTIES']['COLOR']['VALUE']])) {
                $props['COLORS'][$offer['PROPERTIES']['COLOR']['VALUE']] = [
                    'NAME' => $colors[$offer['PROPERTIES']['COLOR']['VALUE']]['UF_NAME'],
                    'IMG_SRC' => $colors[$offer['PROPERTIES']['COLOR']['VALUE']]['IMG_SRC'],
                ];
            }
        }

        return $props;
    }

    private function filterOffersByRests($offers)
    {
        // Фильтруем по остаткам
        $arRests = Functions::getRests(array_keys($offers));
        foreach ($offers as $id => $offer) {
            if (!isset($arRests[$id])) {
                unset($offers[$id]);
            }
        }
        return $offers;
    }
}
