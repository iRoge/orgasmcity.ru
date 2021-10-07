<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Bitrix\Sale\Order;

define('TINKOFF_PAY_SYSTEM_ID', 10);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
$orderId = intval($_GET["orderId"]);
$orderType = $_GET["orderType"];
$order = Order::load($orderId);
if (!$orderId || !$order) {
    Functions::abort404();
} else {
    $APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Поздравляем, вы завершаете оформление заказа!");

    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment) {
        $orderPayments[$payment->getField('PAY_SYSTEM_ID')] = $payment;
    }

    if (!empty($orderPayments[TINKOFF_PAY_SYSTEM_ID])) {
        $orderType = 'prepayment_s1';
    } else {
        $orderType = 'default';
    }
    Asset::getInstance()->addCss('/order-success/style.css');
    $countOrder = 0;
    $arFilter = Array("USER_ID" => $USER->GetID());
    $sql = CSaleOrder::GetList(["DATE_INSERT" => "ASC"], $arFilter);

    while ($result = $sql->Fetch())
    {
        $countOrder++;
    }
    
    if ($orderType == 'default') {
        ?>
        <div class="success-block-wrapper main<?=$countOrder > 1 ? ' no-auth' : ''?>">
            <div class="success-second-block-wrapper">
                <div class="default-success-wrapper your-order-wrapper<?=$countOrder > 1 ? ' no-auth' : ''?>" style="width: 100%;">
                    <span style="width: 100%">Ваш заказ:&nbsp;</span>
                    <span style="width: 100%"><b>№<?=$orderId?></b></span>
                </div>
                <?php if ($countOrder == 1) {?>
                    <div class="default-success-wrapper auth-help-block">
                        <span class="success-block-title">
                            На данный момент вы авторизованы автоматически.
                        </span>
                        <span class="success-block-text">
                            Измените пароль в личном кабинете, чтоб вы могли отслеживать статус своего заказа и уровень бонусной программы
                        </span>
                        <img class="help-img" src="<?=SITE_TEMPLATE_PATH?>/img/authHelp.png" alt="help">
                    </div>
                <?php } ?>
            </div>
            <div class="success-second-block-wrapper">
                <div class="default-success-wrapper operator-will-call<?=$countOrder > 1 ? ' no-auth' : ''?>">
                    <div class="operator-will-call-img-wrap">
                        <img class="operator-img" src="<?=SITE_TEMPLATE_PATH?>/img/operator.png" alt="operator">
                    </div>
                    <span class="operator-will-call-text">
                        Через некоторое время с вами <b>созвоняться наши операторы</b> для подтверждения заказа
                    </span>
                </div>
                <?php if ($countOrder == 1) {?>
                    <div class="default-success-wrapper check-mail">
                        <span class="success-block-title">
                            Проверьте свою почту.
                        </span>
                        <span class="success-block-text">
                            Мы отправим вам письмо подтверждающее успешное завершение оформления заказа
                            (Если письмо не пришло пожалуйста проверьте папку СПАМ)
                        </span>
                        <div class="check-mail-img-wrap">
                            <img class="mail-img" src="<?=SITE_TEMPLATE_PATH?>/img/mailOpen.png" alt="help">
                            <img class="mail-img" src="<?=SITE_TEMPLATE_PATH?>/img/chatMsg.png" alt="help">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
<?php } elseif ($orderType == 'prepayment_s1') { ?>
        <div class="success-block-wrapper main<?=$countOrder > 1 ? ' no-auth' : ''?>">
            <div class="success-second-block-wrapper">
                <div class="default-success-wrapper your-order-wrapper<?=$countOrder > 1 ? ' no-auth' : ''?>">
                    <span style="width: 100%">Ваш заказ:&nbsp;</span>
                    <span style="width: 100%"><b>№<?=$orderId?></b></span>
                </div>
                <div class="default-success-wrapper payment-help-block<?=$countOrder > 1 ? ' no-auth' : ''?>">
                    Для оплаты заказа нажмите кнопку <b style="color:#764ae0;display: contents;">оплатить</b> ниже.
                    <span>Сумма к оплате: <b><?= number_format($order->getPrice(), 0, '', ' ') . ' pублей' ?></b></span>
                </div>
                <div class="success-button-wrapper only-mobile">
                    <button style="width: 100%!important;" class="bttn-pay pay-button" data-order-id="<?= $orderId ?>">Оплатить заказ</button>
                </div>
                <?php if ($countOrder == 1) {?>
                    <div class="default-success-wrapper auth-help-block">
                        <span class="success-block-title">
                            На данный момент вы авторизованы автоматически.
                        </span>
                        <span class="success-block-text">
                            Измените пароль в <a href="/personal/">личном кабинете</a>, чтоб вы могли отслеживать статус своего заказа и уровень бонусной программы
                        </span>
                        <img class="help-img" src="<?=SITE_TEMPLATE_PATH?>/img/authHelp.png" alt="help">
                    </div>
                <?php } ?>
            </div>
            <div class="success-second-block-wrapper">
                <div class="default-success-wrapper operator-will-call<?=$countOrder > 1 ? ' no-auth' : ''?>">
                    <div class="operator-will-call-img-wrap">
                        <img class="operator-img" src="<?=SITE_TEMPLATE_PATH?>/img/operator.png" alt="operator">
                    </div>
                    <span class="operator-will-call-text">
                        Через некоторое время после оплаты с вами <b>созвоняться наши операторы</b> для подтверждения заказа
                    </span>
                </div>
                <?php if ($countOrder == 1) { ?>
                    <div class="default-success-wrapper check-mail">
                        <span class="success-block-title">
                            После оплаты заказа проверьте свою почту.
                        </span>
                        <span class="success-block-text">
                            Мы отправим вам письмо подтверждающее успешное завершение оформления заказа
                            (Если письмо не пришло пожалуйста проверьте папку СПАМ)
                        </span>
                        <div class="check-mail-img-wrap">
                            <img class="mail-img" src="<?=SITE_TEMPLATE_PATH?>/img/mailOpen.png" alt="help">
                            <img class="mail-img" src="<?=SITE_TEMPLATE_PATH?>/img/chatMsg.png" alt="help">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="success-button-wrapper only-pc">
            <button class="bttn-pay pay-button" data-order-id="<?= $orderId ?>">Оплатить заказ</button>
        </div>
        <div class="success-block-attention-wrapper">
            <div class="default-success-wrapper attention-block">
                <b style="display: contents;">Внимание!</b> <br>
                Если вы не оплатите заказ в течении <b style="display: contents;">3 дней</b>, он будет автоматически удален
                Это максимальный срок резервирования, их может заказать другой человек
            </div>
        </div>
<?php
    }
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
