<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Личный кабинет');
?>
<?$APPLICATION->IncludeComponent('qsoft:sailplay.bonuses.history', '', []);?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
