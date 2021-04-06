<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


$sAction = htmlentities(trim($_REQUEST['action']));
$arResult = [
    'STATUS' => 'ERROR',
    'COUNT' => []
];

CBitrixComponent::includeComponentClass('likee:basket');
$obBasket = new LikeeBasket();

if ($sAction == 'ADD2BASKET') {
    $arID = $_REQUEST['id'];

    if (!is_array($arID))
        $arID = [$arID];

    \Bitrix\Main\Type\Collection::normalizeArrayValuesByInt($arID);

    if ($arID) {
        //свойства для добавления в корзину
        $arSuccess = [];
        foreach ($arID as $ID) {
            $arSuccess[] = $obBasket->updateQuantity($ID, 1);
        }

        $arResult['STATUS'] = count(array_filter($arSuccess)) == count($arID) ? 'OK' : 'ERROR';
        $arResult['ITEM_IDS']=$arID;

        if ('ERROR' == $arResult['STATUS']) {
            $error = $APPLICATION->GetException();
            $arResult['MESSAGE'] = $error ? $error->msg : 'ERROR';
        }
    }
} elseif ($sAction == 'get_count') {
    $arResult['STATUS'] = 'OK';
}

if (\Likee\Site\User::isPartner() || $sAction == 'get_count') {
    $arBasket = $obBasket->loadBasket();

    foreach ($arBasket as $arItem) {
        $arResult['COUNT'][$arItem['PRODUCT_ID']] = $arItem['QUANTITY'];
    }
}

//header('Content type: application\json');
$APPLICATION->RestartBuffer();
echo json_encode($arResult);
$APPLICATION->FinalActions();
exit;