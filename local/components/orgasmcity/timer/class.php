<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class TimerComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    public function executeComponent()
    {
        $this->arResult['DATE_TO'] = $this->arParams['DATE_TO'];
        $this->arResult['ARRAY_DATE_TO'] = date_parse_from_format("d.m.Y H:i:s", $this->arParams['DATE_TO']);
        $this->includeComponentTemplate();
    }
}