<?

use Bitrix\Main\Loader;
use Bitrix\Main\FileTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


if (!isset($arParams['CACHE_TIME'])) {
    $arParams['CACHE_TIME'] = 36000000;
}

$arParams['ID'] = intval($arParams['ID']);
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

$arParams['DEPTH_LEVEL'] = intval($arParams['DEPTH_LEVEL']);
if ($arParams['DEPTH_LEVEL'] <= 0) {
    $arParams['DEPTH_LEVEL'] = 1;
}

global $CACHE_MANAGER;
$cache = new CPHPCache();

if ($cache->InitCache($arParams['CACHE_TIME'], 'menu_global', '/menu_global')) {
    $aMenuLinksNew = $cache->getVars();
}
if (empty($aMenuLinksNew)) {
    $cache->StartDataCache();
    $CACHE_MANAGER->StartTagCache('/menu_global');
    $CACHE_MANAGER->RegisterTag("catalogAll");
    $arResult['SECTIONS'] = [];
    $arResult['ELEMENT_LINKS'] = [];
    if (!Loader::includeModule('iblock')) {
        $this->AbortResultCache();
        $CACHE_MANAGER->AbortTagCache();
        return [];
    }

    $mainSection = CIBlockSection::GetByID(MAIN_SECTION_ID)->GetNext();
    $res = CIBlockSection::GetList(
        [
            "SORT" => "ASC",
        ],
        [
            "IBLOCK_ID" => IBLOCK_CATALOG,
            ">LEFT_MARGIN" => $mainSection["LEFT_MARGIN"],
            "<RIGHT_MARGIN" => $mainSection["RIGHT_MARGIN"],
        ],
        false,
        [
            "ID",
            "NAME",
        ]
    );
    $arMainSectionIds = [];
    while ($arItem = $res->Fetch()) {
        $arMainSectionIds[] = $arItem["ID"];
    }
    $products = [];

    // Находим все продукты с секциями
    $rsElements = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'IBLOCK_SECTION_ID' => $arMainSectionIds,
            'ACTIVE' => 'Y',
            '!DETAIL_PICTURE' => false
        ],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_PICTURE', 'PROPERTY_BESTSELLER']
    );
    $arImageIds = [];
    while ($arElement = $rsElements->GetNext()) {
        if (!$arElement["DETAIL_PICTURE"]) {
            continue;
        }
        $arImageIds[] = $arElement["DETAIL_PICTURE"];
        $products[$arElement['ID']] = $arElement;
    }
    if (!empty($arImageIds)) {
        $res = FileTable::getList([
            "select" => [
                "ID",
                "SUBDIR",
                "FILE_NAME",
            ],
            "filter" => [
                "ID" => $arImageIds,
            ],
        ]);
        $arImages = [];
        while ($arItem = $res->Fetch()) {
            $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
            if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
                continue;
            }
            $arImages[$arItem["ID"]] = $src;
        }
        foreach ($products as $id => &$arItem) {
            if (!empty($arImages[$arItem["DETAIL_PICTURE"]])) {
                $arItem["DETAIL_PICTURE"] = $arImages[$arItem["DETAIL_PICTURE"]];
            } else {
                unset($products[$id]);
                continue;
            }
        }
    }
    // Находим все предложения
    $offers = [];
    $rsElements = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $arParams['IBLOCK_OFFERS_ID'],
            'ACTIVE' => 'Y'
        ],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK', 'PROPERTY_BASEPRICE', 'PROPERTY_BASEWHOLEPRICE']
    );
    $prod_keys = array_keys($products);
    $items = [];
    while ($arElement = $rsElements->GetNext()) {
        if (!$products[$arElement['PROPERTY_CML2_LINK_VALUE']]) {
            continue;
        }
        $offers[$arElement['ID']] = $arElement;
    }
    $offerIds = array_keys($offers);
    // Достаем остатки по товарам
    $rests = Functions::getRests($offerIds);

    foreach ($offers as $offerId => $offer) {
        if ((!isset($rests[$offerId]) || $rests[$offerId] < 1)) {
            continue;
        }
        $pid = $offer['PROPERTY_CML2_LINK_VALUE'];
        $items[$pid] = $products[$pid];
        if (!isset($items[$pid]['DISCOUNT'])) {
            $items[$pid]['DISCOUNT'] = \Qsoft\Helpers\PriceUtils::getPrice($offer['PROPERTY_BASEWHOLEPRICE_VALUE'], $offer['PROPERTY_BASEPRICE_VALUE'])['DISCOUNT'];
        }
    }

    // Заполняем массив айдишников нужных нам секций
    $bSpecSec['HITS'] = false;
    $bSpecSec['SALES'] = false;
    $arSectionsIds = [];
    $arAvailableHitsSectionsIds = [];
    $arAvailableHitsItems = [];
    $arAvailableSalesSectionsIds = [];
    $arAvailableSalesItems = [];
    $arAvailableSectionsIds = [];
    foreach ($items as $item) {
        $arAvailableSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
        if ($item['PROPERTY_BESTSELLER_VALUE']) {
            $bSpecSec['HITS'] = true;
            $arAvailableHitsSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
            $arAvailableHitsItems[$item['ID']] = $item;
        }

        if ($item['DISCOUNT'] > 0) {
            $bSpecSec['SALES'] = true;
            $arAvailableSalesSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
            $arAvailableSalesItems[$item['ID']] = $item;
        }
    }

    // Находим все секции
    $arFilter = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'GLOBAL_ACTIVE' => 'Y',
        'IBLOCK_ACTIVE' => 'Y',
        '<=DEPTH_LEVEL' => $arParams['DEPTH_LEVEL'],
        '>=DEPTH_LEVEL' => 2
    );

    $arOrder = ['LEFT_MARGIN' => 'ASC'];

    $rsSections = CIBlockSection::GetList(
        $arOrder,
        $arFilter,
        false,
        array(
            'ID',
            'DEPTH_LEVEL',
            'NAME',
            'SECTION_PAGE_URL',
        )
    );

    $rsSections->SetUrlTemplates('', $arParams['SECTION_URL']);

    while ($arSection = $rsSections->GetNext()) {
        $arResult['SECTIONS'][] = array(
            'ID' => $arSection['ID'],
            'DEPTH_LEVEL' => $arSection['DEPTH_LEVEL'] - 1,
            '~NAME' => $arSection['~NAME'],
            '~SECTION_PAGE_URL' => $arSection['~SECTION_PAGE_URL'],
        );
        $arResult['ELEMENT_LINKS'][$arSection['ID']] = array();
    }

    // Фильтруем секции по массиву $arSectionsIds
    foreach ($arResult['SECTIONS'] as $key => $section) {
        if (in_array($section['ID'], $arMainSectionIds) && ($section['DEPTH_LEVEL'] == 1 || $section['DEPTH_LEVEL'] == 2)) {
            continue;
        } elseif (!in_array($section['ID'], $arAvailableSectionsIds)) {
            unset($arResult['SECTIONS'][$key]);
        }
    }

    //CUSTOM
    if ($bSpecSec['HITS']) {
        $arResult['HITS'] = [
            'UF_NAME' => 'ХИТЫ',
            'UF_CODE' => 'hits',
            'PROPS' => [
                'TEXT_COLOR' => '#005dff'
            ]
        ];
    }

    if ($bSpecSec['SALES']) {
        $arResult['SALES'] = [
            'UF_NAME' => 'СКИДКИ',
            'UF_CODE' => 'sales',
            'PROPS' => [
                'TEXT_COLOR' => '#ff002c'
            ]
        ];
    }
    //END CUSTOM


    $arVariables = [];

    if (($arParams['ID'] > 0) && (intval($arVariables['SECTION_ID']) <= 0) && Loader::includeModule('iblock')) {
        $arSelect = ['ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID'];
        $arFilter = [
            'ID' => $arParams['ID'],
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        ];

        $rsElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        while ($arElement = $rsElements->GetNext()) {
            $arResult['ELEMENT_LINKS'][$arElement['IBLOCK_SECTION_ID']][] = $arElement['~DETAIL_PAGE_URL'];
        }
    }

    $currentUrl = $APPLICATION->GetCurPage();
    $arMenuCatalogGroups = [];

    $aMenuLinksNew = [];
    $menuIndex = 0;
    $previousDepthLevel = 1;

    foreach ($arResult['SECTIONS'] as $arSection) {
        if ($menuIndex > 0) {
            $aMenuLinksNew[$menuIndex - 1][3]['IS_PARENT'] = $arSection['DEPTH_LEVEL'] > $previousDepthLevel;
        }

        if ($menuIndex > 0 && $arSection['DEPTH_LEVEL'] == 1 && !empty($arMenuCatalogGroups)) {
            $aMenuLinksNew[$menuIndex++] = ['&nbsp;', '', [], [
                'FROM_IBLOCK' => true,
                'IS_PARENT' => true,
                'DEPTH_LEVEL' => 2,
            ]];
            $aMenuLinksNew = array_merge($aMenuLinksNew, $arMenuCatalogGroups);

            $menuIndex += count($arMenuCatalogGroups);
            $arMenuCatalogGroups = [];
        }

        $previousDepthLevel = $arSection['DEPTH_LEVEL'];

        $arResult['ELEMENT_LINKS'][$arSection['ID']][] = urldecode($arSection['~SECTION_PAGE_URL']);

        $aMenuLinksNew[$menuIndex++] = array(
            htmlspecialcharsbx($arSection['~NAME']),
            $arSection['~SECTION_PAGE_URL'],
            $arResult['ELEMENT_LINKS'][$arSection['ID']],
            array(
                'FROM_IBLOCK' => true,
                'IS_PARENT' => false,
                'DEPTH_LEVEL' => $arSection['DEPTH_LEVEL'],
            ),
        );

        if ((0 === strpos($currentUrl, $arSection['~SECTION_PAGE_URL']) || 1 == $arSection['DEPTH_LEVEL']) && !empty($arCatalogGroups[$arSection['ID']])) {
            $arMenuCatalogGroups = array_merge($arMenuCatalogGroups, $arCatalogGroups[$arSection['ID']]);
        }
    }
    if (!empty($arMenuCatalogGroups)) {
        $aMenuLinksNew[$menuIndex++] = ['&nbsp;', '', [], [
            'FROM_IBLOCK' => true,
            'IS_PARENT' => true,
            'DEPTH_LEVEL' => 2,
        ]];
        $aMenuLinksNew = array_merge($aMenuLinksNew, $arMenuCatalogGroups);
    }

