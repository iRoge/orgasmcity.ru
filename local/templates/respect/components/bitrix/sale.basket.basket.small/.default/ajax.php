<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


\Bitrix\Main\Loader::includeModule('sale');

$iCount = 0;
$iFUser = intval(CSaleBasket::GetBasketUserID(true));

if ($iFUser > 0) {
    $iCount = \Bitrix\Sale\Internals\BasketTable::getCount([
        'FUSER_ID' => $iFUser,
        'LID' => SITE_ID,
        'ORDER_ID' => 'NULL'
    ]);
}

header('Content-type: application/json');
echo json_encode(['COUNT' => $iCount]);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');