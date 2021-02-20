<?php

namespace Sprint\Migration;


use Bitrix\Main\Loader;

class OrderProblemProperty20171023155827 extends Version
{

    protected $description = "#10266 свойство проблемы с заказом";

    public function up()
    {
        $helper = new HelperManager();

        Loader::includeModule('sale');

        $rsType = \CSalePersonType::GetList([]);
        while ($arType = $rsType->Fetch()) {
            $rsGroup = \CSaleOrderPropsGroup::GetList(
                [],
                [
                    'PERSON_TYPE_ID' => $arType['ID'],
                    'NAME' => 'Служебные'
                ]
            );

            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'Служебные',
                        'SORT' => 10
                    ]
                );
            }

            $arFields = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Проблема с выгрузкой в 1С",
                "TYPE" => "CHECKBOX",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "PROBLEM_1C",
                "USER_PROPS" => "N",
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
                "UTIL" => "Y"
            ];

            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "PERSON_TYPE_ID" => $arType['ID'],
                    "PROPS_GROUP_ID" => $iGroup,
                    "CODE" => "PROBLEM_1C"
                ]
            );

            if(!$rsProp->Fetch())
                \CSaleOrderProps::Add($arFields);
        }

    }

    public function down()
    {
        Loader::includeModule('sale');

        $helper = new HelperManager();

        $rsProp = \CSaleOrderProps::GetList(
            [],
            [
                "CODE" => "PROBLEM_1C"
            ]
        );

        while($arProp = $rsProp->Fetch()) {
            \CSaleOrderProps::Delete($arProp['ID']);
        }

    }

}
