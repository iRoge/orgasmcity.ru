<?php

namespace Likee\Site\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

/**
 * Класс для работы с фидами. Содержит методы для поиска и создания фильтров
 *
 * @package Likee\Site\Helpers
 */
class Feeds
{
    /**
     * название инфоблока группировки
     */
    const IBLOCK_NAME = 'FEEDS_CONFIG';

    public static function getFeedsIblockId()
    {
        static $feedsIblockId = null;
        
        if (is_null($feedsIblockId)) {
            $arIblock = IblockTable::getRow(['filter' => ['CODE' => self::IBLOCK_NAME]]);
            $feedsIblockId = $arIblock ? $arIblock['ID'] : 0;
        }

        return $feedsIblockId;
    }

    public static function getFeedFilterByCode($code)
    {
        $feedsIblockId = self::getFeedsIblockId();
        if (!$feedsIblockId || !Loader::includeModule('iblock')) {
            return false;
        }

        $resFeed = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $feedsIblockId,
                'ACTIVE' => 'Y',
                'CODE' => $code
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PROPERTY_*']
        );
        if (!($rsFeed = $resFeed->GetNextElement(false, false))) {
            return false;
        }

        $arFeedElement = $rsFeed->GetFields();
        $arFeedElementProps = $rsFeed->GetProperties();

        $arFeedFilter = [];

        if (!empty($arFeedElementProps['SECTION']['VALUE'])) {
            $arFeedFilter['SECTION_ID'] = $arFeedElementProps['SECTION']['VALUE'];
            $arFeedFilter['INCLUDE_SUBSECTIONS'] = 'Y';
        }

        // фильтр по цене
        if (!empty($arFeedElementProps['PRICE_FROM']['VALUE']) || !empty($arFeedElementProps['PRICE_TO']['VALUE'])) {
            $arFeedFilter['><PROPERTY_MINIMUM_PRICE'] = [0, PHP_INT_MAX];

            if (!empty($arFeedElementProps['PRICE_FROM']['VALUE'])) {
                $arFeedFilter['><PROPERTY_MINIMUM_PRICE'][0] = $arFeedElementProps['PRICE_FROM']['VALUE'];
            }
            if (!empty($arFeedElementProps['PRICE_TO']['VALUE'])) {
                $arFeedFilter['><PROPERTY_MINIMUM_PRICE'][1] = $arFeedElementProps['PRICE_TO']['VALUE'];
            }
        }

        // фильтр по размеру скидки
        if ((!empty($arFeedElementProps['SEGMENT_FROM']['VALUE']) || !empty($arFeedElementProps['SEGMENT_TO']['VALUE'])) && !empty($arFeedElementProps['PRICESEGMENTID']['VALUE'])) {
            $arSegmentPropertyFlter = [0, PHP_INT_MAX];

            if (!empty($arFeedElementProps['SEGMENT_FROM']['VALUE'])) {
                $arSegmentPropertyFlter[0] = $arFeedElementProps['SEGMENT_FROM']['VALUE'];
            }
            if (!empty($arFeedElementProps['SEGMENT_TO']['VALUE'])) {
                $arSegmentPropertyFlter[1] = $arFeedElementProps['SEGMENT_TO']['VALUE'];
            }

            if ('White' == $arFeedElementProps['PRICESEGMENTID']['VALUE']) {
                $arFeedFilter['><PROPERTY_MAXDISCBP'] = $arSegmentPropertyFlter;
            } else {
                $arFeedFilter['><PROPERTY_SEGMENT_PCT'] = $arSegmentPropertyFlter;
            }
        }

        unset($arFeedElementProps['SECTION'], $arFeedElementProps['PRICE_FROM'], $arFeedElementProps['PRICE_TO']);

        foreach ($arFeedElementProps as $propCode => $propData) {
            if (empty($propData['VALUE']) || 0 === strpos($propCode, 'FC_')) {
                continue;
            }

            /**
             * Предполагаем, что по предложениям всегда ищем в списках
             */
            if (0 === strpos($propCode, 'OFFERS_')) {
                $propCode = substr($propCode, strrpos($propCode, 'OFFERS_') + 7);

                if (is_array($propData['VALUE'])) {
                    $arPropData = $propData['VALUE'];
                } else {
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
                }

                $arFeedFilter['OFFERS']['PROPERTY_' . $propCode] = $arPropData;
            } else {
                if (is_string($propData['VALUE']) && false !== strpos($propData['VALUE'], ',')) {
                    $propData['VALUE'] = explode(',', $propData['VALUE']);
                    $propData['VALUE'] = array_map('trim', $propData['VALUE']);
                }
                $arFeedFilter['PROPERTY_' . $propCode] = $propData['VALUE'];
            }
        }

        /** пост проверка */
        if (isset($arFeedFilter['PROPERTY_MLT']) && isset($arFeedFilter['PROPERTY_MRT'])) {
            $arFeedFilter[] = [
                'LOGIC' => 'OR',
                ['PROPERTY_MRT' => $arFeedFilter['PROPERTY_MRT']],
                ['PROPERTY_MLT' => $arFeedFilter['PROPERTY_MLT']],
            ];

            unset($arFeedFilter['PROPERTY_MLT'], $arFeedFilter['PROPERTY_MRT']);
        }

