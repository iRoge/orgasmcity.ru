<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var LikeeInstagramListComponent $component */
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

<div class="container container--no-padding instagram-block">
    <div class="column-2">
        <a href="http://instagram.com/<?= $component::LOGIN; ?>">
            <img src="<?= SITE_TEMPLATE_PATH; ?>/images/products-grid/instagram.png" alt="in">
        </a>
    </div>
    <? foreach ($arResult['ITEMS'] as $arItem): ?>
        <div class="column-2">
            <a href="<?= $arItem['LINK']; ?>" target="_blank">
                <img src="<?= $arItem['SRC']; ?>" alt="<?= $arItem['NAME']; ?>">
            </a>
        </div>
    <? endforeach; ?>
</div>