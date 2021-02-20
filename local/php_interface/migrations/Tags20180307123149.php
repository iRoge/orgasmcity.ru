<?php

namespace Sprint\Migration;


class Tags20180307123149 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Теги',
            'CODE' => 'CATALOG_TAGS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цена от',
            'CODE' => 'PRICE_FROM',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '200',
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цена до',
            'CODE' => 'PRICE_TO',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '250',
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Размер',
            'CODE' => 'OFFERS_SIZE',
            'PROPERTY_TYPE' => 'S',
            'SORT' => '300',
            'HINT' => 'Список значений разделенных запятыми'
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Материал подкладки',
            'CODE' => 'LININGMATERIAL',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_lining_material'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Материал верха',
            'CODE' => 'UPPERMATERIAL',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_upper_material'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Род изделия',
            'CODE' => 'RHODEPRODUCT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_rhode_product'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цвет',
            'CODE' => 'COLOR',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_color'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Сезон',
            'CODE' => 'SEASON',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_season'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Высота каблука',
            'CODE' => 'HEELHEIGHT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_heel_height'),
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('CATALOG_TAGS');

    }

}
