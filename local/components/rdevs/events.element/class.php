<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\InheritedProperty\ElementValues;

class EventsElementComponent extends EventsComponent
{
    private string $cacheSeoDir = 'seo';
    private array $arEvent = [];
    private array $arColors = [];

    public function executeComponent()
    {
        $this->initCache();
        if ($this->arParams['IBLOCK_CODE'] == 'events') {
            $this->arColors = $this->loadColor();
            $this->arEvent = $this->loadEvent();
        } else {
            $this->arEvent = $this->loadElementGroup();
        }

        $this->arResult['EVENT'] = $this->arEvent;
        $this->arResult['CONTEST'] = $this->arEvent['CONTEST'];
        $this->createChain();
        $this->loadSeoSection();
        $this->IncludeComponentTemplate();
    }

    private function loadEvent()
    {
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], $this->arParams['CURRENT_ELEMENT'], $this->cacheDir)) {
            $arItem = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $res = CIBlockElement::GetList(
                ['DATE_CREATE' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_CODE' => $this->arParams['IBLOCK_CODE'],
                    'CODE' => $this->arParams['CURRENT_ELEMENT'],
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'DETAIL_PICTURE',
                    'DETAIL_TEXT',
                    'DATE_ACTIVE_TO',
                    'DATE_ACTIVE_FROM',
                    'PROPERTY_PHOTO_LINK_ACTIVE',
                    'PROPERTY_PHOTO_LINK',
                    'PROPERTY_PICTURE_POSITION',
                    'PROPERTY_ENABLE_CONTEST',
                    'PROPERTY_CONTEST_RULES_SHOW',
                    'PROPERTY_CONTEST_FORM_TITLE',
                    'PROPERTY_CONTEST_FIELDS_NAME',
                    'PROPERTY_CONTEST_FIELDS_PHONE',
                    'PROPERTY_CONTEST_FIELDS_BIRTHDATE',
                    'PROPERTY_CONTEST_FIELDS_INSTA',
                    'PROPERTY_CONTEST_FIELDS_FILE_1',
                    'PROPERTY_CONTEST_FIELDS_FILE_2',
                    'PROPERTY_CONTEST_BTN_FILE_1',
                    'PROPERTY_CONTEST_BTN_FILE_2',
                    'PROPERTY_CONTEST_CHECK_FILE_1',
                    'PROPERTY_CONTEST_CHECK_FILE_2',
                    'PROPERTY_CONTEST_FORM_BTN_TEXT',
                    'PROPERTY_CONTEST_END',
                    'PROPERTY_CONTEST_RULES',
                    'PROPERTY_CONTEST_EMAILS',
                    'PROPERTY_CONTEST_BTN_COLOR',
                    'PROPERTY_CONTEST_BTN_TEXT_COLOR',
                    'PROPERTY_CONTEST_THANKYOU_TEXT',
                    'PROPERTY_CONTEST_TYPE',
                    'PROPERTY_CONTEST_BTN_TEXT',
                ]
            );

            $arItem = $res->Fetch();
            $arItem['LIST_PAGE_URL'] .= $arItem['SECTION'] . '/';
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

            $arIdPreview[] = $arItem['DETAIL_PICTURE'];

            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $arNewImg[$arLocationImg['ID']] = '/' . COption::GetOptionString('main', 'upload_dir') .
                    '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'];
            }

            $arItem['DETAIL_PICTURE'] = $arNewImg[$arItem['DETAIL_PICTURE']];

            $listRes = CIBlockPropertyEnum::GetList([], ['ID' => $arItem['PROPERTY_PICTURE_POSITION_ENUM_ID']]);
            $arItem['PROPERTY_PICTURE_POSITION_VALUE'] = $listRes->Fetch()['EXTERNAL_ID'];

            if (!empty($arItem['PROPERTY_ENABLE_CONTEST_VALUE'])) {
                $arItem['CONTEST']['ENABLE'] = true;
                $arItem['CONTEST']['SHOW_RULES'] = $arItem['PROPERTY_CONTEST_RULES_SHOW_VALUE'];
                $arItem['CONTEST']['TEXT_RULES'] = $arItem['PROPERTY_CONTEST_RULES_VALUE']['TEXT'];
                $arItem['CONTEST']['FORM_TITLE'] = $arItem['PROPERTY_CONTEST_FORM_TITLE_VALUE'];
                $arItem['CONTEST']['SHOW_NAME_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_NAME_VALUE'];
                $arItem['CONTEST']['SHOW_PHONE_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_PHONE_VALUE'];
                $arItem['CONTEST']['SHOW_BD_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_BIRTHDATE_VALUE'];
                $arItem['CONTEST']['SHOW_INSTA_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_INSTA_VALUE'];
                $arItem['CONTEST']['SHOW_FILE1_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_FILE_1_VALUE'];
                $arItem['CONTEST']['SHOW_FILE2_INPUT'] = $arItem['PROPERTY_CONTEST_FIELDS_FILE_2_VALUE'];
                $arItem['CONTEST']['TEXT_BTN_FILE1'] = $arItem['PROPERTY_CONTEST_BTN_FILE_1_VALUE'];
                $arItem['CONTEST']['TEXT_BTN_FILE2'] = $arItem['PROPERTY_CONTEST_BTN_FILE_2_VALUE'];
                $arItem['CONTEST']['REQ_FILE1'] = $arItem['PROPERTY_CONTEST_CHECK_FILE_1_VALUE'];
                $arItem['CONTEST']['REQ_FILE2'] = $arItem['PROPERTY_CONTEST_CHECK_FILE_2_VALUE'];
                $arItem['CONTEST']['TEXT_BTN_FORM'] = $arItem['PROPERTY_CONTEST_FORM_BTN_TEXT_VALUE'];
                $arItem['CONTEST']['TEXT_END'] = $arItem['PROPERTY_CONTEST_END_VALUE']['TEXT'];
                $arItem['CONTEST']['EMAILS'] = $arItem['PROPERTY_CONTEST_EMAILS_VALUE'];
                $arItem['CONTEST']['TEXT_THANKYOU'] = $arItem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE']['TEXT'];
                $arItem['CONTEST']['TEXT_BTN'] = $arItem['PROPERTY_CONTEST_BTN_TEXT_VALUE'];
                $arItem['CONTEST']['COLOR_BTN'] = $this->arColors[$arItem['PROPERTY_CONTEST_BTN_COLOR_VALUE']];
                $arItem['CONTEST']['COLOR_TEXT_BTN'] = $this->arColors[$arItem['PROPERTY_CONTEST_BTN_TEXT_COLOR_VALUE']];
                $arItem['CONTEST']['STYLE_BTN'] = 'style="';
                $arItem['CONTEST']['STYLE_BTN'] .= !empty($arItem['CONTEST']['COLOR_BTN']) ? ('background-color: #' . $arItem['CONTEST']['COLOR_BTN'] . ';') : '';
                $arItem['CONTEST']['STYLE_BTN'] .= !empty($arItem['CONTEST']['COLOR_TEXT_BTN']) ? ('color: #' . $arItem['CONTEST']['COLOR_TEXT_BTN'] . ';') : '';
                $arItem['CONTEST']['STYLE_BTN'] .= '"';
            }

            if (!empty($arItem)) {
                $this->cache->EndDataCache($arItem);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        return $arItem;
    }

    private function loadElementGroup()
    {
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], $this->arParams['CURRENT_ELEMENT'], $this->cacheDir)) {
            $arItem = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $res = CIBlockSection::GetList(
                ['SORT' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'CODE' => $this->arParams['CURRENT_ELEMENT'],
                ],
                false,
                [
                    'ID',
                ]
            );
            $parentItem = $res->Fetch();

            $res = CIBlockElement::GetList(
                ['DATE_CREATE' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_CODE' => $this->arParams['IBLOCK_CODE'],
                    'IBLOCK_SECTION_ID' => $parentItem['ID'],
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'DETAIL_TEXT',
                    'DATE_ACTIVE_TO',
                    'DATE_ACTIVE_FROM',
                    'PROPERTY_PHOTO_LINK_ACTIVE',
                    'PROPERTY_PHOTO_LINK',
                    'PROPERTY_PICTURE',
                    'PROPERTY_PICTURE_POSITION',
                ]
            );

            $arItem = $res->Fetch();

            $arIdPreview[] = $arItem['PROPERTY_PICTURE_VALUE'];

            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $arNewImg[$arLocationImg['ID']] = '/' . COption::GetOptionString('main', 'upload_dir') .
                    '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'];
            }

            $listRes = CIBlockPropertyEnum::GetList([], ['ID' => $arItem['PROPERTY_PICTURE_POSITION_ENUM_ID']]);
            $arItem['PROPERTY_PICTURE_POSITION_VALUE'] = $listRes->Fetch()['EXTERNAL_ID'];

            $arItem['DETAIL_PICTURE'] = $arNewImg[$arItem['PROPERTY_PICTURE_VALUE']];

            if (!empty($arItem)) {
                $this->cache->EndDataCache($arItem);
            } else {
                $this->cache->AbortDataCache();
            }
        }
        return $arItem;
    }

    private function loadColor()
    {
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'colors', $this->cacheDir)) {
            $arColors = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $rs = CIBlockElement::GetList([], ['IBLOCK_CODE' => 'COLORS'], false, [], ['ID', 'PROPERTY_COLOR']);
            while ($color = $rs->Fetch()) {
                $arColors[$color['ID']] = $color['PROPERTY_COLOR_VALUE'];
            }
            if (!empty($arColors)) {
                $this->cache->EndDataCache($arColors);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        return $arColors;
    }

    private function loadSeoSection()
    {
        global $APPLICATION;
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'event_' . $this->arParams['CURRENT_ELEMENT'], $this->cacheSeoDir)) {
            $seo = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $ipropValues = new ElementValues($this->arParams['IBLOCK_ID'], $this->arEvent['ID']);
            $seo = $ipropValues->getValues();
            if (!empty($seo)) {
                $this->cache->EndDataCache($seo);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        $APPLICATION->SetTitle($seo['ELEMENT_PAGE_TITLE']);
        $APPLICATION->SetPageProperty("title", $seo['ELEMENT_META_TITLE']);
        $APPLICATION->SetPageProperty("keywords", $seo['ELEMENT_META_KEYWORDS']);
        $APPLICATION->SetPageProperty("description", $seo['ELEMENT_META_DESCRIPTION']);

        return $seo;
    }
}
