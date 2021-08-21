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

<form id="main-search-form" class="search-form main-search-form hidden-xs" action="<?= $arResult['FORM_ACTION']; ?>" method="post">
    <div class="search-form-input-wrapper">
        <input id="main-search-input" style="width: 80%" class="search" autocomplete="off" type="search" name="q" maxlength="50" placeholder="Поиск по каталогу" />
        <button id="main-search-btn" type="submit" class="search-btn"><img style="width: 22px" src="/local/templates/respect/img/svg/search.svg" alt="Search"></button>
    </div>
    <ul class="search-suggest"></ul>
</form>
