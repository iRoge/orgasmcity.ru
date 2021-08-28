<?php
use Bitrix\Main\FileTable;
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
            if (!$_POST['ORDER_ID']) {
                $errors[] = 'Не заполненно поле "Ваше имя"';
            } else {
                $orderId = trim($_POST['ORDER_ID']);
            }

            if (!$_POST['ORDER_PHONE']) {
                $errors[] = 'Не заполненно поле "Оценка нашего магазина"';
            } else {
                $orderPhone = trim($_POST['ORDER_PHONE']);
            }
        }

        if (empty($errors)) {
            $el = new CIBlockElement;
            $feedbackID = $el->Add([
                'IBLOCK_ID' => IBLOCK_FEEDBACK,
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'DETAIL_TEXT' => $feedBackText,
                'DETAIL_PICTURE' => isset($_FILES['PHOTO']) ? $_FILES['PHOTO'] : ''
            ]);
            $props = [];
            $props['SCORE'] = $score;

            CIBlockElement::SetPropertyValuesEx($feedbackID, IBLOCK_FEEDBACK, $props);
        } else {
            $this->arResult['ERRORS'] = $errors;
        }
    }
}
