<?php

use Bitrix\Main\Application;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem\BaseServiceHandler;
use Bitrix\Sale\PaySystem\Manager as PaymentManager;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$orderId = intval($_POST['orderId']);

try {
    $sberLink = '';
    $order = Order::load($orderId);

    if (!$orderId) {
        throw new Exception();
    }

    $payment = $order->getPaymentCollection()[0];
    // получаем оплаты с ограничениями
    $obPayment = $payment->getPaySystem();

    if (!in_array($obPayment->getField('CODE'), ONLINE_PAYMENT_CODES)) {
        throw new Exception();
    }

    $context = Application::getInstance()->getContext();
    $service = PaymentManager::getObjectById($obPayment->getField('ID'));
    $paymentInitialization = $service->initiatePay($payment, $context->getRequest(), BaseServiceHandler::STRING);
    $dom = new DOMDocument;
    $dom->loadHTML($paymentInitialization->getTemplate());

    foreach ($dom->getElementsByTagName('form') as $node) {
        $tinkoffLink = $node->getAttribute('action');
        if ($tinkoffLink != '') {
            parse_str(parse_url($tinkoffLink)['query'], $query);
            $orderNUmber = $query['mdOrder'];
            exit($tinkoffLink);
        }
    }
} catch (\Bitrix\Main\ArgumentNullException | \Bitrix\Main\LoaderException | \Bitrix\Main\ArgumentOutOfRangeException |
\Bitrix\Main\ArgumentTypeException | \Bitrix\Main\ArgumentException | \Bitrix\Main\NotSupportedException |
\Bitrix\Main\ObjectException | \Bitrix\Main\SystemException $e) {
    myLog($e->getMessage());
}
