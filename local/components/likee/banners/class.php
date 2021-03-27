<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Site
 * Date: 2017-01-30
 * Time: 17:40:57
 */
class LikeeBannersComponent extends \Likee\Site\Component
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
        if ($this->arParams['IBLOCK_ID'] <= 0) {
            ShowError('Не указан инфоблок');
            return false;
        }


        if ($this->StartResultCache()) {
            if (!$this->loaderModules(['iblock', 'likee.site'])) {
                $this->abortResultCache();
                return false;
            }

            $this->arResult = \CIBlock::GetByID($this->arParams['IBLOCK_ID'])->Fetch();

            if (!$this->arResult) {
                ShowError('Не найден инфоблок');
                $this->abortResultCache();
                return false;
            }

            $this->arResult['BANNERS_LEFT'] = $this->getBanners('left', 2);
            $this->arResult['BANNERS_SLIDER'] = $this->getBanners('center', 2);
            $this->arResult['BANNERS_RIGHT'] = $this->getBanners('right', 2);

            $this->includeComponentTemplate();
        }
    }

    public function getBanners($position, $count)
    {
        $positionList = $this->getPositions();

        if (!isset($positionList[$position])) {
            return [];
        } 

        $rsBanners = \CIBlockElement::GetList(
            [
                'SORT' => 'ASC'
            ],
            [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'DATE_ACTIVE' => 'Y',
                'PROPERTY_POSITION' => $positionList[$position]
            ],
            false,
            ['nTopCount' => $count]
        );

        $arBanners = [];
        while ($obBanner = $rsBanners->GetNextElement(true, false)) {
            $arBanner = $obBanner->GetFields();
            $arBanner['PROPS'] = $obBanner->GetProperties();

            $this->addControls($arBanner);

            \Bitrix\Iblock\Component\Tools::getFieldImageData(
                $arBanner,
                ['PREVIEW_PICTURE'],
                \Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT
            );

            $arBanners[] = $arBanner;
        }

        return $arBanners;
    }

    public function getPositions()
    {
        if (!empty($this->positions)) {
            return $this->positions;
        }

        $enumList = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID"=>$this->arParams['IBLOCK_ID'], "CODE"=>"POSITION"]);
        while ($enumFields = $enumList->GetNext()) {
            $this->positions[$enumFields['XML_ID']] = $enumFields['ID'];
        }

        return  $this->positions;
    }
}