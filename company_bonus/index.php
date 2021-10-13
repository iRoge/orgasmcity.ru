<?php

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Бонусная программа");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о бонусной программе в городе оргазма. За каждый заказ вы будете получать накопительную скидку в процентах от стоимости будущих покупок");
$APPLICATION->SetPageProperty("title", 'Бонусная программа. Город Оргазма');
$APPLICATION->SetAdditionalCss("/local/templates/respect/css/application.css");
Asset::getInstance()->addCss('/local/templates/respect/infopageStyles.css');
?>
<div class="main">
    <?php $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "",
        array(
            "PATH" => "",
            "SITE_ID" => "s1",
            "START_FROM" => "0"
        )
    ); ?>
    <div class="zagolovok-wrapper">
        <div class="zagolovok-background">
            <h1 class="zagolovok"><?php $APPLICATION->ShowTitle(false); ?></h1>
        </div>
    </div>
    <div class="info-page-wrapper">
        <div class="info-page-row">
            <div class="info-page-element-wrapper">
                <img src="1.png" alt="Вы имеете возможность снизить цену благодаря нашей бонусной программе.
Регистрируйтесь на сайте и получайте постоянную скидку за осуществление покупок

Вашу текущую общую сумму покупок и размер скидки вы можете посмотреть в личном кабинете.
Все скидки автоматически суммируются и показываются в каталоге.
">
            </div>
            <div class="info-page-element-wrapper">
                <img src="2.png" alt="Получайте скидку за накопленные суммарные покупки">
            </div>
        </div>
    </div>
</div>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
