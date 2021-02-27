<?
define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа!");
$APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа!");
$APPLICATION->SetTitle("Спасибо за оформление заказа");
?>

    <div class="page-massage">
        Спасибо за оплату заказа,<br>менеджер свяжется с вами в ближайшее время.
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>