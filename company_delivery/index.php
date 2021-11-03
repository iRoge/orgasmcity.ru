<?php
use Bitrix\Main\Page\Asset;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
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
            <div class="info-page-row" style="justify-content: center">
                <?php
                $APPLICATION->IncludeComponent(
                    "qsoft:infopage",
                    "",
                    array(
                        "IBLOCK_CODE" => 'delivery',
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "86400"
                    ),
                    false
                );
                ?>
            </div>
        </div>
    </div>
<?php
$APPLICATION->SetTitle("Доставка");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о доставке в городе оргазма. В городе оргазма широкий выбор доставки. Здесь вы можете заказать товары для взрослых с доставкой на дом курьером, забрать в пунктах самовывоза PickPoint или СДЭК, или выбрать доставку в отделение почты России");
$APPLICATION->SetPageProperty("title", 'Доставка. Город Оргазма');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

