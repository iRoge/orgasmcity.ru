<?php
use Bitrix\Main\FileTable;
use Bitrix\Sale\Order;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityFeedbackListComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
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
            ["nTopCount" => isset($this->arParams['LIMIT']) ? $this->arParams['FILTERS'] : 15],
            $arSelect,
        );

        while ($item = $result->GetNext()) {
            $item['DATE_CREATE'] = FormatDate("x", MakeTimeStamp($item['DATE_CREATE']));
            $arItems[$item['ID']] = $item;
        }

        return $arItems;
    }
}
