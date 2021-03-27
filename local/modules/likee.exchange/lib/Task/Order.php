<?php

namespace Likee\Exchange\Task;

use Bitrix\Catalog\StoreTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Sale\Internals\BasketTable;
use Bitrix\Sale\Internals\StoreProductTable;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;

/**
 * Класс для работы с импортом заказов.
 *
 * @package Likee\Exchange\Task
 */
class Order extends Task
{
    /**
     * @var array Словарь
     */
    var $dictionary = [];
    /**
     * @var string xml для импорта
     */
    var $xml = 'order.xml';
    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {
        $request = Context::getCurrent()->getRequest();
        $arStatuses = \Likee\Exchange\Order::$orderStatus;
        $arPaysystem = \Likee\Exchange\Order::$orderPayment;
        $arDelivery = \Likee\Exchange\Order::$orderDelivery;

        $order = $request->get('order_id');
        $status = $request->get('status_id');

        if ($order && $status) {
            $arOrder = \CSaleOrder::GetByID($order);

            if (!$arOrder) {
                throw new ExchangeException('Заказ ' . $order . ' не найден', ExchangeException::$ERR_NOT_EXIST);
            }

            if (!$arStatuses[$status]) {
                throw new ExchangeException('Статус ' . $status . ' не найден', ExchangeException::$ERR_NOT_EXIST);
            }

            \CSaleOrder::StatusOrder($arOrder['ID'], $arStatuses[$status]);
        } else {

            $arErrors = [];
			
			$this->reader->on('Order', function ($reader, $xml) use (&$arErrors, $arStatuses, $arPaysystem) {
                
                $arStatusesForBuy = ['N', 'R', 'K', 'A', 'O', 'E',];
                $arStatusesForReserve = ['N', 'R', 'K', 'E',];
                $arStatusesForClean = ['N', 'R', 'K', 'A', 'O', 'E', 'C'];

                $order = Helper::xml2array(simplexml_load_string($xml));

                $arOrder = \CSaleOrder::GetByID($order['id']);
                
				$arStatuses = \Likee\Exchange\Order::$orderStatus;
				$arPaysystem = \Likee\Exchange\Order::$orderPayment;
				$arDelivery = \Likee\Exchange\Order::$orderDelivery;

				if (!$arOrder) {
                    throw new ExchangeException('Заказ ' . $order['id'] . ' не найден', ExchangeException::$ERR_NOT_EXIST);
                }

                if (!$arStatuses[$order['status']['id']]) {
                    throw new ExchangeException('Статус ' . $order['status']['id'] . ' не найден', ExchangeException::$ERR_NOT_EXIST);
                }

                if (!$arPaysystem[$order['pay']['id']]) {
                    throw new ExchangeException('Платежная система ' . $order['status']['id'] . ' не найдена', ExchangeException::$ERR_NOT_EXIST);
                }

                if (!$arDelivery[$order['delivery']['id']]) {
                    throw new ExchangeException('Доставка ' . $order['status']['id'] . ' не найдена', ExchangeException::$ERR_NOT_EXIST);
                }

                $arUpdate = [];

                $orderDb = \Bitrix\Sale\Order::load($arOrder['ID']);
                $currentOrderStatus = $orderDb->getField('STATUS_ID');
                $userComment = $orderDb->getField('USER_DESCRIPTION');

                //WARNING
                //Заказы с резервированием отличаются от обычных только этим комментарием. Необходимо завести дополнительный признак.
                $isReservation = ($userComment == 'Резервирование товара') ? true : false;


                if ($order['store']['id']) {
                    $arStore = StoreTable::getRow([
                        'filter' => [
                            'XML_ID' => $order['store']['id']
                        ]
                    ]);

                    if ($arStore) {
                        $arStores = [];
                        /** @var \Bitrix\Sale\Shipment $shipment */
                        foreach ($orderDb->getShipmentCollection() as $shipment) {
                            if (!$shipment->isSystem())
                                $shipment->setStoreId($arStore['ID']);
                        }
                        $orderDb->save();
                    }
                }
                $order['delivery']['price'] = floatval($order['delivery']['price']);

                if ($arOrder['PAY_SYSTEM_ID'] != $arPaysystem[$order['pay']['id']])
                    $arUpdate['PAY_SYSTEM_ID'] = $arPaysystem[$order['pay']['id']];

                if ($arOrder['DELIVERY_ID'] != $arDelivery[$order['delivery']['id']])
                    $arUpdate['DELIVERY_ID'] = $arDelivery[$order['delivery']['id']];

                if ($arOrder['PRICE_DELIVERY'] != $order['delivery']['price'])
                    $arUpdate['PRICE_DELIVERY'] = $order['delivery']['price'];

                if ($arOrder['STATUS_ID'] != $arStatuses[$order['status']]['id']) {
                    \CSaleOrder::StatusOrder($arOrder['ID'], $arStatuses[$order['status']['id']]);
                    $nextOrderStatus = $arStatuses[$order['status']['id']];
                }					
					
                if (count($arUpdate))
                    \CSaleOrder::Update($arOrder['ID'], $arUpdate);

                if (!$order['products']['product'][0]) {
                    $order['products']['product'] = [$order['products']['product']];
                }

                $rsBasket = \CSaleBasket::GetList([], ['ORDER_ID' => $arOrder['ID']]);

                $arBasketItems = [];
                while ($arBasket = $rsBasket->Fetch()) {
                    $arBasketItems[$arBasket['PRODUCT_ID']] = $arBasket;
                }


                $currentOrderStatus = $orderDb->getField('STATUS_ID');
                //Проверка, нужно ли уменьшать остатки на складах.
                $needRemoveFromReserve = false;
                if ($nextOrderStatus == 'F') {
                    $needRemoveFromReserve = true;
                }


                //Проверка, нужно ли заносить позиции заказа в таблицу блокировки
                $needUpdateBuyBlock = false;
                $needUpdateReserveBlock = false;

                if (!$isReservation && in_array($nextOrderStatus, $arStatusesForBuy)) {
                    $needUpdateBuyBlock = true;
                } elseif ($isReservation && in_array($nextOrderStatus, $arStatusesForReserve)) {
                    $needUpdateReserveBlock = true;
                }

                $needCleanData = false;
                if (in_array($nextOrderStatus, $arStatusesForClean))
                    $needCleanData = true;

                if ($needUpdateBuyBlock || $needUpdateReserveBlock || $needCleanData || $needRemoveFromReserve) {
                    \Likee\Exchange\Reserve::clearOrderReserve($arOrder['ID']);
                }


                //Забор из базы всех складов
                $arStores = [];
                $rsStores = StoreTable::getList();
                while ($arStore = $rsStores->fetch()) {
                    $arStores[$arStore['XML_ID']] = $arStore['ID'];
                }
                
                foreach ($order['products']['product'] as $product) {
                    $arProduct = ElementTable::getRow([
                        'filter' => [
                            'XML_ID' => $product['id']
                        ]
                    ]);

                    $rsOffers = \CIBlockElement::GetList(
                        [],
                        [
                            'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                            'PROPERTY_CML2_LINK' => $arProduct['ID']
                        ],
                        false,
                        false,
                        [
                            'ID',
                            'PROPERTY_SIZE'
                        ]
                    );

                    $arOffers = [];
                    while ($arOffer = $rsOffers->Fetch()) {
                        $arOffers[$arOffer['PROPERTY_SIZE_VALUE']] = $arOffer['ID'];
                    }

                    $offer = $arOffers[$product['size']];

                    $rsQuantity = StoreProductTable::getList([
                        'filter' => [
                            'PRODUCT_ID' => $offer,
                            'STORE_ID' => $arStores[$product['store']]
                        ]
                    ]);

                    if (!$offer) {
                        throw new ExchangeException(
                            'Товар ' . $product['id'] . ' с размером ' . $product['size'] . ' не найден',
                            ExchangeException::$ERR_NOT_EXIST
                        );
                    }

                    if ($needRemoveFromReserve) {
                        while ($arQuantity = $rsQuantity->fetch()) {
                            $nextAmount = $arQuantity['AMOUNT'] - $product['count'];

                            $rs = StoreProductTable::update(
                                $arQuantity['ID'],
                                ['AMOUNT' => $nextAmount]
                            );
                            if (!$rs->isSuccess()) {
                                throw new ExchangeException(
                                    'Не удалось обновить остатки для товара ' . $product['id'] . ' с размером ' . $product['size'],
                                    ExchangeException::$ERR_NOT_EXIST
                                );
                            }
                        }
                    }


                    //Занесение в таблицу резервов заказа					
                    if ($needUpdateBuyBlock) {
                        \Likee\Exchange\Reserve::addItemToPurchase($offer, $arStores[$product['store']], $product['count'], $arOrder['ID']);
                    } elseif ($needUpdateReserveBlock) {
                        \Likee\Exchange\Reserve::addItemToReserve($offer, $arStores[$product['store']], $product['count'], $arOrder['ID']);
                    }

                    if ($arBasketItems[$offer]) {
                        BasketTable::update($arBasketItems[$offer]['ID'], ['QUANTITY' => $product['count']]);
                        unset($arBasketItems[$offer]);
                    } else {
                        $arProductPops = \CIBlockPriceTools::GetOfferProperties(
                            $offer,
                            $this->config['IBLOCK_ID'],
                            ['COLOR', 'SIZE', 'ARTICLE']
                        );

                        Add2BasketByProductID($offer, $product['count'], ['ORDER_ID' => $arOrder['ID']], $arProductPops);
                    }

                    foreach ($arBasketItems as $arItem) {
                        \CSaleBasket::Delete($arItem['ID']);
                    }

                    //Очищение резерва
                    //\Likee\Exchange\Reserve::temp();
                }
            });

            $this->reader->read();
        }

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно',
        ]);

        return $this->result;
    }
}