<?php
use Bitrix\Main\Page\Asset;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
$APPLICATION->SetPageProperty("title", 'Контакты. Город Оргазма');
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о контактах в Городе Оргазма");
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
                        "IBLOCK_CODE" => 'contacts',
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
