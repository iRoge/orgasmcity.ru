<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

/**
 * Класс для работы с группировками. Содержит методы для поиска и создания фильтров
 *
 * @package Likee\Site\Helpers
 */
class Groups
{
    /**
     * название инфоблока группировки
     */
    const IBLOCK_GROUPS_NAME = 'CATALOG_GROUPS';

    public static function getGroupsIblockId()
    {
        static $groupsIblockId = null;
        
        if (is_null($groupsIblockId)) {
            try {
                $groupsIblockId = \Likee\Site\Helpers\IBlock::getIBlockId(self::IBLOCK_GROUPS_NAME);
            } catch (\Exception $e) {
                $groupsIblockId = 0;
            }
        }

        return $groupsIblockId;
    }

    public static function getCurentGroupId($id = false)
    {
        static $currentGroupsId = false;
        
        if (false !== $id) {
            $currentGroupsId = $id;
        }

        return $currentGroupsId;
    }

    public static function getGroupIdByCode($code)
    {
        $groupsIblockId = self::getGroupsIblockId();
        
        if ($groupsIblockId) {
            $rsGroups = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $groupsIblockId,
                    'ACTIVE' => 'Y',
                    'CODE' => $code
                ],
                false,
                false,
                ['ID']
            );

            while ($arFields = $rsGroups->GetNext()) {
                return $arFields['ID'];
            }
        }

        return false;
    }

    public static function getGroupById($id)
    {
        $groupsIblockId = self::getGroupsIblockId();
        if (! $groupsIblockId) {
            return false;
        }

        $obCache = \Bitrix\Main\Application::getCache();
        $sCacheDir = 'likee\catalog\groups_sections';

        if ($obCache->initCache(36000000, 'groups_section_'.$id, $sCacheDir)) {
            $arGroupElement = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            \Bitrix\Main\Application::getInstance()->getTaggedCache()->startTagCache($sCacheDir);
            \Bitrix\Main\Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$groupsIblockId);

            $resGroup = \CIBlockElement::GetByID($id);
            if (! ($rsGroup = $resGroup->GetNextElement(false, false))) {
                return false;
            }

            $arGroupElement = $rsGroup->GetFields();
            $arGroupElementProps = $rsGroup->GetProperties();

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($groupsIblockId, $arGroupElement["ID"]);
            $arGroupElement["IPROPERTY_VALUES"] = $ipropValues->getValues();
            
            $arGroupElement['FILTER'] = [];
                
            if (!empty($arGroupElementProps['SECTION']['VALUE'])) {
                $arGroupElement['FILTER']['SECTION_ID'] = $arGroupElementProps['SECTION']['VALUE'];
                $arGroupElement['FILTER']['INCLUDE_SUBSECTIONS'] = 'Y';
            }
            
            // фильтр по цене
            if (!empty($arGroupElementProps['PRICE_FROM']['VALUE']) || !empty($arGroupElementProps['PRICE_TO']['VALUE'])) {
                $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'] = [0, PHP_INT_MAX];
        
                if (! empty($arGroupElementProps['PRICE_FROM']['VALUE'])) {
                    $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'][0] = $arGroupElementProps['PRICE_FROM']['VALUE'];
                }
                if (! empty($arGroupElementProps['PRICE_TO']['VALUE'])) {
                    $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'][1] = $arGroupElementProps['PRICE_TO']['VALUE'];
                }
            }
            
            // фильтр по размеру скидки
            if (( !empty($arGroupElementProps['SEGMENT_FROM']['VALUE']) || !empty($arGroupElementProps['SEGMENT_TO']['VALUE']) ) && !empty($arGroupElementProps['PRICESEGMENTID']['VALUE'])) {
                $arSegmentPropertyFlter = [0, PHP_INT_MAX];
            
                if (! empty($arGroupElementProps['SEGMENT_FROM']['VALUE'])) {
                    $arSegmentPropertyFlter[0] = $arGroupElementProps['SEGMENT_FROM']['VALUE'];
                }
                if (! empty($arGroupElementProps['SEGMENT_TO']['VALUE'])) {
                    $arSegmentPropertyFlter[1] = $arGroupElementProps['SEGMENT_TO']['VALUE'];
                }

                if ('White' == $arGroupElementProps['PRICESEGMENTID']['VALUE']) {
                    $arGroupElement['FILTER']['><PROPERTY_MAXDISCBP'] = $arSegmentPropertyFlter;
                } else {
                    $arGroupElement['FILTER']['><PROPERTY_SEGMENT_PCT'] = $arSegmentPropertyFlter;
                }
            }
            
            unset($arGroupElementProps['SECTION'], $arGroupElementProps['PRICE_FROM'], $arGroupElementProps['PRICE_TO']);
            
            foreach ($arGroupElementProps as $propCode => $propData) {
                if (empty($propData['VALUE'])) {
                    continue;
                }

                /**
                 * Предполагаем, что по предложениям всегда ищем в списках
                 */
                if (0 === strpos($propCode, 'OFFERS_')) {
                    $propCode = substr($propCode, strrpos($propCode, 'OFFERS_')+7);

                    $arPropData = false === strpos($propData['VALUE'], ',') ? [$propData['VALUE']] : explode(',', $propData['VALUE']);
                    $arPropData = array_map('trim', $arPropData);

                    $propertyEnums = \CIBlockPropertyEnum::GetList([], [
                        'IBLOCK_ID' => IBLOCK_OFFERS,
                        'CODE' => $propCode,
                    ]);
                    while ($arEnumFields = $propertyEnums->GetNext()) {
                        $i = array_search($arEnumFields['VALUE'], $arPropData);
                        if (false !== $i) {
                            $arPropData[$i] = $arEnumFields['ID'];
                        }
                    }
                    unset($arEnumFields, $propertyEnums);

                    $arGroupElement['FILTER']['OFFERS']['PROPERTY_'.$propCode] = $arPropData;
                } else {
                    if (is_string($propData['VALUE']) && false !== strpos($propData['VALUE'], ',')) {
                        $propData['VALUE'] = explode(',', $propData['VALUE']);
                        $propData['VALUE'] = array_map('trim', $propData['VALUE']);
                    }
                    $arGroupElement['FILTER']['PROPERTY_'.$propCode] = $propData['VALUE'];
                }
            }

            if (isset($arGroupElement['FILTER']['PROPERTY_F_STORES_O']) && isset($arGroupElement['FILTER']['PROPERTY_F_STORES_R'])) {
                $arGroupElement['F_STORES_FILTER']['PROPERTY_F_STORES'] = 'A';
            } elseif (isset($arGroupElement['FILTER']['PROPERTY_F_STORES_O']) || isset($arGroupElement['FILTER']['PROPERTY_F_STORES_R'])) {
                $arGroupElement['F_STORES_FILTER']['PROPERTY_F_STORES'] = isset($arGroupElement['FILTER']['PROPERTY_F_STORES_O']) ? 'O' : 'R';
            }
            unset($arGroupElement['FILTER']['PROPERTY_F_STORES_O']);
            unset($arGroupElement['FILTER']['PROPERTY_F_STORES_R']);

            $arGroupElement['IS_ACTION'] = ! empty($arGroupElementProps['IS_ACTION']['VALUE']);

            \Bitrix\Main\Application::getInstance()->getTaggedCache()->endTagCache();
            
            
            \Bitrix\Main\Loader::includeModule('highloadblock');

            $hlblock = HL\HighloadBlockTable::getById(HL_ARTICLES_SKU)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock); //генерация класса
            $entityClass = $entity->getDataClass();

            // Выбираем из HL массив ID для фильтра, если они есть
            $arID = array();
            $arraySKU = array();
            $rsData = $entityClass::getList(
                array(
                    'filter' => array(
                        'UF_GROUP' => $arGroupElement['ID']
                    )
                )
            );
            if ($rsData->getSelectedRowsCount() > 0) {
                while ($item = $rsData->fetch()) {
                    $arraySKU[] = $item['UF_SKU'];
                }
                $rsID = \CIBlockElement::GetList(
                    array(),
                    array(
                            'PROPERTY_ARTICLE' => $arraySKU,
                            'IBLOCK_ID' => IBLOCK_CATALOG,
                            'ACTIVE' => 'Y'
                        ),
                    false,
                    false,
                    array('ID')
                );
                while ($arrayID = $rsID->fetch()) {
                    $arID[] = $arrayID['ID'];
                }

                // если записи найдены, то подменяем фильтр
                if (count($arID) > 0) {
                    $arGroupElement['FILTER'] = array('ID' => array_unique($arID));
                }
            }

            $obCache->endDataCache($arGroupElement);
        }

        self::getCurentGroupId($arGroupElement['ID']);
            
        return $arGroupElement;
    }
    
    public static function getGroupLabel($idList)
    {
        $arGroupLabes = null;

        if (is_null($arGroupLabes)) {
            $arGroupLabes = [];
            
            $groupsIblockId = self::getGroupsIblockId();
        
            if ($groupsIblockId) {
                $rsGroups = \CIBlockElement::GetList(
                    [
                        'SORT' => 'ASC'
                    ],
                    [
                        'IBLOCK_ID' => $groupsIblockId,
                        'ACTIVE' => 'Y',
                        '!DETAIL_PICTURE' => false
                    ],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PICTURE', 'CODE']
                );
    
                while ($arFields = $rsGroups->GetNext()) {
                    $arGroupLabes[$arFields['ID']] = [
                        'NAME' => $arFields['NAME'],
                        'SRC' => \CFile::GetPath($arFields['DETAIL_PICTURE']),
                        'PAGE_URL' => '/catalog/'.$arFields['CODE'].'/'
                    ];
                }
            }
        }

        $currentId = self::getCurentGroupId();
        if ($currentId) {
            $idList = [$currentId];
        }

        foreach ($arGroupLabes as $id => $arLabel) {
            if (in_array($id, $idList)) {
                return $arLabel;
            }
        }

        return false;
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        $groupsIblockId = self::getGroupsIblockId();

        if ($groupsIblockId == $arFields['IBLOCK_ID']) {
            $tableName = 'b_iblock_element_property';
            $connection = \Bitrix\Main\Application::getConnection();

            $sql = 'SELECT ID FROM b_iblock_property WHERE IBLOCK_ID = '.IBLOCK_CATALOG.' AND CODE = \'SHOW_IN_GROUPS\'';
            $iShowInGgroupsPropertyId = $connection->queryScalar($sql);

            if (! $iShowInGgroupsPropertyId) {
                return;
            }

            $sql = 'DELETE FROM '.$tableName.' WHERE IBLOCK_PROPERTY_ID = '.$iShowInGgroupsPropertyId.' AND VALUE = \''.$arFields['ID'].'\'';
            $connection->queryExecute($sql);
            $needUpdate = (bool) $connection->getAffectedRowsCount();

            if (!empty($arFields['DETAIL_PICTURE'])) {
                $iGroupId = (int) $arFields['ID'];
                $arGroupData = self::getGroupById($arFields['ID']);
                if (count($arGroupData['FILTER']['ID']) > 0) {
                    $dataFields = [
                        'IBLOCK_PROPERTY_ID' => $iShowInGgroupsPropertyId,
                        'VALUE' => $iGroupId,
                        '~VALUE_NUM' => \CIBlock::roundDB($iGroupId),
                        '~VALUE_ENUM' => intval($iGroupId)
                    ];
                    $helper = $connection->getSqlHelper();

                    $arFilter = [
                        'IBLOCK_ID' => IBLOCK_CATALOG,
                        'ACTIVE' => 'Y',
                    ];
                    $arFilter = array_merge_recursive($arFilter, $arGroupData['FILTER']);

                    $rsItems = \CIBlockElement::GetList(
                        [],
                        $arFilter,
                        false,
                        false,
                        ['ID']
                    );

                    $sqlValues = [];

                    $i = 0;

                    while ($arItems = $rsItems->GetNext()) {
                        $dataFields['IBLOCK_ELEMENT_ID'] = $arItems['ID'];

                        list($prefix, $values) = $helper->prepareInsert($tableName, $dataFields);
                        $sqlValues[$i][] = "(" . $values . ")";
                        $count++;
                        if (count($sqlValues[$i]) > 900) {
                            $i++;
                        }
                    }

                    foreach ($sqlValues as $value) {
                        $sql = "INSERT INTO " . $helper->quote($tableName) . " (" . $prefix . ") VALUES " . implode(',', $value);
                        $connection->queryExecute($sql);
                    }

                    unset($rsItems, $arItems);

                    $needUpdate = true;
                }
            }

            if ($needUpdate) {
                \CIBlock::clearIblockTagCache(IBLOCK_CATALOG);
            }
        }
    }
    
    public static function prepareActionData(&$arGroupElement = [])
    {
        $arGroupElement['ACTION'] = [];

        if (! empty($arGroupElement['IS_ACTION'])) {
            $todayTimestamp = mktime(0, 0, 0, date('n'), date('d'), date('Y'));
            $activeToTimestamp = MakeTimeStamp($arGroupElement['ACTIVE_TO']);
            $activeFromTimestamp = MakeTimeStamp($arGroupElement['ACTIVE_FROM']);

            $arGroupElement['ACTION']['NAME'] = $arGroupElement['NAME'];
            $arGroupElement['ACTION']['TEXT'] = html_entity_decode($arGroupElement['PREVIEW_TEXT']);
            $arGroupElement['ACTION']['DAYS'] = FormatDate('Q', $todayTimestamp, $activeToTimestamp);
            $arGroupElement['ACTION']['DAYS_INT'] = intval($arGroupElement['ACTION']['DAYS']);
            $arGroupElement['ACTION']['MESS'] = "Акция закончилась";
            $arGroupElement['ACTION']['IMAGE'] = $arGroupElement['PREVIEW_PICTURE'] ? \CFile::GetPath($arGroupElement['PREVIEW_PICTURE']) : false;

            if ($activeFromTimestamp > time()) {
                $arGroupElement['ACTION']['MESS'] = 'Акция начнется через '.FormatDate('Q', $todayTimestamp, $activeFromTimestamp);
            } elseif (1 < $arGroupElement['ACTION']['DAYS_INT']) {
                $arGroupElement['ACTION']['MESS'] = 'Акция заканчивается через '.$arGroupElement['ACTION']['DAYS'];
            } elseif (date('z', $activeToTimestamp) == date('z')) {
                $arGroupElement['ACTION']['MESS'] = "Последний день акции";
            }
        }
    }
}
