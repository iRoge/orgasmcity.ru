<?php
use Bitrix\Main\FileTable;
use Bitrix\Sale\Order;
use Qsoft\Helpers\PriceUtils;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class OrgasmCityFeedbackFormComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);

        return $arParams;
    }

    public function executeComponent()
    {
        if (isset($_POST['SUBMIT'])) {
            $this->processPost();
        }
        $this->includeComponentTemplate();
    }

    private function processPost()
    {
        $errors = [];
        if (!$_POST['NAME']) {
            $errors[] = 'Не заполненно поле "Ваше имя"';
        } else {
            $name = trim($_POST['NAME']);
        }

        if (!$_POST['GENDER']) {
            $errors[] = 'Не заполненно поле "Ваш пол"';
        } else {
            $gender = $_POST['GENDER'];
        }

        if (!$_POST['SCORE']) {
            $errors[] = 'Не заполненно поле "Оценка нашего магазина"';
        } else {
            $score = trim($_POST['SCORE']);
        }

        if (!$_POST['FEEDBACK_TEXT']) {
            $errors[] = 'Не заполненно поле "Текст отзыва"';
        } else {
            $feedBackText = trim($_POST['FEEDBACK_TEXT']);
        }

        $hasOrder = $_POST['HAS_ORDER'];
        if ($hasOrder) {
            $orderId = null;
            if (!$_POST['ORDER_ID']) {
                $errors[] = 'Не заполненно поле "Ваше имя"';
            } else {
                $orderId = trim($_POST['ORDER_ID']);
            }
            if ($orderId) {
                $order = Order::load($orderId);
                if (!$order) {
                    $errors[] = 'Указанный email не соответствует email в заказе # ' . $orderId;
                } else {
                    if (!$_POST['ORDER_EMAIL']) {
                        $errors[] = 'Не заполненно поле "Оценка нашего магазина"';
                    } else {
                        $orderEmail = trim(strtolower($_POST['ORDER_EMAIL']));
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
                'DETAIL_PICTURE' => isset($_FILES['PHOTO']) ? $_FILES['PHOTO'] : '',
            ]);
            $props = [];
            $props['SCORE'] = $scores[$score];
            $props['GENDER'] = $genders[$gender];

            CIBlockElement::SetPropertyValuesEx($feedbackID, IBLOCK_FEEDBACK, $props);
            $this->arResult['SUCCESS'] = true;
            $this->arResult['HAS_ORDER'] = $hasOrder;
        } else {
            $this->arResult['ERRORS'] = $errors;
        }
    }
}
