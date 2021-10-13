<?php
use Bitrix\Main\Page\Asset;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Возврат товаров");
$APPLICATION->SetPageProperty("title", 'Возврат. Город Оргазма');
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о совершении возврата в городе оргазма. Если у Вас имеются претензии к качеству товара, купленного в нашем интернет-магазине, или возникла необходимость его возврата/обмена по каким-либо причинам, вы можете написать нам на почту return@orgasmcity.ru");
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
            <?php
            $APPLICATION->IncludeComponent(
                "qsoft:infopage",
                "",
                array(
                    "IBLOCK_CODE" => 'refundNew',
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "86400"
                ),
                false
            );
            ?>
        </div>
    </div>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
