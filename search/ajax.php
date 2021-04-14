<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $CACHE_MANAGER;
global $LOCATION;

if (
    CModule::IncludeModule('search')
    && CModule::IncludeModule('iblock')
) {
    $index = [];
    $q = $_REQUEST['q'];

    $cache = new CPHPCache;
    $availableProds = [];
    $availableOffers = [];
    if ($cache->InitCache(84600, 'search_index', '/')) {
        $index = $cache->GetVars()['search_index'];
        $availableProds = $cache->GetVars()['available_prods'];
    } elseif ($cache->StartDataCache()) {
        $CACHE_MANAGER->StartTagCache('/');
        $CACHE_MANAGER->RegisterTag("catalogAll");
        // Достаем секции
        $index['sections'] = getSectionsIndex();
        // Достаем товары
        $index['items'] = getProductsIndex();
        // Достаем бренды
        $index['brands'] = getBrandsIndex();
        // Достаем остатки
        $rsStoreProduct = \Bitrix\Catalog\ProductTable::getList(
            [
                'select' => ['ID', 'QUANTITY']
            ],
        );
        while ($arStoreProduct = $rsStoreProduct->fetch()) {
            if ($arStoreProduct['QUANTITY'] > 0) {
                $availableOffers[] = $arStoreProduct['ID'];
            }
        }
        // Достаем товары по предложениям
        $res = CIBlockElement::GetList(
            [],
            [
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
                "ID" => $availableOffers,
            ],
            false,
            false,
            [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
            ]
        );
        while ($offer = $res->GetNext()) {
            $availableProds[$offer['PROPERTY_CML2_LINK_VALUE']] = $offer['PROPERTY_CML2_LINK_VALUE'];
        }
        $availableProds = array_unique($availableProds);
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

function processIndex(&$index, $q, $availableProds)
{
    foreach ($index['items'] as $id => $item) {
        if (!isset($availableProds[$id])) {
            unset($index['items'][$id]);
        }
    }
    $q = str_replace('/', '\/', $q);
    $q = str_replace('(', '\(', $q);
    $q = str_replace(')', '\)', $q);
    $q = str_replace('.', '\.', $q);
    $i = 1;
    foreach ($index['sections'] as $key => $section) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $section['index'], $matches) && ($i <= 10)) {
            $index['sections'][$key]['title1'] = $section['title'];
            $index['sections'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $section['title']);
            $index['sections'][$key]['url1'] = $index['sections'][$key]['url'];
            $index['sections'][$key]['url'] = $index['sections'][$key]['url'] . '?q=' . $q;
            if ($section['depth_level'] > 2) {
                foreach ($index['items'] as $item) {
                    if ($item['section_id'] == $section['id']) {
                        $index['sections'][$key]['items'][] = $item['id'];
                    }
                }
                if (empty($index['sections'][$key]['items'])) {
                    unset($index['sections'][$key]);
                    continue;
                }
            }
            $i++;
        } else {
            unset($index['sections'][$key]);
            continue;
        }
    }
    $i = 1;
    foreach ($index['brands'] as $key => $brand) {
        if (preg_match('/(\s|)(\S*' . $q . '\S*)(\s|)/ium', $brand['index'], $matches) && ($i <= 3)) {
            $index['brands'][$key]['title1'] = $brand['title'];
            $index['brands'][$key]['title'] = str_replace($matches[2], '<span style="color: blue">' . $matches[2] . '</span>', $brand['title']);
            $index['brands'][$key]['url1'] = $index['brands'][$key]['url'];
            $index['brands'][$key]['url'] = $index['brands'][$key]['url'] . '?q=' . $q;
            foreach ($index['items'] as $item) {
                if ($item['brand_xml_id'] == $brand['xml_id']) {
                    $index['brands'][$key]['items'][] = $item['id'];
                }
            }
            if (empty($index['brands'][$key]['items'])) {
                unset($index['brands'][$key]);
                continue;
            }
            $i++;
        } else {
            unset($index['brands'][$key]);
            continue;
        }
    }
    $i = 1;
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
            ">DEPTH_LEVEL" => 1
        ],
        false,
        ['*'],
    );
    $arSections = [];
    while ($section = $data->getNext()) {
        $arSections[$section['ID']] = $section;
    }
    // Сортируем массив секций по глубине уровня для удобства итерации
    uasort($arSections, function ($a, $b) {
        return $a['DEPTH_LEVEL'] <=> $b['DEPTH_LEVEL'];
    });
    foreach ($arSections as $section) {
        $result[$section['ID']]['index'] = ucfirst($section['NAME']);
        $result[$section['ID']]['title'] = ($section['DEPTH_LEVEL'] == 4 ? $arSections[$section['IBLOCK_SECTION_ID']]['NAME'] . ' > ' : '') . $section['NAME'];
        $result[$section['ID']]['id'] = $section['ID'];
        $result[$section['ID']]['url'] = $section['SECTION_PAGE_URL'];
        $result[$section['ID']]['depth_level'] = $section['DEPTH_LEVEL'];
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
        $products[$prod['ID']]['brand_xml_id'] = $prod['PROPERTY_VENDOR_VALUE'];
    }
    return $products;
}

function getBrandsIndex()
{
    $brands = [];
    $data = CIBlockElement::GetList(
        array(),
        [
            'IBLOCK_ID' => IBLOCK_VENDORS,
            'ACTIVE' => 'Y',
        ],
        false,
        false,
        [
            "ID",
            "NAME",
            "CODE",
            "SORT",
            "XML_ID",
            "DESCRIPTION",
            "PICTURE",
            "DETAIL_PICTURE",
            "SECTION_PAGE_URL",
        ]
    );
    while ($brand = $data->GetNext()) {
        $brands[$brand['ID']]['index'] = ucfirst($brand['NAME']);
        $brands[$brand['ID']]['title'] = $brand['NAME'];
        $brands[$brand['ID']]['url'] = '/brands/' . $brand['CODE'];
        $brands[$brand['ID']]['name'] = $brand['NAME'];
        $brands[$brand['ID']]['id'] = $brand['ID'];
        $brands[$brand['ID']]['xml_id'] = $brand['XML_ID'];
    }
    return $brands;
}
