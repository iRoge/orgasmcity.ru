<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class BrandsList extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    public function executeComponent()
    {
        $allBrands = Functions::getAllBrands();

        $this->arResult['ITEMS'] = array_slice($allBrands, 0, 18);

        $this->includeComponentTemplate();
    }
}