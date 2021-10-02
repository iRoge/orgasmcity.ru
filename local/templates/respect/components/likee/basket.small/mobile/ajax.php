<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;

CBitrixComponent::includeComponentClass('likee:basket.small');
$obBasket = new  LikeeBasketSmallComponent();
header('Content-type: application/json');
echo json_encode(['COUNT' => $obBasket->getCount(), 'ITEMS' => $obBasket->getItems() ]);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');