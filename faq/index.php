<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Вопрос-ответ");
?>

<?
$APPLICATION->IncludeComponent(
	"qsoft:infopage",
	"",
	array(
		"IBLOCK_CODE" => 'FAQ',
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
	),
	false
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>