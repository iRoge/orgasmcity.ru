<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>
<div class="tt-shopping-layout">
	<div class="tt-wrapper">
		<h6 class="tt-title">ИЗМЕНЕНИЕ ПЕРСОНАЛЬНЫХ ДАННЫХ</h6>
<?$APPLICATION->IncludeComponent("fire:main.profile", "opt", Array(
	),
	false
);?>
	</div>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>