<?php

namespace Sprint\Migration;

class addStockGroups20200117123616 extends Version
{
    protected $description = "Добавляет highload блок в котором хранятся группы складов. Также добавляет привязку склада к группе.";
    private $HLBlockName = 'StockGroups';

    public function up()
    {
        $helper = new HelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => $this->HLBlockName,
            'TABLE_NAME' => 'b_qsoft_stock_groups',
            'LANG' =>
                array(
                    'ru' =>
                        array(
                            'NAME' => 'Группы складов',
                        ),
                )
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'USER_TYPE_ID' => 'string',
            'FIELD_NAME' => 'UF_NAME',
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Название',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Название',
                ),
        ]);

        //Создаем поле привязки к складов к инфоблоку
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STOCK_GROUP',
            'USER_TYPE_ID' => 'stock_group_name',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => null,
            'EDIT_FORM_LABEL' =>
                array (
                    'en' => '',
                    'ru' => 'Группа',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'en' => '',
                    'ru' => 'Группа',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'en' => '',
                    'ru' => 'Группа',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'en' => '',
                    'ru' => '',
                ),
            'HELP_MESSAGE' =>
                array (
                    'en' => '',
                    'ru' => '',
                ),
        ));
    }

    public function down()
    {
        $helper = new HelperManager();

        $helper->Hlblock()->deleteHlblockIfExists($this->HLBlockName);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STOCK_GROUP']);
    }
}
