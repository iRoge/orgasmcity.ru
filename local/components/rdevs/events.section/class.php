<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\InheritedProperty\SectionValues;

class EventsSectionComponent extends EventsComponent
{
    private string $cacheSeoDir = 'seo';
    private array $sections = [];
    private int $currentSectionId = 0;
    private array $arGtmTypes = [
        'actions' => 'Акции',
        'trends' => 'Тренды',
        'news' => 'Новости',
        'articles' => 'Статьи',
        'lookbooks' => 'Лукбуки',
        'konkursy-oprosy' => 'Конкурсы-опросы',
    ];

    public function executeComponent()
    {
        $this->initCache();
        $this->sections = $this->loadSections();
        if ($this->arParams['IBLOCK_CODE'] == 'events') {
            $this->loadEvents();
        } else {
            $this->loadElementGroup();
        }

        if ($this->request->isAjaxRequest()) {
            $this->arResult['IS_AJAX'] = true;
            $this->IncludeComponentTemplate();
        } else {
            $this->loadSeoSection();
            $this->createChain();
            $this->IncludeComponentTemplate();
        }
    }

    private function loadSections()
    {
        $arSection = $this->loadSectionCache();
        foreach ($arSection['MENU'] as $sectionId => $section) {
            if ($section['EXTERNAL_ID'] == $this->arParams['CURRENT_SECTION']) {
                $this->currentSectionId = $sectionId;
                if (!$sectionId) {
                    $this->currentSectionId = $section['ID'];
                }
                break;
            }
        }

        $this->arResult['SECTIONS'] = $arSection;
        return $arSection;
    }

