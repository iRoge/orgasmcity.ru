<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$statusResult = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
   'order' => array('STATUS.SORT'=>'ASC'),
   'filter' => array('LID'=>LANGUAGE_ID),
   'select' => array('STATUS_ID','NAME','DESCRIPTION'),
));

while ($status=$statusResult->fetch()) {
    $arResult['INFO']['STATUS'][$status['STATUS_ID']]['DESCRIPTION']=$status['DESCRIPTION'];
}
