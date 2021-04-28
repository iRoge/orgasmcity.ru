<?php

use Bitrix\Main\Type\DateTime as DateTimeAlias;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Sale\Internals\OrderCouponsTable;
use Qsoft\Logger\Logger;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Проверка промокодов');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

if ($_REQUEST['CHECK']) {
    $fileName = 'check_coupons_' . date('d.m.Y_H:i:s') . '.txt';
    $logger = new Logger($fileName);
    $logger->writeLogMessage('Начало проверки: ' . date('d.m.Y_H:i:s'));
    $arMess = $arErrors = $arCoupons = []; //Массив для вывода логов на экран

    $res = OrderCouponsTable::getList([
        'select' => ['COUPON_ID', 'TYPE'],
    ]);

    while ($arRes = $res->Fetch()) {
        $arCoupons[$arRes['COUPON_ID']]['TYPE'] = $arRes['TYPE'];
        $arCoupons[$arRes['COUPON_ID']]['COUNT'] += 1;
    }

    $arCouponsIds = array_keys($arCoupons);

    $res = DiscountCouponTable::getList([
        'filter' => ['@ID' => $arCouponsIds],
        'select' => ['ID', 'USE_COUNT', 'COUPON', 'ACTIVE_TO']
    ]);

    $currentTime = new DateTimeAlias();
    $need_date = $currentTime;
    $need_date->add('1 day');

    while ($arRes = $res->fetch()) {
        $diff = $arCoupons[$arRes['ID']]['COUNT'] - $arRes['USE_COUNT'];
        if ($diff > 0) {
            if (!empty($arRes['ACTIVE_TO'])) {
                $old_date = $arRes['ACTIVE_TO'];
                DiscountCouponTable::update($arRes['ID'], ['ACTIVE_TO' => $need_date]);
            }
            for ($i=1; $i<=$diff; $i++) {
                $arTmpResult = DiscountCouponTable::saveApplied($arRes['ID'], 1, $currentTime);
                if (!empty($arTmpResult['DEACTIVATE'])) {
                    $arResult['DEACTIVATE']['COUPONS'][$arRes['ID']]['COUPON'] = $arRes['COUPON'];
                    $arResult['DEACTIVATE']['COUPONS'][$arRes['ID']]['COUNT'] += 1;
                } elseif (!empty($arTmpResult['LIMITED'])) {
                    $arResult['LIMITED']['COUPONS'][$arRes['ID']]['COUPON'] = $arRes['COUPON'];
                    $arResult['LIMITED']['COUPONS'][$arRes['ID']]['COUNT'] += 1;
                } elseif (!empty($arTmpResult['INCREMENT'])) {
                    $arResult['INCREMENT']['COUPONS'][$arRes['ID']]['COUPON'] = $arRes['COUPON'];
                    $arResult['INCREMENT']['COUPONS'][$arRes['ID']]['COUNT'] += 1;
                }
            }

            if (!empty($old_date)) {
                DiscountCouponTable::update($arRes['ID'], ['ACTIVE_TO' =>  $old_date]);
                $old_date = '';
            }
        }
    }

    //Сборка читаемого отчета:
    if ($arResult['DEACTIVATE']) {
        $logger->writeLogMessage(sprintf('Деактивированно элементов: %s', count($arResult['DEACTIVATE']['COUPONS'])));
        $arResult['DEACTIVATE']['INFO'] .= sprintf('Деактивированно элементов: %s <br>', count($arResult['DEACTIVATE']['COUPONS']));
        foreach ($arResult['DEACTIVATE']['COUPONS'] as $idCoupon => $arCoupon) {
            $logger->writeLogMessage(sprintf('%s (%s)', $arCoupon['COUPON'], $idCoupon));
            $arResult['DEACTIVATE']['INFO'] .= sprintf('%s (%s)<br>', $arCoupon['COUPON'], $idCoupon);
        }
    }

    if ($arResult['LIMITED']) {
        $logger->writeLogMessage(sprintf('Достигло лимита элементов: %s', count($arResult['LIMITED']['COUPONS'])));
        $arResult['LIMITED']['INFO'] .= sprintf('Достигло лимита: %s <br>', count($arResult['LIMITED']['COUPONS']));
        foreach ($arResult['LIMITED']['COUPONS'] as $idCoupon => $arCoupon) {
            $logger->writeLogMessage(sprintf('%s (%s)', $arCoupon['COUPON'], $idCoupon));
            $arResult['LIMITED']['INFO'] .= sprintf('%s (%s)<br>', $arCoupon['COUPON'], $idCoupon);
        }
    }

    if ($arResult['INCREMENT']) {
        $logger->writeLogMessage(sprintf('Обновлено элементов: %s', count($arResult['INCREMENT']['COUPONS'])));
        $arResult['INCREMENT']['INFO'] .= sprintf('Обновлено элементов: %s <br>', count($arResult['INCREMENT']['COUPONS']));
        foreach ($arResult['INCREMENT']['COUPONS'] as $idCoupon => $arCoupon) {
            echo sprintf('%s (%s) - %s раз', $arCoupon['COUPON'], $idCoupon, $arCoupon['COUNT']);
            $logger->writeLogMessage(sprintf('%s (%s)', $arCoupon['COUPON'], $idCoupon));
            $arResult['INCREMENT']['INFO'] .= sprintf('%s (%s)<br>', $arCoupon['COUPON'], $idCoupon);
        }
    }

    $logger->writeLogMessage('Окончание проверки: ' . date('d.m.Y_H:i:s'));
}


CAdminMessage::ShowMessage([
    'MESSAGE' => 'Скрипт осуществляет проверку промокодов в заказах. <br>
	 Если промокод существует то у него заполниться поле Дата использования. <br>
	  Для запуска нажмите кнопку Проверить.',
    'TYPE' => 'OK',
]);

?>
    <form method="POST">
        <input type="submit" value="Проверить" name="CHECK">
    </form>

<?
if (!empty($arResult['DEACTIVATE']['INFO'])) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => $arResult['DEACTIVATE']['INFO'],
        'TYPE' => 'OK',
    ]);
}

if (!empty($arResult['LIMITED']['INFO'])) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => '<br>', $arResult['LIMITED']['INFO'],
        'TYPE' => 'OK',
    ]);
}

if (!empty($arResult['INCREMENT']['INFO'])) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => $arResult['INCREMENT']['INFO'],
        'TYPE' => 'OK',
    ]);
}

require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
