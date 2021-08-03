<?php
use Qsoft\Helpers\SubscribeManager;
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$email = $_POST['EMAIL'];
try {
    $mailing = SubscribeManager::getSubscriberByEmail($email);
    if (!$mailing) {
        $mailingID = SubscribeManager::addSubscriber($email);
    } else {
        SubscribeManager::updateSubscriber($mailing['ID'], false, true);
        $mailingID = $mailing['ID'];
    }

    if ($GLOBALS['device_type'] == 'mobile') {
        $arResult['MESSAGE'] = "А вот и ваш промокод. Примените его в корзине";
    } else {
        $arResult['MESSAGE'] = "А вот и ваш сюрприз! Введите промокод при оформлении заказа и узнайте свою скидку ;-)
            Что-бы не забыть, ваш промокод отправлен на почту";
    }

    $dateEnd = (new \Bitrix\Main\Type\DateTime())->add('+7 days');

    do {
        $coupon = generateCoupon(4);
    } while (\Bitrix\Catalog\DiscountCouponTable::isExist($coupon));

    $couponsResult = \Bitrix\Sale\Internals\DiscountCouponTable::add(
        [
            'ACTIVE_TO' => $dateEnd,
            'DISCOUNT_ID' => 4,
            'COUPON' => $coupon,
            'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
            'MAX_USE' => 1,
            'USER_ID' => 0,
        ]
    );
    $fields = [
        'PROMOCODE' => $coupon,
        'SUBSCRIBER_ID' => $mailingID,
        'EMAIL' => $email,
    ];
    $html = file_get_contents(__DIR__ . '/surprise.html');
    $subject = 'Как и обещали. Ваш сюрприз!';
    $body = Functions::insertFields($html, $fields);
    Functions::sendMarketingMail($email, $subject, $body, $mailingID);
    $arResult['PROMOCODE'] = $coupon;
} catch (Exception $e) {
    $arResult['STATUS'] = 0;
    $arResult['MESSAGE'] = "Упс, кажется произошла ошибка. Обратитесь в чат поддержки";
    orgasm_logger($e->getMessage(), 'error.log', '/local/logs/', true);
}

function generateCoupon($strength)
{
    $permittedChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $inputLength = strlen($permittedChars);
    $randomString = '';
    for($i = 0; $i < $strength; $i++) {
        $randomCharacter = $permittedChars[mt_rand(0, $inputLength - 1)];
        $randomString .= $randomCharacter;
    }
    return $randomString;
}
?>
<div class="subscribe-message" <?=$arResult['STATUS'] ? '' : 'text-danger'?>>
    <b><?=$arResult['MESSAGE']; ?></b>
    <?php if ($arResult['PROMOCODE']) { ?>
        <div class="promocode-wrapper">
            <?=$arResult['PROMOCODE']?>
        </div>
    <?php } ?>
</div>