    private function loadElementGroup()
    {
        global $LOCATION;
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'allElement', $this->cacheDir)) {
            $arEvents = $this->cache->getVars()['events'];
            $elementsForLocation = $this->cache->getVars()['location'];
        } elseif ($this->cache->StartDataCache()) {
            $res = CIBlockSection::GetList(
                ['SORT' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'DEPTH_LEVEL' => 3,
                ],
                false,
                [
                    '*',
                    'UF_*',
                ]
            );

            $ibName = $this->getIBName();

            while ($arItem = $res->Fetch()) {
                $arItem['PREVIEW_TEXT'] = $arItem['DESCRIPTION'];

                $arItem['SECTION'] = $this->sections['MENU'][$arItem['IBLOCK_SECTION_ID']]['EXTERNAL_ID'];
                $arItem['GTM_TYPE'] = $this->arGtmTypes[$arItem['SECTION']];
                $arItem['DETAIL_PAGE_URL'] = str_replace('#SECTION_CODE#', $arItem['SECTION'], $arItem['SECTION_PAGE_URL']);
                $arItem['DETAIL_PAGE_URL'] = str_replace('#CODE#', $arItem['CODE'], $arItem['DETAIL_PAGE_URL']);
                $arItem['DETAIL_PAGE_URL'] .= $arItem['CODE'] . '/';
                if ($arItem['UF_IS_LOOKBOOK']) {
                    $arItem['DETAIL_PAGE_URL'] .= '1/';
                }

                // определение даты для сортировки
                if (!empty($arItem['UF_DATE_ACTIVE_FROM'])) {
                    $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['UF_DATE_ACTIVE_FROM']);
                } else {
                    $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['DATE_CREATE']);
                }

                if ($arItem['UF_DATE_ACTIVE_TO'] && date_create_from_format('d.m.Y H:i:s', $arItem['UF_DATE_ACTIVE_TO']) <= date_create()) {
                    $arItem['DATE_END'] = true;
                }

                $arItem['UF_DATE_ACTIVE_FROM'] = ConvertDateTime($arItem['UF_DATE_ACTIVE_FROM'], "DD.MM.YYYY г.");
                $arItem['UF_DATE_ACTIVE_TO'] = ConvertDateTime($arItem['UF_DATE_ACTIVE_TO'], "DD.MM.YYYY г.");

                $arItem['DATE_STRING'] = '';
                if (empty($arItem['UF_DATE_ACTIVE_FROM']) && !empty($arItem['UF_DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = 'Завершение акции - ' . $arItem['UF_DATE_ACTIVE_TO'];
                } elseif (!empty($arItem['UF_DATE_ACTIVE_FROM']) && empty($arItem['UF_DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = $arItem['UF_DATE_ACTIVE_FROM'];
                } elseif (!empty($arItem['UF_DATE_ACTIVE_FROM']) && !empty($arItem['UF_DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = $arItem['UF_DATE_ACTIVE_FROM'] . ' - ' . $arItem['UF_DATE_ACTIVE_TO'];
                }

                $arItem['IB_NAME'] = $ibName ?? '';
                $arIdPreview[] = $arItem['PICTURE'];

                if (!empty($arItem['UF_LOCATION'])) {
                    $arItem['UF_LOCATION'] = unserialize($arItem['UF_LOCATION']);
                    if (!empty($arItem['UF_LOCATION'])) {
                        foreach ($arItem['UF_LOCATION'] as $arlocationInfo) {
                            $elementsForLocation[$arlocationInfo[1]][$arItem['ID']] = $arItem['ID'];
                        }
                    } else {
                        $elementsForLocation['ALL'][$arItem['ID']] = $arItem['ID'];
                    }
                }
                $arEvents[$arItem['ID']] = $arItem;
            }

            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $arNewImg[$arLocationImg['ID']] = '/' . COption::GetOptionString('main', 'upload_dir') .
                    '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'];
            }
            foreach ($arEvents as &$arResultNew) {
                $arResultNew['PREVIEW_PICTURE'] = $arNewImg[$arResultNew['PICTURE']];
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

        uasort($arEvents, 'cmp');

        if (!empty($arEvents)) {
            $this->cache->EndDataCache(['events' => $arEvents, 'location' => $elementsForLocation]);
        } else {
            $this->cache->AbortDataCache();
        }

        $arLocElementsIds = $elementsForLocation['ALL'];

        foreach ($LOCATION->getParentCodes() as $locationCode) {
            if ($elementsForLocation[$locationCode]) {
                $arLocElementsIds = array_merge($arLocElementsIds, $elementsForLocation[$locationCode]);
            }
        }

        $arEvents = array_intersect_key($arEvents, array_flip($arLocElementsIds));

        foreach ($arEvents as $eventNum => $arEvent) {
            if ($this->arParams['CURRENT_SECTION'] != $this->arParams['DEFAULT_SECTION']['EXTERNAL_ID'] && $arEvent['SECTION'] != $this->arParams['CURRENT_SECTION']) {
                unset($arEvents[$eventNum]);
            }
            if ($arEvent['UF_NO_SHOW_IN_SECTION']) {
                unset($arEvents[$eventNum]);
            }
        }

        return $this->createPagination($arEvents);
    }

    private function loadEvents()
    {
        global $LOCATION;
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'allEvents', $this->cacheDir)) {
            $arEvents = $this->cache->getVars()['events'];
            $eventsForLocation = $this->cache->getVars()['location'];
        } elseif ($this->cache->StartDataCache()) {
            $res = CIBlockElement::GetList(
                ['DATE_CREATE' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_CODE' => $this->arParams['IBLOCK_CODE'],
                    '!IBLOCK_SECTION_ID' => false,
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'IBLOCK_SECTION_ID',
                    'NAME',
                    'CODE',
                    'DATE_ACTIVE_FROM',
                    'DATE_ACTIVE_TO',
                    'DATE_CREATE',
                    'PREVIEW_PICTURE',
                    'PREVIEW_TEXT',
                    'DETAIL_PAGE_URL',
                    'PROPERTY_LOCATION',
                    'PROPERTY_NO_SHOW_IN_SECTION',
                    'PROPERTY_ELEMENT_LINK',
                ]
            );

            $ibName = $this->getIBName();

            while ($arItem = $res->Fetch()) {
                $arItem['SECTION'] = $this->sections['MENU'][$arItem['IBLOCK_SECTION_ID']]['EXTERNAL_ID'];
                $arItem['GTM_TYPE'] = $this->arGtmTypes[$arItem['SECTION']];
                $arItem['DETAIL_PAGE_URL'] = str_replace('#SECTION_CODE#', $arItem['SECTION'], $arItem['DETAIL_PAGE_URL']);
                $arItem['DETAIL_PAGE_URL'] = str_replace('#CODE#', $arItem['CODE'], $arItem['DETAIL_PAGE_URL']);

                // определение даты для сортировки
                if (!empty($arItem['DATE_ACTIVE_FROM'])) {
                    $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['DATE_ACTIVE_FROM']);
                } else {
                    $arItem['DATE_SORT'] = date_create_from_format('d.m.Y H:i:s', $arItem['DATE_CREATE']);
                }

                if ($arItem['DATE_ACTIVE_TO'] && date_create_from_format('d.m.Y H:i:s', $arItem['DATE_ACTIVE_TO']) <= date_create()) {
                    $arItem['DATE_END'] = true;
                }

                $arItem['DATE_ACTIVE_FROM'] = ConvertDateTime($arItem['DATE_ACTIVE_FROM'], "DD.MM.YYYY г.");
                $arItem['DATE_ACTIVE_TO'] = ConvertDateTime($arItem['DATE_ACTIVE_TO'], "DD.MM.YYYY г.");

                $arItem['DATE_STRING'] = '';
                if (empty($arItem['DATE_ACTIVE_FROM']) && !empty($arItem['DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = 'Завершение акции - ' . $arItem['DATE_ACTIVE_TO'];
                } elseif (!empty($arItem['DATE_ACTIVE_FROM']) && empty($arItem['DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = $arItem['DATE_ACTIVE_FROM'];
                } elseif (!empty($arItem['DATE_ACTIVE_FROM']) && !empty($arItem['DATE_ACTIVE_TO'])) {
                    $arItem['DATE_STRING'] = $arItem['DATE_ACTIVE_FROM'] . ' - ' . $arItem['DATE_ACTIVE_TO'];
                }

                $arItem['IB_NAME'] = $ibName ?? '';
                $arIdPreview[] = $arItem['PREVIEW_PICTURE'];
                if ($arItem['PROPERTY_LOCATION_VALUE']) {
                    foreach ($arItem['PROPERTY_LOCATION_VALUE'] as $locationCode) {
                        $eventsForLocation[$locationCode][$arItem['ID']] = $arItem['ID'];
                    }
                } else {
                    $eventsForLocation['ALL'][$arItem['ID']] = $arItem['ID'];
                }

                $arEvents[$arItem['ID']] = $arItem;
            }

            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $arNewImg[$arLocationImg['ID']] = '/' . COption::GetOptionString('main', 'upload_dir') .
                    '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'];
            }
            foreach ($arEvents as &$arResultNew) {
                $arResultNew['PREVIEW_PICTURE'] = $arNewImg[$arResultNew['PREVIEW_PICTURE']];
            }

            // сортируем
            function cmp($a, $b)
            {
                if ($a['DATE_SORT'] == $b['DATE_SORT']) {
                    return 0;
                }
                return ($a['DATE_SORT'] > $b['DATE_SORT']) ? -1 : 1;
            }

            uasort($arEvents, 'cmp');

            if (!empty($arEvents)) {
                $this->cache->EndDataCache(['events' => $arEvents, 'location' => $eventsForLocation]);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        $arLocEventsIds = $eventsForLocation['ALL'];

        foreach ($LOCATION->getParentCodes() as $locationCode) {
            if ($eventsForLocation[$locationCode]) {
                $arLocEventsIds = array_merge($arLocEventsIds, $eventsForLocation[$locationCode]);
            }
        }

        $arEvents = array_intersect_key($arEvents, array_flip($arLocEventsIds));

        foreach ($arEvents as $eventNum => $arEvent) {
            if ($this->arParams['CURRENT_SECTION'] != 'events' && $arEvent['SECTION'] != $this->arParams['CURRENT_SECTION']) {
                unset($arEvents[$eventNum]);
            }
            if ($arEvent['PROPERTY_NO_SHOW_IN_SECTION_VALUE'] == 'Y') {
                unset($arEvents[$eventNum]);
            }
        }

        return $this->createPagination($arEvents);
    }

    private function createPagination($arEvents)
    {
        $typeNav = mb_strtoupper($this->arParams['IBLOCK_CODE']);
        $navNum = null;
        if (!empty($_SESSION[$typeNav . '_SECTION_NAV_NUM'])) {
            $navNum = $_SESSION[$typeNav . '_SECTION_NAV_NUM'];
        }

        $pageSize = intval($this->request->get('SIZEN_' . $navNum));
        if (!$pageSize) {
            $pageSize = $_COOKIE[$typeNav . '_SORT_TO'];
        }
        $pageSize = in_array($pageSize, [12, 24, 36]) ? $pageSize : 12;
        $dbResult = new CDBResult();
        // костыль для того, что бы номер страницы всегда брался из URL
        CPageOption::SetOptionString("main", "nav_page_in_session", "N");
        $dbResult->InitFromArray($arEvents);
        $dbResult->NavStart($pageSize);
        $_SESSION[$typeNav . '_SECTION_NAV_NUM'] = $dbResult->NavNum;

        $this->arResult['NAV_STRING'] = $dbResult->GetPageNavString(
            $typeNav,
            'events',
            true,
            $this
        );

        $this->arResult['EVENTS'] = $dbResult->arResult;
        return $this->arResult['EVENTS'];
    }

    protected function createChain()
    {
        global $APPLICATION;

        $APPLICATION->AddChainItem(
            $this->arParams['DEFAULT_SECTION']['TITLE'],
            $this->arParams['DEFAULT_SECTION']['LINK']
        );
        if ($this->arParams['CURRENT_SECTION'] != $this->arParams['DEFAULT_SECTION']['EXTERNAL_ID']) {
            $APPLICATION->AddChainItem(
                $this->sections['CHAIN'][$this->arParams['CURRENT_SECTION']]['TITLE'],
                $this->sections['CHAIN'][$this->arParams['CURRENT_SECTION']]['URL']
            );
        }
    }

    private function loadSeoSection()
    {
        global $APPLICATION;
        $seo = [];
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'events_section_' . $this->arParams['CURRENT_SECTION'], $this->cacheSeoDir)) {
            $seo = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $ipropSectionValues = new SectionValues($this->arParams['IBLOCK_ID'], $this->currentSectionId);
            $seo = $ipropSectionValues->getValues();
            if (!empty($seo)) {
                $this->cache->EndDataCache($seo);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        $APPLICATION->SetTitle($seo['SECTION_PAGE_TITLE']);
        $APPLICATION->SetPageProperty("title", $seo['SECTION_META_TITLE']);
        $APPLICATION->SetPageProperty("keywords", $seo['SECTION_META_KEYWORDS']);
        $APPLICATION->SetPageProperty("description", $seo['SECTION_META_DESCRIPTION']);
    }

    private function getIBName()
    {
        return CIBlock::GetByID($this->arParams['IBLOCK_ID'])->Fetch()['NAME'];
    }
}
