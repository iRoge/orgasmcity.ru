<?

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem\Manager as PaymentManager;
use Bitrix\Main\Application;

define('HIDE_TITLE', true);
define('SBERBANK_PAY_SYSTEM_ID', 13);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$orderId = intval($_GET["orderId"]);
$orderType = $_GET["orderType"];
if (!$orderId) {
    Functions::abort404();
} else {
    $APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа");
    $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');
    $APPLICATION->SetTitle("Спасибо за оформление заказа");
    if ($orderType == 'default') {
        ?>
        <div class="page-massage page__message-order">
            Спасибо,<br>
            номер вашего заказа <b>#<?= $orderId ?></b>.<br>
            Менеджер свяжется с вами в ближайшее время.
            <br>
            <br>
            <a class="bttn bttn--present"
               target="_blank"
               title="Получить подарок"
               href="https://xchange.lydforce.com/?utm_source=fashion_respectshoes&amp;utm_medium=web&amp;utm_campaign=lydforce_button">
                Получить подарок
            </a>
        </div>
        <?
            $order = Order::load($orderId);
            $arRRItems = [];
        foreach ($order->getBasket()->getBasketItems() as $basketItem) {
            $arProduct = $basketItem->getFieldValues();
            $productSize = '';
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            unset($arProductElement);
            // сохраняем данные по товару
            $arRRItems[] = '{id: ' . (int) $arProduct['PRODUCT_ID'] . ', ' .
                'qnt: ' . (int) $arProduct['QUANTITY'] . ', ' .
                'price: ' . (int) $arProduct['PRICE'] . '}';
        }
        ?>
        <script type="text/javascript">
            // RetailRocket
            (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                try {
                    rrApi.order({
                        "transaction": <?=$orderId?>,
                        "items": <?= '[' . implode(', ', $arRRItems) . ']'; ?>,
                    });
                } catch(e) {}
            });
        </script>
        <?
        if (env("useMetric", true)) {
            unset($_SESSION['NEW_ORDER_ID']);
            // metrika ecommerce
            $arEcommerceOrderBasket = [];
            $arFlocktoryOrderBasket = [];
            $sFlocktorySpotValue = 'order';
            $price = (int)$order->getPrice();
            $propertyCollection = $order->getPropertyCollection();
            foreach ($propertyCollection as $orderProperty) {
                if ('ORDER_TYPE' == $orderProperty->getField('CODE')) {
                    $orderType = $orderProperty->getValue();
                    break;
                }
            }
            if ($orderType && $price) {
                // Include Segmento
                if ($orderType == "RESERVATION") {
                    Asset::getInstance()->addString('<script type="text/javascript">
                        var _rutarget = window._rutarget || [];
                        _rutarget.push({"event": "thankYou", "order_id": '.$orderId.', "conv_id": "takeinshop"});
                    </script>', false, AssetLocation::AFTER_JS);
                } else {
                    Asset::getInstance()->addString('<script type="text/javascript">
                        var _rutarget = window._rutarget || [];
                        _rutarget.push({"event": "thankYou", "order_id": '.$orderId.', "total_cost": '.$price.', "conv_id": "order"});
                    </script>', false, AssetLocation::AFTER_JS);
                }
                define("INC_SEGMENTO", true);
            }
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
                    $detailPicture = 'https://respect-shoes.ru'.CFile::GetPath($arProductElement['PROPERTY_CML2_LINK_DETAIL_PICTURE']);
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

                $arFlocktoryOrderBasket[] = '{id: ' . (int) $arProduct['PRODUCT_ID'] . ', ' .
                    'title: \'' . htmlspecialchars($arProduct['NAME']) . '\', ' .
                    'price: ' . (int) $arProduct['PRICE'] . ', ' .
                    'image: \'' . $detailPicture . '\', ' .
                    'count: ' . (int) $arProduct['QUANTITY'] . '}';
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
                $email = preg_replace('/\D+/i', '', $propertyCollection->getPhone()->getValue()).'@unknown.email';
            } elseif ($propertyCollection->getUserEmail()->getValue()) {
                $email = $propertyCollection->getUserEmail()->getValue();
            } else {
                $email = $order->getUserId().'@unknown.email';
            } ?>
            <script type="text/javascript">
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                "ecommerce": {
                    "purchase": {
                        "actionField": {
                            "id" : "<?= $order->getId() ?>"
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
                    id: <?= (int) $order->getId(); ?>,
                    price: <?= (int) $order->getPrice(); ?>,
                    custom_field: '<?= $sFlocktorySpotValue; ?>',
                    items: <?= '[' . implode(', ', $arFlocktoryOrderBasket) . ']'; ?>
                },
                spot:'<?= $sFlocktorySpotValue; ?>'
            }]);
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q = window.criteo_q || [];
            window.criteo_q.push(
                {event: "setAccount", account: 55655},
                {event: "setEmail", email: "<?= ($USER->IsAuthorized() ? md5($USER->GetEmail()) : '') ?>"},
                {event: "setSiteType", type: deviceType},
                {event: "trackTransaction", id: <?= $order->getId() ?>, item: <?= CUtil::PhpToJSObject($arEcommerceOrderBasket) ?>}
            );
            </script>
        <?}?>
    <?} elseif ($orderType == 'prepayment_s1') { ?>
        <div class="page-massage page__message-order">
            Номер вашего заказа <b>#<?= $orderId ?></b>.<br>
            <?= Bitrix\Main\Config\Option::get("respect", "order_success_text", "");?><br>
            <form method="get">
                <input type="text" hidden name="orderId" value="<?= $orderId ?>">
                <input type="text" hidden name="orderType" value="prepayment_s2">
                <button type="submit" class="bttn pay-button">Оплатить</button>
            </form>
        </div>
        <?
        $order = Order::load($orderId);
        $arRRItems = [];
        foreach ($order->getBasket()->getBasketItems() as $basketItem) {
            $arProduct = $basketItem->getFieldValues();
            $productSize = '';
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            unset($arProductElement);
            // сохраняем данные по товару
            $arRRItems[] = '{id: ' . (int) $arProduct['PRODUCT_ID'] . ', ' .
                'qnt: ' . (int) $arProduct['QUANTITY'] . ', ' .
                'price: ' . (int) $arProduct['PRICE'] . '}';
        }
        ?>
        <script type="text/javascript">
            // RetailRocket
            (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                try {
                    rrApi.order({
                        "transaction": <?=$orderId?>,
                        "items": <?= '[' . implode(', ', $arRRItems) . ']'; ?>,
                    });
                } catch(e) {}
            });
        </script>
    <?} elseif ($orderType == 'prepayment_s2') {
        $APPLICATION->SetPageProperty('NOT_SHOW_NAV_CHAIN', 'Y');

        try {
            if (!$orderId) {
                throw new Exception();
            }

            $order = Order::load($orderId);
            $payment = $order->getPaymentCollection()[0];

            // получаем оплаты с ограничениями
            $obPayment = $payment->getPaySystem();

            if (!in_array($obPayment->getField('CODE'), ONLINE_PAYMENT_CODES)) {
                throw new Exception();
            }

            $context = Application::getInstance()->getContext();

            $service = PaymentManager::getObjectById($obPayment->getField('ID'));
            $paymentInitialization = $service->initiatePay($payment, $context->getRequest());
            $paymentInitialization->getTemplate();
        } catch (Exception $exception) {
            Functions::abort404();
        }
    }
    ?>
<?}?>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
