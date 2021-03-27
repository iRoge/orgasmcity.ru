<?php
/**
 * User: Azovcev Artem
 * Date: 05.03.17
 * Time: 0:26
 */

namespace Likee\Site\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Likee\Site\Helpers;

/**
 * Класс для работы с тегами каталога
 *
 * @package Likee\Site\Helpers
 */
class Tags
{
    /**
     * @var string Папка с кэшем
     */
    private static $sCacheDir = '/likee/site/tags';
    /**
     * @var array Инфоблоки
     */
    private static $arIBlocks = null;
    /**
     * @var array Кэш
     */
    private static $arCache = [];

    public static function getTagsIblockId()
    {
        try {
            return Helpers\IBlock::getIBlockId('CATALOG_TAGS');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSectionIdByCodePath($arVariables)
    {
        $iIBlockID = self::getTagsIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        if (empty($arVariables['SECTION_CODE_PATH'])) {
            return true;
        }

        $obCache = Application::getCache();
        $sCacheId = 'sections_'.$iIBlockID;

        if ($obCache->initCache(604800, $sCacheId, self::$sCacheDir)) {
            $arTagSections = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $arTagSections = [];

            Application::getInstance()->getTaggedCache()->startTagCache(self::$sCacheDir);
            Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$iIBlockID);

            if (Loader::includeModule('iblock')) {
                $arSectionCodePath = [];

                $arFilter = ['IBLOCK_ID' => $iIBlockID, 'ACTIVE' => 'Y'];
                $arSelect = ['ID', 'CODE', 'DEPTH_LEVEL'];
                $rsSection = \CIBlockSection::GetTreeList($arFilter, $arSelect);
                while ($arSection = $rsSection->Fetch()) {
                    $arSectionCodePath = array_slice($arSectionCodePath, 0, $arSection['DEPTH_LEVEL']-1);
                    $arSectionCodePath[] = $arSection['CODE'];
                    $sCodePath = implode('/', $arSectionCodePath);

                    $arTagSections[$sCodePath] = $arSection['ID'];
                }
            }

            Application::getInstance()->getTaggedCache()->endTagCache();

            $obCache->endDataCache($arTagSections);
        }

        return isset($arTagSections[$arVariables['SECTION_CODE_PATH']]) ? $arTagSections[$arVariables['SECTION_CODE_PATH']] : false;
    }

    public static function getSectionTagsByCodePath($arVariables)
    {
        $iIBlockID = self::getTagsIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        $iSectionId = self::getSectionIdByCodePath($arVariables);
        if (! $iSectionId) {
            return false;
        }

        $obCache = Application::getCache();
        $sCacheId = 'section_tags_'.$iSectionId;
    
        if ($obCache->initCache(604800, $sCacheId, self::$sCacheDir)) {
            $arResult = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $arResult = [];

            Application::getInstance()->getTaggedCache()->startTagCache(self::$sCacheDir);
            Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$iIBlockID);

            if (Loader::includeModule('iblock')) {
                $arElementFilter = [
                    'IBLOCK_ID' => $iIBlockID,
                    'ACTIVE' => 'Y',
                    'SECTION_ID' => $iSectionId
                ];
            
                $rsItems = \CIBlockElement::GetList(
                    ['NAME' => 'ASC'],
                    $arElementFilter,
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'NAME', 'CODE']
                );
            
                while ($arItem = $rsItems->Fetch()) {
                    $arItem['URL'] = '/'.$arVariables['SECTION_CODE_PATH'].'/tag_'.$arItem['CODE'];
                    $arResult[$arItem['CODE']] = $arItem;
                }
            }
    
            Application::getInstance()->getTaggedCache()->endTagCache();
    
            $obCache->endDataCache($arResult);
        }

