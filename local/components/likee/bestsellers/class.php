<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Respect
 * Date: 2017-01-26
 * Time: 16:23:01
 */
class LikeeBestsellersComponent extends \Likee\Site\Component
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams['PRODUCTS_IBLOCK_ID'] = intval($arParams['PRODUCTS_IBLOCK_ID']);
        $arParams['ACTIONS_IBLOCK_ID'] = intval($arParams['ACTIONS_IBLOCK_ID']);

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0)
            $arParams['CACHE_TIME'] = 604800;


        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->arParams['PRODUCTS_IBLOCK_ID'] <= 0) {
            ShowError('Не указан инфоблок продуктов');
            return false;
        }

        if ($this->arParams['ACTIONS_IBLOCK_ID'] <= 0) {
            ShowError('Не указан инфоблок акций');
            return false;
        }

        if ($this->StartResultCache()) {
            if (!$this->loaderModules(['likee.site', 'iblock'])) {
                $this->abortResultCache();
                return false;
            }

            $this->arResult['ACTIONS'] = $this->getActions();
            $this->arResult['PRODUCTS'] = $this->getProducts();


            $this->includeComponentTemplate();
        }
    }

    public function getActions()
    {
        $rsBigActions = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $this->arParams['ACTIONS_IBLOCK_ID'],
                '!PREVIEW_PICTURE' => false,
                '!PROPERTY_SHOW_IN_BESTSELLERS' => false,
                '!PROPERTY_BIG' => false
            ],
            false,
            ['nTopCount' => 1],
            ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL']
        );

        $arActions['BIG'] = $rsBigActions->GetNext(true, false);

        $arFilterSmall = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->arParams['ACTIONS_IBLOCK_ID'],
            '!PREVIEW_PICTURE' => false,
            '!PROPERTY_SHOW_IN_BESTSELLERS' => false
        ];


        if ($arActions['BIG']) {
            $arFilterSmall['!=ID'] = $arActions['BIG'];
        }

        $rsAction = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            $arFilterSmall,
            false,
            ['nTopCount' => 1],
            ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL']
        );

        $arActions['SMALL'] = $rsAction->GetNext(true, false);

        foreach ($arActions as $sKey => &$arAction) {
            if (empty($arAction))
                continue;

            $obIpropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arAction['IBLOCK_ID'], $arAction['ID']);
            $arProduct['IPROPERTY_VALUES'] = $obIpropValues->getValues();

            Bitrix\Iblock\Component\Tools::getFieldImageData(
                $arAction,
                ['PREVIEW_PICTURE'],
                Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT
            );

            if (!empty($arAction['PREVIEW_PICTURE']) && is_array($arAction['PREVIEW_PICTURE'])) {
                if ($sKey == 'BIG')
                    $arAction['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arAction['PREVIEW_PICTURE'], 650, 650, true);
                else
                    $arAction['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arAction['PREVIEW_PICTURE'], 325, 325, true);
            }
        }
        unset($arAction);


        return $arActions;
    }

    public function getProducts()
    {
        $rsProducts = CIBlockElement::GetList(
            [
                'RAND' => 'ASC'
            ],
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $this->arParams['PRODUCTS_IBLOCK_ID'],
                '!PROPERTY_SHOW_IN_BESTSELLERS' => false,
                [
                    'LOGIC' => 'OR',
                    ['!PREVIEW_PICTURE' => false],
                    ['!DETAIL_PICTURE' => false]
                ]
            ],
            false,
            ['nTopCount' => 8],
            ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
        );


        $arProducts = [];
        while ($arProduct = $rsProducts->GetNext(true, false)) {

            if (!empty($arProduct['PREVIEW_PICTURE']) || !empty($arProduct['DETAIL_PICTURE'])) {
                $arProduct['PREVIEW_PICTURE'] = $arProduct['DETAIL_PICTURE'];

                Bitrix\Iblock\Component\Tools::getFieldImageData(
                    $arProduct,
                    ['PREVIEW_PICTURE'],
                    Bitrix\Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT
                );

                $arProduct['PREVIEW_PICTURE']['SRC'] = \Likee\Site\Helper::getResizePath($arProduct['PREVIEW_PICTURE'], 325, 325, true);
            }

            $arProducts[] = $arProduct;
        }

        return $arProducts;
    }
}