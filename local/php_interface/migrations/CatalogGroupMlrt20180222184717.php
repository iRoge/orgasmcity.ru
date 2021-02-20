<?php

namespace Sprint\Migration;


class CatalogGroupMlrt20180222184717 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'MLT',
            'CODE' => 'MLT',
            'HINT' => 'Выбор привед к отображению в меню спец. раздела',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_mlt'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'MRT',
            'CODE' => 'MRT',
            'HINT' => 'Выбор привед к отображению в меню спец. раздела',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_mrt'),
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'MLT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'MRT');
    }

}
