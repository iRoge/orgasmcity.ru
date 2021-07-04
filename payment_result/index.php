<?

use Qsoft\Helpers\BonusSystem;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
$orderId = $_REQUEST['OrderId'];
$tinkoffPaymentId = $_REQUEST['PaymentId'];
$success = $_REQUEST['Success'];
$errorMsg = '';
if ($success) {
    if (!$tinkoffPaymentId || !$orderId) {
        define("ERROR_404", "Y");
    } else {
        include(dirname(__FILE__) . "/../bitrix/modules/tinkoff.payment/install/sale_payment/tinkoff/sdk/tinkoff_autoload.php");
        $tinkoffClient = new TinkoffMerchantAPI(
            TINKOFF_TERMINAL_ID, // Идентификатор терминала
            TINKOFF_TERMINAL_PASSWORD // Пароль терминала
        );
        $data = [
            'TerminalKey' => TINKOFF_TERMINAL_ID,
            'PaymentId' => $tinkoffPaymentId,
        ];
        $response = json_decode($tinkoffClient->getState($data), true);
        if (!$response || $response['ErrorCode'] || $response['Status'] != 'CONFIRMED') {
            $success = false;
            if ($response['Status'] != 'CONFIRMED') {
                $errorMsg = 'Итоговый статус оплаты не соответствует подтвержденному';
            } elseif (isset($response['Message'])) {
                $errorMsg = $response['Message'];
            } else {
                $errorMsg = 'Ошибка оплаты';
            }
        } else {
            $order = \Bitrix\Sale\Order::load($orderId);
            if ($order) {
                $paymentCollection = $order->getPaymentCollection();
                $onePayment = $paymentCollection[0];
                $onePayment->setPaid("Y"); // выставляем оплату
                $order->setField('STATUS_ID', 'ZS'); // Устанавливаем статус "Подтвержден, отправить заказ поставщику"
                $order->save();
                global $USER;
                $bonusHelper = new BonusSystem($USER->GetID());
                $bonusHelper->recalcUserBonus();
            } else {
                $success = false;
                $errorMsg = 'Заказ ' . $orderId . ' не найден';
            }
        }
    }
} else {
    if (isset($response['Message'])) {
        $errorMsg = $response['Message'];
    } else {
        $errorMsg = 'Ошибка оплаты';
    }
}

if ($success) {
    $APPLICATION->SetPageProperty("title", "Спасибо за оплату заказа!");
    $APPLICATION->SetPageProperty("description", "Спасибо за оплату заказа!");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Спасибо за оплату заказа");
    ?>
    <div class="page-massage page__message-order text-success">
        Спасибо, ваш заказ <b>№ <?= $orderId ?></b> оплачен. <br>
        Дождитесь звонка оператора для подтверждения доставки<br>
    </div>
<?php
} else {
    $APPLICATION->SetPageProperty("title", "Ошибка при оплате заказа");
    $APPLICATION->SetPageProperty("description", "Ошибка при оплате заказа");
    $APPLICATION->SetTitle("Ошибка");
    ?>
    <div class="page-massage page__message-order">
        Оплата заказа завершена с ошибкой. <br>
        Причина ошибки: <span class="text-danger"><b><?=$errorMsg?></b></span> <br>
        Связаться со службой поддержки вы можете по телефону
        <a class="phone-top-link" href="tel:<?=SUPPORT_PHONE?>">
            <?=SUPPORT_PHONE?>
        </a><br>
        Повторно попытаться провести оплату вы можете через страницу <a href="/personal/orders/">истории заказов</a>
    </div>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>