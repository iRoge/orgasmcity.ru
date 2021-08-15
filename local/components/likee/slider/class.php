<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Project: Dveplanety_ru_master
 * Date: 2016-11-28
 * Time: 11:08:49
 */
use Bitrix\Main;

class LikeeSliderComponent extends \CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 604800;
        }

        if (!$arParams['COUNT']) {
            $arParams['COUNT'] = 20;
        }

        $arParams['AUTOPLAY_CODE'] = trim($arParams['AUTOPLAY_CODE']);

        return $arParams;
    }

    public function executeComponent()
    {
        if (empty($this->arParams['IBLOCK_ID'])) {
            ShowError('Не передан обязательный параметр IBLOCK_ID');
            return;
        }

        if ($this->startResultCache()) {
            if (!Main\Loader::includeModule('iblock')) {
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
        global $LOCATION;
        
        $arFilter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'N',
            'ACTIVE_DATE' => 'Y',
            '!PREVIEW_PICTURE' => false,
            [
                'LOGIC' => 'OR',
                ['PROPERTY_LOCATION' => false],
                ['PROPERTY_LOCATION' => ''],
                ['=PROPERTY_LOCATION' => $LOCATION->getName()],
                ['=PROPERTY_LOCATION' => $LOCATION->getRegion()],
                ['=PROPERTY_LOCATION' => $LOCATION->getCountry()],
            ]
        ];

        $hasDomainLogic = (bool) \CIBlockProperty::GetList([], [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'CODE' => 'RESPECTDOMAINS'
        ])->SelectedRowsCount();

        if ($hasDomainLogic) {
            $arFilter[] = [
                'LOGIC' => 'OR',
                ['PROPERTY_RESPECTDOMAINS' => false],
                ['PROPERTY_RESPECTDOMAINS' => [SITE_ID], 'PROPERTY_RESPECTDOMAINS_HIDDEN' => false],
                ['!PROPERTY_RESPECTDOMAINS' => [SITE_ID], '!PROPERTY_RESPECTDOMAINS_HIDDEN' => false],
            ];
        }

        $rsItems = CIBlockElement::GetList(
            [
                'SORT' => 'ASC',
            ],
            $arFilter,
            false,
            [
                'nTopCount' => $this->arParams['COUNT']
            ]
        );

        $arItems = [];
        
        while (count($arItems) < $this->arParams['COUNT'] && $obItem = $rsItems->GetNextElement(true, false)) {
            $arItem = $obItem->GetFields();
            $arItem['PROPS'] = $obItem->GetProperties();
            $arItem['LINK'] = $arItem['PROPS']['LINK']['VALUE'];



            \Bitrix\Iblock\Component\Tools::getFieldImageData(
                $arItem,
                ['PREVIEW_PICTURE', 'DETAIL_PICTURE'],
                \Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT
            );

            foreach ($arItem['PROPS']['LINKS']['VALUE'] as $num => $coordinates) {
                $arItem['BANNER']['MULTIPLY_LINKS'][] = [
                    'LINK' => $arItem['PROPS']['LINKS']['DESCRIPTION'][$num],
                    'STYLE' => $this->getBannerLinkStyle($coordinates, $arItem['PREVIEW_PICTURE']['WIDTH'], $arItem['PREVIEW_PICTURE']['HEIGHT']),
                ];
            }

            $arItems[$arItem['ID']] = $arItem;
            $this->addControls($arItem);
        }
        return $arItems;
    }

    /**
     * Добавление элементов управления
     * @param $arItem
     * @return bool
     */
    public function addControls(&$arItem)
    {
        if (!$arItem['IBLOCK_ID'] || !$arItem['ID']) {
            return false;
        }

        $arButtons = CIBlock::GetPanelButtons(
            $arItem['IBLOCK_ID'],
            $arItem['ID'],
            0,
            array('SECTION_BUTTONS' => false, 'SESSID' => false)
        );

        $arItem['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];
        $arItem['DELETE_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];
        $arItem['CONTROL_ID'] = $this->getEditAreaId($arItem['ID']);
    }


    private function getBannerLinkStyle($val, $w, $h)
    {
        $val = explode(",", trim($val));
        return "left:" . (intval(100 * $val[0] / $w)) . "%;" .
            "top:" . (intval(100 * $val[1] / $h)) . "%;" .
            "right:" . (100 - intval(100 * $val[2] / $w)) . "%;" .
            "bottom:" . (100 - intval(100 * $val[3] / $h)) . "%";
    }
}
