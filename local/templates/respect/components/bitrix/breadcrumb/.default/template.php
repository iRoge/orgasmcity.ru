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

$sHtml .= '<p itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs in-catalog">';

$i = 1;
foreach ($arResult as $iKey => $arItem) {
    if (empty($arItem['TITLE'])) continue;

    $sClass = '';
    $sHtml .= '<span itemscope itemtype="http://schema.org/ListItem">';
    if ($iKey !== $iCount - 1) {
        $sHtml .= '<a itemprop="item" href="' . $arItem['LINK'] . '"><span itemprop="name">' . $arItem['TITLE'] . '</span></a> <img src="' . SITE_TEMPLATE_PATH . '/img/bc-right.png" /> ';
    } elseif ($GLOBALS['SEO_PAGE_ELEMENT'] == true) {
        $sHtml .= '<a itemprop="item" href="' . $arItem['LINK'] . '"><span itemprop="name">' . $arItem['TITLE'] . '</span></a>';
    } else {
        $sHtml .= '<span>' . $arItem['TITLE'] . '</span>';
    }
    $sHtml .= '<meta itemprop="position" content="' . $i . '" /></span>';
    $i++;
}
$sHtml .= '</p>';

return $sHtml;
