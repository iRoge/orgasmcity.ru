<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Bitrix\Main\Event;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Order;
use Likee\Exchange\Tables\OrderQueueTable;

/**
 * Класс с обработчиками событий для заказов.
 *
 * @package Likee\Exchange
 */
class Events
{
    /**
     * Оброботчик сохранения заказа.
     *
     * Если заказ новый, добавляет его в очередь на обработку.
     *
     * @param Event $event Событие
     */
    public static function orderSaveHandler(Event $event)
    {
        $isNew = $event->getParameter('IS_NEW');
        if (!$isNew) {
            return;
        }
        $order = $event->getParameter('ENTITY');
        OrderQueueTable::add([
            'ORDER_ID' => $order->getId(),
            'STATUS' => OrderQueueTable::STATUS_NEW,
            'ATTEMPTS' => 0,
            'DATE_INSERT' => DateTime::createFromTimestamp(time())
        ]);
    }
}
