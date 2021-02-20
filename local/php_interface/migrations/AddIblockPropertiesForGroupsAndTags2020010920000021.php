<?php


namespace Sprint\Migration;

class AddIblockPropertiesForGroupsAndTags2020010920000021 extends Version
{

    protected $description = "Добавляет свойства Тип застежки, Стиль для инфоблока группировок. Добавляет свойства Тип застежки, Страна происхождения, Стиль, Бренд для инфоблока тегов.";
    private $iblock_groups_code = 'CATALOG_GROUPS';
    private $iblock_tags_code = 'CATALOG_TAGS';

    public function up()
    {
        $helper = new HelperManager();
        $IBLOCK_GROUPS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_groups_code);
        $IBLOCK_TAGS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_tags_code);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_GROUPS_ID, [
            'NAME' => 'Тип застежки',
            'CODE' => 'ZASTEGKA',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_zastegka'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_GROUPS_ID, [
            'NAME' => 'Стиль',
            'CODE' => 'STYLE',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_style'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_TAGS_ID, [
            'NAME' => 'Тип застежки',
            'CODE' => 'ZASTEGKA',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_zastegka'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_TAGS_ID, [
            'NAME' => 'Страна происхождения',
            'CODE' => 'COUNTRY',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_country'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_TAGS_ID, [
            'NAME' => 'Стиль',
            'CODE' => 'STYLE',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_style'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_TAGS_ID, [
            'NAME' => 'Бренд',
            'CODE' => 'BRAND',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size' => '1', 'width' => '0', 'group' => 'N', 'multiple' => 'N', 'TABLE_NAME' => 'b_1c_dict_brand'),
        ]);
    }

    public function down()
    {
        $helper = new HelperManager();
        $IBLOCK_GROUPS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_groups_code);
        $IBLOCK_TAGS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_tags_code);
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_GROUPS_ID, 'ZASTEGKA');
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_GROUPS_ID, 'STYLE');
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_TAGS_ID, 'COUNTRY');
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_TAGS_ID, 'STYLE');
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_TAGS_ID, 'BRAND');
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_TAGS_ID, 'ZASTEGKA');
    }
}
