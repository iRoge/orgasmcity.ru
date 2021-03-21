<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");
define('NOT_NEED_H1', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("ОШИБКА 404");
?>
<div class="tt-page404">
	<h1 class="tt-title">ОШИБКА 404</h1>
	<p>Похоже, что такой страницы не существует на сайте!</p>
	<a href="/" class="btn">ВЕРНУТЬСЯ НА ГЛАВНУЮ</a>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>