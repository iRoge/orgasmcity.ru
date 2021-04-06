<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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
global $APPLICATION;

$sHtml = '';
$iCount = count($arResult);
if ($iCount <= 1) return $sHtml;

$sHtml .= '<p class="breadcrumbs in-catalog">';

foreach ($arResult as $iKey => $arItem) {
    if (empty($arItem['TITLE'])) continue;

    $sClass = '';

    if ($iKey !== $iCount - 1) {
        $sHtml .= '<a href="' . $arItem['LINK'] . '">' . $arItem['TITLE'] . '</a> <img src="' . SITE_TEMPLATE_PATH . '/img/bc-right.png" /> ';
    } elseif ($GLOBALS['SEO_PAGE_ELEMENT'] == true) {
        $sHtml .= '<a href="' . $arItem['LINK'] . '">' . $arItem['TITLE'] . '</a>';
    } else {
        $sHtml .= '<span>' . $arItem['TITLE'] . '</span>';
    }
}
$sHtml .= '</p>';

return $sHtml;
