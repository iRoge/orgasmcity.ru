<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class NewComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    public function executeComponent()
    {
        $this->arResult['ITEMS'] = $this->getItems();
        $this->includeComponentTemplate();
    }

    public function getItems()
    {
        global $CACHE_MANAGER;
        $cache = new CPHPCache;
        $arResultItems = [];
        if ($cache->InitCache(86400, 'actionBanners', '/banners')) {
            $arResultItems = $cache->GetVars()['banners'];
        } elseif ($cache->StartDataCache()) {
            $CACHE_MANAGER->StartTagCache('/actionBanners');
            $CACHE_MANAGER->RegisterTag('catalogAll');

            $rsItems = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => IBLOCK_GROUPS,
                    'ACTIVE' => 'Y',
                    'PROPERTY_SHOW_ACTION' => 1,
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'PREVIEW_PICTURE',
                    'NAME',
                    'CODE',
                ]
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
                if ($count == 3) {
                    break;
                }
            }

            $CACHE_MANAGER->endTagCache();
            $cache->EndDataCache(['banners' => $arResultItems]);
        }

        return $arResultItems;
    }
}