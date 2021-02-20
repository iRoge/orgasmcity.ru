<?php

namespace Sprint\Migration;

class ColorFilter201911131552123213 extends Version
{

    protected $description = "Добавляет свойство базового цвета к ИБ Группировок и Тегов";

    public function up()
    {
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Группировки',
            'CODE' => 'CATALOG_GROUPS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1'
        ]);
        $iblockId2 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Теги',
            'CODE' => 'CATALOG_TAGS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1'
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Базовый цвет',
            'CODE' => 'COLORSFILTER',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_colors_filter'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId2, [
            'NAME' => 'Базовый цвет',
            'CODE' => 'COLORSFILTER',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_colors_filter'),
        ]);
    }

    public function down()
    {
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Группировки',
            'CODE' => 'CATALOG_GROUPS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1'
        ]);
        $iblockId2 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Теги',
            'CODE' => 'CATALOG_TAGS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1'
        ]);

        $helper->Iblock()->deletePropertyIfExists($iblockId1, 'COLORSFILTER');
        $helper->Iblock()->deletePropertyIfExists($iblockId2, 'COLORSFILTER');
    }
}
