<?php

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Гарантия анонимности");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Гарантия анонимности в городе оргазма. Мы трепетно относимся к каждому клиенту, поэтому никто не узнает о вашей покупке в нашем магазине. Каждый товар мы тщательно упаковываем в темный непросвечиваемый пакет");
$APPLICATION->SetPageProperty("title", 'Гарантия анонимности. Город Оргазма');
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
        <div class="zagolovok-background">
            <h1 class="zagolovok"><? $APPLICATION->ShowTitle(false); ?></h1>
        </div>
    </div>
    <div class="info-page-wrapper">
        <div class="info-page-row">
            <div class="info-page-element-wrapper">
                <img src="1.png" alt="Личная информация при оформлении заказа 1">
            </div>
            <div class="info-page-element-wrapper">
                <img src="2.png" alt="Личная информация при оформлении заказа 2">
            </div>
        </div>
        <div class="info-page-row">
            <div class="info-page-element-wrapper">
                <img src="3.png" alt="Личная информация при оформлении заказа 3">
            </div>
            <div class="info-page-element-wrapper">
                <img src="4.png" alt="Абсолютно вся информация при заполнении анкеты является строго конфиденциальной и мы не в праве разглашать ее третьим лицам.
Мы всегда стоим на страже каждого своего клиента.">
            </div>
        </div>
    </div>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>