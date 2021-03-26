<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var array $arItem */
/** @var string $sClass */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
?>
<? foreach ($arItem['LABELS'] as $sClass => $arLabel) : ?>
	<div class="<?= strtolower($arLabel['NAME']) ?>-sel"><?= $arLabel['NAME'] ?></div>
<? endforeach; ?>
<div class="bestsel-one">
	<div class="photo-bestsel">
		<a href="<?= $arItem['DETAIL_PAGE_URL']; ?>">
			<img src="<?= $arItem['PICTURE']['SRC']; ?>" />
		</a>
	</div>
	<div class="about-bestsel">
		<p class="name-sel"><?= $arItem['NAME']; ?></p>
		<div class="info-sel">
			<p class="price-sel"> <?= $arItem['MIN_PRICE']['PRINT_VALUE']; ?></p>
			<a href="<?= $arItem['DETAIL_PAGE_URL']; ?>"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/cart-bestsel.png"></a>
		</div>
	</div>
</div>