<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class addPersonalPhoneEmailOrderProps20200422181147 extends Version
{
    protected $description = "Добавляет свойства EMAIL_PROFILE и PHONE_PROFILE в заказ";

    protected $moduleVersion = "3.13.4";

    public function up()
    {
        Loader::includeModule('sale');

        $rsType = \CSalePersonType::GetList([]);
        while ($arType = $rsType->Fetch()) {
            $rsGroup = \CSaleOrderPropsGroup::GetList(
                [],
                [
                    'PERSON_TYPE_ID' => $arType['ID'],
                    'NAME' => 'Контактное лицо'
                ]
            );
            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'Контактное лицо',
                        'SORT' => 100
                    ]
                );
            }
            $arFields1 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Телефон профиля",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "PHONE_PROFILE",
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
                "UTIL" => "N"
            ];
            \CSaleOrderProps::Add($arFields1);

            $arFields2 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Почта профиля",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "EMAIL_PROFILE",
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
                "UTIL" => "N"
            ];
            \CSaleOrderProps::Add($arFields2);
        }
    }

    public function down()
    {
        Loader::includeModule('sale');
        try {
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "PHONE_PROFILE"
                ]
            );

            while ($arProp = $rsProp->Fetch()) {
                \CSaleOrderProps::Delete($arProp['ID']);
            }

            // Удаляем свойство неполного номера дома
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "EMAIL_PROFILE"
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
