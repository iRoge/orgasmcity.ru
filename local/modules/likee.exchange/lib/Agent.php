<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Likee\Exchange\Tables\OrderQueueErrorTable;
use Likee\Exchange\Tables\OrderQueueTable;

/**
 * Класс агента. Агент обрабатывает полученные заказы, обновляет время и статус заказа.
 *
 * @namespace Likee\Exchange
 */
class Agent
{
    /**
     * Обрабатывает очередь заказов
     *
     * @return string Имя функции агента
     */
    public static function handleOrderQueue()
    {
        Loader::includeModule('sale');
        $arConfig = Config::get();
        $rsQueue = OrderQueueTable::getList([
            'filter' => [
                'STATUS' => [
                    OrderQueueTable::STATUS_NEW,
                    OrderQueueTable::STATUS_ERROR,
                    OrderQueueTable::STATUS_PAYMENT_NEW,
                    OrderQueueTable::STATUS_PAYMENT_ERROR,
                ],
            ],
            'order' => [
                'PRIORITET' => 'ASC',
                'ATTEMPTS' => 'ASC',
                'ORDER_ID' => 'DESC',
            ],
            'limit' => 5,
        ]);
        while ($arQueue = $rsQueue->Fetch()) {
            $bError = false;
            $paymentTag = '';
            if (in_array($arQueue['STATUS'], [OrderQueueTable::STATUS_PAYMENT_NEW, OrderQueueTable::STATUS_PAYMENT_ERROR,]) && !empty($arQueue['ORDER_NUMBER'])) {
                $result = Order::getOrderPayment($arQueue['ORDER_ID'], $arQueue['ORDER_NUMBER']);
                $arData = $result['arData'];
                $paymentTag = $result['paymentTag'];
                $queueType = 'onlinePayment';
            } else {
                $arData = Order::getOrderData($arQueue['ORDER_ID']);
                $queueType = 'order';
            }
            if ($arData) {
                if ($queueType == 'order') {
                    list($order, $xml, $data, $attempts) = static::sendOrderXml($arData, $arConfig['API'], $arConfig['LOGIN'], $arConfig['PASSWORD'], $arQueue);
                } elseif ($queueType == 'onlinePayment') {
                    list($order, $xml, $data, $attempts) = static::sendOrderPaymentXml($arData, $paymentTag, $arConfig['API'], $arConfig['LOGIN'], $arConfig['PASSWORD'], $arQueue);
                }

                if ($order['status'] != 'success') {
                    OrderQueueErrorTable::add([
                        'QUEUE_ID' => $arQueue['ID'],
                        'QUERY' => $xml,
                        'ANSWER' => $data,
                        'DATE' => DateTime::createFromTimestamp(time())
                    ]);
                    $prioritet = 2;
                    if (empty($order['status'])) {
                        $prioritet = 1; //сетевые ошибки
                    }
                    if ($attempts % 5 == 0) {
                        $arEventFields = [
                            'ORDER_ID' => $arQueue['ORDER_ID'],
                            'ORDER_DATE' => $arQueue['DATE_INSERT'],
                            'ATTEMPTS' => $attempts,
                        ];
                        \CEvent::Send('ORDER_MISTAKES_1C', SITE_ID, $arEventFields);
                    }
                    OrderQueueTable::update(
                        $arQueue['ID'],
                        [
                            'STATUS' => $queueType == 'order' ? OrderQueueTable::STATUS_ERROR : OrderQueueTable::STATUS_PAYMENT_ERROR,
                            'ATTEMPTS' => $attempts,
                            'DATE_ATTEMPT' => DateTime::createFromTimestamp(time()),
                            'PRIORITET' => $prioritet,
                        ]
                    );
                    $bError = true;
                } else {
                    OrderQueueTable::update(
                        $arQueue['ID'],
                        [
                            'STATUS' => $queueType == 'order' ? OrderQueueTable::STATUS_SUCCESS : OrderQueueTable::STATUS_PAYMENT_SUCCESS,
                            'ATTEMPTS' => $attempts,
                            'DATE_ATTEMPT' => DateTime::createFromTimestamp(time()),
                            'PRIORITET' => 0,
                        ]
                    );
                }
                if (in_array($arQueue['STATUS'], [OrderQueueTable::STATUS_NEW, OrderQueueTable::STATUS_PAYMENT_NEW]) && $arConfig['ACTIVE2']) {
                    if ($queueType == 'order') {
                        list($order, $xml, $data, $attempts) = static::sendOrderXml($arData, $arConfig['API2'], $arConfig['LOGIN2'], $arConfig['PASSWORD2'], $arQueue, "curl_order_send2.txt");
                    } elseif ($queueType == 'onlinePayment') {
                        list($order, $xml, $data, $attempts) = static::sendOrderPaymentXml($arData, $paymentTag, $arConfig['API2'], $arConfig['LOGIN2'], $arConfig['PASSWORD2'], $arQueue, "curl_order_payment_send2.txt");
                    }

                    if ($order['status'] != 'success') {
                        OrderQueueErrorTable::add([
                            'QUEUE_ID' => $arQueue['ID'],
                            'QUERY' => $xml,
                            'ANSWER' => $data,
                            'DATE' => DateTime::createFromTimestamp(time())
                        ]);
                    }
                }
            } else {
                OrderQueueErrorTable::add([
                    'QUEUE_ID' => $arQueue['ID'],
                    'QUERY' => '',
                    'ANSWER' => $queueType == 'order' ? 'Отсутствие заказа' : 'Отсутствие заказа при отправке шлюза',
                    'DATE' => DateTime::createFromTimestamp(time())
                ]);
                OrderQueueTable::delete(
                    $arQueue['ID']
                );
                $arEventFields = [
                    'ORDER_ID' => $arQueue['ORDER_ID'],
                    'ORDER_DATE' => $arQueue['DATE_INSERT'],
                ];
                \CEvent::Send('ORDER_DELETED_1C', SITE_ID, $arEventFields);
            }
            $arProp = \CSaleOrderPropsValue::GetList(
                [],
                [
                    'CODE' => 'PROBLEM_1C',
                    'ORDER_ID' => $arQueue['ORDER_ID']
                ]
            )->Fetch();
            if ($arProp) {
                \CSaleOrderPropsValue::Update($arProp['ID'], ['VALUE' => $bError ? 'Y' : 'N']);
            } else {
                $obOrder = \Bitrix\Sale\Order::load($arQueue['ORDER_ID']);
                if ($obOrder) {
                    $rsProp = \CSaleOrderProps::GetList(
                        [],
                        [
                            "PERSON_TYPE_ID" => $obOrder->getPersonTypeId(),
                            "CODE" => "PROBLEM_1C"
                        ]
                    );
                    if ($arProp = $rsProp->Fetch()) {
                        $arFields = [
                            "ORDER_ID" => $arQueue['ORDER_ID'],
                            "ORDER_PROPS_ID" => $arProp['ID'],
                            "NAME" => "Проблема с выгрузкой в 1С",
                            "CODE" => "PROBLEM_1C",
                            "VALUE" => $bError ? 'Y' : 'N'
                        ];
                        \CSaleOrderPropsValue::Add($arFields);
                    }
                }
            }
        }
        return '\Likee\Exchange\Agent::handleOrderQueue();';
    }

