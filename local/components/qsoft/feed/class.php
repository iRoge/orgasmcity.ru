<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\FileTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Location\LocationTable;
use Likee\Site\Helpers\HL;
use Qsoft\Helpers\ComponentHelper;
use Qsoft\Logger\Logger;

class QsoftFeed extends ComponentHelper
{
    /** @var $logger Logger */
    private $logger;
    private $feed_settings;
    private $feed_filter;
    private $feed_products;
    private $feed_offers;
    private $feed_items;
    private $defaultStoresType;
    private $feed_iblock;
    private $item_props = [
        'BRAND',
        'ARTICLE',
        'TYPEPRODUCT'
    ];
    private $LockFilePath;
    private $XmlFilePath;
    private $return_array;
    private $arParentChildSections;
    private $feedCodesAllLocations = [
        'retail_rocket',
        'criteo',
    ];

    private bool $showcaseFeed = false;
    /**
     * @var array|array[]
     */
    private array $uniqShowcase = [];

    private array $article_intersect = [];


    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        return $arParams;
    }

    /**
     * @return array|bool|mixed
     * @throws Exception
     */
    public function executeComponent()
    {
        set_time_limit(500);
        $this->defineLockFilePath();
        $this->logger = new Logger('FeedLog.txt');
        if (!$this->checkLockFile()) {
            try {
                $start = date('d.m.Y H:i:s');
                $this->createLockFile();
                $this->getIBlock();
                $this->getFeedSettings();
                $arReplace = [
                    '#NAME#' => $this->feed_settings['NAME'],
                    '#CATEGORY#' => $this->getTemplateName(),
                    '#FILE#' => $this->arParams['FEED_SETTINGS_CODE'],
                ];
                $this->logger->addSavedMessage(Loc::getMessage("HEADER_LOG", $arReplace), 'COMMON');
                $this->logger->addSavedMessage(Loc::getMessage("START_EXPORT_FEEDS", ['#DATE#' => $start]), 'COMMON');
                if ($this->arParams['NEWS_YANDEX_TURBO'] == '1') {
                    $this->loadRssNews();
                } else {
                    $is1C = $this->arParams['FEED_1C'] == '1' ? true : false;
                    $addPreorder = in_array($this->feed_settings['TEMPLATE_NAME'], ['retail_rocket', 'mindbox']) && $this->feed_settings['PROPERTY_ADD_PREORDER_VALUE'];

                    if ($this->showcaseFeed) {
                        $this->loadShowcases();
                    } else {
                        $this->setLocationCode();
                    }
                    $this->getFeedFilter($is1C, $addPreorder);
                    $this->loadParentChildSections();
                    $this->loadProductsByFilter($is1C);
                    $this->loadOffersByFilter($addPreorder);
                    $this->getDefaultStoresType();

                    if ($is1C) {
                        $this->getResultItemsFor1C();
                    } else {
                        if ($this->showcaseFeed) {
                            foreach ($this->uniqShowcase['result'] as $uniqId => $showcase) {
                                $this->setLocationCode($showcase['UNIQ_ID']);
                                $this->getResultItems($showcase['STORES']);

                                $this->getXmlFilePath($showcase['UNIQ_ID']);

                                if (!empty($this->article_intersect['IDS'])) {
                                    $this->intersectArticles();
                                }

                                $this->writeFeedToFile();
                            }
                        } else {
                            $bAllLocation = false;
                            foreach ($this->feedCodesAllLocations as $allLocationName) {
                                if (preg_match('/' . $allLocationName . '/i', $this->feed_settings['TEMPLATE_NAME'])) {
                                    $this->getResultItemsWithAllLocations();
                                    $bAllLocation = true;
                                    break;
                                }
                            }
                            if (!$bAllLocation) {
                                $this->getResultItems();
                            }
                        }
                    }
                }


                if (!$this->showcaseFeed) {
                    $this->getXmlFilePath();

                    if (!empty($this->article_intersect['IDS'])) {
                        $this->intersectArticles();
                    }

                    $this->writeFeedToFile();
                }
                $this->deleteLockFile();
                $this->logger->addSavedMessage(Loc::getMessage("END_EXPORT_FEEDS", ['#DATE#' => date('d.m.Y H:i:s')]), 'COMMON');
                $this->logger->pasteSeparator();
                $this->logger->writeSavedMessagesIntoFile();
                $this->logger->pasteSeparator();
                return $this->return_array;
            } catch (Exception $e) {
                $this->logger->addSavedMessage($this->logger->getExceptionInfo($e), 'COMMON');
                $this->defineLockFilePath();
                $this->deleteLockFile();
                $this->return_array['FAILED'];
                $this->return_array['ERRORS'] = $e->getMessage();
                $this->logger->addSavedMessage(Loc::getMessage("END_EXPORT_FEEDS", ['#DATE#' => date('d.m.Y H:i:s')]), 'COMMON');
                $this->logger->pasteSeparator();
                $this->logger->writeSavedMessagesIntoFile();
                $this->logger->pasteSeparator();
                return $this->return_array;
            }
        }
        return false;
    }

    private function loadParentChildSections()
    {
        $rsSection = CIBlockSection::GetList([], ['IBLOCK_ID' => IBLOCK_CATALOG], '', ['ID', 'IBLOCK_SECTION_ID', 'NAME']);
        while ($arSection = $rsSection->Fetch()) {
            $arSectionParentChild[$arSection['ID']] = [
                'ID' => $arSection['ID'],
                'PARENT_ID' => $arSection['IBLOCK_SECTION_ID'],
                'NAME' => $arSection['NAME'],
            ];

            if (!empty($this->feed_settings['PROPERTY_USE_CUSTOM_STRUCTURE_VALUE'])) {
                $arSectionParentChild[$arSection['ID']]['ALTERNATIVE_NAME'] = $this->feed_settings['PROPERTY_CUSTOM_STRUCTURE_VALUE'][$arSection['ID']];
            }
        }

        $this->arParentChildSections = $arSectionParentChild;
        $this->arResult['SECTIONS'] = $arSectionParentChild;

        //Собираем хлебные крошки вторым циклом, чтобы быть уверенными что все разделы находятся в массиве
        //Перебираем arResult, чтобы сразу перезаписать его
        foreach ($this->arResult['SECTIONS'] as &$arSection) {
            $arSection['PATH'] = $this->getSectionPath($arSection);
        }
    }

    private function defineLockFilePath(): void
    {
        $this->LockFilePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/catalog_export/feed.lock';
    }

    /**
     * @throws Exception
     */
    private function createLockFile(): void
    {
        $str_start = date('d.m.Y H:i:s');
        file_put_contents($this->LockFilePath, $str_start);
        if (!$this->checkLockFile()) {
            throw new Exception(Loc::getMessage("LOCK_FILE_NOT_FOUND"));
        }
    }

    /**
     * @throws Exception
     */
    private function getIBLock(): void
    {

        $arIBlock = CIBlock::GetList(
            [],
            ['CODE' => IBLOCK_FEEDS]
        )->Fetch();

        if (empty($arIBlock)) {
            throw new Exception(Loc::getMessage('IBLOCK_NOT_LOAD'));
        }

        $this->feed_iblock = $arIBlock;
    }

    /**
     * @throws Exception
     */
    private function getFeedSettings()
    {
        if (!empty($this->arParams['FEED_SETTINGS_CODE'])) {
            $settings = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $this->feed_iblock['ID'],
                    'CODE' => $this->arParams['FEED_SETTINGS_CODE'],
                    'ACTIVE' => 'Y',
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'IBLOCK_SECTION_ID',

                    'PROPERTY_FC_UPDATE_IMPORT',
                    'PROPERTY_FC_UPDATE_TIME',
                    'PROPERTY_FC_UPDATE_PERIOD',
                    'PROPERTY_LOCATION',
                    'PROPERTY_STORAGE_CONTROL',

                    'PROPERTY_SECTION',
                    'PROPERTY_PRICE_FROM',
                    'PROPERTY_PRICE_TO',
                    'PROPERTY_OFFERS_SIZE',
                    'PROPERTY_PRICESEGMENTID',
                    'PROPERTY_SUBTYPEPRODUCT',
                    'PROPERTY_COLLECTION',
                    'PROPERTY_UPPERMATERIAL',
                    'PROPERTY_LININGMATERIAL',
                    'PROPERTY_RHODEPRODUCT',
                    'PROPERTY_SEASON',
                    'PROPERTY_COUNTRY',
                    'PROPERTY_BRAND',

                    'PROPERTY_STORES',
                    'PROPERTY_RESERVATION',
                    'PROPERTY_DELIVERY',

                    'PROPERTY_USE_CUSTOM_STRUCTURE',
                    'PROPERTY_CUSTOM_STRUCTURE',

                    'PROPERTY_CHANGES_DAY_COUNT',
                    'PROPERTY_ADD_PREORDER',

                    'PROPERTY_LEGAL_ENTITY',
                    'PROPERTY_SHOWCASE_FEED',
                    'PROPERTY_ARTICLE_INTERSECT',
                ]
            )->Fetch();

            if (empty($settings)) {
                throw new Exception(Loc::getMessage("FEED_SETTINGS_NOT_LOAD"));
            }

            $rsSection = CIBlockSection::GetByID($settings['IBLOCK_SECTION_ID']);
            if ($arSection = $rsSection->Fetch()) {
                $settings['TEMPLATE_NAME'] = $arSection['CODE'];
            }

            $settings['PROPERTY_LOCATION_VALUE'] = $settings['PROPERTY_STORAGE_CONTROL_VALUE']['LOC_ID'];
            if ($settings['PROPERTY_STORAGE_CONTROL_VALUE']['USE_THIS']) {
                $settings['PROPERTY_STORES_VALUE'] = $settings['PROPERTY_STORAGE_CONTROL_VALUE']['RESULT_STORES'];
            }


            if ($settings['PROPERTY_SHOWCASE_FEED_VALUE'] == 'Y' && $this->arParams['FEED_SETTINGS_CODE'] == 'mindbox') {
                $this->showcaseFeed = true;
            }

            if (!empty($settings['PROPERTY_ARTICLE_INTERSECT_VALUE'])) {
                foreach (explode(',', $settings['PROPERTY_ARTICLE_INTERSECT_VALUE']) as $article) {
                    $article = trim($article);
                    $this->article_intersect['ARTICLES'][$article] = $article;
                }
            }

            $this->arResult['FEED_SETTINGS'] = $settings;
            $this->feed_settings = $settings;
        } else {
            throw new Exception(Loc::getMessage("NO_FEED_CODE"));
        }
    }

    private function loadRssNews()
    {
        $arFilter = [
            'IBLOCK_ID' => IBLOCK_BLOG,
            'PROPERTY_ADD_RSS_CHANNEL_VALUE' => 'Y'
        ];
        $arSelect = [
            'IBLOCK_ID',
            'ID',
            'NAME',
            'DATE_ACTIVE_FROM',
            'DATE_CREATE',
            'PREVIEW_PICTURE',
            'DETAIL_TEXT',
            'DETAIL_PICTURE',
            'USER_NAME',
            'DETAIL_PAGE_URL',
            'PROPERTY_PICTURE_POSITION',
            'PROPERTY_PHOTO_LINK',
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        while ($arItem = $res->GetNext(true, false)) {
            // определение даты для сортировки
            if (!empty($arItem['DATE_ACTIVE_FROM'])) {
                $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['DATE_ACTIVE_FROM']);
            } else {
                $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['DATE_CREATE']);
            }
            $this->arResult[] = $arItem;
            if (!empty($arItem["PREVIEW_PICTURE"])) {
                $arImageIds[] = $arItem["PREVIEW_PICTURE"];
            }
            if (!empty($arItem["DETAIL_PICTURE"])) {
                $arImageIds[] = $arItem["DETAIL_PICTURE"];
            }
        }

        // получаем секции для блога
        $res = CIBlockSection::GetList(
            ['DEPTH_LEVEL' => 'ASC'],
            [
                'ACTIVE' => 'Y',
                'DEPTH_LEVEL' => [2, 3],
                'IBLOCK_ID' => \Functions::getEnvKey('IBLOCK_BLOG', 72),
            ],
            false,
            [
                '*',
                'UF_*',
            ]
        );

        while ($parentItem = $res->Fetch()) {
            if ($parentItem['DEPTH_LEVEL'] == 2) {
                $arBlogSection[$parentItem['ID']] = $parentItem['CODE'];
                continue;
            }
            if ($parentItem['UF_ADD_RSS_CHANNEL'] != 1) {
                continue;
            }

            $arSectionIds[] = $parentItem['ID'];

            $parentItem['SECTION'] = $arBlogSection[$parentItem['IBLOCK_SECTION_ID']];
            $parentItem['DETAIL_PAGE_URL'] = str_replace('#SECTION_CODE#', $parentItem['SECTION'], $parentItem['SECTION_PAGE_URL']);
            $parentItem['DETAIL_PAGE_URL'] = str_replace('#CODE#', $parentItem['CODE'], $parentItem['DETAIL_PAGE_URL']);
            $parentItem['DETAIL_PAGE_URL'] .= $parentItem['CODE'] . '/';
            if ($parentItem['UF_IS_LOOKBOOK']) {
                $parentItem['DETAIL_PAGE_URL'] .= '1/';
            }

            // определение даты для сортировки
            if (!empty($parentItem['UF_DATE_ACTIVE_FROM'])) {
                $parentItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $parentItem['UF_DATE_ACTIVE_FROM']);
            } else {
                $parentItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $parentItem['DATE_CREATE']);
            }

            $arImageIds[] = $parentItem['PICTURE'];
            $arElementSection[$parentItem['ID']] = $parentItem;
        }
        //получаем элементы внутри секций
        $res = CIBlockElement::GetList(
            [],
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => \Functions::getEnvKey('IBLOCK_BLOG', 72),
                'IBLOCK_SECTION_ID' => $arSectionIds,
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'IBLOCK_SECTION_ID',
                'NAME',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'DETAIL_TEXT',
                'PROPERTY_IMG_LINKS_LEFT',
                'PROPERTY_IMG_LINKS_RIGHT',
                'PROPERTY_IMG_LINK_LEFT',
                'PROPERTY_IMG_LINK_RIGHT',
                'PROPERTY_PHOTO_LINK_ACTIVE',
                'PROPERTY_PHOTO_LINK',
                'PROPERTY_PICTURE',
                'PROPERTY_PICTURE_POSITION',
            ]
        );

        while ($arItemElement = $res->Fetch()) {
            $resultItem = [];
            if ($arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['UF_IS_LOOKBOOK'] == 0) {
                if ($arItemElement['PROPERTY_PICTURE_VALUE']) {
                    $arImageIds[] = $arItemElement['PROPERTY_PICTURE_VALUE'];
                }
                $resultItem['NAME'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['NAME'];
                $resultItem['DETAIL_PAGE_URL'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['DETAIL_PAGE_URL'];
                $resultItem['PREVIEW_PICTURE'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['PICTURE'];
                $resultItem['DETAIL_PICTURE'] = $arItemElement['PROPERTY_PICTURE_VALUE'];
                $resultItem['PROPERTY_PICTURE_POSITION_VALUE'] = $arItemElement['PROPERTY_PICTURE_POSITION_VALUE'];
                $resultItem['DATE_SORT'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['DATE_SORT'];
                $resultItem['DETAIL_TEXT'] = $arItemElement['DETAIL_TEXT'];

                $this->arResult[] = $resultItem;
            } else {
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['IS_LOOKBOOK'] = true;
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['NAME'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['NAME'];
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['DETAIL_PAGE_URL'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['DETAIL_PAGE_URL'];
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['PREVIEW_PICTURE'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['PICTURE'];
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['DATE_SORT'] = $arElementSection[$arItemElement['IBLOCK_SECTION_ID']]['DATE_SORT'];

                $ar2Page = [];
                $ar2Page['DETAIL_TEXT'] = $arItemElement['DETAIL_TEXT'];

                if ($arItemElement['PREVIEW_PICTURE']) {
                    $arImageIds[] = $arItemElement['PREVIEW_PICTURE']; //левая страница
                    $ar2Page['LEFT']['IMG'] = $arItemElement['PREVIEW_PICTURE'];
                }
                if ($arItemElement['DETAIL_PICTURE']) {
                    $arImageIds[] = $arItemElement['DETAIL_PICTURE']; //правая страница
                    $ar2Page['RIGHT']['IMG'] = $arItemElement['DETAIL_PICTURE'];
                }

                foreach (['LEFT', 'RIGHT'] as $page) {
                    if (!empty($arItemElement['PROPERTY_IMG_LINK_' . $page . '_VALUE'])) {
                        $ar2Page[$page]['LINK'] = $arItemElement['PROPERTY_IMG_LINK_' . $page . '_VALUE'];
                    } else {
                        $linkNum = 0;
                        while (empty($ar2Page[$page]['LINK'])) {
                            if (!isset($arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'][$linkNum])) {
                                $ar2Page[$page]['LINK'] = '';
                                break;
                            }
                            if (!empty($arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'][$linkNum]['LINK'])) {
                                $ar2Page[$page]['LINK'] = $arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'][$linkNum]['LINK'];
                            } elseif (!empty($arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'][$linkNum]['ART'])) {
                                $resProduct = CIBlockElement::GetList(
                                    array(),
                                    array(
                                        'ACTIVE' => 'Y',
                                        'IBLOCK_ID' => IBLOCK_CATALOG,
                                        'PROPERTY_ARTICLE' => $arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'][$linkNum]['ART'],
                                    ),
                                    false,
                                    false,
                                    array(
                                        'ID',
                                        'IBLOCK_ID',
                                        'CODE',
                                        'PROPERTY_ARTICLE',
                                    )
                                );
                                $artLink = $resProduct->Fetch()['CODE'];
                                if (!empty($artLink)) {
                                    $ar2Page[$page]['LINK'] = '/' . $artLink . '/';
                                }
                            }
                            $linkNum++;
                        }
                    }
                }
                $arLookbooks[$arItemElement['IBLOCK_SECTION_ID']]['SLIDES'][] = $ar2Page;
            }
        }
        foreach ($arLookbooks as $arLookbook) {
            $this->arResult[] = $arLookbook;
        }

        if (!empty($arImageIds)) {
            $res = FileTable::getList(array(
                "select" => array(
                    "ID",
                    "SUBDIR",
                    "FILE_NAME",
                ),
                "filter" => array(
                    "ID" => $arImageIds,
                ),
            ));
            $arImages = array();
            while ($arItem = $res->Fetch()) {
                $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
                if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                    continue;
                }
                $arImages[$arItem["ID"]] = $src;
            }
            foreach ($this->arResult as &$arItem) {
                if (!empty($arImages[$arItem["PREVIEW_PICTURE"]])) {
                    $arItem["PREVIEW_PICTURE"] = $arImages[$arItem["PREVIEW_PICTURE"]];
                }
                if (!empty($arImages[$arItem["DETAIL_PICTURE"]])) {
                    $arItem["DETAIL_PICTURE"] = $arImages[$arItem["DETAIL_PICTURE"]];
                }
                if (!empty($arItem['IS_LOOKBOOK'])) {
                    foreach ($arItem['SLIDES'] as &$slide) {
                        foreach (['LEFT', 'RIGHT'] as $page) {
                            if (!empty($arImages[$slide[$page]['IMG']])) {
                                $slide[$page]['IMG'] = $arImages[$slide[$page]['IMG']];
                            }
                        }
                    }
                    unset($slide);
                }
            }
        }
        // сортируем
        function cmp($a, $b)
        {
            if ($a['DATE_SORT'] == $b['DATE_SORT']) {
                return 0;
            }
            return ($a['DATE_SORT'] > $b['DATE_SORT']) ? -1 : 1;
        }

        uasort($this->arResult, 'cmp');
    }

    private function getFeedFilter($is1C = false, $addPreorder = false)
    {
        list($productPropertiesMap, $offersPropertiesMap) = array_values($this->getPropertiesMap());

        if (!$is1C) {
            if ($addPreorder) {
                $filter = [
                    'PRODUCT' => ['IBLOCK_ID' => IBLOCK_CATALOG, ['LOGIC' => 'OR', 'ACTIVE' => 'Y', ['LOGIC' => 'AND', 'ACTIVE' => 'N', 'PROPERTY_PREORDER_VALUE' => 'Y']]],
                    'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS],
                    'RESULT' => [],
                ];
            } else {
                $filter = [
                    'PRODUCT' => ['IBLOCK_ID' => IBLOCK_CATALOG, 'ACTIVE' => 'Y'],
                    'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS, 'ACTIVE' => 'Y'],
                    'RESULT' => [],
                ];
            }
        } else {
            if (!empty($this->feed_settings['PROPERTY_CHANGES_DAY_COUNT_VALUE'])) {
                $stmp = AddToTimeStamp(array("DD" => -$this->feed_settings['PROPERTY_CHANGES_DAY_COUNT_VALUE']));
                $stmp = date("d.m.Y H:i:s", $stmp);
                $filter = [
                    'PRODUCT' => ['IBLOCK_ID' => IBLOCK_CATALOG, '>TIMESTAMP_X' => $stmp],
                    'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS],
                    'RESULT' => [],
                ];
            } else {
                $filter = [
                    'PRODUCT' => ['IBLOCK_ID' => IBLOCK_CATALOG],
                    'OFFER' => ['IBLOCK_ID' => IBLOCK_OFFERS],
                    'RESULT' => [],
                ];
            }
        }

        foreach ($productPropertiesMap as $property) {
            if (!empty($this->feed_settings[$property . '_VALUE'])) {
                $propertyName = ($property === 'PROPERTY_SECTION') ? 'IBLOCK_SECTION_ID' : $property;
                $filter['PRODUCT'][$propertyName] = $this->feed_settings[$property . '_VALUE'];
            }
        }

        foreach ($offersPropertiesMap as $key => $value) {
            if (!empty($this->feed_settings[$key . '_VALUE'])) {
                $filter['OFFER'][$value] = $this->feed_settings[$key . '_VALUE'];
            }
        }

        $this->getFilterSizes($filter);

        $this->getFilterSections($filter);

        $this->getFilterPrices($filter, $this->feed_settings);

        $this->getGroupFilterPriceSegment($filter);

        $this->feed_filter = $filter;
    }

    private function getPropertiesMap(): array
    {
        return [
            'PRODUCT' => [
                'PROPERTY_SECTION',
                'PROPERTY_SUBTYPEPRODUCT',
                'PROPERTY_COLLECTION',
                'PROPERTY_UPPERMATERIAL',
                'PROPERTY_LININGMATERIAL',
                'PROPERTY_RHODEPRODUCT',
                'PROPERTY_SEASON',
                'PROPERTY_COUNTRY',
                'PROPERTY_BRAND',
            ],
            'OFFER' => [
                'PROPERTY_OFFERS_SIZE' => 'PROPERTY_SIZE',
            ],
            'SETTINGS' => [
                'PROPERTY_FC_UPDATE_IMPORT',
                'PROPERTY_FC_UPDATE_TIME',
                'PROPERTY_FC_UPDATE_PERIOD',
            ]
        ];
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

    private function loadRelatedSections($arSection): array
    {
        $arSectionIds = [];

        if (is_array($arSection) && array_key_exists("LEFT_MARGIN", $arSection) && array_key_exists(
            "RIGHT_MARGIN",
            $arSection
        )) {
            $res = CIBlockSection::GetList(
                [
                    "SORT" => "ASC",
                ],
                [
                    "IBLOCK_ID" => IBLOCK_CATALOG,
                    ">LEFT_MARGIN" => $arSection["LEFT_MARGIN"],
                    "<RIGHT_MARGIN" => $arSection["RIGHT_MARGIN"],
                ],
                [
                    "ID",
                ],
                false
            );

            while ($arItem = $res->Fetch()) {
                $arSectionIds[] = $arItem["ID"];
            }
        }

        return $arSectionIds;
    }

    private function getFilterPrices(array &$filter, array $data)
    {
        if (!empty($this->feed_settings['PROPERTY_PRICE_FROM_VALUE'])) {
            $filter['RESULT']['MIN_PRICE'] = intval($data['PROPERTY_PRICE_FROM_VALUE']);
        }

        if (!empty($this->feed_settings['PROPERTY_PRICE_TO_VALUE'])) {
            $filter['RESULT']['MAX_PRICE'] = intval($data['PROPERTY_PRICE_TO_VALUE']);
        }
    }

    private function getGroupFilterPriceSegment(array &$filter)
    {
        if (!empty($this->feed_settings['PROPERTY_PRICESEGMENTID_VALUE'])) {
            $filter['RESULT']['SEGMENT'] = $this->feed_settings['PROPERTY_PRICESEGMENTID_VALUE'];
        }
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    private function loadProductsByFilter($is1C = false)
    {
        $res = CIBlockElement::GetList(
            [],
            $this->feed_filter['PRODUCT'],
            false,
            false,
            [
                "ID",
                "IBLOCK_ID",
                "NAME",
                "XML_ID",
                "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PREVIEW_PICTURE",
                "SORT",
                "SHOW_COUNTER",
                "DETAIL_PAGE_URL",
            ]
        );
        $arProducts = $this->processProducts($res, $is1C);

        if (empty($arProducts)) {
            throw new Exception(Loc::getMessage("FILTERED_PRODUCTS_NOT_FOUND"));
        }

        $this->feed_products = $arProducts;
    }

    /**
     * @param $res CDBResult
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function processProducts($res, $is1C = false)
    {
        $arProducts = array();
        $arImageIds = array();
        // Массивы для линковки категорий и подкатегорий каталога
        $arVidTypeproduct = array();
        $arTypeproductSubtypeproduct = array();

        /** @var  $oItem  _CIBElement */
        while ($oItem = $res->getNextElement()) {
            $arItem = $oItem->getFields();
            $arItem['PROPERTIES'] = $oItem->GetProperties([], ['CODE' => $this->item_props]);

            $arProducts[$arItem["ID"]] = [
                "ID" => $arItem["ID"],
                "NAME" => $arItem['NAME'],
                "GUID_1C" => $arItem['XML_ID'],
                "DETAIL_PICTURE" => $arItem["DETAIL_PICTURE"],
                "PREVIEW_PICTURE" => $arItem["PREVIEW_PICTURE"],
                "PROPERTY_TYPEPRODUCT_VALUE" => HL::getFieldValueByProp(
                    $arItem ['PROPERTIES']['TYPEPRODUCT'],
                    'UF_NAME'
                ),
                "PROPERTY_MANUFACTURER_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['MANUFACTURER'], 'UF_NAME'),
                "PROPERTY_MODEL_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['MODEL'], 'UF_NAME'),
                "PROPERTY_SHOE_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['SHOE'], 'UF_NAME'),
                "PROPERTY_UPPERMATERIAL_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['UPPERMATERIAL'], 'UF_NAME'),
                "PROPERTY_LININGMATERIAL_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['LININGMATERIAL'], 'UF_NAME'),
                "PROPERTY_MATERIALSOLE_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['MATERIALSOLE'], 'UF_NAME'),
                "PROPERTY_COLORSFILTER_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['COLORSFILTER'], 'UF_NAME'),
                "PROPERTY_BRAND_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['BRAND'], 'UF_NAME'),
                "PROPERTY_SEASON_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['SEASON'], 'UF_NAME'),
                "PROPERTY_COUNTRY_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['COUNTRY'], 'UF_NAME'),
                "PROPERTY_HEELHEIGHT_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['HEELHEIGHT'], 'UF_NAME'),
                "PROPERTY_RHODEPRODUCT_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['RHODEPRODUCT'], 'UF_NAME'),
                "PROPERTY_ZASTEGKA_VALUE" => HL::getFieldValueByProp($arItem['PROPERTIES']['ZASTEGKA'], 'UF_NAME'),
                "PROPERTY_SIZERANGE_VALUE" => $arItem['PROPERTIES']['SIZERANGE']['VALUE'],
                "PROPERTY_HEELHEIGHT_TYPE_VALUE" => $arItem['PROPERTIES']['HEELHEIGHT_TYPE']['VALUE'],
                "PROPERTY_VID_VALUE" => $arItem['PROPERTIES']['VID']['VALUE'],
                "PROPERTY_TYPEPRODUCT_VALUE_ID" => $arItem['PROPERTIES']['TYPEPRODUCT']['VALUE'],
                "PROPERTY_SUBTYPEPRODUCT_VALUE" => $arItem['PROPERTIES']['SUBTYPEPRODUCT']['VALUE'],
                "PROPERTY_ARTICLE_VALUE" => $arItem['PROPERTIES']['ARTICLE']['VALUE'],
                "PROPERTY_KOD_1S_VALUE" => $arItem['PROPERTIES']['KOD_1S']['VALUE'],
                "PROPERTY_PREORDER_VALUE" => $arItem['PROPERTIES']['PREORDER']['VALUE'],
                "SORT" => $arItem["SORT"],
                "SHOW_COUNTER" => $arItem["SHOW_COUNTER"],
                "DETAIL_PAGE_URL" => SITE_DIR . $arItem["CODE"],
                "IBLOCK_SECTION_ID" => $arItem["IBLOCK_SECTION_ID"],
                "MORE_PHOTO" => $arItem['PROPERTIES']['MORE_PHOTO']['VALUE'],
                "PRICESEGMENT" => $arItem['PROPERTIES']['PRICESEGMENTID']['VALUE'],
            ];
            $arImageIds[] = $arItem["DETAIL_PICTURE"];
            if (!empty($arItem['PROPERTIES']['MORE_PHOTO']['VALUE']) && is_array($arItem['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
                $arImageIds = array_merge($arImageIds, $arItem['PROPERTIES']['MORE_PHOTO']['VALUE']);
            }
            if (!empty($arItem["PREVIEW_PICTURE"])) {
                $arImageIds[] = $arItem["PREVIEW_PICTURE"];
            }
            // Заполняем информацию о категории товара в каталоге
            if (!empty($arItem['PROPERTIES']['VID']['VALUE']) && !empty($arItem ['PROPERTIES']['TYPEPRODUCT']['VALUE'])) {
                $arVidTypeproduct[$arItem['PROPERTIES']['VID']['VALUE']][$arItem ['PROPERTIES']['TYPEPRODUCT']['VALUE']] = true;
            }
            if (!empty($arItem['PROPERTIES']['TYPEPRODUCT']['VALUE']) && !empty($arItem['PROPERTIES']['SUBTYPEPRODUCT']['VALUE'])) {
                $arTypeproductSubtypeproduct[$arItem['PROPERTIES']['TYPEPRODUCT']['VALUE']][$arItem['PROPERTIES']['SUBTYPEPRODUCT']['VALUE']] = true;
            }
            if (isset($arItem['IBLOCK_SECTION_ID'])) {
                $arCurrentUsesSectionsIds[$arItem['IBLOCK_SECTION_ID']] = $arItem['IBLOCK_SECTION_ID'];
            }

            if (!empty($this->article_intersect['ARTICLES'][$arItem['PROPERTIES']['ARTICLE']['VALUE']])) {
                $this->article_intersect['IDS'][$arItem['ID']] = $arItem['PROPERTIES']['ARTICLE']['VALUE'];
            }
        }

        if (!empty($arImageIds)) {
            $res = FileTable::getList(array(
                "select" => array(
                    "ID",
                    "SUBDIR",
                    "FILE_NAME",
                ),
                "filter" => array(
                    "ID" => $arImageIds,
                ),
            ));
            $arImages = array();
            while ($arItem = $res->Fetch()) {
                $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
                if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                    continue;
                }
                $arImages[$arItem["ID"]] = $src;
            }
            foreach ($arProducts as $id => &$arItem) {
                foreach ($arItem["MORE_PHOTO"] as &$morePhotoId) {
                    if (!empty($arImages[$morePhotoId])) {
                        $morePhotoId = $arImages[$morePhotoId];
                    }
                }
                if (!empty($arImages[$arItem["DETAIL_PICTURE"]])) {
                    $arItem["DETAIL_PICTURE"] = $arImages[$arItem["DETAIL_PICTURE"]];
                } else {
                    if (!$is1C && !$this->showcaseFeed) {
                        unset($arProducts[$id]);
                        continue;
                    }
                }
                if (!empty($arImages[$arItem["PREVIEW_PICTURE"]])) {
                    $arItem["PREVIEW_PICTURE"] = $arImages[$arItem["PREVIEW_PICTURE"]];
                }
                if (empty($arItem["PROPERTY_TYPEPRODUCT_VALUE"])) {
                    if (!$is1C) {
                        unset($arProducts[$id]);
                        continue;
                    }
                }
                if (empty($arItem["IBLOCK_SECTION_ID"])) {
                    if (!$is1C) {
                        unset($arProducts[$id]);
                        continue;
                    }
                }
            }
        }
        // Формируем и добавляем информацию о структуре каталога в $arResult['CATEGORIES']
        $this->getCategories($arVidTypeproduct, $arTypeproductSubtypeproduct);

        // Формируем и добавляем информацию о структуре каталога в $arResult['CATEGORIES_IDS']
        $this->createCategoriesList($arCurrentUsesSectionsIds);
        return $arProducts;
    }

    private function createCategoriesList($sectionIds)
    {
        foreach ($sectionIds as $sectionId) {
            while (!empty($sectionId)) {
                if (!isset($this->arResult['CATEGORIES_IDS'][$sectionId])) {
                    $this->arResult['CATEGORIES_IDS'][$sectionId] = [
                        'ID' => $sectionId,
                        'PARENT_ID' => $this->arParentChildSections[$sectionId]['PARENT_ID'],
                        'NAME' => $this->arParentChildSections[$sectionId]['NAME']
                    ];
                }
                $sectionId = $this->arParentChildSections[$sectionId]['PARENT_ID'];
            }
        }
        ksort($this->arResult['CATEGORIES_IDS']);
    }

    /**
     * @param array $arVidTypeproduct
     * @param array $arTypeproductSubtypeproduct
     *
     * @return array
     */
    private function getCategories($arVidTypeproduct, $arTypeproductSubtypeproduct)
    {
        $result = array();

        $vidTitles = array_keys($arVidTypeproduct);
        $vidTitles = $this->getCategoriesTitles('Vid', $vidTitles);

        $typeproductTitles = array();
        foreach (array_values($arVidTypeproduct) as $value) {
            foreach ($value as $typeVal => $subvalue) {
                if (!empty($typeVal)) {
                    $typeproductTitles[] = $typeVal;
                }
            }
        }
        $typeproductTitles = array_merge($typeproductTitles, array_keys($arTypeproductSubtypeproduct));
        $typeproductTitles = $this->getCategoriesTitles('Typeproduct', $typeproductTitles);

        $subtypeproductTitles = array();
        foreach (array_values($arTypeproductSubtypeproduct) as $value) {
            foreach ($value as $subtypeVal => $subvalue) {
                if (!empty($subtypeVal)) {
                    $subtypeproductTitles[] = $subtypeVal;
                }
            }
        }
        $subtypeproductTitles = $this->getCategoriesTitles('Subtypeproduct', $subtypeproductTitles);

        foreach ($arVidTypeproduct as $vid => $typeproductList) {
            $result[] = [
                'id' => $this->reduceCategoryId($vid),
                'title' => $vidTitles[$vid]
            ];

            foreach ($typeproductList as $typeproduct => $value) {
                $result[] = [
                    'parentId' => $this->reduceCategoryId($vid),
                    'id' => $this->reduceCategoryId($vid . $typeproduct),
                    'title' => $typeproductTitles[$typeproduct]
                ];

                foreach ($arTypeproductSubtypeproduct[$typeproduct] as $subtypeproduct => $subvalue) {
                    $result[] = [
                        'parentId' => $this->reduceCategoryId($vid . $typeproduct),
                        'id' => $this->reduceCategoryId($vid . $typeproduct . $subtypeproduct),
                        'title' => $subtypeproductTitles[$subtypeproduct]
                    ];
                }
            }
        }

        $this->arResult['CATEGORIES'] = $result;

        return $result;
    }

    /**
     * Получает разом все названия разделов структуры каталога из HL
     *
     * @param string $HLName
     * @param array $categoriesList
     *
     * @return array
     */
    private function getCategoriesTitles($HLName, $categoriesList)
    {
        $result = array();
        $obEntity = HL::getEntityClassByHLName($HLName);
        if (!empty($obEntity) && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();

            $rsData = $sClass::getList([
                'select' => ['UF_NAME', 'UF_XML_ID'],
                'filter' => ['UF_XML_ID' => $categoriesList]
            ]);

            while ($entry = $rsData->fetch()) {
                $result[$entry['UF_XML_ID']] = $entry['UF_NAME'];
            }
        }
        return $result;
    }

    /**
     * Убирает из id категории лишние нули, чтобы влезть в лимит 20 символов у GoodsXML
     *
     * @param string $categoryId
     *
     * @return string
     */
    private function reduceCategoryId($categoryId)
    {
        return preg_replace('/(?<=\D)(0+)(?=\d)/', '', $categoryId);
    }

    /**
     * @throws Exception
     */
    private function loadOffersByFilter($addPreorder = false)
    {
        $res = CIBlockElement::GetList(
            [
                "SORT" => "ASC",
            ],
            $this->feed_filter['OFFER'],
            false,
            false,
            [
                "ID",
                "ACTIVE",
                'XML_ID',
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_SIZE",
            ]
        );

        $arOffers = $this->processOffers($res, $addPreorder);
        $this->feed_offers = $arOffers;
    }

    /**
     * @param $res CDBResult
     * @return array
     */
    private function processOffers($res, $addPreorder = false)
    {
        $arOffers = [];
        while ($arItem = $res->Fetch()) {
            if (!$this->feed_products[$arItem["PROPERTY_CML2_LINK_VALUE"]]) {
                continue;
            }
            if ($addPreorder) {
                if ($arItem['ACTIVE'] == 'N' && !$this->feed_products[$arItem["PROPERTY_CML2_LINK_VALUE"]]['PROPERTY_PREORDER_VALUE']) {
                    continue;
                }
            }
            $arOffers[$arItem["ID"]] = [
                'GUID_1C' => $arItem['XML_ID'],
                'PROPERTY_CML2_LINK_VALUE' => $arItem['PROPERTY_CML2_LINK_VALUE'],
                'PROPERTY_SIZE_VALUE' => $arItem['PROPERTY_SIZE_VALUE'],
            ];
        }

        return $arOffers;
    }

    private function getResultItems($stores = false): array
    {
        if (!empty($this->items)) {
            return $this->items;
        }
        global $LOCATION;

        $arRests = $this->getRests($stores ?: $this->feed_settings['PROPERTY_STORES_VALUE']);

        $items = [];
        foreach ($this->feed_offers as $offerId => $value) {
            if (!$arRests[$offerId]) {
                continue;
            }
            $pid = $value["PROPERTY_CML2_LINK_VALUE"];
            if (!$items[$pid]) {
                $items[$pid] = $this->feed_products[$pid];
            }

            $items[$pid]["OFFERS"][$offerId]['SIZE'] = $value["PROPERTY_SIZE_VALUE"];
            $items[$pid]["OFFERS"][$offerId]['GUID_1C'] = $value["GUID_1C"];

            foreach ($arRests[$offerId] as $storeId => $count) {
                if (!in_array($storeId, $items[$pid]['STORAGES_AVAILABILITY'])) {
                    $items[$pid]['STORAGES_AVAILABILITY'][] = $storeId;
                }

                if ($stores) {
                    $items[$pid]["OFFERS"][$offerId]['DELIVERY'] = $stores[$storeId][0] == 1;
                    $items[$pid]["OFFERS"][$offerId]['RESERV'] = $stores[$storeId][1] == 1;
                }
            }
            $items[$pid]["SIZES"][] = $value["PROPERTY_SIZE_VALUE"];
        }
        $exeptionReg = $LOCATION->exepRegionFlag;
        $arPrices = $LOCATION->getProductsPrices(array_keys($items));
        $checkIfDonorTarget = $LOCATION->checkIfLocationIsDonorTarget($LOCATION->code);
        $regionStores = $LOCATION->getStorages(false, true);
        if ($checkIfDonorTarget) {
            $arDefaultBranchPrices = $LOCATION->getProductsPrices(array_keys($items), $LOCATION->DEFAULT_BRANCH);
        }
        foreach ($items as $key => &$item) {
            if ($checkIfDonorTarget) {
                if (empty(array_intersect($item['STORAGES_AVAILABILITY'], array_keys($regionStores))) && !$exeptionReg) {
                    $arPrice = $arDefaultBranchPrices[$key];
                    $item['PRICE'] = $arPrice["PRICE"];
                    $item['OLD_PRICE'] = $arPrice["OLD_PRICE"];
                    $item['PERCENT'] = $arPrice["PERCENT"];
                    $item['SEGMENT'] = $arPrice["SEGMENT"];
                } else {
                    $arPrice = $arPrices[$key];
                    $item['PRICE'] = $arPrice["PRICE"];
                    $item['OLD_PRICE'] = $arPrice["OLD_PRICE"];
                    $item['PERCENT'] = $arPrice["PERCENT"];
                    $item['SEGMENT'] = $arPrice["SEGMENT"];
                }
            } else {
                $arPrice = $arPrices[$key];
                $item['PRICE'] = $arPrice["PRICE"];
                $item['OLD_PRICE'] = $arPrice["OLD_PRICE"];
                $item['PERCENT'] = $arPrice["PERCENT"];
                $item['SEGMENT'] = $arPrice["SEGMENT"];
            }

            //Добавляем выводимую цену (в зависимости от сегмента)
            if ($item['SEGMENT'] == 'White') {
                $item['VIEW_PRICE'] = $item['OLD_PRICE'];
            } else {
                $item['VIEW_PRICE'] = $item['PRICE'];
            }

            if (!isset($item['PRICE'])) {
                unset($items[$key]);
                continue;
            }
            if (isset($this->feed_filter['RESULT']['MIN_PRICE'])) {
                $min_price = $this->feed_filter['RESULT']['MIN_PRICE'];
                if ($item['PRICE'] < $min_price) {
                    unset($items[$key]);
                    continue;
                }
            }
            if (isset($this->feed_filter['RESULT']['MAX_PRICE'])) {
                $max_price = $this->feed_filter['RESULT']['MAX_PRICE'];
                if ($item['PRICE'] > $max_price) {
                    unset($items[$key]);
                    continue;
                }
            }
            if (isset($this->feed_filter['RESULT']['SEGMENT']) && !in_array($arPrice['SEGMENT'], $this->feed_filter['RESULT']['SEGMENT'])) {
                unset($items[$key]);
                continue;
            }
            if (isset($this->feed_filter['RESULT']['SEGMENT_FROM'])) {
                $from = $this->feed_filter['RESULT']['SEGMENT_FROM'];
                if ($item['PERCENT'] < $from) {
                    unset($items[$key]);
                    continue;
                }
            }
            if (isset($this->feed_filter['RESULT']['SEGMENT_TO'])) {
                $to = $this->feed_filter['RESULT']['SEGMENT_TO'];
                if ($item['PERCENT'] > $to) {
                    unset($items[$key]);
                    continue;
                }
            }
        }

        $this->feed_items = $items;
        $this->arResult['ITEMS'] = $items;

        $arReplace = [
            '#COUNT#' => count($items),
            '#ITEMS#' => $this->logger->num2word(
                count($items),
                array(
                    Loc::getMessage("ONE_ITEM"),
                    Loc::getMessage("2_3_4_ITEMS"),
                    Loc::getMessage("MORE_ITEMS")
                )
            )
        ];

        $this->logger->addSavedMessage(Loc::getMessage("EXPORTED_ITEMS_COUNT", $arReplace), 'COMMON');
        return $items;
    }

    private function getResultItemsWithAllLocations()
    {
        if (!empty($this->items)) {
            return $this->items;
        }
        global $LOCATION;
        $items = [];
        $arShowcases = [];
        $arUniqueShowcases = $LOCATION->getUniqueShowcases();
        $arDefaultBranchPrices = $LOCATION->getProductsPrices(array_keys($this->feed_products), $LOCATION->DEFAULT_BRANCH);
        foreach ($arUniqueShowcases['result'] as $uniqId => $showcase) {
            $arRests = $this->getRests($showcase['STORES']);
            $arBranchPrices = $LOCATION->getProductsPrices(array_keys($this->feed_products), $showcase['BRANCH_ID']);
            foreach ($this->feed_offers as $offerId => $value) {
                $pid = $value["PROPERTY_CML2_LINK_VALUE"];
                if (!$items[$pid]) {
                    $items[$pid] = $this->feed_products[$pid];
                    $items[$pid]['FILTER_STRING'] = $this->setFilterString($items[$pid]);
                }
                if (!$items[$pid]["OFFERS"][$offerId]) {
                    $items[$pid]["OFFERS"][$offerId] = $value["PROPERTY_SIZE_VALUE"];
                }
                if (!$arShowcases['DEFAULT_PRICE'][$pid]) {
                    $arShowcases['DEFAULT_PRICE'][$pid] = $arDefaultBranchPrices[$pid];
                }
                if (empty(array_diff_key($arRests[$offerId], $LOCATION->DEFAULT_STORAGES))) {
                    $arPrice = $arDefaultBranchPrices[$pid];
                    $item['PRICE'] = $arPrice["PRICE"];
                    $item['OLD_PRICE'] = $arPrice["OLD_PRICE"];
                    $item['PERCENT'] = $arPrice["PERCENT"];
                    $item['SEGMENT'] = $arPrice["SEGMENT"];
                    if ($arPrice['SEGMENT'] === 'White') {
                        $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['PRICE'] = $arPrice['OLD_PRICE'];
                    } else {
                        $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['PRICE'] = $arPrice['PRICE'];
                        if ($arPrice['OLD_PRICE'] > 0) {
                            $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['OLD_PRICE'] = $arPrice['OLD_PRICE'];
                        }
                    }
                    $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['SEGMENT'] = $arPrice['SEGMENT'];
                } else {
                    $arPrice = $arBranchPrices[$pid];
                    $item['PRICE'] = $arPrice["PRICE"];
                    $item['OLD_PRICE'] = $arPrice["OLD_PRICE"];
                    $item['PERCENT'] = $arPrice["PERCENT"];
                    $item['SEGMENT'] = $arPrice["SEGMENT"];
                    if ($arPrice['SEGMENT'] === 'White') {
                        $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['PRICE'] = $arPrice['OLD_PRICE'];
                    } else {
                        $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['PRICE'] = $arPrice['PRICE'];
                        if ($arPrice['OLD_PRICE'] > 0) {
                            $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['OLD_PRICE'] = $arPrice['OLD_PRICE'];
                        }
                    }
                    $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['SEGMENT'] = $arPrice['SEGMENT'];
                }
                if ($arRests[$offerId]) {
                    $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['AVAILABLE'] = true;
                    if (!in_array($value["PROPERTY_SIZE_VALUE"], $items[$pid]["SIZES"])) {
                        $items[$pid]["SIZES"][] = $value["PROPERTY_SIZE_VALUE"];
                    }
                } else {
                    $arShowcases[$showcase['UNIQ_ID']]['OFFERS'][$offerId]['AVAILABLE'] = false;
                }
            }
        }
        $this->feed_items = $items;
        $this->arResult['ITEMS'] = $items;
        $this->arResult['UNIQUE_SHOWCASES'] = $arShowcases;
        $arReplace = [
            '#COUNT#' => count($items),
            '#ITEMS#' => $this->logger->num2word(
                count($items),
                array(
                    Loc::getMessage("ONE_ITEM"),
                    Loc::getMessage("2_3_4_ITEMS"),
                    Loc::getMessage("MORE_ITEMS")
                )
            )
        ];

        $this->logger->addSavedMessage(Loc::getMessage("EXPORTED_ITEMS_COUNT", $arReplace), 'COMMON');
        return $items;
    }

    private function getResultItemsFor1C(): array
    {
        if (!empty($this->items)) {
            return $this->items;
        }
        $items = [];
        foreach ($this->feed_offers as $offerId => $value) {
            $pid = $value["PROPERTY_CML2_LINK_VALUE"];
            if (!$items[$pid]) {
                $items[$pid] = $this->feed_products[$pid];
            }
            $items[$pid]["SIZES"][] = $value["PROPERTY_SIZE_VALUE"];
            $items[$pid]["OFFERS"][$offerId] = $value["PROPERTY_SIZE_VALUE"];
        }
        $this->feed_items = $items;
        $this->arResult['ITEMS'] = $items;

        $arReplace = [
            '#COUNT#' => count($items),
            '#ITEMS#' => $this->logger->num2word(
                count($items),
                array(
                    Loc::getMessage("ONE_ITEM"),
                    Loc::getMessage("2_3_4_ITEMS"),
                    Loc::getMessage("MORE_ITEMS")
                )
            )
        ];

        $this->logger->addSavedMessage(Loc::getMessage("EXPORTED_ITEMS_COUNT", $arReplace), 'COMMON');
        return $items;
    }

    public function getDefaultStoresType()
    {
        $filter = [];
        $this->getGroupFilterStores($filter);
        $storesType = $filter['RESULT']['STORES'];

        $this->defaultStoresType = $storesType;

        return true;
    }

    private function getGroupFilterStores(array &$filter)
    {
        if (isset($this->stores)) {
            $filter['RESULT']['STORES'] = $this->stores;
            return;
        }

        $stores = 0;
        if (!empty($this->feed_settings['PROPERTY_DELIVERY_VALUE'])) {
            $stores += 1;
        }

        if (!empty($this->feed_settings['PROPERTY_RESERVATION_VALUE'])) {
            $stores += 2;
        }

        if (!in_array($stores, [1, 2])) {
            $stores = false;
        }

        $this->stores = $stores;
        $filter['RESULT']['STORES'] = $stores;
    }

    /**
     * @throws Exception
     */
    private function deleteLockFile(): void
    {
        if (file_exists($this->LockFilePath)) {
            $str_start = file_get_contents($this->LockFilePath);
            $str_end = date('d.m.Y H:i:s');
            $str_diff = date('i:s', strtotime($str_end) - strtotime($str_start));
            unlink($this->LockFilePath);

            if ($this->checkLockFile()) {
                throw new Exception(Loc::getMessage("LOCK_FILE_NOT_DELETED"));
            }

            $this->return_array = [
                'END_TIME' => $str_end,
                'DURATION' => $str_diff,
                'STATUS' => 'SUCCESS',
                'ERRORS' => [],
            ];
        }
    }

    private function getXmlFilePath($uniqShowcase = false): void
    {
        $prefUniq = '';

        if ($uniqShowcase) {
            $prefUniq = $uniqShowcase . ' - ';
        }

        $this->XmlFilePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/catalog_export/' . $prefUniq . $this->arParams['FEED_SETTINGS_CODE'] . '.xml';
    }

    private function intersectArticles()
    {
        $this->arResult['ITEMS'] = array_intersect_key($this->arResult['ITEMS'], $this->article_intersect['IDS']);
    }

    private function writeFeedToFile(): void
    {
        ob_clean();
        ob_start();
        $this->includeComponentTemplate();
        $RESULT = ob_get_contents();
        ob_end_clean();
        if (!empty($RESULT)) {
            file_put_contents($this->XmlFilePath, $RESULT);
        }
    }

    private function checkLockFile(): bool
    {
        return file_exists($this->LockFilePath);
    }

    private function loadShowcases()
    {
        global $LOCATION;
        $this->uniqShowcase = $LOCATION->getUniqueShowcases();
    }

    /**
     * @param $code
     */
    private function setLocationCode($code = false): void
    {
        global $LOCATION;
        if ($code) {
            $LOCATION->code = $code;
        } elseif (!empty($this->feed_settings['PROPERTY_LOCATION_VALUE'])) {
            $LOCATION->code = $this->feed_settings['PROPERTY_LOCATION_VALUE'];
        }
        /*
        if (!empty($this->feed_settings['PROPERTY_LOCATION_VALUE'])) {
            $res = LocationTable::GetList(array(
                "select" => array(
                    "ID",
                    "CODE",
                    "NAME_RU" => "NAME.NAME",
                ),
                "filter" => array(
                    "NAME.NAME" => $this->feed_settings['PROPERTY_LOCATION_VALUE'],
                    "NAME.LANGUAGE_ID" => "ru",
                ),
            ))->Fetch();

            if (empty($res) || empty($res['CODE'])) {
                throw new Exception(Loc::getMessage("LOCATION_NOT_SET"));
            }

            $LOCATION->code = $res['CODE'];
        }
        */
    }

    private function getRests($stores = false)
    {
        global $LOCATION;
        $arRests = $LOCATION->getRests(array_keys($this->feed_offers), $this->defaultStoresType, false, false, $stores);
        return $arRests;
    }

    private function setFilterString($item): string
    {
        $filter_string = '';

        $filters = [
            'PROPERTY_LININGMATERIAL_VALUE' => 'Материал подкладки',
            'PROPERTY_MATERIALSOLE_VALUE' => 'Материал подошвы',
            'PROPERTY_SEASON_VALUE' => 'Cезон',
            'PROPERTY_COUNTRY_VALUE' => 'Страна происхождения',
            'PROPERTY_HEELHEIGHT_TYPE_VALUE' => 'Высота каблука',
        ];

        foreach ($filters as $key => $filter_name) {
            if (!empty($item[$key])) {
                $filter_string .= sprintf('%s=%s, ', $filter_name, $item[$key]);
            }
        }
        return rtrim($filter_string, ', ');
    }


    /**
     * @param array $arSection
     * @return string
     * Рекурсивная функция которая собирает путь до текущего раздела
     */
    private function getSectionPath(array $arSection): string
    {
        if (!empty($arSection['PARENT_ID'])) {
            return $this->getSectionPath($this->arResult['SECTIONS'][$arSection['PARENT_ID']]) .
                ' > ' . $arSection['NAME'];
        } else {
            return $arSection['NAME'];
        }
    }
}
