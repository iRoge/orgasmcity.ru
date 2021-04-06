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

<form class="search-form phone--only" action="<?= $arResult['FORM_ACTION']; ?>">
    <div class="input-group">
        <input class="clearable" autocomplete="off" type="text" name="q" maxlength="50" placeholder="Найти нужную модель обуви">
        <button type="submit"><i class="icon icon-search"></i></button>
    </div>
    <ul class="search-suggest">
    </ul>
</form>