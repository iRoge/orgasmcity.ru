<?php
use Bitrix\Main\FileTable;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityMiniBannersComponent extends CBitrixComponent
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
        $this->arResult['ITEMS'] = $this->getBanners();

        $this->includeComponentTemplate();
    }

    public function getBanners()
    {
        $cache = new CPHPCache;
        $arResultItems = [];

        if ($cache->InitCache(86400, 'banners|' . serialize($this->arParams['FILTERS']), 'banners')) {
            $arResultItems = $cache->GetVars()['banners'];
        } elseif ($cache->StartDataCache()) {
            $this->cacheManager->StartTagCache('miniBanners');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = $this->arParams['FILTERS'];
            $rsItems = CIBlockElement::GetList(
                ['SORT' => 'ASC'],
                $arFilter,
                false,
                false,
                ['ID', 'CODE', 'NAME', 'PREVIEW_PICTURE']
            );

            $count = 0;
            while ($arItem = $rsItems->GetNext())
            {
                if (!$arItem["PREVIEW_PICTURE"]) {
                    continue;
                }
                $arItem['PREVIEW_PICTURE_SRC'] = CFile::GetPath($arItem["PREVIEW_PICTURE"]);
                $arResultItems[$arItem['ID']] = $arItem;
                $count++;
                if ($count == 4) {
                    break;
                }
            }

            $this->cacheManager->endTagCache();
            $cache->EndDataCache(['banners' => $arResultItems]);
        }

        return $arResultItems;
    }

}
