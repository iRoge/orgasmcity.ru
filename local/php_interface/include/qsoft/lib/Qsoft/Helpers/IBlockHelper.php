<?php

namespace Qsoft\Helpers;

use \CIBlockElement;
use \CIBlockSection;
use \CIBlockProperty;

class IBlockHelper
{
    public static function getElementIds(int $iblockID, array $elementFilter = []): array
    {
        $elementFilter['IBLOCK_ID'] = $iblockID;
        $dbElements = CIBlockElement::GetList(
            [],
            $elementFilter,
            false,
            false,
            ['ID']
        );

        $arElementID = [];
        while ($arElement = $dbElements->Fetch()) {
            $arElementID[] = $arElement['ID'];
        }

        return $arElementID;
    }

    /**
     * Возвращает массив свойств элементов инфоблока
     *
     * @param int $iblockID идентификатор инфоблока
     * @param array $arElementID
     * @param array $propertyCodes массив символьных кодов свойств
     *
     * @return array
     */
    public static function getPropertyArray(int $iblockID, array $arElementID, array $propertyCodes = []): array
    {
        $arResult = array_flip($arElementID);
        CIBlockElement::GetPropertyValuesArray(
            $arResult,
            $iblockID,
            [],
            ['CODE' => $propertyCodes]
        );

        $arQueryingValues = self::getCategorizedPropertyValues($arResult, ['E', 'G']);

        if ($arQueryingValues['BASE_TYPE']['E']) {
            self::queryForValuesLinkedWithElement($arResult, $arQueryingValues['BASE_TYPE']['E']);
        }

        if ($arQueryingValues['BASE_TYPE']['G']) {
            self::queryForValuesLinkedWithSection($arResult, $arQueryingValues['BASE_TYPE']['G']);
        }

        if ($arQueryingValues['USER_TYPE']) {
            self::queryForUserTypePropertiesValue($arResult, $arQueryingValues['USER_TYPE']);
        }

        $sort = 0;
        foreach ($arResult as &$arElement) {
            $arElement = array_map(
                function ($arProperty) use (&$sort) {
                    return [
                        'CODE' => $arProperty['CODE'],
                        'NAME' => $arProperty['NAME'],
                        'VALUE' => $arProperty['PROPERTY_TYPE'] == 'L' ? $arProperty['VALUE_ENUM'] : $arProperty['VALUE'],
                        'SORT' => ++$sort
                    ];
                },
                $arElement
            );
        }
        unset($arElement);

        return $arResult;
    }

