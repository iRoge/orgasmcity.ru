<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

global $CACHE_MANAGER;
global $LOCATION;

if (
    CModule::IncludeModule('search')
    && CModule::IncludeModule('iblock')
    && CModule::IncludeModule('highloadblock')
) {
    $index = [];
    $q = $_REQUEST['q'];

    $cache = new CPHPCache;
    $availableProds = [];
    if ($cache->InitCache(84600, 'search_index223', '/')) {
        $index = $cache->GetVars()['search_index'];
        $availableProds = $cache->GetVars()['available_prods'];
    } elseif ($cache->StartDataCache()) {
        $CACHE_MANAGER->StartTagCache('/');
        $CACHE_MANAGER->RegisterTag("catalogAll");
        // Достаем секции
        $index['sections'] = getSectionsIndex();
//        // Достаем свойства
//        $index['properties'] = getPropertiesIndex();
        // Достаем товары
        $index['items'] = getProductsIndex();
        // Достаем остатки
        $rsStoreProduct = \Bitrix\Catalog\ProductTable::getList(
            [
                'select' => ['ID', 'QUANTITY']
            ],
        );
        while ($arStoreProduct = $rsStoreProduct->fetch()) {
            if ($arStoreProduct['QUANTITY'] > 0) {
                $availableProds[] = $arStoreProduct['ID'];
            }
        }

        if (!empty($index)) {
            $CACHE_MANAGER->EndTagCache();
            $cache->EndDataCache(array (
                'search_index' => $index,
                'available_prods' => $availableProds,
            ));
        } else {
            $CACHE_MANAGER->AbortTagCache();
            $cache->AbortDataCache();
        }
    }
    if ($q != '') {
        processIndex($index, $q, $availableProds);
    } else {
        $index = [];
    }
    echo json_encode($index);
}

function processIndex(&$index, $q, $available_prods)
{
    foreach ($index['items'] as $id => $item) {
        if (!in_array($id, $available_prods)) {
            unset($index['items'][$id]);
        }
    }
    $q = str_replace('/', '\/', $q);
    $q = str_replace('(', '\(', $q);
    $q = str_replace(')', '\)', $q);
    $q = str_replace('.', '\.', $q);
    $i = 1;
    pre($q);
    pre($available_prods);
//    pre($index['items']);
    foreach ($index['sections'] as $key => $section) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $section['index'], $matches) && ($i <= 20)) {
            $index['sections'][$key]['title1'] = $section['title'];
            $index['sections'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $section['title']);
            $index['sections'][$key]['url1'] = $index['sections'][$key]['url'];
            $index['sections'][$key]['url'] = $index['sections'][$key]['url'] . '?q=' . $q;
            $i++;
        } else {
            unset($index['sections'][$key]);
            continue;
        }
//        foreach ($index['items'] as $item) {
//            if ($item['section_id'] == $section['id']) {
//                $index['sections'][$key]['items'][] = $item['id'];
//            }
//        }
//        if (empty($index['sections'][$key]['items'])) {
//            unset($index['sections'][$key]);
//        }
    }
    pre($index['items']);
//    foreach ($index['properties'] as $key => $item) {
//        foreach ($item as $id => $value) {
//            if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $value['index'], $matches) && ($i <= 20)) {
//                if ($no_changes != 'Y') {
//                    $index['properties'][$key][$id]['title1'] = $value['title'];
//                    $index['properties'][$key][$id]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $value['title']);
//                }
//                $index['properties'][$key][$id]['url1'] = $index['properties'][$key][$id]['url'];
//                $index['properties'][$key][$id]['url'] = $index['properties'][$key][$id]['url'] . '&q=' . $q;
//                $i++;
//            } else {
//                unset($index['properties'][$key][$id]);
//                continue;
//            }
//            foreach ($index['items'] as $elem) {
//                if ($elem[$key] == $value['xml_id']) {
//                    $index['properties'][$key][$id]['items'][] = $item['id'];
//                }
//            }
//            if (empty($index['properties'][$key][$id]['items'])) {
//                unset($index['properties'][$key][$id]);
//            }
//        }
//        if (empty($index['properties'][$key])) {
//            unset($index['properties'][$key]);
//        }
//    }
//    $i = 1;
    foreach ($index['items'] as $key => $item) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $item['title'], $matches) && ($i <= 5)) {
            $index['items'][$key]['title1'] = $item['title'];
            $index['items'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $item['title']);
            $index['items'][$key]['url1'] = $index['items'][$key]['url'];
            $index['items'][$key]['url'] = $index['items'][$key]['url'] . '?q=' . $q;
            $i++;
        } else {
            unset($index['items'][$key]);
        }
    }
}

