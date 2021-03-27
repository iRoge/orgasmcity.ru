<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"]) > 0) {
    LocalRedirect($backurl);
}

$APPLICATION->SetTitle("Авторизация");
?>
<style>
    .auth-ok p {
        margin: 10px 0;
        text-align: center;
    }
</style>
<div class="auth-ok">
    <p>Вы зарегистрированы и успешно авторизовались.</p>
    <p><a href="<?= SITE_DIR ?>">Вернуться на главную страницу</a></p>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>