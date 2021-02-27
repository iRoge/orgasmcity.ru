<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

global $CACHE_MANAGER;
global $LOCATION;

if ((CModule::IncludeModule('search'))&&(CModule::IncludeModule('iblock'))&&(CModule::IncludeModule('highloadblock'))) {
    $index = [];
    $q = $_REQUEST['q'];
    $no_changes = $_REQUEST['no_changes'];

    $cache = new CPHPCache;
    $article_rest = [];
    if ($cache->InitCache(84600, 'search_index1|' . $LOCATION->getName(), '/')) {
        $index = $cache->GetVars()['search_index'];
        $available_prods = $cache->GetVars()['available_prods'];
    } elseif ($cache->StartDataCache()) {
        $CACHE_MANAGER->StartTagCache('/');
        $CACHE_MANAGER->RegisterTag("catalogAll");
        // Достаем секции
        $index['sections'] = getSectionsIndex();
        // Достаем свойства
        $index['properties'] = getPropertiesIndex();
        // Достаем товары
        $index['items'] = getProductsIndex();
        // Достаем остатки
        $available_prods = $LOCATION->getAvailableProductsByIds(null);
        if (!empty($index)) {
            $CACHE_MANAGER->EndTagCache();
            $cache->EndDataCache(array (
                'search_index' => $index,
                'available_prods' => $available_prods,
            ));
        } else {
            $CACHE_MANAGER->AbortTagCache();
            $cache->AbortDataCache();
        }
    }
    if ($q != '') {
        processIndex($index, $q, $available_prods, $no_changes);
    } else {
        $index = [];
    }
    echo json_encode($index);
}

