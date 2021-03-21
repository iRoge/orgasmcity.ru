<?
if (isset($_REQUEST["register"]) && $_REQUEST["register"] == "yes")
	Header('Location:/register/');
define("NEED_AUTH", true);
define('NOT_NEED_H1', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Вход в аккаунт");
?>
<div class="tt-empty-cart">
	<span class="tt-icon icon-f-76"></span>
	<h1 class="tt-title">ВХОД В АККАУНТ</h1>
	<p>ВЫ ЗАРЕГИСТРИРОВАНЫ И УСПЕШНО АВТОРИЗОВАЛИСЬ</p>
	<a href="<?=SITE_DIR?>" class="btn">ПЕРЕЙТИ НА ГЛАВНУЮ</a>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>