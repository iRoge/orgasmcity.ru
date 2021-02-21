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
        $isLocal = $_REQUEST['PROPS']['IS_LOCAL'];
        foreach ($basket as $basketItem) {
            $prodId = $basketItem->getProductId();
            $quantity = $basketItem->getQuantity();
            $status = static::getReservStatus();
            $storeId = static::getStorageId($prodId, $status, $isLocal);
            static::log("productId: ".$prodId.", storeId: ".$storeId.", quantity: ".$quantity.", status: ".$status);
            ReserveStorageTable::add([
                'PRODUCT_ID' => $prodId,
                'QUANTITY' => $quantity,
                'ORDER_ID' => $orderId,
                'STORAGE_ID' => $storeId,
                'STATUS' => $status,
            ]);
        }
    }
    private static function getStorageId($productId, $status, $isLocal = 'Y')
    {
        global $LOCATION;

        if ($status == "R") {
            $context = Context::getCurrent();
            $request = $context->getRequest();
            $storageId = $request->get('DELIVERY_STORE_ID');
        } else {
            if ($isLocal == 'Y') {
                $arRests = $LOCATION->getRests($productId, 1);
                // Убираем дефолтные склады
                $arRests[$productId] = array_diff_key($arRests[$productId], $LOCATION->DEFAULT_STORAGES);
            } else {
                $arRests = $LOCATION->getRests($productId, 1, false, false, $LOCATION->DEFAULT_STORAGES);
            }
            $arRests = reset($arRests);
            // получаем все активные склады
            $res = StoreTable::getList(array(
                "select" => array(
                    "ID", "SORT"
                ),
                "filter" => array(
                    "ACTIVE" => "Y",
                ),
            ));
            $arStorages = array();
            while ($arItem = $res->fetch()) {
                if (!$arRests[$arItem['ID']]) {
                    continue;
                }
                $arStorages[$arItem['ID']] = $arItem;
                $arStorages[$arItem['ID']]['REST'] = $arRests[$arItem['ID']];
            }
            usort($arStorages, function ($a, $b) {
                if ($a['SORT'] === $b['SORT']) {
                    if ($a['REST'] === $b['REST']) {
                        return ($a['ID'] > $b['ID']) ? 1 : -1;
                    }
                    return ($a['REST'] > $b['REST']) ? -1 : 1;
                }
                    return ($a['SORT'] > $b['SORT']) ? 1 : -1;
            });
            $storageId = $arStorages[0]['ID'];
        }
        if ($storageId) {
            return $storageId;
        } else {
            static::log("ОШИБКА: не получен STORAGE_ID");
            return 0;
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
        global $LOCATION;
        global $USER;
        // если нет ID ТП, то ничего не делаем
        if (!$arFields["PRODUCT_ID"]) {
            return;
        }
        $arOffer = \CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arFields["PRODUCT_ID"],
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ),
            false,
            array(
                "nTopCount" => 1,
            ),
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
            )
        )->Fetch();
        // если нет ID товара, то ничего не делаем
        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return;
        }
        $productId = $arOffer["PROPERTY_CML2_LINK_VALUE"];
        $arPrice = array();
        $isLocal = 'Y';
        if ($_REQUEST['isLocal'] === 'N') {
            $arPrice = $LOCATION->getProductsPrices(array($productId), $LOCATION->DEFAULT_BRANCH);
        } elseif ($_REQUEST['isLocal'] === 'Y') {
            $arPrice = $LOCATION->getProductsPrices(array($productId));
        } elseif (isset($GLOBALS['localBasketFlag'])) {
            if ($GLOBALS['localBasketFlag'] === 'N') {
                $arPrice = $LOCATION->getProductsPrices(array($productId), $LOCATION->DEFAULT_BRANCH);
            } else {
                $arPrice = $LOCATION->getProductsPrices(array($productId));
            }
            unset($GLOBALS['localBasketFlag']);
        } else {
            $basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
            $basketItems = $basket->getBasketItems();
            foreach ($basketItems as $arItem) {
                if ($arItem->getProductId() == $arOffer['ID']) {
                    $basketPropertyCollection = $arItem->getPropertyCollection();
                    foreach ($basketPropertyCollection as $basketPropertyItem) {
                        if ($basketPropertyItem->getField('CODE') == "IS_LOCAL") {
                            $isLocal = $basketPropertyItem->getField('VALUE');
                        }
                    }
                }
                if ($isLocal === 'N') {
                    $arPrice = $LOCATION->getProductsPrices(array($productId), $LOCATION->DEFAULT_BRANCH);
                } else {
                    $arPrice = $LOCATION->getProductsPrices(array($productId));
                }
            }
        }
        if ($arPrice[$productId]['PRICE']) {
            $price = false;
            if ($arPrice[$productId]['SEGMENT'] == 'White' && in_array($_REQUEST['action'], ['reserv', '1click']) && !$USER->isAuthorized()) {
                $price = $arPrice[$productId]['OLD_PRICE'];
            }
            $arFields['PRICE']['PRICE'] = $price ? $price : $arPrice[$productId]['PRICE'];
            $arFields['DISCOUNT_PRICE'] = $price ? $price : $arPrice[$productId]['PRICE'];
            $arFields['RESULT_PRICE']['BASE_PRICE'] = $price ? $price : $arPrice[$productId]['PRICE'];
            $arFields['RESULT_PRICE']['DISCOUNT_PRICE'] = $price ? $price : $arPrice[$productId]['PRICE'];
        }
    }

    protected static function log($message)
    {
        qsoft_logger($message, "orderEvents.txt");
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
