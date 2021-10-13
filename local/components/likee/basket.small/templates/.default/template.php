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
    <i class="icon icon-cart"></i>
    <? if ($arResult['COUNT'] > 0): ?>
        <span class="shortcut-informer"><?= $arResult['COUNT']; ?></span>
    <? endif; ?>
</a>
