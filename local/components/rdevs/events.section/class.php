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
        'blog' => 'Блог',
    ];

    public function executeComponent()
    {
        $this->initCache();
        $this->sections = $this->loadSections();
        if ($this->arParams['IBLOCK_CODE'] == 'blog') {
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
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'allElement', $this->cacheDir)) {
            $arEvents = $this->cache->getVars()['events'];
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

            while ($arItem = $res->GetNext()) {
                $arItem['PREVIEW_TEXT'] = $arItem['DESCRIPTION'];

                $arItem['SECTION'] = $this->sections['MENU'][$arItem['IBLOCK_SECTION_ID']]['EXTERNAL_ID'];
                $arItem['GTM_TYPE'] = $this->arGtmTypes[$arItem['SECTION']];
                $arItem['DETAIL_PAGE_URL'] = str_replace('#SECTION_CODE#', $arItem['SECTION'], $arItem['SECTION_PAGE_URL']);
                $arItem['DETAIL_PAGE_URL'] .= $arItem['CODE'] . '/';

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
            $this->cache->EndDataCache(['events' => $arEvents]);
        } else {
            $this->cache->AbortDataCache();
        }

        foreach ($arEvents as $eventNum => $arEvent) {
            if ($this->arParams['CURRENT_SECTION'] != $this->arParams['DEFAULT_SECTION']['EXTERNAL_ID'] && $arEvent['SECTION'] != $this->arParams['CURRENT_SECTION']) {
                unset($arEvents[$eventNum]);
            }
        }

        return $this->createPagination($arEvents);
    }

    private function loadEvents()
    {
        $arEvents = [];
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'allPosts', $this->cacheDir)) {
            $arEvents = $this->cache->getVars()['posts'];
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
                $this->cache->EndDataCache(['posts' => $arEvents]);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        foreach ($arEvents as $eventNum => $arEvent) {
            if ($this->arParams['CURRENT_SECTION'] != 'blog' && $arEvent['SECTION'] != $this->arParams['CURRENT_SECTION']) {
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

        $APPLICATION->SetTitle($seo['SECTION_PAGE_TITLE'] ?? 'Блог');
        $APPLICATION->SetPageProperty("title", $seo['SECTION_META_TITLE'] ?? 'Блог о сексологии в Городе Оргазма');
        $APPLICATION->SetPageProperty("keywords", $seo['SECTION_META_KEYWORDS'] ?? 'сексология, блог о сексе, секс товары, товары для взрослых, обзор секс товаров, обзор вибраторов, обзор дилдо, обзор фалосов, обзор мастурбаторов');
        $APPLICATION->SetPageProperty("description", $seo['SECTION_META_DESCRIPTION'] ?? 'В Городе Оргазма вы можете узнать множество полезной информации о товарах для взрослых: обзор вибраторов, обзор дилдо, обзор фалосов, обзор мастурбаторов');
    }

    private function getIBName()
    {
        return CIBlock::GetByID($this->arParams['IBLOCK_ID'])->Fetch()['NAME'];
    }
}