        return $arResult;
    }

    public static function getSectionTagByPathAndCode($arVariables, $sTagCode)
    {
        $iIBlockID = self::getTagsIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        $arSectionTags = self::getSectionTagsByCodePath($arVariables);
        if (empty($arSectionTags[$sTagCode])) {
            return false;
        }

        $obCache = Application::getCache();
        $sCacheId = 'tags_'.$sTagCode.'_'.md5(serialize($arVariables));
    
        if ($obCache->initCache(604800, $sCacheId, self::$sCacheDir)) {
            $arResult = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $arResult = [];
        
            Application::getInstance()->getTaggedCache()->startTagCache(self::$sCacheDir);
            Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$iIBlockID);
            
            if (Loader::includeModule('iblock')) {
                $res = \CIBlockElement::GetByID($arSectionTags[$sTagCode]['ID']);
                if ($rsItem = $res->GetNextElement()) {
                    $arResult = $rsItem->GetFields();
                    $arTagElementProps = $rsItem->GetProperties();

                    $arResult['FILTER'] = self::buldTagFilterByPropsValues($arTagElementProps);
                    
                    $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);
                    $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
                }
            }
            
            Application::getInstance()->getTaggedCache()->endTagCache();
    
            $obCache->endDataCache($arResult);
        }

        return $arResult;
    }


    public static function checkSectionTagPath(&$arVariables)
    {
        $iIBlockID = self::getTagsIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        if (!$arVariables || !is_array($arVariables)) {
            return false;
        }

        if (empty($arVariables['SECTION_CODE_PATH'])) {
            return false;
        }

        $tagCode = false;

        $arSectionTemp = explode('/', trim($arVariables['SECTION_CODE_PATH'], '/'));
        $arSectionTemp = array_reverse($arSectionTemp);
        $section_main=substr($arVariables['SECTION_CODE_PATH'], 0, strpos($arVariables['SECTION_CODE_PATH'], "/tag_"));

        if (! empty($arSectionTemp[0]) && 0 === strpos($arSectionTemp[0], 'tag_')) {
            $tagCode = array_shift($arSectionTemp);
            $tagCode = substr($tagCode, 4);

            $arFilter["=CODE"]=$arSectionTemp[0];
            $arSelect=array("ID","SECTION_PAGE_URL");
            $rsSection = \CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
            $section_id=0;

            while ($arResSect = $rsSection->GetNext()) {
                if (strpos($arResSect["SECTION_PAGE_URL"], $section_main)!==false) {
                    $section_id=$arResSect["ID"];
                }
            }
            $arVariables['SECTION_ID'] = $section_id;
            $arVariables['SECTION_CODE'] = $arSectionTemp[0];
            $arVariables['SECTION_CODE_PATH'] = implode('/', array_reverse($arSectionTemp));
        }

        return $tagCode;
    }

    protected static function buldTagFilterByPropsValues($arElementProps)
    {
        $arFilter = [];
    
        if (!empty($arElementProps['PRICE_FROM']['VALUE']) || !empty($arElementProps['PRICE_TO']['VALUE'])) {
            $arFilter['><PROPERTY_MINIMUM_PRICE'] = [0, PHP_INT_MAX];
    
            if (! empty($arElementProps['PRICE_FROM']['VALUE'])) {
                $arFilter['><PROPERTY_MINIMUM_PRICE'][0] = $arElementProps['PRICE_FROM']['VALUE'];
            }
                
            if (! empty($arElementProps['PRICE_TO']['VALUE'])) {
                $arFilter['><PROPERTY_MINIMUM_PRICE'][1] = $arElementProps['PRICE_TO']['VALUE'];
            }
        }
    
        unset($arElementProps['SECTION'], $arElementProps['PRICE_FROM'], $arElementProps['PRICE_TO']);
    
        foreach ($arElementProps as $propCode => $propData) {
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

                $arFilter['OFFERS']['PROPERTY_'.$propCode] = $arPropData;
            } else {
                if (is_string($propData['VALUE']) && false !== strpos($propData['VALUE'], ',')) {
                    $propData['VALUE'] = explode(',', $propData['VALUE']);
                    $propData['VALUE'] = array_map('trim', $propData['VALUE']);
                }
                $arFilter['PROPERTY_'.$propCode] = $propData['VALUE'];
            }
        }

        return $arFilter;
    }
}
