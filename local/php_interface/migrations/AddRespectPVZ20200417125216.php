<?php

namespace Sprint\Migration;

class AddRespectPVZ20200417125216 extends Version
{
    protected $description = "Добавляет пользовательские ПВЗ свойства складам, создает запись в таблице ПВЗ";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        //Создаем пользовательские поля
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STORE_IS_PVZ',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'DEFAULT_VALUE' => 0,
                    'DISPLAY' => 'CHECKBOX',
                    'LABEL' =>
                        array(
                            0 => null,
                            1 => null,
                        ),
                    'LABEL_CHECKBOX' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Активировать ПВЗ',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Активировать ПВЗ',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Активировать ПВЗ',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Активировать ПВЗ',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Активировать ПВЗ',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STORE_PVZ_CARD',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'DEFAULT_VALUE' => 0,
                    'DISPLAY' => 'CHECKBOX',
                    'LABEL' =>
                        array(
                            0 => '',
                            1 => '',
                        ),
                    'LABEL_CHECKBOX' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата картой',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата картой',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата картой',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата картой',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата картой',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STORE_PVZ_CASH',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'DEFAULT_VALUE' => 0,
                    'DISPLAY' => 'CHECKBOX',
                    'LABEL' =>
                        array(
                            0 => '',
                            1 => '',
                        ),
                    'LABEL_CHECKBOX' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата наличными',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата наличными',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата наличными',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата наличными',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна оплата наличными',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STORE_PVZ_DRESS',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'DEFAULT_VALUE' => 0,
                    'DISPLAY' => 'CHECKBOX',
                    'LABEL' =>
                        array(
                            0 => '',
                            1 => '',
                        ),
                    'LABEL_CHECKBOX' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна примерка',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна примерка',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна примерка',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна примерка',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Возможна примерка',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STORE_PVZ_TEXT',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'SIZE' => 35,
                    'ROWS' => 2,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Описание ПВЗ',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Описание ПВЗ',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Описание ПВЗ',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Описание ПВЗ',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Описание ПВЗ',
                ),
        ));
        //Добавляем запись в таблицу ПВЗ
        global $DB;
        $DB->query("INSERT INTO b_qsoft_pvz(`NAME`, `CLASS_NAME`, `ACTIVE`) VALUES ('Respect', 'Respect', 'Y')");
    }

    public function down()
    {
        //удаляем пользовательские поля
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STORE_IS_PVZ']);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STORE_PVZ_CARD']);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STORE_PVZ_CASH']);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STORE_PVZ_DRESS']);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_STORE_PVZ_TEXT']);
        //удаляем запись из таблицы ПВЗ
        global $DB;
        $DB->query("DELETE FROM b_qsoft_pvz WHERE `CLASS_NAME` = 'Respect'");
    }
}
