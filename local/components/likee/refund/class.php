<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Dveplanety_ru_master
 * Date: 2017-02-21
 * Time: 13:00:54
 */

use Bitrix\Main\Loader;

class LikeeRefundComponent extends \CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 604800;
        }

        return $arParams;
    }

    public function executeComponent()
    {
        if (empty($this->arParams['IBLOCK_ID'])) {
            ShowError('Не передан обязательный параметр IBLOCK_ID');
            return;
        }

        if ($this->startResultCache()) {
            if (!Loader::includeModule('iblock')) {
                $this->abortResultCache();
                ShowError('Для работы необходим модуль iblock');
                return;
            }

            $this->arResult = CIBlock::GetByID($this->arParams['IBLOCK_ID'])->Fetch();

            if (!$this->arResult) {
                $this->abortResultCache();
                ShowError('Инфоблок не найден');
                return;
            }

            $this->arResult['ITEMS'] = $this->getItems();

            $this->includeComponentTemplate();
        }
    }

    /**
     * Получение элементов инфоблока
     * @return array
     */
    public function getItems()
    {
        $rsItems = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                '!PREVIEW_TEXT' => false,
                'ACTIVE' => 'Y'
            ],
            false,
            false,
            [
                'ID',
                'NAME',
                'PREVIEW_TEXT',
                'PROPERTY_OPEN'
            ]
        );

        $arItems = [];

        while ($arItem = $rsItems->Fetch()) {
            $this->addControls($arItem);
            $arItems[] = $arItem;
        }

        return $arItems;
    }

    /**
     * Метод возвращает разделы по переданным ID
     * @param $arSectionsId
     * @return array|bool
     */
    public function getSectionsById($arSectionsId)
    {
        $arSections = [];

        if (!empty($arSectionsId)) {
            if (!is_array($arSectionsId)) $arSectionsId = [$arSectionsId];

            $rsSections = \Bitrix\Iblock\SectionTable::getList([
                'filter' => [
                    'ID' => $arSectionsId
                ]
            ]);

            while ($arSection = $rsSections->fetch()) {
                $arSections[$arSection['ID']] = $arSection;
            }
        }

        return $arSections;
    }

    /**
     * Добавление элементов управления
     * @param $arItem
     * @return bool
     */
    public function addControls(&$arItem)
    {
        if (!$arItem['IBLOCK_ID'] || !$arItem['ID']) return false;

        $arButtons = CIBlock::GetPanelButtons(
            $arItem['IBLOCK_ID'],
            $arItem['ID'],
            0,
            array('SECTION_BUTTONS' => false, 'SESSID' => false)
        );

        $arItem['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];
        $arItem['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];
        $arItem['CONTROL_ID'] = $this->getEditAreaId($arItem['ID']);
    }

}