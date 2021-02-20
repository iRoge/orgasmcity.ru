<?php

namespace Sprint\Migration;

use Bitrix\Main\ArgumentException;

class UserFields20190819115251 extends Version
{

    protected $description = "Создание пользовательских полей для подписки по e-mail и sms";

    public function up()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->UserTypeEntity()->saveUserTypeEntity(array(
                'ENTITY_ID' => 'USER',
                'FIELD_NAME' => 'UF_SUBSCRIBE_EMAIL',
                'USER_TYPE_ID' => 'boolean',
                'XML_ID' => 'SUBSCRIBE_EMAIL',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'N',
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
                        'ru' => '',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => '',
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
                'FIELD_NAME' => 'UF_SUBSCRIBE_SMS',
                'USER_TYPE_ID' => 'boolean',
                'XML_ID' => 'SUBSCRIBE_SMS',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'N',
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
                        'ru' => '',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => '',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => '',
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
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SUBSCRIBE_EMAIL');
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());

            return false;
        }
        try {
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SUBSCRIBE_SMS');
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());

            return false;
        }

        return true;
    }
}