//CUSTOM
    $arMenuLinkSales = [];
    $arMenuLinkHits = [];

    if (!empty($arResult['SALES'])) {
        $arMenuLinkSales[] = array(
            $arResult['SALES']['UF_NAME'],
            '/catalog/' . $arResult['SALES']['UF_CODE'] . '/',
            array(
                '/catalog/' . $arResult['SALES']['UF_CODE'] . '/',
            ),
            array(
                'HIGHLIGHT' => 'Y',
                'IS_PARENT' => true,
                'FROM_IBLOCK' => true,
                'DEPTH_LEVEL' => 1,
                'PROPS' => $arResult['SALES']['PROPS']
            ),
            ''
        );

        $arSalesSections = [];

        foreach ($arAvailableSalesItems as $item) {
            $nav = CIBlockSection::GetNavChain(false, $item['IBLOCK_SECTION_ID']);

            $iDepth = 1;
            while ($arSection = $nav->Fetch()) {
                $arSalesSections[$iDepth][$arSection['ID']] = $arSection;
                $iDepth++;
            }
        }

        krsort($arSalesSections[2]);
        // Фильтруем секции по массиву $arSectionsIds
        $arAvailable3rdLvlSalesSectionsIds = [];
        foreach ($arSalesSections[4] as $key => $section) {
            if (!in_array($key, $arAvailableSalesSectionsIds)) {
                unset($arSalesSections[4][$key]);
                continue;
            }
            $arAvailable3rdLvlSalesSectionsIds[$section['IBLOCK_SECTION_ID']] = $section['IBLOCK_SECTION_ID'];
        }

        foreach ($arSalesSections[3] as $key => $section) {
            if (!in_array($key, $arAvailableSalesSectionsIds)) {
                continue;
            }
            $arAvailable3rdLvlSalesSectionsIds[$section['ID']] = $section['ID'];
        }

        foreach ($arSalesSections[2] as $arSalesSection2) {
            foreach ($arSalesSections[3] as $key => $arSalesSection3) {
                if (!in_array($key, $arAvailable3rdLvlSalesSectionsIds)) {
                    unset($arSalesSections[3][$key]);
                    continue;
                }
                if ($arSalesSection3['IBLOCK_SECTION_ID'] == $arSalesSection2['ID']) {
                    $sPath = '/catalog/' . $arResult['SALES']['UF_CODE'] . '/' . reset($arSalesSections[1])['CODE'] . '/' . $arSalesSection2['CODE'] . '/' . $arSalesSection3['CODE'] . '/';
                    $arMenuLinkSales[] = array(
                        $arSalesSection3['NAME'],
                        $sPath,
                        array($sPath),
                        array(
                            'IS_PARENT' => true,
                            'DEPTH_LEVEL' => 2,
                            'FROM_IBLOCK' => true,
                        ),
                        ''
                    );
                    foreach ($arSalesSections[4] as $arSalesSection4) {
                        if ($arSalesSection4['IBLOCK_SECTION_ID'] == $arSalesSection3['ID']) {
                            $sPath = '/catalog/' . $arResult['SALES']['UF_CODE'] .'/' . reset($arSalesSections[1])['CODE'] .  '/' . $arSalesSection2['CODE'] . '/' . $arSalesSection3['CODE'] . '/' . $arSalesSection4['CODE'] . '/';
                            $arMenuLinkSales[] = array(
                                $arSalesSection4['NAME'],
                                $sPath,
                                array($sPath),
                                array(
                                    'FROM_IBLOCK' => true,
                                    'DEPTH_LEVEL' => 3,
                                ),
                                ''
                            );
                        }
                    }
                }
            }
        }
        if (empty($arSalesSections[3])) {
            unset($arMenuLinkSales);
        }
    }

    if (!empty($arMenuLinkSales)) {
        $aMenuLinksNew = array_merge($arMenuLinkSales, $aMenuLinksNew);
    }

    if (!empty($arResult['HITS'])) {
        $arMenuLinkHits[] = array(
            $arResult['HITS']['UF_NAME'],
            '/catalog/' . $arResult['HITS']['UF_CODE'] . '/',
            array(
                '/catalog/' . $arResult['HITS']['UF_CODE'] . '/'
            ),
            array(
                'IS_PARENT' => true,
                'FROM_IBLOCK' => true,
                'DEPTH_LEVEL' => 1,
                'PROPS' => $arResult['HITS']['PROPS']
            ),
            ''
        );

        $arHitsSections = [];

        foreach ($arAvailableHitsItems as $item) {
            $nav = CIBlockSection::GetNavChain(false, $item['IBLOCK_SECTION_ID']);

            $iDepth = 1;
            while ($arSection = $nav->Fetch()) {
                $arHitsSections[$iDepth][$arSection['ID']] = $arSection;
                $iDepth++;
            }
        }


        krsort($arHitsSections[2]);
        $arAvailable3rdLvlHitsSectionsIds = [];
        // Фильтруем секции по массиву $arSectionsIds
        foreach ($arHitsSections[4] as $key => $section) {
            if (!in_array($key, $arAvailableHitsSectionsIds)) {
                unset($arHitsSections[4][$key]);
                continue;
            }
            $arAvailable3rdLvlHitsSectionsIds[$section['IBLOCK_SECTION_ID']] = $section['IBLOCK_SECTION_ID'];
        }

        foreach ($arHitsSections[3] as $key => $section) {
            if (!in_array($key, $arAvailableHitsSectionsIds)) {
                continue;
            }
            $arAvailable3rdLvlHitsSectionsIds[$section['ID']] = $section['ID'];
        }

        foreach ($arHitsSections[2] as $arHitsSection2) {
            foreach ($arHitsSections[3] as $key => $arHitsSection3) {
                if (!in_array($key, $arAvailable3rdLvlHitsSectionsIds)) {
                    unset($arHitsSections[3][$key]);
                    continue;
                }
                if ($arHitsSection3['IBLOCK_SECTION_ID'] == $arHitsSection2['ID']) {
                    $sPath = '/catalog/' . $arResult['HITS']['UF_CODE'] .'/' . reset($arHitsSections[1])['CODE'] .  '/' . $arHitsSection2['CODE'] . '/' . $arHitsSection3['CODE'] . '/';
                    $arMenuLinkHits[] = array(
                        $arHitsSection3['NAME'],
                        $sPath,
                        array($sPath),
                        array(
                            'IS_PARENT' => true,
                            'DEPTH_LEVEL' => 2,
                            'FROM_IBLOCK' => true,
                        ),
                        ''
                    );
                    foreach ($arHitsSections[4] as $arHitsSection4) {
                        if ($arHitsSection4['IBLOCK_SECTION_ID'] == $arHitsSection3['ID']) {
                            $sPath = '/catalog/' . $arResult['HITS']['UF_CODE'] .'/' . reset($arHitsSections[1])['CODE'] .  '/' . $arHitsSection2['CODE'] . '/' . $arHitsSection3['CODE'] . '/' . $arHitsSection4['CODE'] . '/';
                            $arMenuLinkHits[] = array(
                                $arHitsSection4['NAME'],
                                $sPath,
                                array($sPath),
                                array(
                                    'FROM_IBLOCK' => true,
                                    'DEPTH_LEVEL' => 3,
                                ),
                                ''
                            );
                        }
                    }
                }
            }
        }
        if (empty($arHitsSections[3])) {
            unset($arMenuLinkHits);
        }
    }

    if (!empty($arMenuLinkHits)) {
        $aMenuLinksNew = array_merge($arMenuLinkHits, $aMenuLinksNew);
    }

//END CUSTOM
    $cache->EndDataCache($aMenuLinksNew);
    $CACHE_MANAGER->EndTagCache();
}
return $aMenuLinksNew;
