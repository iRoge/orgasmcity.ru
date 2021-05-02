<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Qsoft\Helpers\ComponentHelper;
use Qsoft\Pvzmap\PVZFactory;

class PVZMap extends ComponentHelper
{
    private $cacheKey = 'PVZ';
    protected string $relativePath = '/qsoft/pvzmap';

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 86400;
        }

        if (empty($arParams['CACHE_TYPE'])) {
            $arParams['CACHE_TYPE'] = "A";
        }

        if (empty($arParams['IS_AJAX'])) {
            $arParams['IS_AJAX'] = false;
        }

        parent::onPrepareComponentParams($arParams);

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->arParams['IS_AJAX']) {
            if (Loader::includeModule('qsoft.pvzmap')) {
                $this->arResult['PVZ'] = $this->getPVZCollectionByCity();
            }
        }

        $this->IncludeComponentTemplate();
    }

    private function getPVZCollection()
    {
        global $LOCATION;
        global $CACHE_MANAGER;

        //Возвращать json всех доступных для данного заказа ПВЗ.
        $arReturn = [];
        //$cache = new CPHPCache;
        //if ($cache->InitCache($this->arParams['CACHE_TIME'], $this->cacheKey, '/')) {
        //    $arReturn = $cache->GetVars();
        //}
        //if (empty($arReturn)) {
        //    $cache->StartDataCache();
        //    $CACHE_MANAGER->StartTagCache('/');
        //    $CACHE_MANAGER->RegisterTag("catalogAll");
            $arReturn = PVZFactory::getPVZCollection();
        //    if (!empty($arReturn)) {
        //        $CACHE_MANAGER->EndTagCache();
        //        $cache->EndDataCache($arReturn);
        //    } else {
        //        $CACHE_MANAGER->AbortTagCache();
        //        $cache->AbortDataCache();
        //    }
        //}

        $arReturn['CENTER'] = $LOCATION->getLocationCoords();
        //Сборка массива с данными о службах доставки
        $arClasses = array_keys($arReturn['CLASS_MAP']);
        $arReturn['DELIVERY_SERVICES'] = $this->getDeleliveriesArray($arClasses);

        return $arReturn;
    }

    private function getPVZCollectionByCity()
    {
        global $LOCATION;
        switch ($LOCATION->getRegion()) {
            case 'Московская область':
                $city = 'Москва';
                break;
            case 'Ленинградская область':
                $city = 'Санкт-Петербург';
                break;
            default:
                $city = $LOCATION->getName();
        }
        $arPVZ = $this->getPVZCollection();
        $arPVZ['PVZ'] = PVZFactory::getPVZCollectionByCity($city, $arPVZ['PVZ']);
        foreach ($arPVZ['PVZ'] as $key => $pvz) {
            if (empty($pvz)) {
                unset($arPVZ['PVZ'][$key]);
            }
        }
        return $arPVZ;
    }

    public function getPVZCollectionByCityAsArray()
    {
        $this->arParams = $this->onPrepareComponentParams([]);
        return $this->getPVZCollectionByCity();
    }

    private function getDeleliveriesArray($arClasses)
    {
        $arReturn = [];

        foreach ($arClasses as $class_name) {
            if (isset($GLOBALS['PVZ_IDS'][$class_name])) {
                $arReturn[$class_name]['ID'] = (string)$GLOBALS['PVZ_IDS'][$class_name];
                $arReturn[$class_name]['PRICE'] = (string)$GLOBALS['PVZ_PRICES'][$class_name];
            }
        }

        return $arReturn;
    }
}
