<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class DadataNewPropsForOrder20200311115240 extends Version
{
    protected $description = "Добавляет новые свойства для заказа и пользователя по тикету 130995";

    protected $moduleVersion = "3.13.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_REGIONFIAS',
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
                    'ru' => 'Код ФИАС региона',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС региона',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС региона',
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
            'FIELD_NAME' => 'UF_AREAFIAS',
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
                    'ru' => 'Код ФИАС района в регионе',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС района в регионе',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС района в регионе',
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
            'FIELD_NAME' => 'UF_CITYFIAS',
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
                    'ru' => 'Код ФИАС города',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС города',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС города',
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
            'FIELD_NAME' => 'UF_DISTRICTFIAS',
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
                    'ru' => 'Код ФИАС района города (заполняется, только если район есть в ФИАС)',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС района города (заполняется, только если район есть в ФИАС)',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС района города (заполняется, только если район есть в ФИАС)',
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
            'FIELD_NAME' => 'UF_SETTLEMENTFIAS',
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
                    'ru' => 'Код ФИАС нас. пункта',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС нас. пункта',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС нас. пункта',
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
            'FIELD_NAME' => 'UF_STREETFIAS',
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
                    'ru' => 'Код ФИАС улицы',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Код ФИАС улицы',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Код ФИАС улицы',
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
                "CODE" => "REGIONFIAS",
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
                "CODE" => "AREAFIAS",
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
                "CODE" => "CITYFIAS",
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

            $arFields4 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Почтовый индекс",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "DISTRICTFIAS",
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
            \CSaleOrderProps::Add($arFields4);

            $arFields5 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "ФИАС код",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "SETTLEMENTFIAS",
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
            \CSaleOrderProps::Add($arFields5);

            $arFields6 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Номер дома (без строения и корпуса)",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "STREETFIAS",
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
            \CSaleOrderProps::Add($arFields6);
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_REGIONFIAS');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_AREAFIAS');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_CITYFIAS');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_DISTRICTFIAS');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SETTLEMENTFIAS');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_STREETFIAS');

            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => array(
                        "REGIONFIAS",
                        "AREAFIAS",
                        "CITYFIAS",
                        "DISTRICTFIAS",
                        "SETTLEMENTFIAS",
                        "STREETFIAS",
                    ),
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