function processIndex(&$index, $q, $available_prods, $no_changes = 'N')
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
    foreach ($index['sections'] as $key => $section) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $section['index'], $matches) && ($i <= 20)) {
            if ($no_changes != 'Y') {
                $index['sections'][$key]['title1'] = $section['title'];
                $index['sections'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $section['title']);
            }
            $index['sections'][$key]['url1'] = $index['sections'][$key]['url'];
            $index['sections'][$key]['url'] = $index['sections'][$key]['url'] . '?q=' . $q;
            $i++;
        } else {
            unset($index['sections'][$key]);
            continue;
        }
        foreach ($index['items'] as $item) {
            if ($item['section_id'] == $section['id']) {
                $index['sections'][$key]['items'][] = $item['id'];
            }
        }
        if (empty($index['sections'][$key]['items'])) {
            unset($index['sections'][$key]);
        }
    }
    foreach ($index['properties'] as $key => $item) {
        foreach ($item as $id => $value) {
            if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $value['index'], $matches) && ($i <= 20)) {
                if ($no_changes != 'Y') {
                    $index['properties'][$key][$id]['title1'] = $value['title'];
                    $index['properties'][$key][$id]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $value['title']);
                }
                $index['properties'][$key][$id]['url1'] = $index['properties'][$key][$id]['url'];
                $index['properties'][$key][$id]['url'] = $index['properties'][$key][$id]['url'] . '&q=' . $q;
                $i++;
            } else {
                unset($index['properties'][$key][$id]);
                continue;
            }
            foreach ($index['items'] as $elem) {
                if ($elem[$key] == $value['xml_id']) {
                    $index['properties'][$key][$id]['items'][] = $item['id'];
                }
            }
            if (empty($index['properties'][$key][$id]['items'])) {
                unset($index['properties'][$key][$id]);
            }
        }
        if (empty($index['properties'][$key])) {
            unset($index['properties'][$key]);
        }
    }
    $i = 1;
    foreach ($index['items'] as $key => $item) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $item['title'], $matches) && ($i <= 5)) {
            if ($no_changes != 'Y') {
                $index['items'][$key]['title1'] = $item['title'];
                $index['items'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $item['title']);
            }
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
    $data = CIBlockSection::GetList(
        false,
        array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => IBLOCK_CATALOG,
        ),
        false,
        array('*'),
        false
    );
    $arSections = [];
    while ($section = $data->getNext()) {
        $arSections[$section['ID']] = $section;
    }
    // Сортируем массив секций по глубине уровня для удобства итерации
    usort($arSections, function ($a, $b) {
        return $a['DEPTH_LEVEL'] <=> $b['DEPTH_LEVEL'];
    });
    $womensDepth2SectionsIds = [];
    $mensDepth2SectionsIds = [];
    foreach ($arSections as $section) {
        if ($section['DEPTH_LEVEL'] == '1') {
            if ($section['CODE'] == 'dlya_zhenshchin') {
                $wSectionId = $section['ID'];
            } elseif ($section['CODE'] == 'dlya_muzhchin') {
                $mSectionId = $section['ID'];
            }
        }
        if ($section['DEPTH_LEVEL'] == '2') {
            if ($section['IBLOCK_SECTION_ID'] == $wSectionId) {
                $womensDepth2SectionsIds[] = $section['ID'];
            } elseif ($section['IBLOCK_SECTION_ID'] == $mSectionId) {
                $mensDepth2SectionsIds[] = $section['ID'];
            }
        }
        if ($section['DEPTH_LEVEL'] == '3') {
            if (in_array($section['IBLOCK_SECTION_ID'], $womensDepth2SectionsIds)) {
                $result[$section['ID']]['index'] = ucfirst($section['NAME']);
                $result[$section['ID']]['title'] = ucfirst($section['NAME']) . ' для женщин';
                $result[$section['ID']]['section_name'] = $section['NAME'];
                $result[$section['ID']]['type'] = 'W';
                $result[$section['ID']]['id'] = $section['ID'];
                $result[$section['ID']]['url'] = $section['SECTION_PAGE_URL'];
            } elseif (in_array($section['IBLOCK_SECTION_ID'], $mensDepth2SectionsIds)) {
                $result[$section['ID']]['index'] = ucfirst($section['NAME']);
                $result[$section['ID']]['title'] = ucfirst($section['NAME']) . ' для мужчин';
                $result[$section['ID']]['section_name'] = $section['NAME'];
                $result[$section['ID']]['type'] = 'M';
                $result[$section['ID']]['id'] = $section['ID'];
                $result[$section['ID']]['url'] = $section['SECTION_PAGE_URL'];
            }
        }
    }
    usort($result, function ($a, $b) {
        if ($a['type'] == $b['type']) {
            return $a['title'] <=> $b['title'];
        } elseif ($a['section_name'] > $b['section_name']) {
            return 1;
        } elseif ($a['section_name'] < $b['section_name']) {
            return -1;
        } elseif ($a['section_name'] == $b['section_name']) {
            return $a['type'] == 'M' ? 1 : -1;
        }
        return -1;
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
            'ID',
            'DETAIL_PAGE_URL',
            'PREVIEW_PICTURE',
            'DETAIL_PICTURE',
            'NAME',
            'PROPERTY_ARTICLE',
            'IBLOCK_SECTION_ID',
            'PROPERTY_BRAND',
            'PROPERTY_UPPERMATERIAL',
            'PROPERTY_LININGMATERIAL',
            'PROPERTY_SEASON',
            'PROPERTY_COUNTRY',
            'PROPERTY_COLORSFILTER',
        ]
    );
    while ($prod = $data->GetNext()) {
        if (!$prod['PREVIEW_PICTURE'] && !$prod['DETAIL_PICTURE']) {
            continue;
        }
        $products[$prod['ID']]['title'] = $prod['NAME'] . ' ' . $prod['PROPERTY_ARTICLE_VALUE'];
        $products[$prod['ID']]['url'] = $prod['DETAIL_PAGE_URL'];
        $products[$prod['ID']]['name'] = $prod['NAME'];
        $products[$prod['ID']]['id'] = $prod['ID'];
        $products[$prod['ID']]['section_id'] = $prod['IBLOCK_SECTION_ID'];
        $products[$prod['ID']]['Brand'] = $prod['PROPERTY_BRAND_VALUE'];
        $products[$prod['ID']]['Uppermaterial'] = $prod['PROPERTY_UPPERMATERIAL_VALUE'];
        $products[$prod['ID']]['Liningmaterial'] = $prod['PROPERTY_LININGMATERIAL_VALUE'];
        $products[$prod['ID']]['Season'] = $prod['PROPERTY_SEASON_VALUE'];
        $products[$prod['ID']]['Country'] = $prod['PROPERTY_COUNTRY_VALUE'];
        $products[$prod['ID']]['Colorsfilter'] = $prod['PROPERTY_COLORSFILTER_VALUE'][0];
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
