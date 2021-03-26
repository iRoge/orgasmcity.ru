<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeBasketSmallComponent $component */
$this->setFrameMode(true);
?>

<a id="basket-small" class="shortcut" href="<?= $arParams['PATH_TO_BASKET']; ?>">
	<p class="shortcut-informer count"><?= $arResult['COUNT']; ?></p>
	<img src="<?= SITE_TEMPLATE_PATH; ?>/img/cart-bestsel.png" />
</a>

<script style="display: none;">
    BX.message({
        'BASKET_SMALL_AJAX_PATH': '<?= $templateFolder; ?>/ajax.php'
    });
</script>
