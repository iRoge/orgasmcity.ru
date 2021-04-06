<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

<div class="container">
    <ul>
        <? foreach ($arResult['SECTIONS'] as $arSection): ?>
            <li><a href="<?= $arSection['SECTION_PAGE_URL'] ?>"><?= $arSection['NAME']; ?></a></li>
        <? endforeach; ?>
    </ul>
</div>