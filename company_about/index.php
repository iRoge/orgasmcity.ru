<?php

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("О компании");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о секс шопе Город Оргазма. Город Оргазма - это огромный выбор секс товаров на любой вкус и цвет. Здесь вы можете купить с доставкой на дом секс куклы, вибраторы, фаллоимитаторы, мастурбаторы, дилдо, члены, БДСМ игрушки, товары для взрослых, фистинг");
$APPLICATION->SetPageProperty("title", 'О компании. Город Оргазма');
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
                <img src="1.png" alt="OrgasmCity - это огромный выбор секс товаров на любой вкус и цвет. На текущий момент у нас в продаже насчитывается более 16000 ассортимента.">
            </div>
            <div class="info-page-element-wrapper">
                <img src="2.png" alt="Многие люди не получают полного удовольствия от секса. Это происходит по самым разным причинам и может быть какая-то физиологическая или эмоциональная проблема. И то, и другое, может элементарно решаться товарами из нашего ассортимента. Кому-то поможет копеешная смазочка, кому-то достаточно будет сменить цвет белья...
Мы отточили и автоматизировали все бухгалтерские,логистические и складские процессы до предела, благодаря чему наши цены на товары стали еще доступнее. ">
            </div>
        </div>
        <div class="info-page-row">
            <div class="info-page-element-wrapper">
                <img src="3.png" alt="Нет предела совершенства, именно поэтому мы будем стараться делать доступнее еще больший ассортимент товаров, чтобы каждый имел возможность разнообразить свою сексуальную жизнь и стать более уверенным в себе человеком.
Наша цель - осчастливить людей разнообразием в сексуальной жизни. ">
            </div>
            <div class="info-page-element-wrapper">
                <img src="4.png" alt="Научно доказано, что богатая сексуальная жизнь человека приводит его к повышенной работоспособности и радости в жизни, поэтому OrgasmCity это не просто секс-шоп,
OrgasmCity - это ваш магазин счастья!">
            </div>
        </div>
    </div>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>