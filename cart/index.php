<? define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Корзина");
$APPLICATION->SetTitle("Корзина");
?>
<? $APPLICATION->IncludeComponent(
    "qsoft:order",
    ".default",
    array(
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 31536000,
    )
); ?>
<?/*
// Include Segmento
\Bitrix\Main\Page\Asset::getInstance()->addString('<script type="text/javascript">
    var _rutarget = window._rutarget || [];
    _rutarget.push({"event": "cart"});
</script>', false, \Bitrix\Main\Page\AssetLocation::AFTER_JS);
define("INC_SEGMENTO", true);
*/
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
