<?

use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;

define('HIDE_TITLE', true);
define('TINKOFF_PAY_SYSTEM_ID', 10);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$orderId = intval($_GET["orderId"]);
$orderType = $_GET["orderType"];
$order = Order::load($orderId);
if (!$orderId || !$order) {
    Functions::abort404();
} else {
    $APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Спасибо за оформление заказа");

    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment) {
        $orderPayments[$payment->getField('PAY_SYSTEM_ID')] = $payment;
    }

    if (!empty($orderPayments[TINKOFF_PAY_SYSTEM_ID])) {
        $orderType = 'prepayment_s1';
    } else {
        $orderType = 'default';
    }

    if ($orderType == 'default') {
        ?>
        <div class="page-massage page__message-order">
            Спасибо,<br>
            номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            С вами созвонятся операторы для подтверждения заказа.<br>
            Отследить статус заказа вы можете <a href="/personal/orders/">на странице</a>.
            Сейчас вы авторизованы автоматически,<br>
            но вам нужно поменять пароль в <a href="/personal/">личном кабинете</a>, чтобы вы могли зайти в следующий раз
            <br>
        </div>
<?php } elseif ($orderType == 'prepayment_s1') { ?>
        <div class="page-massage page__message-order">
            Номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            Через некоторое время после оплаты с вами созвонятся операторы для подтверждения заказа.<br>
            Сейчас вы авторизованы автоматически,<br>
            но вам нужно поменять пароль в <a href="/personal/">личном кабинете</a>, чтобы вы могли зайти в следующий раз<br>
            <?= Option::get("respect", "order_success_text", ""); ?>
            <p>Сумма к оплате: <b><?= number_format($order->getPrice(), 0, '', ' ') . ' p.' ?></b></p>
            <br>
            <button class="bttn pay-button" data-order-id="<?= $orderId ?>">Оплатить</button>
        </div>
<?php
    }
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
