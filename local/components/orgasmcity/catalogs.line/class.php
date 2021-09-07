<?php
use Bitrix\Main\FileTable;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityCatalogsLineComponent extends CBitrixComponent
{
    private $cacheManager;

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        global $CACHE_MANAGER;
        $this->cacheManager = $CACHE_MANAGER;

        if (!isset($arParams['SHOW_SLIDER'])) {
            $arParams['SHOW_SLIDER'] = true;
        }

        return $arParams;
    }

    public function executeComponent()
    {
        global $DEVICE;
        $this->arResult['ITEMS'] = $this->getCatalogs();
        $this->arResult['SHOW_SLIDER'] = $this->arParams['SHOW_SLIDER'] && !($DEVICE->isMobile() || $DEVICE->isTablet()) && count($this->arResult['ITEMS']) > 4;
        $this->arResult['SHOW_BACKGROUND'] = $this->arParams['SHOW_BACKGROUND'] ?? true;
        $this->includeComponentTemplate();
    }

    public function getCatalogs()
    {
        $productsCache = new CPHPCache;
        $arSections = [];

        if ($productsCache->InitCache(86400, 'catalogs|' . serialize($this->arParams['FILTERS']), 'catalogsLine')) {
            $arSections = $productsCache->GetVars()['catalogs'];
        } elseif ($productsCache->StartDataCache()) {
            $this->cacheManager->StartTagCache('catalogsLine');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = $this->arParams['FILTERS'];
            $rsSections = CIBlockSection::GetList(
                ['SORT' => 'ASC'],
                $arFilter,
                false,
                ['ID', 'CODE', 'NAME', 'SECTION_PAGE_URL']
            );

            $count = 0;
            $maxCount = $this->arParams['MAX_COUNT'] ?? 12;
            while ($arSection = $rsSections->GetNext())
            {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.svg';
                $imgPath = SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.svg';
                $arSection['IMG_PATH'] = is_file($filePath) ? $imgPath : SITE_TEMPLATE_PATH . '/img/svg/catalogs/779.svg';
                $arSections[] = $arSection;
                $count++;
                if ($count == $maxCount) {
                    break;
                }
            }

            $this->cacheManager->endTagCache();
            $productsCache->EndDataCache(['catalogs' => $arSections]);
        }

        return $arSections;
    }
}
