<?php

namespace Sprint\Migration;

class WhatsappVideoButtonInReserv20200506105735 extends Version
{
    protected $description = "Добавляет поля видео-онлайн консультации в свойства складов";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        //Создаем пользовательские поля
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_WHATSAPP_VIDEO',
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
                    'ru' => 'Whatsapp видео-онлайн консультация',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Whatsapp видео-онлайн консультация',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Whatsapp видео-онлайн консультация',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Whatsapp видео-онлайн консультация',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Whatsapp видео-онлайн консультация',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_WHATSAPP_NUM',
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
                    'SIZE' => 20,
                    'ROWS' => 1,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Номер телефона Whatsapp',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Номер телефона Whatsapp',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Номер телефона Whatsapp',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Номер телефона Whatsapp',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Номер телефона Whatsapp',
                ),
        ));
    }

    public function down()
    {
        //удаляем пользовательские поля
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_WHATSAPP_VIDEO']);
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists('CAT_STORE', ['UF_WHATSAPP_NUM']);
    }
}
