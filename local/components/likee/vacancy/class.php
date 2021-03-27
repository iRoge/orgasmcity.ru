<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Dveplanety_ru_master
 * Date: 2017-02-20
 * Time: 16:55:43
 */
use Bitrix\Main\Loader;
use \Likee\Location\Location;

class LikeeVacancyComponent extends \Likee\Site\Component
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0)
            $arParams['CACHE_TIME'] = 604800;

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        if (!$this->loaderModules(['iblock', 'likee.site', 'likee.location'])) {
            return;
        }

        if ($this->arParams['IBLOCK_ID'] <= 0) {
            ShowError('Не передан обязательный параметр IBLOCK_ID');
            return;
        }

        if (!empty($GLOBALS['CITY_FILTER'])) {
            $this->arParams['LOCATION'] = $GLOBALS['CITY_FILTER'];
        } else {
            $this->arParams['LOCATION'] = Location::getCurrent();
        }

        if ($this->startResultCache()) {
            $this->arResult = CIBlock::GetByID($this->arParams['IBLOCK_ID'])->Fetch();

            if (!$this->arResult) {
                ShowError('Инфоблок не найден');
                return;
            }

            $this->arResult['COUNT'] = 0;

            if ($this->arParams['LOCATION']) {
                $this->arResult['SECTION'] = $this->getSectionByName($this->arParams['LOCATION']['CITY_NAME']);
                $this->arResult['ITEMS'] = $this->getItems($this->arResult['SECTION']['ID']);
                $this->arResult['COUNT'] = count($this->arResult['ITEMS']);
            }

            $this->includeComponentTemplate();
        }

        $sTitle = $this->arResult['COUNT'] . ' ' . \Likee\Site\Helper::strMorph($this->arResult['COUNT'], 'вакансия', 'вакансии', 'вакансий') . ' в г. ';

        $APPLICATION->SetPageProperty('VACANCY_CITY', $sTitle);
    }

    /**
     * Получение элементов инфоблока
     * @param int $iSectionId
     * @return array
     */
    public function getItems($iSectionId = 0)
    {
        $rsItems = \CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'SECTION_ID' => intval($iSectionId) > 0 ? intval($iSectionId) : -1
            ]
        );

        $arItems = [];

        while ($obItem = $rsItems->GetNextElement(true, false)) {
            $arItem = $obItem->GetFields();
            $arItem['PROPS'] = $obItem->GetProperties();

            $this->addControls($arItem);

            foreach ($arItem['PROPS'] as &$arProp) {
                if ($arProp['VALUE']['TYPE'] == 'HTML') {
                    $arProp['VALUE']['~TEXT'] = html_entity_decode($arProp['VALUE']['TEXT']);
                }
            }

            $arItems[] = [
                'IBLOCK_ID' => $arItem['IBLOCK_ID'],
                'ID' => $arItem['ID'],
                'NAME' => $arItem['NAME'],
                'SALARY_FROM' => $arItem['PROPS']['SALARY_FROM'],
                'SALARY_TO' => $arItem['PROPS']['SALARY_TO'],
                'DUTIES' => $arItem['PROPS']['DUTIES'],
                'REQUIREMENTS' => $arItem['PROPS']['REQUIREMENTS'],
                'CONDITIONS' => $arItem['PROPS']['CONDITIONS'],
                'TYPE' => $arItem['PROPS']['TYPE'],
                'EDIT_LINK' => $arItem['EDIT_LINK'],
                'DELETE_LINK' => $arItem['DELETE_LINK'],
                'CONTROL_ID' => $arItem['CONTROL_ID'],
            ];
        }

        return $arItems;
    }

    /**
     * Получение id раздела по названию
     * @param $sName
     * @return array
     */
    public function getSectionByName($sName)
    {
        $arSection = [];

        if (!empty($sName)) {
            $arSection = \Bitrix\Iblock\SectionTable::getRow([
                'filter' => [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'NAME' => $sName
                ],
                'select' => ['ID', 'NAME']
            ]);
        }

        return $arSection;
    }
}