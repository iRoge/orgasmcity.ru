<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

class addPropertyProductLocationForOrder20200226144613 extends Version
{
    protected $description = "Добавляет пользовательские свойства для CSale модуля. А именно PRODUCT_REGION И PRODUCT_CITY";

    public function up()
    {
        Loader::includeModule('sale');

        $rsType = \CSalePersonType::GetList([]);
        while ($arType = $rsType->Fetch()) {
            $rsGroup = \CSaleOrderPropsGroup::GetList(
                [],
                [
                    'PERSON_TYPE_ID' => $arType['ID'],
                    'NAME' => 'Местоположение товаров'
                ]
            );
            if ($arGroup = $rsGroup->Fetch()) {
                $iGroup = $arGroup['ID'];
            } else {
                $iGroup = \CSaleOrderPropsGroup::Add(
                    [
                        'PERSON_TYPE_ID' => $arType['ID'],
                        'NAME' => 'Местоположение товаров',
                        'SORT' => 100
                    ]
                );
            }
            $arFields1 = [
                "PERSON_TYPE_ID" => $arType['ID'],
                "NAME" => "Город товаров",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "PRODUCT_CITY",
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
                "NAME" => "Регион товаров",
                "TYPE" => "STRING",
                "REQUIED" => "N",
                "DEFAULT_VALUE" => "N",
                "SORT" => 100,
                "CODE" => "PRODUCT_REGION",
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
        $helper = $this->getHelperManager();

        try {
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "PRODUCT_REGION"
                ]
            );

            while ($arProp = $rsProp->Fetch()) {
                \CSaleOrderProps::Delete($arProp['ID']);
            }

            // Удаляем свойство неполного номера дома
            $rsProp = \CSaleOrderProps::GetList(
                [],
                [
                    "CODE" => "PRODUCT_CITY"
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
