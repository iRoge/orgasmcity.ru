<?php

namespace Sprint\Migration;


use Bitrix\Main\UserFieldTable;

class Version20170208091302 extends Version
{

    protected $description = "#6198 Пользовательское свойство UF_FAVORITES объекта USER";

    public function up()
    {
        $helper = new HelperManager();

        /**
         * Добавление пользовательского свойства Избранное
         */

        $arFields = array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_FAVORITES',
            'USER_TYPE_ID' => 'iblock_element',
            'XML_ID' => 'XML_FAVORITES_FIELD',
            'SORT' => 500,
            'MULTIPLE' => 'Y',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => '',
            ),
            'EDIT_FORM_LABEL' => array(
                'ru' => 'Избранное',
                'en' => 'Favorites',
            ),
            'LIST_COLUMN_LABEL' => array(
                'ru' => 'Избранное',
                'en' => 'Favorites',
            ),
            'LIST_FILTER_LABEL' => array(
                'ru' => 'Избранное',
                'en' => 'Favorites',
            ),
            'ERROR_MESSAGE' => array(
                'ru' => 'Ошибка при заполнении пользовательского свойства «Избранное»',
                'en' => 'An error in completing the user field «Favorites»',
            ),
        );


        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists($arFields['ENTITY_ID'], $arFields['FIELD_NAME'], $arFields);

    }

    public function down()
    {
        $helper = new HelperManager();

        //your code ...

    }

}
