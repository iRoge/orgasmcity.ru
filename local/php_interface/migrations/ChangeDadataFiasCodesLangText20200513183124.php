<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class ChangeDadataFiasCodesLangText20200513183124 extends Version
{
    protected $description = "Правит тексты для фиас данных в заказе";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
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
            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'REGIONFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС региона",
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
            \CSaleOrderProps::Update($id, $arFields);

            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'AREAFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС района в регионе",
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
            \CSaleOrderProps::Update($id, $arFields);

            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'CITYFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС города",
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
            \CSaleOrderProps::Update($id, $arFields);

            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'DISTRICTFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС района города (заполняется, только если район есть в ФИАС)",
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
            \CSaleOrderProps::Update($id, $arFields);

            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'SETTLEMENTFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС нас. пункта",
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
            \CSaleOrderProps::Update($id, $arFields);

            $rsProp = \CSaleOrderProps::GetList(
                array(),
                array(
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    'CODE' => 'STREETFIAS',
                ),
                false,
                false,
                array('ID')
            );
            while ($arProp = $rsProp->Fetch()) {
                $id = $arProp['ID'];
            }
            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Код ФИАС улицы",
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
            \CSaleOrderProps::Update($id, $arFields);
        }
    }

    public function down()
    {
        //your code ...
    }
}