    /**
     * Устанавливает название элемента инфоблока в качестве значения свойства, связанного с этим элементом
     *
     * @param array $arResult где нужно установить значения свойств типа 'E'
     * @param array $arQueryingValues свойства-ссылки на элемент инфоблока, полученные из $arResult
     *
     * @return void
     */
    private static function queryForValuesLinkedWithElement(array &$arResult, array $arQueryingValues)
    {
        if (empty($arQueryingValues)) {
            return;
        }

        $dbSections = CIBlockElement::GetList(
            [],
            ['ID' => $arQueryingValues],
            false,
            ['ID', 'NAME']
        );

        $arActualValues = [];
        while ($arSection = $dbSections->Fetch()) {
            $arActualValues[$arSection['ID']] = $arSection['NAME'];
        }

        foreach ($arResult as $elementKey => $arElement) {
            foreach ($arElement as $propertyKey => $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] == 'E') {
                    $arResult[$elementKey][$propertyKey]['VALUE'] = $arActualValues[$arProperty['VALUE']];
                }
            }
        }
    }

    /**
     * Устанавливает название раздела инфоблока в качестве значения свойства, связанного с этим разделом
     *
     * @param array $arResult где нужно установить значения свойств типа 'G'
     * @param array $arQueryingValues свойства-ссылки на разделы инфоблока, полученные из $arResult
     *
     * @return void
     */
    private static function queryForValuesLinkedWithSection(array &$arResult, array $arQueryingValues)
    {
        if (empty($arQueryingValues)) {
            return;
        }

        $dbSections = CIBlockSection::GetList(
            [],
            ['ID' => $arQueryingValues],
            false,
            ['ID', 'NAME']
        );

        $arActualValues = [];
        while ($arSection = $dbSections->Fetch()) {
            $arActualValues[$arSection['ID']] = $arSection['NAME'];
        }

        foreach ($arResult as $elementKey => $arElement) {
            foreach ($arElement as $propertyKey => $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] == 'G') {
                    $arResult[$elementKey][$propertyKey]['VALUE'] = $arActualValues[$arProperty['VALUE']];
                }
            }
        }
    }

    /**
     * Устанавливает фактическое значение свойств пользовательского типа
     *
     * @param array $arResult где нужно установить фактические значения свойств
     * @param array $arQueryingValues свойства пользовательского типа, полученные из $arResult
     *
     * @return void
     */
    private static function queryForUserTypePropertiesValue(array &$arResult, array $arQueryingValues)
    {
        if (empty($arQueryingValues)) {
            return;
        }

        $arUserTypeDescriptions = CIBlockProperty::GetUserType();

        $arActualValues = [];
        foreach ($arQueryingValues as $userType => $arValues) {
            foreach ($arValues as $value => $arProperty) {
                $arActualValues[$userType][$value] = (string) call_user_func_array(
                    $arUserTypeDescriptions[$userType]['GetPublicViewHTML'],
                    [
                        $arProperty,
                        ['VALUE' => $value],
                        ['MODE' => 'SIMPLE_TEXT']
                    ]
                );
            }
        }

        foreach ($arResult as $elementKey => $arElement) {
            foreach ($arElement as $propertyKey => $arProperty) {
                if ($arProperty['USER_TYPE']) {
                    $arResult[$elementKey][$propertyKey]['VALUE'] = $arActualValues[$arProperty['USER_TYPE']][$arProperty['VALUE']];
                }
            }
        }
    }

    /**
     * Возвращает значения свойств переданного типа
     *
     * @param array $arResult где производить поиск свойств
     * @param array $propertyTypes фильтр типов свойств
     * @param array $propertyUserTypes фильтр пользовательских типов свойств
     *
     * @return array
     */
    private static function getCategorizedPropertyValues(array $arResult, array $propertyTypes = [], array $propertyUserTypes = []): array
    {
        $hasTypeFilter = !empty($propertyTypes);
        $hasUserTypeFilter = !empty($propertyUserTypes);
        $arQueryingValues = [
            'BASE_TYPE' => [],
            'USER_TYPE' => []
        ];

        foreach ($arResult as $elementKey => $arElement) {
            foreach ($arElement as $propertyKey => $arProperty) {
                if (!$arProperty['USER_TYPE'] && !in_array($arProperty['VALUE'], $arQueryingValues['BASE_TYPE'][$arProperty['PROPERTY_TYPE']])) {
                    if ($hasTypeFilter && !in_array($arProperty['PROPERTY_TYPE'], $propertyTypes)) {
                        continue;
                    }

                    $arQueryingValues['BASE_TYPE'][$arProperty['PROPERTY_TYPE']] = $arProperty['VALUE'];
                    continue;
                }

                if ($arProperty['USER_TYPE'] && !in_array($arProperty['VALUE'], array_keys($arQueryingValues['USER_TYPE'][$arProperty['USER_TYPE']]))) {
                    if ($hasUserTypeFilter && !in_array($arProperty['USER_TYPE'], $propertyUserTypes)) {
                        continue;
                    }

                    $arQueryingValues['USER_TYPE'][$arProperty['USER_TYPE']][$arProperty['VALUE']] = $arProperty;
                }
            }
        }

        return $arQueryingValues;
    }
}
