<? use Likee\Site\Favorites;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//этот компонент обсдлуживает только ajax запросы
class LikeeItemsAjaxComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        \Bitrix\Main\Loader::includeModule('likee.site');

        if ($this->arParams['ELEMENT_ID'] > 0) {
            if (Favorites::getInstance()->isInFavorites($this->arParams['ELEMENT_ID'])) {
                Favorites::getInstance()->remove($this->arParams['ELEMENT_ID']);
            } else {
                Favorites::getInstance()->add($this->arParams['ELEMENT_ID']);
            }
        }

        $arFavorites = Favorites::getInstance()->getIdArray();
        $arFavorites = $this->checkAvailability($arFavorites);

        $arBasketItems = [];
		$arBasketOffers=[];

        $iFUser = intval(CSaleBasket::GetBasketUserID(true));

        if ($iFUser > 0) {
            $rsBasket = \Bitrix\Sale\Internals\BasketTable::getList([
                'filter' => [
                    'FUSER_ID' => $iFUser,
                    'LID' => SITE_ID,
                    'ORDER_ID' => 'NULL',
                    'CAN_BUY' => 'Y'
                ]
            ]);

            while ($arBasket = $rsBasket->fetch()) {
                $arBasketItems[] = \CCatalogSku::GetProductInfo($arBasket['PRODUCT_ID'])['ID'];
				$arBasketOffers[] = (int) $arBasket['PRODUCT_ID'];
            }
        }


        header('Content-type: application-json');
        $APPLICATION->RestartBuffer();
        $arResult = [
            'STATUS' => 'OK',
            'FAVORITES' => $arFavorites,
            'BASKET' => $arBasketItems,
			'BASKET_OFFERS' => $arBasketOffers,
        ];
        echo json_encode($arResult);
        $APPLICATION->FinalActions();
        exit;
    }

    protected function checkAvailability($arFavorites = [])
    {
        static $arStaticCache = [];

        global $USER;

        if (empty($arFavorites))
            return [];

        $arCacheKeys = [];

        if ($USER->IsAuthorized()) {
            $arCacheKeys['USER_ID'] = $USER->GetID();
        } else {
            $arCacheKeys['F_USER_ID'] = CSaleBasket::GetBasketUserID();
        }

        $arFilter = \Likee\Site\Helpers\Catalog::getDefaultFilter(['ID' => $arFavorites]);

        $sCacheKey = md5(serialize(array_merge($arFilter, $arCacheKeys)));

        if (array_key_exists($sCacheKey, $arStaticCache))
            return $arStaticCache[$sCacheKey];

        $obCache = \Bitrix\Main\Application::getCache();
        if ($obCache->initCache(3600, $sCacheKey)) {
            $arFavorites = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {

            $rsItems = CIBlockElement::GetList(['ID' => 'ASC'], $arFilter, false, false, ['ID', 'IBLOCK_ID']);

            $arFavorites = [];
            while ($arItem = $rsItems->Fetch()) {
                $arFavorites[] = intval($arItem['ID']);
            }

            $obCache->endDataCache($arFavorites);
        }

        $arStaticCache[$sCacheKey] = $arFavorites;

        return $arFavorites;
    }

    public function count()
    {
        $arFavorites = Favorites::getInstance()->getIdArray();
        $arFavorites = $this->checkAvailability($arFavorites);
        return count($arFavorites);
    }
}