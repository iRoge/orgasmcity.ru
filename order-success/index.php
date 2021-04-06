<?

use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;

global $LOCATION;

define('HIDE_TITLE', true);
define('SBERBANK_PAY_SYSTEM_ID', 16);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$orderId = intval($_GET["orderId"]);
$orderType = $_GET["orderType"];
$branchId = $LOCATION->getUserShowcase();
if (!$orderId) {
    Functions::abort404();
} else {
    $APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Спасибо за оформление заказа");
    $GLOBALS['ORDER_SUCCESS']['ID'] = $orderId; // для Criteo

    $order = Order::load($orderId);
    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment) {
        $orderPayments[$payment->getField('PAY_SYSTEM_ID')] = $payment;
    }

    if (!empty($orderPayments[SBERBANK_PAY_SYSTEM_ID])) {
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

        if ($orderType == 'RESERVATION') {
            $sucMessage = Option::get("respect", "order_success_text_reservation", 'Менеджер свяжется с вами в ближайшее время.');
        }

        if (!$sucMessage) {
            $sucMessage = 'Менеджер свяжется с вами в ближайшее время.';
        }
        ?>
        <div class="page-massage page__message-order">
            Спасибо,<br>
            номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            <?= $sucMessage ?>
            <!-- RU-20 срочно скрыли кнопку
            <br>
            <br>
            <a class="bttn bttn--present"
               target="_blank"
               title="Получить подарок"
               href="https://xchange.lydforce.com/?utm_source=fashion_respectshoes&amp;utm_medium=web&amp;utm_campaign=lydforce_button">
                Получить подарок
            </a>
            -->
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

            //для Criteo
            $GLOBALS['ORDER_SUCCESS']['ITEMS'][] = [
                'id' => sprintf('%s-%s', $arProduct['PRODUCT_ID'], $branchId),
                'price' => $arProduct['PRICE'],
                'quantity' => $arProduct['QUANTITY']
            ];
            //для RTBHouse
            $GLOBALS['ORDER_SUCCESS_RTBHOUSE']['ITEMS'][] = [
                'id' => sprintf('%s-%s', $propertyValues['PRODUCT_ID']['VALUE'], $branchId),
                'price' => $arProduct['PRICE'],
                'quantity' => $arProduct['QUANTITY']
            ];
        }
        if (env("useMetric", true)) {
            unset($_SESSION['NEW_ORDER_ID']);
            // metrika ecommerce
            $arEcommerceOrderBasket = [];
            $arFlocktoryOrderBasket = [];
            $sFlocktorySpotValue = 'order';
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
                // сохраняем данные по товару
                $arEcommerceOrderBasket[] = [
                    'id' => $arProduct['PRODUCT_ID'],
                    'name' => htmlspecialchars($arProduct['NAME']),
                    'price' => ((int)$arProduct['PRICE']),
                    'variant' => $productSize,
                    'quantity' => $arProduct['QUANTITY']
                ];

                $arEcommerceOrderBasketCriteo[] = [
                    'id' => sprintf('%s-%s', $arProduct['PRODUCT_ID'], $branchId),
                    'name' => htmlspecialchars($arProduct['NAME']),
                    'price' => ((int)$arProduct['PRICE']),
                    'variant' => $productSize,
                    'quantity' => $arProduct['QUANTITY']
                ];

                $arFlocktoryOrderBasket[] = '{id: ' . (int)$arProduct['PRODUCT_ID'] . ', ' .
                    'title: \'' . htmlspecialchars($arProduct['NAME']) . '\', ' .
                    'price: ' . (int)$arProduct['PRICE'] . ', ' .
                    'image: \'' . $detailPicture . '\', ' .
                    'count: ' . (int)$arProduct['QUANTITY'] . '}';
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
            $sFlocktorySpotValue = strtolower($orderType);
            // данные пользователя для flocktory
            $name = $propertyCollection->getPayerName()->getValue();
            if ('order' != $sFlocktorySpotValue && $propertyCollection->getPhone()->getValue()) {
                $email = preg_replace('/\D+/i', '', $propertyCollection->getPhone()->getValue()) . '@unknown.email';
            } elseif ($propertyCollection->getUserEmail()->getValue()) {
                $email = $propertyCollection->getUserEmail()->getValue();
            } else {
                $email = $order->getUserId() . '@unknown.email';
            } ?>
            <script type="text/javascript">
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    "ecommerce": {
                        "purchase": {
                            "actionField": {
                                "id": "<?= $order->getId() ?>"
                            },
                            "products": <?= CUtil::PhpToJSObject($arEcommerceOrderBasket) ?>
                        }
                    }
                });
                gtag('event', 'work', {'event_category': 'global_new_order'});
                window.respectMetrkiaGoal = window.respectMetrkiaGoal || [];
                window.respectMetrkiaGoal.push('global_new_order');
                <? foreach ($goals as $goalName) : ?>
                window.respectMetrkiaGoal.push('<?= $goalName ?>');
                <? endforeach; ?>
                window.flocktory = window.flocktory || [];
                window.flocktory.push(['postcheckout', {
                    user: {
                        <? if (!empty($name)) : ?>
                        name: '<?= $name ?>',
                        <? endif ?>
                        email: '<?= $email ?>'
                    },
                    order: {
                        id: <?= (int)$order->getId(); ?>,
                        price: <?= (int)$order->getPrice(); ?>,
                        custom_field: '<?= $sFlocktorySpotValue; ?>',
                        items: <?= '[' . implode(', ', $arFlocktoryOrderBasket) . ']'; ?>
                    },
                    spot: '<?= $sFlocktorySpotValue; ?>'
                }]);
                <? if (!empty($_SESSION['CRITEO_NEW_ORDER_ID'])) :?>
                var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                    <? unset($_SESSION['CRITEO_NEW_ORDER_ID']);?>
                <? endif;?>
            </script>
        <? } ?>
    <? } elseif ($orderType == 'prepayment_s1') { ?>
        <div class="page-massage page__message-order">
            Номер вашего заказа <b>№ <?= $orderId ?></b>.<br>
            <?= Option::get("respect", "order_success_text", ""); ?>
            <p>Сумма к оплате: <b><?= number_format($order->getPrice(), 0, '', ' ') . ' p.' ?></b></p>
            <a href="#" class="bttn pay-button" data-order-id="<?= $orderId ?>">Оплатить</a>
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

            //для Criteo
            $GLOBALS['ORDER_SUCCESS']['ITEMS'][] = [
                'id' => sprintf('%s-%s', $arProduct['PRODUCT_ID'], $branchId),
                'price' => $arProduct['PRICE'],
                'quantity' => $arProduct['QUANTITY']
            ];
            //для RTBHouse
            $GLOBALS['ORDER_SUCCESS_RTBHOUSE']['ITEMS'][] = [
                'id' => sprintf('%s-%s', $propertyValues['PRODUCT_ID']['VALUE'], $branchId),
                'price' => $arProduct['PRICE'],
                'quantity' => $arProduct['QUANTITY']
            ];
        }
        ?>
        <script type="text/javascript">
            // RetailRocket
            (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                try {
                    rrApi.order({
                        "transaction": <?=$orderId?>,
                        "items": <?= '[' . implode(', ', $arRRItems) . ']'; ?>,
                    });
                } catch (e) {
                }
            });
        </script>
        <?
    }
} ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