function getSectionsIndex()
{
    $result = [];
    $mainSection = CIBlockSection::GetByID(MAIN_SECTION_ID)->GetNext();
    $data = CIBlockSection::GetList(
        false,
        [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => IBLOCK_CATALOG,
            ">LEFT_MARGIN" => $mainSection["LEFT_MARGIN"],
            "<RIGHT_MARGIN" => $mainSection["RIGHT_MARGIN"],
            ">DEPTH_LEVEL" => 2
        ],
        false,
        ['*'],
    );
    $arSections = [];
    while ($section = $data->getNext()) {
        $arSections[$section['ID']] = $section;
    }
    // Сортируем массив секций по глубине уровня для удобства итерации
    usort($arSections, function ($a, $b) {
        return $a['DEPTH_LEVEL'] <=> $b['DEPTH_LEVEL'];
    });
    foreach ($arSections as $section) {
        $result[$section['ID']]['index'] = ucfirst($section['NAME']);
        $result[$section['ID']]['title'] = $section['NAME'];
        $result[$section['ID']]['id'] = $section['ID'];
        $result[$section['ID']]['url'] = $section['SECTION_PAGE_URL'];
    }
    usort($result, function ($a, $b) {
        if ($a['title'] > $b['title']) {
            return 1;
        } elseif ($a['title'] < $b['title']) {
            return -1;
        }

        return 0;
    });
    return $result;
}

function getProductsIndex()
{
    $products = [];
    $data = CIBlockElement::GetList(
        array(),
        [
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'ACTIVE' => 'Y',
        ],
        false,
        false,
        [
            "ID",
            "IBLOCK_ID",
            "IBLOCK_SECTION_ID",
            "XML_ID",
            "NAME",
            "CODE",
            "DETAIL_PICTURE",
            "PREVIEW_PICTURE",
            "SORT",
            "PROPERTY_ARTICLE",
            "PROPERTY_DIAMETER",
            "PROPERTY_LENGTH",
            "PROPERTY_BESTSELLER",
            "PROPERTY_VENDOR",
            "PROPERTY_VOLUME",
            "PROPERTY_MATERIAL",
            "PROPERTY_COLLECTION",
            "SHOW_COUNTER",
        ]
    );
    while ($prod = $data->GetNext()) {
        if (!$prod['DETAIL_PICTURE']) {
            continue;
        }
        $products[$prod['ID']]['title'] = $prod['NAME'] . ' ' . $prod['PROPERTY_ARTICLE_VALUE'];
        $products[$prod['ID']]['url'] = $prod['DETAIL_PAGE_URL'];
        $products[$prod['ID']]['name'] = $prod['NAME'];
        $products[$prod['ID']]['id'] = $prod['ID'];
        $products[$prod['ID']]['section_id'] = $prod['IBLOCK_SECTION_ID'];
//        $products[$prod['ID']]['Brand'] = $prod['PROPERTY_BRAND_VALUE'];
//        $products[$prod['ID']]['Uppermaterial'] = $prod['PROPERTY_UPPERMATERIAL_VALUE'];
//        $products[$prod['ID']]['Liningmaterial'] = $prod['PROPERTY_LININGMATERIAL_VALUE'];
//        $products[$prod['ID']]['Season'] = $prod['PROPERTY_SEASON_VALUE'];
//        $products[$prod['ID']]['Country'] = $prod['PROPERTY_COUNTRY_VALUE'];
//        $products[$prod['ID']]['Colorsfilter'] = $prod['PROPERTY_COLORSFILTER_VALUE'][0];
    }
    return $products;
}

function getPropertiesIndex()
{
    $arHLFilters = [
        'Brand' => 'Бренд: ',
        'Uppermaterial' => 'Материал верха: ',
        'Liningmaterial' => 'Материал подкладки: ',
        'Season' => 'Сезон: ',
        'Country' => 'Страна происхождения: ',
        'Colorsfilter' => 'Цвет: ',
//        'Typeproduct => '',
//        'Subtypeproduct' => '',
    ];
    $result = [];
    foreach ($arHLFilters as $HLBName => $lang) {
        $result[$HLBName] = getHLBLockItems($HLBName, $lang);
    }
    uasort($result, function ($a, $b) {
        return $a['title'] <=> $b['title'];
    });
    return $result;
}

function getHLBLockItems($HLBName, $lang = null)
{
    $HLBName_filterName = [
        'Brand' => 'brand',
        'Uppermaterial' => 'uppermaterial',
        'Liningmaterial' => 'liningmaterial',
        'Season' => 'season',
        'Country' => 'country',
        'Colorsfilter' => 'color',
    ];
    $result = [];
    $hlblock = HLBT::getList(array('filter' => array('=NAME' => $HLBName)))->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $rsData = $entity_data_class::getList(array(
        'filter' => array(),
        'select' => array('*'),
    ));
    while ($arData = $rsData->fetch()) {
        $result[$arData['UF_XML_ID']]['index'] = $arData['UF_NAME'];
        $result[$arData['UF_XML_ID']]['title'] = $lang . $arData['UF_NAME'];
        $result[$arData['UF_XML_ID']]['xml_id'] = $arData['UF_XML_ID'];
        $result[$arData['UF_XML_ID']]['url'] = '/catalog/?set_filter=Y&' . $HLBName_filterName[$HLBName] . '=' . $arData['UF_XML_ID'];
    }
    return $result;
}