    public static function sendOrderXml(array $arData, string $url, string $user, string $password, array $arQueue = [], string $logfile = "curl_order_send.txt")
    {
        $attempts = $arQueue['ATTEMPTS'] + 1;
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><order></order>");
        Helper::array2xml($arData, $xml);
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml->asXML());
        $xml = $domxml->saveXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $message = "Заказ: ".$arQueue['ORDER_ID']." - Попытка: ".$attempts." - Код ответа curl: ".$code;
        if ($error) {
            $message .= " - Ошибка curl: ".$error;
        }
        curl_close($ch);
        $order = Helper::xml2array(simplexml_load_string($data));
        if (mb_strpos($order['text'], "Заказ с номером") !== false && mb_strpos($order['text'], "уже существует") !== false) {
            $order['status'] = 'success';
        }
        $message .= " - Статус 1С: ".($order['status'] ?: "нет");
        if ($order['status'] == "error") {
            $message .= " - Ошибка 1С: ".$order['text'];
        }
        qsoft_logger($message, $logfile);

        return [$order, $xml, $data, $attempts];
    }

    public static function sendOrderPaymentXml(array $arData, string $paymentTag, string $url, string $user, string $password, array $arQueue = [], string $logfile = "curl_order_payment_send.txt")
    {
        $attempts = $arQueue['ATTEMPTS'] + 1;
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>" . $paymentTag);
        Helper::array2xml($arData, $xml);
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml->asXML());
        $xml = $domxml->saveXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $message = "Заказ: " . $arData['id'] . ', Оплата: ' . $arData['pay']['id'] . ', Шлюз: ' . $arData['pay']['order_number'] . " - Попытка: " . $attempts . " - Код ответа curl: " . $code;

        if ($error) {
            $message .= " - Ошибка curl: " . $error;
        }

        curl_close($ch);
        $dataArr = Helper::xml2array(simplexml_load_string($data));
        $message .= " - Статус 1С: " . ($dataArr['status'] ?: "нет");

        if ($dataArr['status'] == "error") {
            $message .= " - Ошибка 1С: " . $dataArr['text'];
        }

        qsoft_logger($message, $logfile);

        return [$dataArr, $xml, $data, $attempts];
    }

    public static function updateSectionsActivity()
    {

        \Bitrix\Main\Loader::includeModule('likee.site');
        \Bitrix\Main\Loader::includeModule('likee.exchange');

        \Likee\Exchange\Task\Rests::updateSectionsActivity();

        return '\Likee\Exchange\Agent::updateSectionsActivity();';
    }

    public static function removeOldReservedCount()
    {
        $sql = 'DELETE t1 
        FROM b_likee_items_reserve_storage AS t1 CROSS JOIN (
            SELECT rs.ID
            FROM b_likee_items_reserve_storage rs
            INNER JOIN b_sale_order o ON rs.ORDER_ID = o.ID
            WHERE rs.STATUS = \'R\' AND o.DATE_INSERT <= DATE_SUB(SYSDATE(), INTERVAL 2 DAY)
        ) AS t2 USING(ID);';
        
        $connection = Application::getConnection();
        $connection->query($sql);

        return '\Likee\Exchange\Agent::removeOldReservedCount();';
    }
}
