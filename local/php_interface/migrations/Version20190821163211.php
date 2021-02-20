<?php

namespace Sprint\Migration;

use Bitrix\Main\ArgumentException;

class Version20190821163211 extends Version
{

    protected $description = "Перенос пользовательских полей для подписки СП";

    public function up()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->UserTypeEntity()->saveUserTypeEntity(array(
                'ENTITY_ID' => 'USER',
                'FIELD_NAME' => 'UF_SP_SUB_TIME',
                'USER_TYPE_ID' => 'datetime',
                'XML_ID' => 'SP_SUB_TIME',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'DEFAULT_VALUE' =>
                            array(
                                'TYPE' => 'NONE',
                                'VALUE' => '',
                            ),
                        'USE_SECOND' => 'Y',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => 'Время последнего изменения подписки в SP',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => 'Время последнего изменения подписки в SP',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => 'Время последнего изменения подписки в SP',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
            ));
        } catch (ArgumentException $e) {
            $this->outError($e->getMessage());
            return false;
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());
            return false;
        }
        try {
            $helper->UserTypeEntity()->saveUserTypeEntity(array(
                'ENTITY_ID' => 'USER',
                'FIELD_NAME' => 'UF_SP_LAST_TAG',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'SP_LAST_TAG',
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
                        'ru' => 'Последний тег SP',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => 'Последний тег SP',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => 'Последний тег SP',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
            ));
        } catch (ArgumentException $e) {
            $this->outError($e->getMessage());
            return false;
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());
            return false;
        }

        return true;
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SP_LAST_TAG');
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());
            return false;
        }
        try {
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SP_SUB_TIME');
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());
            return false;
        }

        return true;
    }
}
