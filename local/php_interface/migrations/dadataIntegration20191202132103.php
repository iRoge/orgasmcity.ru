<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class dadataIntegration20191202132103 extends Version
{

    protected $description = "Добaвляет пользовательские поля для почтового индекса и ФИАС кода для USER + эти же свойства для CSale модуля + свойство неполного номера дома для CSale";

    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_POSTALCODE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'Y',
            'SETTINGS' =>
                array (
                    'SIZE' => 20,
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Почтовый индекс',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Почтовый индекс',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Почтовый индекс',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => null,
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));

        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_FIASCODE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'Y',
            'SETTINGS' =>
                array (
                    'SIZE' => 20,
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'ФИАС код',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'ФИАС код',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'ФИАС код',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => null,
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));

        Loader::includeModule('sale');

        $rsType = \CSalePersonType::GetList([]);
        while ($arType = $rsType->Fetch()) {
            $rsGroup = \CSaleOrderPropsGroup::GetList(
                [],
                [
                    'PERSON_TYPE_ID' => $arType['ID'],
                    'NAME' => 'Адрес доставки'
                ]
            );
            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'Адрес доставки',
                        'SORT' => 10
                    ]
                );
            }
            $arFields1 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Почтовый индекс",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "POSTALCODE",
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "IS_FILTERED" => "Y",
                "IS_LOCATION4TAX" => "N",
                "PROPS_GROUP_ID" => $iGroup,
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "UTIL" => "N"
            ];
            \CSaleOrderProps::Add($arFields1);

            $arFields2 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "ФИАС код",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "FIASCODE",
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "IS_FILTERED" => "Y",
                "IS_LOCATION4TAX" => "N",
                "PROPS_GROUP_ID" => $iGroup,
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "UTIL" => "N"
            ];
            \CSaleOrderProps::Add($arFields2);

            $arFields3 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Номер дома (без строения и корпуса)",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "HOUSE_NUM",
                "USER_PROPS" => "Y",
                "IS_LOCATION" => "N",
                "IS_FILTERED" => "Y",
                "IS_LOCATION4TAX" => "N",
                "PROPS_GROUP_ID" => $iGroup,
                "SIZE1" => 0,
                "SIZE2" => 0,
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "UTIL" => "N"
            ];
            \CSaleOrderProps::Add($arFields3);
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        try {
            // Удаляем свойства Почтового индекса
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_POSTALCODE');

            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "POSTALCODE"
                ]
            );

            while ($arProp = $rsProp->Fetch()) {
                \CSaleOrderProps::Delete($arProp['ID']);
            }

            // Удаляем свойства ФИАС кода
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_FIASCODE');
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "FIASCODE"
                ]
            );
            while ($arProp = $rsProp->Fetch()) {
                \CSaleOrderProps::Delete($arProp['ID']);
            }

            // Удаляем свойство неполного номера дома
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "HOUSE_NUM"
                ]
            );
            while ($arProp = $rsProp->Fetch()) {
                \CSaleOrderProps::Delete($arProp['ID']);
            }
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());

            return false;
        }

        return true;
    }
}
