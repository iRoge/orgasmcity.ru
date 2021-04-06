<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

\Likee\Site\Helper::addBodyClass('page--product');
if (! empty($arResult['BODY_CLASS'])) {
    \Likee\Site\Helper::addBodyClass($arResult['BODY_CLASS']);
}

$GLOBALS['ELEMENT_BREADCRUMB'] = $arResult['ELEMENT_BREADCRUMB'];