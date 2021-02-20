<?php

namespace Sprint\Migration;

class ImportOffer20190816111052 extends Version
{

    protected $description = "feature/112695.FixOffers";

    // ИД полей с умным фильтром
    private $arPropOFFERS = [237];
    private $arPropCATALOG = [238, 239, 248, 249, 261, 264];

    public function up()
    {
        // Удаляем фасетный индекс для ИБ Каталога товаров и ТП
        \Bitrix\Iblock\PropertyIndex\Manager::dropIfExists(IBLOCK_OFFERS);
        \Bitrix\Iblock\PropertyIndex\Manager::dropIfExists(IBLOCK_CATALOG);

        // Для свойств отключаем параметр Умный фильтр
        $ibp = new \CIBlockProperty;
        $arFields = [
            'IBLOCK_ID' => IBLOCK_OFFERS,
            'SMART_FILTER' => 'N'
        ];
        foreach ($this->arPropOFFERS as $id) {
            $res = $ibp->Update($id, $arFields) == 1 ? 'ok' : 'error';
            echo 'Property id = ' . $id . ' edit ' . $res . '</br>';
        }
        $arFields = [
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'SMART_FILTER' => 'N'
        ];
        foreach ($this->arPropCATALOG as $id) {
            $res = $ibp->Update($id, $arFields) == 1 ? 'ok' : 'error';
            echo 'Property id = ' . $id . ' edit ' . $res . '</br>';
        }
    }

    public function down()
    {

        // Для свойств включаем параметр Умный фильтр
        $ibp = new \CIBlockProperty;
        $arFields = [
            'IBLOCK_ID' => IBLOCK_OFFERS,
            'SMART_FILTER' => 'Y'
        ];
        foreach ($this->arPropOFFERS as $id) {
            $res = $ibp->Update($id, $arFields) == 1 ? 'ok' : 'error';
            echo 'Property id = ' . $id . ' edit ' . $res . '</br>';
        }
        $arFields = [
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'SMART_FILTER' => 'Y'
        ];
        foreach ($this->arPropCATALOG as $id) {
            $res = $ibp->Update($id, $arFields) == 1 ? 'ok' : 'error';
            echo 'Property id = ' . $id . ' edit ' . $res . '</br>';
        }

        // Создаем фасетный индекс для ИБ Каталога товаров и ТП
        \Bitrix\Iblock\PropertyIndex\Manager::createIndexer(IBLOCK_OFFERS);
        \Bitrix\Iblock\PropertyIndex\Manager::createIndexer(IBLOCK_CATALOG);
    }
}
