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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$iCount = count($arResult['ITEMS']);
?>

<a id="basket-small" class="shortcut" href="<?= $arParams['PATH_TO_BASKET']; ?>">
    <i class="icon icon-cart"></i>
    <? if ($iCount > 0): ?>
        <span class="shortcut-informer"><?= $iCount; ?></span>
    <? endif; ?>
</a>

<script style="display: none;">
    BX.message({
        'BASKET_SMALL_AJAX_PATH': '<?= $templateFolder; ?>/ajax.php'
    });
</script>