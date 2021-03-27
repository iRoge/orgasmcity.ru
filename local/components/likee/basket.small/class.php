<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Site
 * Date: 2017-03-16
 * Time: 19:40:35
 */
class LikeeBasketSmallComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $this->arResult['COUNT'] = $this->getCount();


        $this->includeComponentTemplate();
    }

    public function getCount()
    {
        \Bitrix\Main\Loader::includeModule('sale');

        $iCount = 0;
        $iFUser = intval(CSaleBasket::GetBasketUserID(true));

        if ($iFUser > 0) {
            $iCount = \Bitrix\Sale\Internals\BasketTable::getCount([
                'FUSER_ID' => $iFUser,
                'LID' => SITE_ID,
                'ORDER_ID' => 'NULL',
                'CAN_BUY' => 'Y'
            ]);
        }

        return $iCount;
    }

    public function getItems()
    {
        \Bitrix\Main\Loader::includeModule('sale');

        $arBasketItems = [];

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        if ($basket->getBasketItems()) {
            
            $order = Bitrix\Sale\Order::create(\Bitrix\Main\Context::getCurrent()->getSite(), \Bitrix\Sale\Fuser::getId());
            $order->setBasket($basket);

            foreach ($basket->getBasketItems() as $basketItem) {
                $arProduct = $basketItem->getFieldValues();

                $arBasketItems[] = [
                    'id' => $arProduct['PRODUCT_ID'], 
                    'price' => ((int)$arProduct['PRICE']),
                    'count' => ((int)$arProduct['QUANTITY'])
                ];
            }
        }

        return $arBasketItems;
    }
}