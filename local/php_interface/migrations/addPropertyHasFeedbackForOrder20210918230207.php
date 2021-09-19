<?php

namespace Sprint\Migration;


use Bitrix\Main\Loader;

class addPropertyHasFeedbackForOrder20210918230207 extends Version
{
    protected $description = "Добавляет свойство HAS_FEEDBACK к заказу";

    protected $moduleVersion = "3.25.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        Loader::includeModule('sale');

        $rsType = \CSalePersonType::GetList([]);
        while ($arType = $rsType->Fetch()) {
            $rsGroup = \CSaleOrderPropsGroup::GetList(
                [],
                [
                    'PERSON_TYPE_ID' => $arType['ID'],
                    'NAME' => 'Служебные свойства'
                ]
            );
            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'Служебные свойства',
                        'SORT' => 15
                    ]
                );
            }

            $arFields = $this->getOrderFieldArray("HAS_ORDER", "Заказ имеет отзыв", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);
        }
    }

    public function down()
    {
        try {
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => [
                        "HAS_ORDER"
                    ]
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

    private function getOrderFieldArray($fieldCode, $fieldName, $personTypeID, $groupID)
    {
        return [
            "PERSON_TYPE_ID" => $personTypeID,
            "NAME" => $fieldName,
            "TYPE" => "CHECKBOX",
            "REQUIED" => "N",
            "DEFAULT_VALUE" => "N",
            "SORT" => 100,
            "CODE" => $fieldCode,
            "USER_PROPS" => "N",
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
