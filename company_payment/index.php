<?php

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Способы оплаты");
$APPLICATION->SetPageProperty("title", 'Оплата. Город Оргазма');
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о способах оплаты в городе оргазма . В городе оргазма вы можете заказать и оплатить товары для взрослых одним из следующих способов: при получении товара или онлайн банковской картой на сайте");
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
                    <img src="1.png" alt="Оплата при получении заказа Заказ отправляется после его подтверждения оператору, без предоплаты. Оплатить заказ вы можете курьеру, в постамате, в пункте выдачи, или при самовывозе в кассу в зависимости от выбранной доставки при получении заказа наличными или банковской картой. ">
                </div>
                <div class="info-page-element-wrapper">
                    <img src="2.png" alt="Способы оплаты разные, а доставка всегда самая быстрая">
                </div>
            </div>
            <div class="info-page-row">
                <div class="info-page-element-wrapper">
                    <img src="3.png" alt="Оплачивайте свои покупки любым удобным для вас способом Совершая покупки в OrgasmCity.ru вы автоматически становитесь участником нашей бонусной программы">
                </div>
                <div class="info-page-element-wrapper">
                    <img src="4.png" alt="Оплата банковской картой на сайте">
                </div>
            </div>
        </div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
