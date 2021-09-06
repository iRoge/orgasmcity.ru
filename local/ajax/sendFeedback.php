<?php

use Bitrix\Sale\Order;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$arResult = [];

$errors = [];
if (!$_GET['NAME']) {
    $errors[] = 'Не заполненно поле "Ваше имя"';
} else {
    $name = trim($_GET['NAME']);
}

if (!$_GET['GENDER']) {
    $errors[] = 'Не заполненно поле "Ваш пол"';
} else {
    $gender = $_GET['GENDER'];
}

if (!$_GET['SCORE']) {
    $errors[] = 'Не заполненно поле "Оценка"';
} else {
    $score = trim($_GET['SCORE']);
}

if (!$_GET['FEEDBACK_TEXT']) {
    $errors[] = 'Не заполненно поле "Текст отзыва"';
} else {
    $feedBackText = trim($_GET['FEEDBACK_TEXT']);
}

$hasOrder = $_GET['HAS_ORDER'];
if ($hasOrder) {
    $orderId = null;
    if (!$_GET['ORDER_ID']) {
        $errors[] = 'Не заполненно поле "Ваше имя"';
    } else {
        $orderId = trim($_GET['ORDER_ID']);
    }
    if ($orderId) {
        $order = Order::load($orderId);
        if (!$order) {
            $errors[] = 'Указанный email не соответствует email в заказе # ' . $orderId;
        } else {
            if (!$_GET['ORDER_EMAIL']) {
                $errors[] = 'Не заполненно поле "Оценка нашего магазина"';
            } else {
                $orderEmail = trim(strtolower($_GET['ORDER_EMAIL']));
                if (!filter_var($orderEmail, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Не верно указан email в заказе';
                }
                $propertyCollection = $order->getPropertyCollection();
                $realEmail = strtolower($propertyCollection->getUserEmail()->getValue());
                if ($realEmail != $orderEmail) {
                    $errors[] = 'Указанный email не соответствует email в заказе # ' . $orderId;
                }
            }
        }
    }
}

if (empty($errors)) {
    $property_enums = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => IBLOCK_FEEDBACK, "CODE" => "SCORE"]);
    while($enum_fields = $property_enums->GetNext(true, false)) {
        $scores[$enum_fields["VALUE"]] = $enum_fields["ID"];
    }

    $property_enums = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => IBLOCK_FEEDBACK, "CODE" => "GENDER"]);
    while($enum_fields = $property_enums->GetNext(true, false)) {
        $genders[$enum_fields["VALUE"]] = $enum_fields["ID"];
    }

    $el = new CIBlockElement;
    $feedbackID = $el->Add([
        'IBLOCK_ID' => IBLOCK_FEEDBACK,
        'NAME' => $name,
        'ACTIVE' => 'Y',
        'DETAIL_TEXT' => $feedBackText,
    ]);
    $props = [];
    $props['SCORE'] = $scores[$score];
    $props['GENDER'] = $genders[$gender];
    $props['PRODUCT_ID'] = isset($_GET['PRODUCT_ID']) ? $_GET['PRODUCT_ID'] : null;

    CIBlockElement::SetPropertyValuesEx($feedbackID, IBLOCK_FEEDBACK, $props);
    $arResult['SUCCESS'] = true;
    $arResult['HAS_ORDER'] = $hasOrder;
} else {
    $arResult['SUCCESS'] = false;
    $arResult['ERRORS'] = $errors;
}


echo json_encode($arResult);