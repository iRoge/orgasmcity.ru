<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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

$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])): ?>
<div class="container container--no-padding">
    <div class="column-8 pre-1">
        <div class="container products-grid">
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <div class="column-<?= $arItem['PROPERTIES']['BLOCK_SIZE']['VALUE_XML_ID']; ?> column-md-1 column-xs-1">
                    <a class="catalog-banner" href="<?= $arItem['PROPERTIES']['LINK']['VALUE']; ?>" style="background-image:url('<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>');">
                        <span class="catalog-banner__text"><?= $arItem['NAME']; ?></span>
                    </a>
                </div>
            <? endforeach; ?>
        </div>
        <div class="spacer--3"></div>
    </div>
</div>
<? endif; ?>