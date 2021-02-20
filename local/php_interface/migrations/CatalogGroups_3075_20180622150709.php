<?php

namespace Sprint\Migration;


class CatalogGroups_3075_20180622150709 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Вид изделия для Интернет-магазина',
            'CODE' => 'SUBTYPEPRODUCT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_subtype_product'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Коллекция',
            'CODE' => 'COLLECTION',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_collection'),
        ]);
        
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Страна происхождения',
            'CODE' => 'COUNTRY',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_country'),
        ]);
        
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Высота каблука',
            'CODE' => 'HEELHEIGHT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_heel_height'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'PriceSegmentID',
            'CODE' => 'PRICESEGMENTID',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_pricesegmentid'),
        ]);
    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SUBTYPEPRODUCT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'COLLECTION');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'COUNTRY');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'HEELHEIGHT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'PRICESEGMENTID');
    }

}
