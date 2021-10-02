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
        $cache->AbortDataCache();
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
            "CODE",
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
        $price = \Qsoft\Helpers\PriceUtils::getCachedPriceForUser($offerId);
        if ((!isset($rests[$offerId]) || $rests[$offerId] < 1 || !$price)) {
            continue;
        }
        $pid = $offer['PROPERTY_CML2_LINK_VALUE'];
        $items[$pid] = $products[$pid];
        if (!isset($items[$pid]['DISCOUNT'])) {
            $items[$pid]['DISCOUNT'] = $price['DISCOUNT'];
        }
    }

    // Заполняем массив айдишников нужных нам секций
    $bSpecSec['SALES'] = false;
    $arSectionsIds = [];
    $arAvailableSalesSectionsIds = [];
    $arAvailableSalesItems = [];
    $arAvailableSectionsIds = [];
    foreach ($items as $item) {
        $arAvailableSectionsIds[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];

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
        [
            'ID',
            'DEPTH_LEVEL',
            'NAME',
            'SECTION_PAGE_URL',
            'CODE',
        ]
    );

    $rsSections->SetUrlTemplates('', $arParams['SECTION_URL']);

    while ($arSection = $rsSections->GetNext()) {
        $arResult['SECTIONS'][] = array(
            'ID' => $arSection['ID'],
            'CODE' => $arSection['CODE'],
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

    if ($bSpecSec['SALES']) {
        $arResult['SALES'] = [
            'UF_NAME' => 'Скидки до -40%',
            'UF_CODE' => 'sales',
            'PROPS' => [
                'TEXT_COLOR' => 'white',
                'IS_SPECIAL' => 'Y'
            ]
        ];
    }

    if ($arParams['ID'] > 0) {
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

        $filePath = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.svg';
        $imgPath = SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.svg';
        $filePathWebp = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.webp';
        $imgPathWebp = SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSection['ID'] . '.webp';
        $aMenuLinksNew[$menuIndex++] = array(
            htmlspecialcharsbx($arSection['~NAME']),
            $arSection['~SECTION_PAGE_URL'],
            $arResult['ELEMENT_LINKS'][$arSection['ID']],
            array(
                'FROM_IBLOCK' => true,
                'IS_PARENT' => false,
                'DEPTH_LEVEL' => $arSection['DEPTH_LEVEL'],
                'IMG_PATH' => is_file($filePath) ? $imgPath : SITE_TEMPLATE_PATH . '/img/svg/catalogs/default.svg',
                'IMG_PATH_WEBP' => is_file($filePathWebp) ? $imgPathWebp : SITE_TEMPLATE_PATH . '/img/svg/catalogs/776.webp',
                'ID' => $arSection['ID'],
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

            $arSectionsToAdd = [];
            while ($arSection = $nav->Fetch()) {
                if ($arSection['ID'] == 642) {
                    continue 2;
                }
                if ($arSection['DEPTH_LEVEL'] == 4) {
                    continue;
                }
                $arSectionsToAdd[$iDepth][$arSection['ID']] = $arSection;
                $iDepth++;
            }

            foreach ($arSectionsToAdd as $depth => $sections) {
                foreach ($sections as $section) {
                    $arSalesSections[$depth][$section['ID']] = $section;
                }
            }
        }

        krsort($arSalesSections[2]);
        // Фильтруем секции по массиву $arSectionsIds
        $arAvailable3rdLvlSalesSectionsIds = [];

        foreach ($arSalesSections[3] as $key => $section) {
            $arAvailable3rdLvlSalesSectionsIds[$section['ID']] = $section['ID'];
        }

        foreach ($arSalesSections[2] as $arSalesSection2) {
            foreach ($arSalesSections[3] as $key => $arSalesSection3) {
                if (!in_array($key, $arAvailable3rdLvlSalesSectionsIds)) {
                    unset($arSalesSections[3][$key]);
                    continue;
                }
                if ($arSalesSection3['IBLOCK_SECTION_ID'] == $arSalesSection2['ID']) {
                    $imgPath = SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSalesSection3['ID'] . '.svg';
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSalesSection3['ID'] . '.svg';
                    $filePathWebp = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSalesSection3['ID'] . '.webp';
                    $imgPathWebp = SITE_TEMPLATE_PATH . '/img/svg/catalogs/' . $arSalesSection3['ID'] . '.webp';
                    $catalogPathCode = '/' . reset($arSalesSections[1])['CODE'] . '/' . $arSalesSection2['CODE'] . '/' . $arSalesSection3['CODE'] . '/';
                    $sPath = '/catalog/' . $arResult['SALES']['UF_CODE'] . $catalogPathCode;
                    $arMenuLinkSales[] = array(
                        $arSalesSection3['NAME'],
                        $sPath,
                        array($sPath),
                        array(
                            'IMG_PATH' => is_file($filePath) ? $imgPath :  SITE_TEMPLATE_PATH . '/img/svg/catalogs/default.svg',
                            'IMG_PATH_WEBP' => is_file($filePathWebp) ? $imgPathWebp : SITE_TEMPLATE_PATH . '/img/svg/catalogs/776.webp',
                            'IS_PARENT' => false,
                            'DEPTH_LEVEL' => 2,
                            'FROM_IBLOCK' => true,
                            'ID' => $arSalesSection3['ID'],
                        ),
                        ''
                    );
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

//END CUSTOM
    $cache->EndDataCache($aMenuLinksNew);
    $CACHE_MANAGER->EndTagCache();
}
return $aMenuLinksNew;
