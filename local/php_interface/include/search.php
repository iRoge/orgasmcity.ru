<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('search', 'BeforeIndex', 'BeforeIndexHandler');
//Добавление артикула в title, проверка, что бы в поиск не попали товары без картинок
function BeforeIndexHandler($arFields)
{
    if (!\Bitrix\Main\Loader::includeModule('iblock')) {
        return $arFields;
    }
    if ($arFields['MODULE_ID'] == 'iblock'
        && $arFields['PARAM2'] == IBLOCK_CATALOG
        && substr($arFields['ITEM_ID'], 0, 1) != 'S'
    ) {
        $dbItem = CIBlockElement::GetByID($arFields['ITEM_ID']);

        if ($arItem = $dbItem->fetch()) {
            if (empty($arItem['DETAIL_PICTURE'])&&($arItem['ACTIVE']!='Y')) {
                $arFields["BODY"] = '';
                $arFields["TITLE"] = '';

                return $arFields;
            }
        }

        $db_props = CIBlockElement::GetProperty(
            $arFields['PARAM2'],
            $arFields['ITEM_ID'],
            array('sort' => 'asc'),
            array('CODE' => 'ARTICLE')
        );

        if ($ar_props = $db_props->Fetch()) {
            $arFields['TITLE'] .= ' ' . $ar_props['VALUE'];
            $arFields['BODY'] = $ar_props['VALUE'];
        }
    }

    return $arFields;
}
