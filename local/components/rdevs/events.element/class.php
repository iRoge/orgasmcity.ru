<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\InheritedProperty\ElementValues;

class EventsElementComponent extends EventsComponent
{
    private string $cacheSeoDir = 'seo';
    private array $arPost = [];
    private array $arColors = [];

    public function executeComponent()
    {
        $this->initCache();
        $this->arPost = $this->loadEvent();
        $this->arResult['POST'] = $this->arPost;
        $this->arResult['CONTEST'] = $this->arPost['CONTEST'];
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


            if (!empty($arItem)) {
                $this->cache->EndDataCache($arItem);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        return $arItem;
    }

    private function loadSeoSection()
    {
        global $APPLICATION;
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'event_' . $this->arParams['CURRENT_ELEMENT'], $this->cacheSeoDir)) {
            $seo = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $ipropValues = new ElementValues($this->arParams['IBLOCK_ID'], $this->arPost['ID']);
            $seo = $ipropValues->getValues();
            if (!empty($seo)) {
                $this->cache->EndDataCache($seo);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        $APPLICATION->SetTitle($seo['ELEMENT_PAGE_TITLE'] ?? $this->arResult['POST']['NAME']);
        $APPLICATION->SetPageProperty("title", $seo['ELEMENT_META_TITLE']);
        $APPLICATION->SetPageProperty("keywords", $seo['ELEMENT_META_KEYWORDS']);
        $APPLICATION->SetPageProperty("description", $seo['ELEMENT_META_DESCRIPTION']);

        return $seo;
    }
}
