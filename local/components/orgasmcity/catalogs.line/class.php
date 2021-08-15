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

        return $arParams;
    }

    public function executeComponent()
    {
        $this->arResult['ITEMS'] = $this->getCatalogs();

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
            while ($arSection = $rsSections->GetNext())
            {
                $arSections[] = $arSection;
                $count++;
                if ($count == 12) {
                    break;
                }
            }

            $this->cacheManager->endTagCache();
            $productsCache->EndDataCache(['catalogs' => $arSections]);
        }

        return $arSections;
    }
}
