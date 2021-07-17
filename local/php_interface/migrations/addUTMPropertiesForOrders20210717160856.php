<?php

namespace Sprint\Migration;


use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

class addUTMPropertiesForOrders20210717160856 extends Version
{
    protected $description = "Добавляет ЮТМ метки в заказ по которым пришел покупатель перед совершением заказа";

    protected $moduleVersion = "3.25.1";

    /**
     * @return bool|void
     * @throws LoaderException
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
                    'NAME' => 'UTM метки'
                ]
            );
            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'UTM метки',
                        'SORT' => 15
                    ]
                );
            }

            $arFields = $this->getOrderFieldArray("UTM_SOURCE", "UTM source", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("UTM_MEDIUM", "UTM medium", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("UTM_CAMPAIGN", "UTM campaign", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("UTM_CONTENT", "UTM content", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);

            $arFields = $this->getOrderFieldArray("UTM_TERM", "UTM term", $arType['ID'], $iGroup);
            \CSaleOrderProps::Add($arFields);
        }
    }

    public function down()
    {
        try {
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => array(
                        "UTM_SOURCE",
                        "UTM_MEDIUM",
                        "UTM_CAMPAIGN",
                        "UTM_CONTENT",
                        "UTM_TERM",
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
