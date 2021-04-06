<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

use \Bitrix\Sale\Fuser;
use \Bitrix\Sale\Basket;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Page\AssetLocation;

\Likee\Site\Helper::addBodyClass('page--product');

$GLOBALS['ELEMENT_BREADCRUMB'] = $arResult['ELEMENT_BREADCRUMB'];
$GLOBALS['CATALOG_ELEMENT_ID'] = $arResult['ID'];

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slider-pro.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.sliderPro.js');
Asset::getInstance()->addString('<script>(
  function($) { $(function() { 
      $(".sp-image").removeClass("sp-image_hide");
      $( "#example5" ).sliderPro({
        height: 500,
        orientation: "vertical",
        loop: false,
        arrows: true,
        autoplay: false,
        buttons: false,
        thumbnailsPosition: "left",
        thumbnailPointer: true,
        breakpoints: {
          990: {
            thumbnailsPosition: "bottom",
            arrows: false,
            orientation: "horizontal"
          }
        }
      });
	
}); })(jQuery);</script>', false, AssetLocation::AFTER_JS);

// получаем корзину для кнопки "в корзине"
$basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
$arResult["BASKET_OFFERS"] = array();
$arBasketItems = $basket->getBasketItems();
foreach ($arBasketItems as $arItem) {
    $offerId = $arItem->getProductId();
    $arResult["BASKET_OFFERS"][$offerId] = $offerId;
}
// фикс для безразмерной номенклатуры
if ($arResult['SINGLE_SIZE']) {
    if ($arResult["BASKET_OFFERS"][$arResult['SINGLE_SIZE']]) { ?>
<script>$("#buy-btn").val("В корзине")</script>
    <? }
} ?>
<script>inBasket = JSON.parse('<?= json_encode($arResult["BASKET_OFFERS"] ?? array(), JSON_UNESCAPED_UNICODE) ?>') || [];</script>
<?
// Виджет примерки (реализация функции redirectToProductByCode() в ./script.js)
if ($arResult["ONLINE_TRY_ON"] && $USER->IsAuthorized()) {
    $asset = Asset::getInstance();
    $widgetInitialization = "<script>
        $(function() {
            new_init({$USER->GetID()}, {$arResult['ID']}, (size, id, code) => redirectToProductByCode(code, id), 'respect', null, null, '{$arResult["ARTICLE"]}');
            $('input#fittin_widget_button.authorized ').on('click', () => new_click_widjet());
        });
    </script>";
    // Использован addString() вместо addJs(), чтобы подключить скрипт виджета примерки после подключения jQuery
    $asset->addString('<script type="text/javascript" src="//api.fittin.ru/admin/api/lumen/public/js/vidget.js"></script>', false, AssetLocation::AFTER_JS);
    $asset->addString($widgetInitialization, false, AssetLocation::AFTER_JS);
    $asset->addCss('//api.fittin.ru/admin/api/lumen/public/css/style.css');
}
