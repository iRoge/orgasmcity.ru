<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Likee\Site\Helpers;
use \Likee\Location\Location;

class CityDomain
{
    /**
     * @var string Папка с кэшем
     */
    private static $sCacheDir = '/likee/site/city_seo';

    protected static $siteLocationId = false;

    public static function isCitySubdomain()
    {
        return (bool) self::$siteLocationId;
    }

    public static function OnEpilog()
    {
        global $APPLICATION;

        if (self::$siteLocationId) {
            // настройка SEO
            if (! empty($GLOBALS['arLocation']['CITY_NAME'])) {
                $title = $APPLICATION->GetPageProperty('title') ?: $APPLICATION->GetTitle();
                $cityName = $GLOBALS['arLocation']['CITY_NAME'];
    
                if (!empty($GLOBALS['CATALOG_ELEMENT_ID'])) {
                    $APPLICATION->SetPageProperty('title', $title.' | интернет-магазин Orgasmcity | '.$cityName);
                } elseif (!empty($GLOBALS['CATALOG_SECTION_ID']) || \CSite::InDir(SITE_DIR . 'catalog/') || \CSite::InDir(SITE_DIR . 'actions/')) {
                    $cityNameWhere = $APPLICATION->GetProperty('CITY_NAME_WHERE', $cityName);
                    $APPLICATION->SetPageProperty('title', $title.' в '.$cityNameWhere);
                } else {
                    $APPLICATION->SetPageProperty('title', $title.' | '.$cityName);
                }
            }
        }
    }

    public function getLocationsSiteInfo()
    {
        static $arLocationsSiteInfo = null;

        if (is_null($arLocationsSiteInfo)) {
            $arLocationsSiteInfo = [];

            $obCache = Application::getCache();
            if ($obCache->initCache(86400, 'locations_site', '/likee/locations/')) {
                $arLocationsSiteInfo = $obCache->getVars();
            } else {
                $rsSites = \CSite::GetList($by = "sort", $order = "asc", ['ACTIVE' => 'Y']);
                
                while ($arSite = $rsSites->Fetch()) {
                    if ('Y' == $arSite['DEF']) {
                        $arLocationsSiteInfo[0] = [
                            'SITE_ID' => $arSite['ID'],
                            'LOCATION_ID' => false,
                            'SERVER_NAME' => $arSite['SERVER_NAME']
                        ];
                    } elseif (preg_match('/^(.+?) \((\d+)\)$/i', $arSite['NAME'], $m)) {
                        $arLocationsSiteInfo[$m[1]] = [
                            'SITE_ID' => $arSite['ID'],
                            'LOCATION_ID' => (int) $m[2],
                            'SERVER_NAME' => $arSite['SERVER_NAME']
                        ];
                    }
                }

                unset($rsSites, $arSite);

                if ($obCache->startDataCache()) {
                    $obCache->endDataCache($arLocationsSiteInfo);
                }
            }
        }

        return $arLocationsSiteInfo;
    }

    public static function getCitySeoIblockId()
    {
        try {
            return Helpers\IBlock::getIBlockId('CATALOG_CITY_SEO');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSectionIdByCodePath($arVariables)
    {
        $iIBlockID = self::getCitySeoIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        if (empty($arVariables['SECTION_CODE_PATH'])) {
            return true;
        }

        $obCache = Application::getCache();
        $sCacheId = 'sections_'.$iIBlockID;

        if ($obCache->initCache(604800, $sCacheId, self::$sCacheDir)) {
            $arCitySeoSections = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $arCitySeoSections = [];

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

                    $arCitySeoSections[$sCodePath] = $arSection['ID'];
                }
            }

            Application::getInstance()->getTaggedCache()->endTagCache();

            $obCache->endDataCache($arCitySeoSections);
        }

        return isset($arCitySeoSections[$arVariables['SECTION_CODE_PATH']]) ? $arCitySeoSections[$arVariables['SECTION_CODE_PATH']] : false;
    }

    public static function getCityCatalogSeoByPathAndCode($arVariables, $sTagCode = false)
    {
        if (!self::$siteLocationId || empty($arVariables['SECTION_CODE_PATH'])) {
            return false;
        }

        $arVariables['SECTION_CODE_PATH'] = self::$siteLocationId.'/'.$arVariables['SECTION_CODE_PATH'];

        $iIBlockID = self::getCitySeoIblockId();
        if ($iIBlockID <= 0) {
            return false;
        }

        $iSectionId = self::getSectionIdByCodePath($arVariables);
        if (! $iSectionId) {
            return false;
        }

        $obCache = Application::getCache();
        $sCacheId = 'seo_'.intval($sTagCode).'_'.md5(serialize($arVariables));

        if ($obCache->initCache(604800, $sCacheId, self::$sCacheDir)) {
            $arResult = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $arResult = [];
        
            Application::getInstance()->getTaggedCache()->startTagCache(self::$sCacheDir);
            Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$iIBlockID);
            
            if (Loader::includeModule('iblock')) {
                if ($sTagCode) {
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
                        ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'DETAIL_TEXT']
                    );

                    while ($arItem = $rsItems->Fetch()) {
                        if ($sTagCode != $arItem['CODE']) {
                            continue;
                        }

                        $arResult = $arItem;
                        $arResult['DESCRIPTION'] = $arResult['DETAIL_TEXT'];
                        
                        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);
                        $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
                        
                        break;
                    }
                } else {
                    $arResult = \CIBlockSection::GetByID($iSectionId)->GetNext();
                    $arResult["IPROPERTY_VALUES"] = [];

                    if ($arResult) {
                        $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arResult["IBLOCK_ID"], $arResult['ID']);
                        foreach ($ipropValues->getValues() as $key => $value) {
                            $key = str_replace('SECTION_', 'ELEMENT_', $key);
                            $arResult["IPROPERTY_VALUES"][$key] = $value;
                        }
                    }
                }
            }
            
            Application::getInstance()->getTaggedCache()->endTagCache();
    
            $obCache->endDataCache($arResult);
        }

        return $arResult;
    }
}
