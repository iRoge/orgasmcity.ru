<?php
use Bitrix\Main\FileTable;
use Qsoft\Helpers\BonusSystem;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityUserBonusInfoComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);

        return $arParams;
    }

    public function executeComponent()
    {
        global $USER;
        if (!$USER->IsAuthorized()) {
            LocalRedirect('/auth/?back_url=/personal/bonuses/');
        }
        $bonusHelper = new BonusSystem($USER->GetID());
        $bonusHelper->recalcUserBonus();
        $this->arResult['USER_ORDER_SUM'] = $bonusHelper->getUsersPaidOrdersSum();
        $this->arResult['USER_BONUS'] = $bonusHelper->getCurrentBonus();
        $this->includeComponentTemplate();
    }
}
