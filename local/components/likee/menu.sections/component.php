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


$HLar = array();

$cache = Bitrix\Main\Data\Cache::createInstance();
if ($cache->initCache($arParams['CACHE_TIME'], 'menu_global', '/menu_global')) {
    $aMenuLinksNew = $cache->getVars();
}
if (empty($aMenuLinksNew)) {
    $cache->startDataCache();
    $arResult['SECTIONS'] = array();
    $arResult['ELEMENT_LINKS'] = array();
    if (!Loader::includeModule('iblock')) {
        $this->AbortResultCache();
    } else {
        $mainSection = CIBlockSection::GetByID(574)->GetNext();
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
            array(),
            array(
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'IBLOCK_SECTION_ID' => $arMainSectionIds,
                'ACTIVE' => 'Y',
                '!DETAIL_PICTURE' => false
            ),
            false,
            false,
            ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_PICTURE']
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
            $res = FileTable::getList(array(
                "select" => array(
                    "ID",
                    "SUBDIR",
                    "FILE_NAME",
                ),
                "filter" => array(
                    "ID" => $arImageIds,
                ),
            ));
            $arImages = array();
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
            array(),
            array(
                'IBLOCK_ID' => $arParams['IBLOCK_OFFERS_ID'],
                'ACTIVE' => 'Y'
            ),
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK']
        );
        $prod_keys = array_keys($products);
        $items = [];
        while ($arElement = $rsElements->GetNext()) {
            if (!$products[$arElement['PROPERTY_CML2_LINK_VALUE']]) {
                continue;
            }
            $offers[$arElement['ID']] = $arElement;
        }
        $rests = [];
        $offerIds = array_keys($offers);
        // Достаем остатки по товарам
        foreach (array_chunk($offerIds, 5000) as $ids) {
            $rsStoreProduct = CCatalogProduct::GetList(
                [],
                ['ID' => $ids],
                false,
                false,
                ['ID', 'QUANTITY']
            );
            while ($arStoreProduct = $rsStoreProduct->fetch()) {
                $rests[$arStoreProduct['ID']] = $arStoreProduct['QUANTITY'];
            }
        }

        foreach ($offers as $offerId => $offer) {
            if ((!isset($rests[$offerId]) || $rests[$offerId] < 1)) {
                continue;
            }
            $pid = $offer['PROPERTY_CML2_LINK_VALUE'];
            $items[$pid] = $products[$pid];
        }

        // Заполняем массив айдишников нужных нам секций
//        $bSpecSec['MRT'] = false;
//        $bSpecSec['MLT'] = false;
        $arSectionsIds = [];
        $arAvailableMRTSectionsIds = [];
        $arAvailableMLTSectionsIds = [];
        $arAvailableSectionsIds = [];
        foreach ($items as $item) {
            $arAvailableSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
//            if ($item['PROPERTY_MRT_VALUE']) {
//                $bSpecSec['MRT'] = true;
//                $arAvailableMRTSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
//            }
//            if ($item['PROPERTY_MLT_VALUE']) {
//                $bSpecSec['MLT'] = true;
//                $arAvailableMLTSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
//            }
        }

        // Находим все секции
        $arFilter = array(
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'GLOBAL_ACTIVE' => 'Y',
            'IBLOCK_ACTIVE' => 'Y',
            '<=DEPTH_LEVEL' => $arParams['DEPTH_LEVEL'],
            '>=DEPTH_LEVEL' => 2
        );

        $arOrder = array('LEFT_MARGIN' => 'ASC');

        $rsSections = CIBlockSection::GetList(
            $arOrder,
            $arFilter,w

        // Фильтруем секции по массиву $arSectionsIds
        foreach ($arResult['SECTIONS'] as $key => $section) {
            if (in_array($section['ID'], $arMainSectionIds) && $section['DEPTH_LEVEL'] == 1 || $section['DEPTH_LEVEL'] == 2) {
                continue;
            } elseif (!in_array($section['ID'], $arAvailableSectionsIds)) {
                unset($arResult['SECTIONS'][$key]);
            }
        }

        //CUSTOM

//        foreach (['MLT', 'MRT'] as $sCode) {
//            if ($bSpecSec[$sCode] === false) {
//                continue;
//            }
//            $arResult[$sCode] = [];
//            $obEntity = \Likee\Site\Helpers\HL::getEntityClassByHLName($sCode);
//
//            if (!empty($obEntity) && is_object($obEntity)) {
//                $sClass = $obEntity->getDataClass();
//                $arResult[$sCode] = $sClass::getRow([
//                    'order' => ['UF_DATE_UPDATE' => 'DESC']
//                ]);
//                $HLar[$sCode] = $sClass::getRow([
//                    'order' => ['UF_DATE_UPDATE' => 'DESC'],
//                    'select' => array('*')
//                ]);
//            }
//        }
        //END CUSTOM
    }


    $arVariables = [];

    if (($arParams['ID'] > 0) && (intval($arVariables['SECTION_ID']) <= 0) && Loader::includeModule('iblock')) {
        $arSelect = array('ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID');
        $arFilter = array(
            'ID' => $arParams['ID'],
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        );

        $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

        while ($arElement = $rsElements->GetNext()) {
            $arResult['ELEMENT_LINKS'][$arElement['IBLOCK_SECTION_ID']][] = $arElement['~DETAIL_PAGE_URL'];
        }
    }

    $currentUrl = $APPLICATION->GetCurPage();
    $arMenuCatalogGroups = [];

    $aMenuLinksNew = array();
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
//    $arMenuLinkMLT = $arMenuLinkMRT = [];
//
//    if (!empty($arResult['MLT'])) {
//        $arMenuLinkMLT[] = array(
//            $arResult['MLT']['UF_NAME'],
//            '/catalog/' . $arResult['MLT']['UF_CODE'] . '/',
//            array(
//                '/catalog/' . $arResult['MLT']['UF_CODE'] . '/',
//            ),
//            array(
//                'HIGHLIGHT' => 'Y',
//                'IS_PARENT' => true,
//                'FROM_IBLOCK' => true,
//                'DEPTH_LEVEL' => 1,
//                'PROPS' => $HLar['MLT']
//            ),
//            ''
//        );
//
//        $rsElements = \CIBlockElement::GetList(
//            [],
//            [
//                'IBLOCK_ID' => IBLOCK_CATALOG,
//                'ACTIVE' => 'Y',
//                '!PROPERTY_MLT' => false,
//                '!DETAIL_PICTURE' => false
//            ]
//        );
//
//        $arMLTSections = [];
//
//        while ($arElement = $rsElements->Fetch()) {
//            $nav = CIBlockSection::GetNavChain(false, $arElement['IBLOCK_SECTION_ID']);
//
//            $iDepth = 2;
//            while ($arSection = $nav->Fetch()) {
//                $arMLTSections[$iDepth][$arSection['ID']] = $arSection;
//                $iDepth++;
//            }
//        }
//        krsort($arMLTSections[2]);
//        // Фильтруем секции по массиву $arSectionsIds
//        $arAvailable3rdLvlMLTSectionsIds = [];
//        foreach ($arMLTSections[4] as $key => $section) {
//            if (!in_array($key, $arAvailableMLTSectionsIds)) {
//                unset($arMLTSections[4][$key]);
//                continue;
//            }
//            $arAvailable3rdLvlMLTSectionsIds[$section['IBLOCK_SECTION_ID']] = $section['IBLOCK_SECTION_ID'];
//        }
//        foreach ($arMLTSections[2] as $arMLTSection2) {
//            foreach ($arMLTSections[3] as $key => $arMLTSection3) {
//                if (!in_array($key, $arAvailable3rdLvlMLTSectionsIds)) {
//                    unset($arMLTSections[3][$key]);
//                    continue;
//                }
//                if ($arMLTSection3['IBLOCK_SECTION_ID'] == $arMLTSection2['ID']) {
//                    $sPath = '/catalog/' . $arResult['MLT']['UF_CODE'] . '/' . $arMLTSection2['CODE'] . '/' . $arMLTSection3['CODE'] . '/';
//                    $arMenuLinkMLT[] = array(
//                        $arMLTSection3['NAME'],
//                        $sPath,
//                        array($sPath),
//                        array(
//                            'IS_PARENT' => true,
//                            'DEPTH_LEVEL' => 2,
//                            'FROM_IBLOCK' => true,
//                        ),
//                        ''
//                    );
//                    foreach ($arMLTSections[4] as $arMLTSection4) {
//                        if ($arMLTSection4['IBLOCK_SECTION_ID'] == $arMLTSection3['ID']) {
//                            $sPath = '/catalog/' . $arResult['MLT']['UF_CODE'] . '/' . $arMLTSection2['CODE'] . '/' . $arMLTSection3['CODE'] . '/' . $arMLTSection4['CODE'] . '/';
//                            $arMenuLinkMLT[] = array(
//                                $arMLTSection4['NAME'],
//                                $sPath,
//                                array($sPath),
//                                array(
//                                    'FROM_IBLOCK' => true,
//                                    'DEPTH_LEVEL' => 3,
//                                ),
//                                ''
//                            );
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($arMLTSections[3])) {
//            unset($arMenuLinkMLT);
//        }
//
//        if (!empty($arCatalogGroups['MLT'][$arResult['MLT']['UF_XML_ID']])) {
//            $arMenuLinkMLT[] = ['&nbsp;', '', [], [
//                'FROM_IBLOCK' => true,
//                'IS_PARENT' => true,
//                'DEPTH_LEVEL' => 2,
//            ]];
//            $arMenuLinkMLT = array_merge($arMenuLinkMLT, $arCatalogGroups['MLT'][$arResult['MLT']['UF_XML_ID']]);
//        }
//    }

//    $aMenuLinksNew = array_merge($arMenuLinkMLT, $aMenuLinksNew);
//
//    if (!empty($arResult['MRT'])) {
//        $arMenuLinkMRT[] = array(
//            $arResult['MRT']['UF_NAME'],
//            '/catalog/' . $arResult['MRT']['UF_CODE'] . '/',
//            array(
//                '/catalog/' . $arResult['MRT']['UF_CODE'] . '/'
//            ),
//            array(
//                'BUTTON' => 'Y',
//                'IS_PARENT' => true,
//                'FROM_IBLOCK' => true,
//                'DEPTH_LEVEL' => 1,
//                'PROPS' => $HLar['MRT']
//            ),
//            ''
//        );
//
//
//        $rsElements = \CIBlockElement::GetList(
//            [],
//            [
//                'IBLOCK_ID' => IBLOCK_CATALOG,
//                'ACTIVE' => 'Y',
//                '!PROPERTY_MRT' => false,
//                '!DETAIL_PICTURE' => false
//            ]
//        );
//
//        $arMRTSections = [];
//
//        while ($arElement = $rsElements->Fetch()) {
//            $nav = CIBlockSection::GetNavChain(false, $arElement['IBLOCK_SECTION_ID']);
//
//            $iDepth = 2;
//            while ($arSection = $nav->Fetch()) {
//                $arMRTSections[$iDepth][$arSection['ID']] = $arSection;
//                $iDepth++;
//            }
//        }
//
//        krsort($arMRTSections[2]);
//        $arAvailable3rdLvlMRTSectionsIds = [];
//        // Фильтруем секции по массиву $arSectionsIds
//        foreach ($arMRTSections[4] as $key => $section) {
//            if (!in_array($key, $arAvailableMRTSectionsIds)) {
//                unset($arMRTSections[4][$key]);
//                continue;
//            }
//            $arAvailable3rdLvlMRTSectionsIds[$section['IBLOCK_SECTION_ID']] = $section['IBLOCK_SECTION_ID'];
//        }
//        foreach ($arMRTSections[2] as $arMRTSection2) {
//            foreach ($arMRTSections[3] as $key => $arMRTSection3) {
//                if (!in_array($key, $arAvailable3rdLvlMRTSectionsIds)) {
//                    unset($arMRTSections[3][$key]);
//                    continue;
//                }
//                if ($arMRTSection3['IBLOCK_SECTION_ID'] == $arMRTSection2['ID']) {
//                    $sPath = '/catalog/' . $arResult['MRT']['UF_CODE'] . '/' . $arMRTSection2['CODE'] . '/' . $arMRTSection3['CODE'] . '/';
//                    $arMenuLinkMRT[] = array(
//                        $arMRTSection3['NAME'],
//                        $sPath,
//                        array($sPath),
//                        array(
//                            'BUTTON' => 'Y',
//                            'IS_PARENT' => true,
//                            'DEPTH_LEVEL' => 2,
//                            'FROM_IBLOCK' => true,
//                        ),
//                        ''
//                    );
//                    foreach ($arMRTSections[4] as $arMRTSection4) {
//                        if ($arMRTSection4['IBLOCK_SECTION_ID'] == $arMRTSection3['ID']) {
//                            $sPath = '/catalog/' . $arResult['MRT']['UF_CODE'] . '/' . $arMRTSection2['CODE'] . '/' . $arMRTSection3['CODE'] . '/' . $arMRTSection4['CODE'] . '/';
//                            $arMenuLinkMRT[] = array(
//                                $arMRTSection4['NAME'],
//                                $sPath,
//                                array($sPath),
//                                array(
//                                    'BUTTON' => 'Y',
//                                    'FROM_IBLOCK' => true,
//                                    'DEPTH_LEVEL' => 3,
//                                ),
//                                ''
//                            );
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($arMRTSections[3])) {
//            unset($arMenuLinkMRT);
//        }
//
//
//        if (!empty($arCatalogGroups['MRT'][$arResult['MRT']['UF_XML_ID']])) {
//            $arMenuLinkMRT[] = ['&nbsp;', '', [], [
//                'BUTTON' => 'Y',
//                'FROM_IBLOCK' => true,
//                'IS_PARENT' => true,
//                'DEPTH_LEVEL' => 2,
//            ]];
//            $arMenuLinkMRT = array_merge($arMenuLinkMRT, $arCatalogGroups['MRT'][$arResult['MRT']['UF_XML_ID']]);
//        }
//    }
//    $aMenuLinksNew = array_merge($arMenuLinkMRT, $aMenuLinksNew);
//END CUSTOM

    $cache->endDataCache($aMenuLinksNew);
}
return $aMenuLinksNew;
