<?php

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Гарантия низкой цены");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "В городе оргазма вы гарантировано купите по самой низкой цене товары для взрослых: вибраторы, фаллоимитаторы, мастурбаторы, дилдо, члены, БДСМ игрушки, товары для взрослых, фистинг");
$APPLICATION->SetPageProperty("title", 'Гарантия низкой цены. Город Оргазма');
Asset::getInstance()->addCss('/local/templates/respect/infopageStyles.css');
?>
    <div class="main">
        <? $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "",
            array(
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "0"
            )
        ); ?>
        <div class="zagolovok-wrapper">
            <div class="zagolovok-background"><h1 class="zagolovok"><? $APPLICATION->ShowTitle(false); ?></h1></div>
        </div>
        <div class="info-page-wrapper">
            <div class="info-page-row">
                <div class="info-page-element-wrapper">
                    <img src="1.png" alt="Мы постоянно занимаемся анализом рынка конкуренции среди интим магазинов и регулярно меняем цены для того, чтобы вы покупали качественный товар по самой низкой цене.
">
                </div>
                <div class="info-page-element-wrapper">
                    <img src="2.png" alt="Аналитика рынка это непросто, но мы справляемся!">
                </div>
            </div>
            <div class="info-page-row">
                <div class="info-page-element-wrapper">
                    <img src="3.png" alt="Мы трепетно относимся к каждому клиенту, поэтому в нашем магазине представлена исключительно качественная и сертифицировання продукция. ">
                </div>
                <div class="info-page-element-wrapper">
                    <img src="4.png" alt="Мы гарантированно продадим вам любой товар по самой низкой цене среди всех юридически честных онлайн интим магазинов в России. Юридически честные магазины - это магазины, которые по требованию честно предоставляют любые документы товарной сертификации и указывают юридическое лицо и адрес у себя на сайте. ">
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>