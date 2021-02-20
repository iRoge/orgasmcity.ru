<?php

namespace Sprint\Migration;


class CatalogGroupsSizes20180115191402 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Размер',
            'CODE' => 'OFFERS_SIZE',
            'PROPERTY_TYPE' => 'S',
            'SORT' => '300',
            'HINT' => 'Список значений разделенных запятыми'
        ]);
    }

    public function down(){
        $helper = new HelperManager();
        
        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'OFFERS_SIZE');
    }

}
