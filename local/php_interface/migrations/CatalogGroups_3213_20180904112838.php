<?php

namespace Sprint\Migration;


class CatalogGroups_3213_20180904112838 extends Version {

    protected $description = "Логотипы для групп";

    public function up(){
        $helper = new HelperManager();
        
        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $iGroupsIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'CODE' => 'SHOW_IN_GROUPS',
            'NAME' => 'Показывать иконки групп',
            'SORT' => '1000',
            'PROPERTY_TYPE' => 'E',
            'LIST_TYPE' => 'L',
            'MULTIPLE_CNT' => '1',
            'MULTIPLE' => 'Y',
            'LINK_IBLOCK_ID' => $iGroupsIBlockID,
        ]);
    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SHOW_IN_GROUPS');
    }

}
