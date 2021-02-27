<?
define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Спасибо за оформление заказа!");
$APPLICATION->SetPageProperty("description", "Спасибо за оформление заказа!");
$APPLICATION->SetTitle("Спасибо за оформление заказа");
?>

    <div class="page-massage">
        Оплата заказа завершена с ошибкой,
        <br>
        свяжитесь с менеджером магазина и мы обязательно исправим это недоразумение.
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>