        if (! empty($arFeedFilter['OFFERS'])) {
            $arSubFilter = $arFeedFilter['OFFERS'];

            $arSubFilter['IBLOCK_ID'] = IBLOCK_OFFERS;
            $arSubFilter['ACTIVE'] = 'Y';
            $arSubFilter['CATALOG_AVAILABLE'] = 'Y';

            $arFeedFilter['ID'] = \CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubFilter);
        }

        return $arFeedFilter;
    }

    public static function OnAfterIBlockElement($arFields)
    {
        $feedsIblockId = self::getFeedsIblockId();

        if ( $feedsIblockId == $arFields['IBLOCK_ID'] && (Loader::includeModule('iblock') && Loader::includeModule('catalog')) ) {
            $PROFILE_ID = false;

            $rsProfile = \CCatalogExport::GetList([], [
                'FILE_NAME' => $arFields['CODE']
            ]);
            while($element = $rsProfile->Fetch()) {
                $PROFILE_ID = $element['ID'];
            }
            unset($rsProfile, $element);

            if ($PROFILE_ID) {
                \CAgent::RemoveAgent("CCatalogExport::PreGenerateExport(".$PROFILE_ID.");", "catalog");

                $arPropertyByCode = [];
                $rsProperty = \CIBlockProperty::GetList([], [
                    'IBLOCK_ID' => $arFields['IBLOCK_ID'],
                ]);
                while($element = $rsProperty->Fetch()) {
                    $arPropertyByCode[$element['CODE']] = $element['ID'];
                }
                unset($rsProperty, $element);

                $feedUpdateImport = empty($arFields['PROPERTY_VALUES'][$arPropertyByCode['FC_UPDATE_IMPORT']][0]['VALUE']) ? false : true;

                if (false === $feedUpdateImport) {
                    $feedUpdateTime = array_pop($arFields['PROPERTY_VALUES'][$arPropertyByCode['FC_UPDATE_TIME']]);
                    $feedUpdatePeriod = array_pop($arFields['PROPERTY_VALUES'][$arPropertyByCode['FC_UPDATE_PERIOD']]);

                    if (!empty($feedUpdateTime['VALUE'])) {
                        $feedUpdateTimeMinutes = 0;

                        if (preg_match('/^0?(\d+):0?(\d+)$/', trim($feedUpdateTime['VALUE']), $m)) {
                            $feedUpdateTime = (int) $m[1];
                            $feedUpdateTimeMinutes = (int) $m[2];
                        } else {
                            $feedUpdateTime = (int) $feedUpdateTime['VALUE'];
                        }
                        
                        $feedUpdateTime = ((24 > $feedUpdateTime) ? $feedUpdateTime : 0);
                        $feedUpdateTimeMinutes = ((59 > $feedUpdateTimeMinutes) ? $feedUpdateTimeMinutes : 0);
                        
                        $feedStartDate = new \DateTime();
                        $feedStartDate->setTime($feedUpdateTime, $feedUpdateTimeMinutes);
                        if ($feedStartDate->getTimestamp() < time()) {
                            $feedStartDate->modify('+1 day');
                        }

                        \CAgent::AddAgent(
                            "CCatalogExport::PreGenerateExport(".$PROFILE_ID.");", 
                            "catalog", 
                            "N", 
                            86400, 
                            $feedStartDate->format('d.m.Y H:i:s'), 
                            "Y", 
                            $feedStartDate->format('d.m.Y H:i:s')
                        );
                    } elseif (!empty($feedUpdatePeriod['VALUE'])) {
                        $feedUpdatePeriod = (int) $feedUpdatePeriod['VALUE'];
                        $feedUpdatePeriod = ($feedUpdatePeriod ?: 24);

                        \CAgent::AddAgent(
                            "CCatalogExport::PreGenerateExport(".$PROFILE_ID.");", 
                            "catalog", 
                            "N", 
                            $feedUpdatePeriod*60*60, 
                            "", 
                            "Y"
                        );
                    }
                }
            }
        }
    }

    public static function OnItemsExport($task)
    {
        if ( (empty($task) || $task != 'rests') || !(Loader::includeModule('iblock') && Loader::includeModule('catalog')) ) {
            return;
        }
        
        $feedsIblockId = self::getFeedsIblockId();
        $arImportFeedCodes = [];

        if ($feedsIblockId) {
            $rsFeeds = \CIBlockElement::GetList([], [
                'IBLOCK_ID' => $feedsIblockId,
                '!PROPERTY_FC_UPDATE_IMPORT' => false
            ], false, false, [
                'ID',
                'IBLOCK_ID',
                'CODE'
            ]);
            while($arElement = $rsFeeds->Fetch()) {
                $arImportFeedCodes[$arElement['CODE']] = $arElement['ID'];
            }
            unset($rsFeeds, $arElement);
        }

        if ($arImportFeedCodes) {
            $feedStartDate = new \DateTime();
            $feedStartDate->modify('+20 minutes');

            $rsProfile = \CCatalogExport::GetList([], []);
            while($arElement = $rsProfile->Fetch()) {
                if (array_key_exists($arElement['FILE_NAME'], $arImportFeedCodes)) {
                    \CAgent::RemoveAgent('CCatalogExport::PreGenerateExport('.$arElement['ID'].');', 'catalog');
                    \CAgent::AddAgent(
                        'CCatalogExport::PreGenerateExport('.$arElement['ID'].');', 
                        'catalog', 
                        'N', 
                        86400*30, 
                        $feedStartDate->format('d.m.Y H:i:s'), 
                        'Y',
                        $feedStartDate->format('d.m.Y H:i:s')
                    );
                }
            }
            unset($rsProfile, $arElement);
        }
    }
}