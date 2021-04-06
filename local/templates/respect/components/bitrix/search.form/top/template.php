<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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

$this->setFrameMode(true);
?>

<form id="main-search-form" class="search-form main-search-form" action="<?= $arResult['FORM_ACTION']; ?>" method="post">
    <input id="main-search-input" class="search search-btn-disable" autocomplete="off" type="search" name="q" maxlength="50" placeholder="Поиск товаров и брендов" />
    <button id="main-search-btn" type="submit" class="search-btn"></button>
    <ul class="search-suggest"></ul>
</form>
