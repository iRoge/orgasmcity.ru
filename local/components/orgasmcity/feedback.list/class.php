<?php
use Bitrix\Main\FileTable;
use Bitrix\Sale\Order;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityFeedbackListComponent extends CBitrixComponent
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
        $this->arResult['ITEMS'] = $this->getItems();
        if (isset($this->arParams['PRODUCT_ID'])) {
            $this->arResult['PRODUCT_ID'] = $this->arParams['PRODUCT_ID'];
        }
        $this->includeComponentTemplate();
    }

    public function getItems()
    {
        $arItems = [];
        $cache = new CPHPCache;

        if ($cache->InitCache($this->arParams['CACHE_TIME'], 'feedbacks|' . serialize($this->arParams['FILTERS']), 'feedbacks')) {
            $arItems = $cache->GetVars()['feedbacks'];
        } elseif ($cache->StartDataCache()) {
            $this->cacheManager->StartTagCache('feedbacks');
            $this->cacheManager->RegisterTag('catalogAll');

            $arFilter = $this->arParams['FILTERS'];
            $arSelect = [
                'ID',
                'NAME',
                'DETAIL_TEXT',
                'DATE_CREATE',
                'PROPERTY_GENDER',
                'PROPERTY_SCORE',
            ];

            $result = CIBlockElement::GetList(
                ["ID" => "DESC"],
                $arFilter,
                false,
                false,
                $arSelect,
            );

            while ($item = $result->GetNext()) {
                $arItems[$item['ID']] = $item;
            }

            $this->cacheManager->endTagCache();
            $cache->EndDataCache(['feedbacks' => $arItems]);
        }


        return $arItems;
    }
}
