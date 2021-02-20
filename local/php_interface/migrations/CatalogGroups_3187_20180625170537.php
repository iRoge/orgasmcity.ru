<?php

namespace Sprint\Migration;


class CatalogGroups_3187_20180625170537 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Процент уценки/скидки ОТ (ценовой сегмент)',
            'CODE' => 'SEGMENT_FROM',
            'PROPERTY_TYPE' => 'N',
            'HINT' => 'Необходимо использовать со связкой PriceSegmentID'
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Процент уценки/скидки ДО (ценовой сегмент)',
            'CODE' => 'SEGMENT_TO',
            'PROPERTY_TYPE' => 'N',
            'HINT' => 'Необходимо использовать со связкой PriceSegmentID'
        ]);

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Процент уценки/скидки ценового сегмента',
            'CODE' => 'SEGMENT_PCT',
            'PROPERTY_TYPE' => 'N',
            'HINT' => 'Значение поля формируется автоматически'
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SEGMENT_FROM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SEGMENT_TO');

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SEGMENT_PCT');
    }

}
