<?php

namespace Qsoft\Events;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use \CIBlockElement;
use \Bitrix\Main\Context;
use \Bitrix\Main\Event;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\Fuser;
use Bitrix\Catalog\StoreTable;
use \Likee\Exchange\Tables\ReserveStorageTable;
use Bitrix\Sale\Order as Sale;
use Qsoft\Helpers\PriceUtils;
use \Qsoft\Sailplay\Tasks\TaskManager;

class Order
{
    private const STATUS_RESERV = 'R';
    private const STATUS_PURCHASE = 'P';
    public function OnOrderSaveHandler(Event $event)
    {
        try {
            if (!Loader::includeModule("likee.exchange")) {
                return;
            };
        } catch (LoaderException $e) {
            return;
        }
        $isNew = $event->getParameter("IS_NEW");
        if (!$isNew) {
            return;
        }
        $order = $event->getParameter('ENTITY');
        $orderId = $order->getId();
        static::log("=============");
        static::log("orderId: ".$orderId);
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
            $prodId = $basketItem->getProductId();
            $quantity = $basketItem->getQuantity();
            $status = static::getReservStatus();
            static::log("productId: ".$prodId.", quantity: ".$quantity.", status: ".$status);
            ReserveStorageTable::add([
                'PRODUCT_ID' => $prodId,
                'QUANTITY' => $quantity,
                'ORDER_ID' => $orderId,
                'STATUS' => $status,
            ]);
        }
    }
    private static function getReservStatus()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $action = $request->get('action');
        if ($action === 'reserv') {
            return self::STATUS_RESERV;
        } else {
            return self::STATUS_PURCHASE;
        }
    }
    public static function OnGetOptimalPriceResultHandler(&$arFields)
    {
        // если нет ID ТП, то ничего не делаем
        if (!$arFields["PRODUCT_ID"]) {
            return;
        }
        $arOffer = \CIBlockElement::GetList(
            [],
            [
                "ID" => $arFields["PRODUCT_ID"],
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "nTopCount" => 1,
            ],
            [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                'PROPERTY_BASEWHOLEPRICE',
                'PROPERTY_BASEPRICE',
            ]
        )->Fetch();
        // если нет ID товара, то ничего не делаем
        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return;
        }
        $arPrice = PriceUtils::getReducedPrice($arOffer['PROPERTY_BASEWHOLEPRICE_VALUE'], $arOffer['PROPERTY_BASEPRICE_VALUE']);
        if (!$arPrice) {
            $arFields = [];
            return;
        }
        $arFields['PRICE']['PRICE'] = $arPrice['PRICE'];
        $arFields['DISCOUNT_PRICE'] = $arPrice['PRICE'];
        $arFields['RESULT_PRICE']['BASE_PRICE'] = $arPrice['PRICE'];
        $arFields['RESULT_PRICE']['DISCOUNT_PRICE'] = $arPrice['PRICE'];
    }

    protected static function log($message)
    {
        orgasm_logger($message, "orderEvents.txt");
    }

    public function OnSaleStatusOrderHandler($orderId, $orderStatus)
    {
        if ($orderStatus != 'O') {
            return;
        }
        $data = [];
        $order = Sale::load($orderId);
        $orderPropertyCollection = $order->getPropertyCollection();
        foreach ($orderPropertyCollection as $orderPropertyItem) {
            if ($orderPropertyItem->getField('CODE') == "ORDER_TYPE") {
                $orderType = $orderPropertyItem->getField('VALUE');
            }
            if ($orderPropertyItem->getField('CODE') == "PHONE") {
                $data['order_phone'] = $orderPropertyItem->getField('VALUE');
            }
            if ($orderPropertyItem->getField('CODE') == "EMAIL") {
                $data['order_email'] = $orderPropertyItem->getField('VALUE');
            }
        }
        if ($orderType == 'RESERVATION') {
            return;
        }

        $data['order_num'] = $orderId;
        $data['type_order'] = $orderType;
        $data['l_date'] = $order->getDateInsert()->getTimestamp();
        $basket = $order->getBasket();
        $i = 1;

        foreach ($basket as $basketItem) {
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            foreach ($basketPropertyCollection as $basketPropertyItem) {
                if ($basketPropertyItem->getField('CODE') == "KOD_1S") {
                    $sku = $basketPropertyItem->getField('VALUE');
                }
            }
            $cart[$i] = [
                'sku' => $sku,
                'price' => $basketItem->getPrice(),
                'quantity' => $basketItem->getQuantity()
            ];
            $i++;
        }
        $data['cart'] = json_encode($cart);

        $taskManager = new TaskManager();
        $taskManager->setUser($order->getUserId());
        $taskManager->addTask('sendOrder', $data);
    }
}
