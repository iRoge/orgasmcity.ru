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

<form id="mob-search-form" class="col-xs-12 search-form mob-search-form" action="<?= $arResult['FORM_ACTION']; ?>" method="post">
    <input id="mob-search-input" class="search" autocomplete="off" type="search" name="q" maxlength="50" placeholder="Поиск по каталогу" />
    <button id="mob-search-btn" type="submit" class="search-btn"></button>
    <ul class="search-suggest"></ul>
</form>
