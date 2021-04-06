<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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
?>
<div class="clearfix">
    <? if (!empty($arResult)) : ?>
    <div class="links-wrapper">
        <? foreach ($arResult as $arItem) : ?>
            <a class="lk-menu__item<? if ($arItem['SELECTED']) :
                ?> lk-menu__item_active<?
                                   endif; ?>" href="<?= $arItem['LINK'] ?>"><?= $arItem['TEXT'] ?></a>
        <? endforeach; ?>
    </div>
        <hr>
    <? endif; ?>
</div>