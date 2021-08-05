<?

use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;

define('HIDE_TITLE', true);
define('TINKOFF_PAY_SYSTEM_ID', 10);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$orderId = intval($_GET["orderId"]);
$orderType = $_GET["orderType"];
if (!$orderId) {
    Functions::abort404();
} else {
    $APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Спасибо за оформление заказа");

    $order = Order::load($orderId);
    if (!$order) {
        Functions::abort404();
    }
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
        $propertyCollection = $order->getPropertyCollection();

        foreach ($propertyCollection as $orderProperty) {
            if ('ORDER_TYPE' == $orderProperty->getField('CODE')) {
                $orderType = $orderProperty->getValue();
                break;
            }
        }
        ?>
        <div class="page-massage page__message-order">
            Спасибо,<br>
            номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            С вами созвонятся операторы для подтверждения заказа.<br>
            Отследить статус заказа вы можете <a href="/personal/orders/">на странице</a>.
            Сейчас вы авторизованы автоматически, но вам нужно поменять пароль в <a href="/personal/">личном кабинете</a>, чтобы вы могли зайти в следующий раз
            <br>
        </div>
        <?php
        $arRRItems = [];
        foreach ($order->getBasket()->getBasketItems() as $basketItem) {
            $arProduct = $basketItem->getFieldValues();
            $productSize = '';
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            $propertyValues = $basketPropertyCollection->getPropertyValues();

            unset($arProductElement);
        }
        if (env("useMetric", true)) {
            unset($_SESSION['NEW_ORDER_ID']);
            // metrika ecommerce
            $arEcommerceOrderBasket = [];
            $price = (int)$order->getPrice();

            // информация по корзине
            foreach ($order->getBasket()->getBasketItems() as $basketItem) {
                $arProduct = $basketItem->getFieldValues();
                $productSize = '';
                $basketPropertyCollection = $basketItem->getPropertyCollection();
                foreach ($basketPropertyCollection as $basketProperty) {
                    $arProp = $basketProperty->getFieldValues();
                    if ($arProp['CODE'] == 'SIZE') {
                        $productSize = $arProp['VALUE'];
                        break;
                    }
                }
                // получаем картинку
                $detailPicture = '';
                $arProductElement = CIBlockElement::GetList(
                    array(),
                    array(
                        "ID" => $arProduct['PRODUCT_ID'],
                        "IBLOCK_ID" => IBLOCK_OFFERS,
                    ),
                    false,
                    false,
                    array(
                        'ID',
                        'IBLOCK_ID',
                        'PROPERTY_CML2_LINK.DETAIL_PICTURE'
                    )
                )->GetNext();
                if ($arProductElement && !empty($arProductElement['PROPERTY_CML2_LINK_DETAIL_PICTURE'])) {
                    $detailPicture = 'https://respect-shoes.ru' . CFile::GetPath($arProductElement['PROPERTY_CML2_LINK_DETAIL_PICTURE']);
                }
                unset($arProductElement);
            }
            // цели для метрики
            $goals = [];
            switch ($orderType) {
                case "RESERVATION":
                    $goals[] = "submit_reserved";
                    break;
                case "ONE_CLICK":
                    $goals[] = "submit_fast_order";
                    break;
                default:
                    $goals[] = "submit_cart_order";
            }

            $name = $propertyCollection->getPayerName()->getValue();
            if ($propertyCollection->getPhone()->getValue()) {
                $email = preg_replace('/\D+/i', '', $propertyCollection->getPhone()->getValue()) . '@unknown.email';
            } elseif ($propertyCollection->getUserEmail()->getValue()) {
                $email = $propertyCollection->getUserEmail()->getValue();
            } else {
                $email = $order->getUserId() . '@unknown.email';
            } ?>
        <? } ?>
    <? } elseif ($orderType == 'prepayment_s1') { ?>
        <div class="page-massage page__message-order">
            Номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            <?= Option::get("respect", "order_success_text", ""); ?>
            <p>Сумма к оплате: <b><?= number_format($order->getPrice(), 0, '', ' ') . ' p.' ?></b></p>
            Сейчас вы авторизованы автоматически, но вам нужно поменять пароль в <a href="/personal/">личном кабинете</a>, чтобы вы могли зайти в следующий раз
            <br>
            <button class="bttn pay-button" data-order-id="<?= $orderId ?>">Оплатить</button>
        </div>
        <?
        $arRRItems = [];

        foreach ($order->getBasket()->getBasketItems() as $basketItem) {
            $arProduct = $basketItem->getFieldValues();
            $productSize = '';
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            $propertyValues = $basketPropertyCollection->getPropertyValues();
            unset($arProductElement);
            // сохраняем данные по товару
            $arRRItems[] = '{id: ' . (int)$arProduct['PRODUCT_ID'] . ', ' .
                'qnt: ' . (int)$arProduct['QUANTITY'] . ', ' .
                'price: ' . (int)$arProduct['PRICE'] . '}';
        }
    }
} ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
