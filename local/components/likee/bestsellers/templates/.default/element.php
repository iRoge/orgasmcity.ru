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

<a class="products-item products-item--square" href="<?= $arItem['DETAIL_PAGE_URL']; ?>">
    <div class="products-item__content">
        <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>" alt="<?= $arItem['PREVIEW_PICTURE']['ALT']; ?>">
    </div>
</a>