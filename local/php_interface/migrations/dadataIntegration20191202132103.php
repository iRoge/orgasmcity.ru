<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class dadataIntegration20191202132103 extends Version
{
    protected $description = "Добавляет новые свойства для заказа и пользователя по тикету 130995";

    protected $moduleVersion = "3.13.4";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_REGIONFIAS', 'Код ФИАС региона'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_AREAFIAS', 'Код ФИАС района в регионе'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_CITYFIAS', 'Код ФИАС города'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_DISTRICTFIAS', 'Код ФИАС района города (заполняется, только если район есть в ФИАС)'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_SETTLEMENTFIAS', 'Код ФИАС нас. пункта'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_STREETFIAS', 'Код ФИАС улицы'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_POSTALCODE', 'Почтовый индекс'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_FIASCODE', 'ФИАС код'));

        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_HOUSE', 'Номер дома'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_ST', 'Строение'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_HOUSING', 'Корпус'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_ENTRANCE', 'Подъезд'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_FLOOR', 'Этаж'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_APARTMENT', 'Квартира или офис'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_INTERCOM', 'Домофон'));
        $helper->UserTypeEntity()->saveUserTypeEntity($this->getUserFieldArray('UF_TIME', 'Желаемое время доставки'));

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
            $arFields = $this->getOrderFieldArray("REGIONFIAS", "Код ФИАС региона", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("AREAFIAS", "Код ФИАС района в регионе", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("CITYFIAS", "Код ФИАС города", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("DISTRICTFIAS", "Код ФИАС района города (заполняется, только если район есть в ФИАС)", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("SETTLEMENTFIAS", "Код ФИАС нас. пункта", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("STREETFIAS", "Код ФИАС улицы", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("POSTALCODE", "Почтовый индекс", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("FIASCODE", "ФИАС код", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("HOUSE_NUM", "Номер дома (без строения и корпуса)", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);
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
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_POSTALCODE');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_FIASCODE');

            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_HOUSE');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_ST');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_HOUSING');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_ENTRANCE');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_FLOOR');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_APARTMENT');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_INTERCOM');
            $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_TIME');

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
                        "POSTALCODE",
                        "FIASCODE",
                        "HOUSE_NUM",
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

    private function getUserFieldArray($fieldName, $langText)
    {
        return [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => $fieldName,
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
                array(
                    'SIZE' => 20,
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'ru' => $langText,
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'ru' => $langText,
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'ru' => $langText,
                ),
            'ERROR_MESSAGE' =>
                array(
                    'ru' => null,
                ),
            'HELP_MESSAGE' =>
                array(
                    'ru' => null,
                ),
        ];
    }

    private function getOrderFieldArray($fieldCode, $fieldName, $personTypeID, $groupID)
    {
        return [
            "PERSON_TYPE_ID" => $personTypeID,
            "NAME" => $fieldName,
            "TYPE" => "STRING",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => "N",
            "SORT" => 100,
            "CODE" => $fieldCode,
            "USER_PROPS" => "Y",
            "IS_LOCATION" => "N",
            "IS_FILTERED" => "Y",
            "IS_LOCATION4TAX" => "N",
            "PROPS_GROUP_ID" => $groupID,
            "SIZE1" => 0,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N",
            "UTIL" => "N"
        ];
    }
}
