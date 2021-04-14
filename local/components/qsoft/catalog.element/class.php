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
use Qsoft\Helpers\ComponentHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

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
    private bool $isPreorder = false;

    public function executeComponent()
    {
        if ($_REQUEST['action'] == 'subscribe') {
            return false;
        }
        $this->checkElement();
        $this->setUploadDir();
        $this->loadModules();
        $this->prepareResult();
        $this->checkElementResult();
        $this->beforeTemplate();
        $this->forFlocktory();
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
            ['ID', 'IBLOCK_ID', 'DETAIL_TEXT', 'NAME', 'ACTIVE', 'PROPERTY_PREORDER']
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
//            if (!empty($arEntity)) {
//                $this->endTagCache();
//                $this->saveToCache($key, $arEntity);
//            } else {
//                $this->abortTagCache();
//                $this->abortCache();
//            }
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
            'ACTIVE' => $this->isPreorder ? '' : 'Y',
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'CODE' => $this->arParams['ELEMENT_CODE'],
        ];
        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "NAME",
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
            'IBLOCK_ID'
        ];
        $rsOffers = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($objOffer = $rsOffers->GetNextElement()) {
            $offerFields = $objOffer->GetFields();
            $arOffers[$offerFields['ID']] = $offerFields;
            $arOffers[$offerFields['ID']]['PROPERTIES']['COLOR'] = $objOffer->GetProperties(
                [],
                ['CODE' => 'color']
            );
            $arOffers[$offerFields['ID']]['PROPERTIES']['SIZE'] = $objOffer->GetProperties(
                [],
                ['CODE' => 'size']
            );
        }
        return $arOffers;
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function loadRests(): array
    {
        $arRests = [];

        $rsStoreProduct = \Bitrix\Catalog\ProductTable::getList(
            [
                'filter' => [
                    'ID' => array_keys($this->arResult['OFFERS'])
                ],
                'select' => ['ID', 'QUANTITY']
            ],
        );
        while ($arStoreProduct = $rsStoreProduct->fetch()) {
            $arRests[$arStoreProduct['ID']] = $arStoreProduct['QUANTITY'];
        }

        return $arRests;
    }

    private function checkElementResult(): void
    {
        \Likee\Site\Helpers\Catalog::checkElementResult($this->arResult);
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

        $arPropsForGTM = [
            'COLLECTION',
            'COLORSFILTER',
            'SUBTYPEPRODUCT',
            'TYPEPRODUCT',
            'VID',
        ];

        $arNeedleProps = array_merge($this->arParams['PROPERTY_CODE'], $arPropsForGTM);

        foreach ($arNeedleProps as $prop_code) {
            if (!empty($this->arResult['PROPERTIES'][$prop_code]['VALUE'])) {
                $arProps[$prop_code] = $this->arResult['PROPERTIES'][$prop_code];
            }
        }

        foreach ($arProps as $iKey => &$arProp) {
            if (empty($arProp['VALUE'])) {
                unset($arProps[$iKey]);
                continue;
            }

            is_array($arProp['VALUE']) ? $arProp['VALUE'] = $arProp['VALUE'][0] : '';

            if ($arProp['PROPERTY_TYPE'] == 'S') {
                $arProp['VALUE'] = HL::getFieldValueByProp($arProp, 'UF_NAME');
                $tooptipON = HL::getFieldValueByProp($arProp, 'UF_TOOLTIP_ON');
                if ($tooptipON) {
                    $arProp['TOOLTIP'] = HL::getFieldValueByProp($arProp, 'UF_TOOLTIP');

                    if (!empty($arProp['TOOLTIP'])) {
                        $this->arResult['AR_PROPS_TOOLTIP'][$arProp['CODE']] = $arProp['TOOLTIP'];
                    }
                }
            }
        }

        if ($arProps['RHODEPRODUCT']['VALUE'] != 'Женские') {
            unset($arProps['HEELHEIGHT']);
        } elseif ($arProps['HEELHEIGHT']['VALUE'] || $arProps['HEELHEIGHT']['VALUE'] === 0) {
            $arProps['HEELHEIGHT']['VALUE'] = $arProps['HEELHEIGHT']['VALUE'] === 0 || $arProps['HEELHEIGHT']['VALUE'] == 'Без каблука' ? 'Без каблука' : (string)($arProps['HEELHEIGHT']['VALUE'] / 10) . ' см';
        } else {
            unset($arProps['HEELHEIGHT']);
        }

        $this->arResult['PROPS_GTM'] = $arProps;
        $arProps = array_intersect_key($arProps, array_flip($this->arParams['PROPERTY_CODE']));

        unset($arProp);

        return $arProps;
    }

    private function getSizesProperties()
    {
        $arSizes = [];
        if ($this->arResult['PROPERTIES']['LENGTH']['VALUE']) {
            $arSizes['LENGTH'] = $this->arResult['PROPERTIES']['LENGTH']['VALUE'];
        }
        if ($this->arResult['PROPERTIES']['HEIGHT']['VALUE']) {
            $arSizes['HEIGHT'] = $this->arResult['PROPERTIES']['HEIGHT']['VALUE'];
        }
        if ($this->arResult['PROPERTIES']['WIDTH']['VALUE']) {
            $arSizes['WIDTH'] = $this->arResult['PROPERTIES']['WIDTH']['VALUE'];
        }
        return $arSizes;
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function prepareResult(): void
    {
        $this->arResult = $this->getEntity('product', 'loadProduct');
        $this->arResult['NAME'] = $this->forSEO['NAME'];
        $this->arResult['DETAIL_TEXT'] = $this->forSEO['DETAIL_TEXT'];
        $this->arResult['OFFERS'] = $this->getEntity('offers', 'loadOffers');
        $this->arResult['RESTS'] = $this->loadRests();
        $this->arResult['IPROPERTY_VALUES'] = $this->getEntity('iprops', 'loadInheritedProperties');
        $this->arResult['PHOTOS'] = $this->getEntity('images', 'loadImages');
        $this->arResult['DISPLAY_PROPERTIES'] = $this->getDisplayProperties();
        $this->arResult['SIZES_PROPERTIES'] = $this->getSizesProperties();
        $this->arResult['PATH'] = $this->getEntity('path', 'loadSectionsPath');
        $this->arResult['SECTION_SIZES_TAB'] = reset($this->getEntity('size_table', 'loadSizesTable'));
        $this->arResult['USER'] = $this->loadUserData();
        $this->arResult['ARTICLE'] = trim($this->arResult['PROPERTIES']['ARTICLE']['VALUE']);

        $this->arResult['PICTURES_COUNT'] = count($this->arResult['PICTURES']);

        $key = key($this->arResult['RESTS']['ALL']);
        $this->arResult['SINGLE_SIZE'] = (count($this->arResult['RESTS']['ALL']) == 1 && $this->arResult['RESTS']['ALL'][$key]['SIZE'] === true) ? $key : false;
        //цена товара
        $this->arResult['PRICE_PRODUCT'] = $this->getPrice();

        $this->arResult['SHOW_ONE_CLICK'] = $this->getShowOneClick();
        $this->arParams['OFFER_IBLOCK_ID'] = IBLOCK_OFFERS;

        $this->arResult['ELEMENT_BREADCRUMB'] = $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
        if (strlen($this->arResult['ARTICLE']) > 0) {
            $this->arResult['ELEMENT_BREADCRUMB'] = Loc::getMessage("ARTICLE") . $this->arResult['ARTICLE'];
        }

        $this->arResult["ONLINE_TRY_ON"] = $this->arResult['PROPERTIES']['ONLINE_TRY_ON']['VALUE'] === 'Y';
        unset($this->arResult['PROPERTIES']['ONLINE_TRY_ON']);
        unset($this->arResult['DISPLAY_PROPERTIES']['ONLINE_TRY_ON']);

        $this->arResult["FAVORITES"] = $this->checkFavorites($this->arResult['ID']);
    }

    private function beforeTemplate(): void
    {
        $GLOBALS['ELEMENT_BREADCRUMB'] = $this->arResult['ELEMENT_BREADCRUMB'];
        $GLOBALS['CATALOG_ELEMENT_ID'] = sprintf('%s-%s', $this->arResult['ID'], $this->arResult['BRANCH_ID']);
        Helper::addBodyClass('page--product');
        $this->includeStyles();
        $this->includeScripts();
        $this->checkAndSetNoIndex();
        $this->arResult['IS_PREORDER'] = $this->isPreorder;
    }

    private function forFlocktory(): void
    {
        $arFlocktory = array(
            'PRICE' => $this->arResult["PRICE_PRODUCT"][$this->arResult["ID"]]["PRICE"],
            'CATEGORY_ID' => $this->arResult["IBLOCK_SECTION_ID"],
            'BRAND' => $this->arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"],
            'QUANTITY' => 1,
        );

        $this->arResult["FLOCKTORY"] = $arFlocktory;
    }

    private function getImageArray(array $arImage): array
    {
        $arSRCMedium = Functions::ResizeImageGet($arImage, $this->srcSizeMedium);
        $arTHUMB = Functions::ResizeImageGet($arImage, $this->thumbSize);
        $arSRCThousand = Functions::ResizeImageGet($arImage, $this->srcThousand);

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
        global $USER;
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.sliderPro.js');

        if ($this->arResult["ONLINE_TRY_ON"] && $USER->IsAuthorized()) {
            $sizes = [];

            foreach ($this->arResult['RESTS']['ALL'] as $key => $item) {
                $sizeDelivery = false;
                $sizeReservation = false;
                $sizeIsLocal = 'Y';

                if (!empty($this->arResult['RESTS']['DELIVERY'][$key])) {
                    $sizeDelivery = true;
                    $sizeIsLocal = $this->arResult['RESTS']['DELIVERY'][$key]['IS_LOCAL'];
                }
                if (!empty($this->arResult['RESTS']['RESERVATION'][$key])) {
                    $sizeReservation = true;
                }

                $sizes[$item['SIZE']] = [
                    $key,
                    $sizeDelivery,
                    $sizeReservation,
                    $sizeIsLocal,
                ];

                $jsonSizes = json_encode($sizes);
            }

            $asset = Asset::getInstance();
            $widgetInitialization = "<script>
            $(document).ready(function() {
                let options = {
                    customerId: {$USER->GetID()},
                    productId: {$this->arResult['ID']},
                    source: 'respect',
                    sizes: {$jsonSizes},
                    callbacks: {
                        addToCart:
                            function (offerId, isLocal) {
                                console.log('Добавить в корзину . ID #' + offerId + ', isLocal=' + isLocal);
                                basketHandler(offerId, isLocal);
                            },
                        reserve:
                            function (offerId) {
                                console.log('Зарезервировать. ID #' + offerId);
                                reserveHandler(offerId);
                            }
                    }
                };
                $('#fittin_widget_button').fittin(options);
            });
            </script>";
            // Использован addString() вместо addJs(), чтобы подключить скрипт виджета примерки после подключения jQuery
            $asset->addString('<script type="text/javascript" src="https://widget.fittin.ru/widget.js"></script>', false, AssetLocation::AFTER_JS);
            $asset->addString($widgetInitialization, false, AssetLocation::AFTER_JS);
            //$asset->addCss('//api.fittin.ru/admin/api/lumen/public/css/style.css');
        }
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
        $arSectionPath = array();
        $rsPath = CIBlockSection::GetNavChain(
            $this->arParams["IBLOCK_ID"],
            $this->arResult['IBLOCK_SECTION_ID'],
            array("ID", "SECTION_PAGE_URL")
        );
        $rsPath->SetUrlTemplates("", $this->arParams["SECTION_URL"]);
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
        global $LOCATION;
        $section = $this->arResult['PATH'];
        $section = array_pop($section);
        $GLOBALS['SEO_PAGE_ELEMENT'] = true;
        $GLOBALS['SEO_ELEMENT_ARTICLE'] = $this->arResult['ARTICLE'];
        $GLOBALS['SEO_ELEMENT_NAME'] = $this->arResult['NAME'];
        $GLOBALS['SEO_CURRENT_PAGE'] = $section['SECTION_PAGE_URL'];
        if ($LOCATION->getPoddomen(false, true)) {
            $seo = $LOCATION->getPoddomenSeo($section['SECTION_PAGE_URL']);
            $seo = str_replace("#ARTICLE#", $this->arResult['ARTICLE'], $seo);
            $seo = str_replace("#NAME#", $this->arResult['NAME'], $seo);
        }

        if ($this->arParams["SET_TITLE"]
            || $this->arParams["ADD_ELEMENT_CHAIN"]
            || $this->arParams["SET_BROWSER_TITLE"] === 'Y'
            || $this->arParams["SET_META_KEYWORDS"] === 'Y'
            || $this->arParams["SET_META_DESCRIPTION"] === 'Y'
        ) {
            $this->arResult["META_TAGS"] = array();
            $resultCacheKeys[] = "META_TAGS";

            if ($this->arParams["SET_TITLE"]) {
                $this->arResult["META_TAGS"]["TITLE"] = (
                $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ""
                    ? $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                    : $this->arResult["NAME"]
                );
            }

            if ($this->arParams["ADD_ELEMENT_CHAIN"]) {
                $this->arResult["META_TAGS"]["ELEMENT_CHAIN"] = (
                $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ""
                    ? $this->arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                    : $this->arResult["NAME"]
                );
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
            if ($this->arResult["META_TAGS"]["BROWSER_TITLE"] !== '') {
                $APPLICATION->SetPageProperty("title", $this->arResult["META_TAGS"]["BROWSER_TITLE"]);
            }
        }

        if ($this->arParams["SET_META_KEYWORDS"] === 'Y') {
            if ($this->arResult["META_TAGS"]["KEYWORDS"] !== '') {
                $APPLICATION->SetPageProperty("keywords", $this->arResult["META_TAGS"]["KEYWORDS"]);
            }
        }

        if ($this->arParams["SET_META_DESCRIPTION"] === 'Y') {
            if ($this->arResult["META_TAGS"]["DESCRIPTION"] !== '') {
                $APPLICATION->SetPageProperty("description", $this->arResult["META_TAGS"]["DESCRIPTION"]);
            }
        }

        if (!empty($seo['ELEMENT_PAGE_TITLE']) || !empty($seo['SECTION_PAGE_TITLE'])) {
            $APPLICATION->SetTitle($seo['ELEMENT_PAGE_TITLE'] ?: $seo['SECTION_PAGE_TITLE']);
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
        global $LOCATION;
        // настройки в админке
        $minOneClickPrice = COption::GetOptionString('respect', 'one_click_min');
        if ($this->arResult['PRICE_PRODUCT'][$this->arResult['ID']]['PRICE'] < $minOneClickPrice) {
            return false;
        }
        // не показываем кнопку 1 клик, если местоположение является регионом-пациентом
        if ($LOCATION->checkIfLocationIsDonorTarget($LOCATION->code)) {
            return false;
        }
        return true;
    }

    private function getPrice()
    {
        global $LOCATION;
        if (in_array('Y', array_column($this->arResult['RESTS']['DELIVERY'], 'IS_LOCAL')) || !empty($this->arResult['RESTS']['RESERVATION'])) {
            $price = $LOCATION->getProductsPrices($this->arResult['ID']);
            if (!empty($price[$this->arResult['ID']])) {
                return $price;
            }
        } else {
            $price = $LOCATION->getProductsPrices($this->arResult['ID'], $LOCATION->DEFAULT_BRANCH);
            if (!empty($price[$this->arResult['ID']])) {
                return $price;
            }
        }
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
}
