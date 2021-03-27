<?php


namespace Qsoft\Pvzmap\PVZ;

use COption;
use Qsoft\Pvzmap\iPvz;
use CModule;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Qsoft\Pvzmap\PVZFactory;

class Respect implements iPvz
{
    private function loadData()
    {
        $stores = \CCatalogStore::GetList(
            ['SORT' => 'ASC'],
            [
                'ACTIVE' => 'Y',
            ],
            false,
            false,
            [
                'ID',
                'TITLE',
                'ADDRESS',
                'GPS_N',
                'GPS_S',
                'XML_ID',
                'UF_PHONES',
                'UF_CITY',
                'UF_METRO',
                'SCHEDULE',
                'UF_STORE_IS_PVZ',
                'UF_STORE_PVZ_CARD',
                'UF_STORE_PVZ_CASH',
                'UF_STORE_PVZ_DRESS',
                'UF_STORE_PVZ_TEXT',
            ]
        );
        return $stores;
    }

    private function prepareData()
    {
        /*
        CModule::IncludeModule('highloadblock');
        $hlblock = HLBT::getList(array('filter' => array('=NAME' => 'Metro')))->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $rsData = $entity_data_class::getList(array(
            'filter' => array(),
            'select' => array('ID', 'UF_NAME'),
        ));
        while ($metro = $rsData->fetch()) {
            $arMetro[$metro['ID']] = $metro['UF_NAME'];
        }
        */
        $stores = $this->loadData();
        foreach (PVZFactory::loadPVZ() as $pvz) {
            $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
        }
        $hideOnlyPrepayment = $hideOnlyPrepayment['Respect'];
        while ($store = $stores->Fetch()) {
            if (!$store['UF_STORE_IS_PVZ']) {
                continue;
            }
            if ($hideOnlyPrepayment == 'Y' && (!($store['UF_STORE_PVZ_CARD'] === '1' || $store['UF_STORE_PVZ_CASH'] === '1'))) {
                continue;
            }

            $store['UF_PHONES'] = unserialize($store['UF_PHONES']);
            /*
            $store['UF_METRO'] = unserialize($store['UF_METRO']);
            $store['UF_METRO'] = $arMetro[$store['UF_METRO'][0]];
            if (!empty($store['UF_METRO'])) {
                $store['ADDRESS'] = 'м. ' . $store['UF_METRO'] . ', ' . $store['ADDRESS'];
                $store['ADDRESS'] = $store['UF_METRO'] . ', ' . $store['ADDRESS'];
            }
                */
            $arPVZ[strtoupper($store['UF_CITY'])][] = $store;
        }
        return $arPVZ;
    }

    public function getData()
    {
        //Обертка  prepareData
        return $this->prepareData();
    }

    public function getArray()
    {
        return $this->prepareData();
    }

    public function getArrayByCity($city, $arPVZ)
    {
        $city = mb_strtoupper($city);

        return $arPVZ[$city];
    }
}
