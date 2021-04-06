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

/**
 * @param array $arResult
 * @param int $iPage
 * @return string
 */
function getUrlForPage($arResult, $iPage)
{
    static $sUrl;

    if (is_null($sUrl)) {
        global $APPLICATION;
        $sUrl = $APPLICATION->GetCurPageParam('PAGEN_' . $arResult['NavNum'] . '=#PAGE#', ['PAGEN_' . $arResult['NavNum'], 'load-more']);
    }

    return str_replace('#PAGE#', $iPage, $sUrl);
}

function getShowAllUrl($arResult)
{
    global $APPLICATION;

    $sUrl = $APPLICATION->GetCurPageParam('SHOWALL_' . $arResult['NavNum'] . '=1', ['SHOWALL_' . $arResult['NavNum'], 'PAGEN_' . $arResult['NavNum'], 'load-more']);

    return $sUrl;
}


if (!$arResult['NavShowAlways']) {
    if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false))
        return;
}

$bWide = $arResult['NavPageNomer'] > 3 && ($arResult['NavPageCount'] - $arResult['NavPageNomer']) > 2;

if ($arResult['NavPageCount'] < 2) return;
?>

<? if ($arResult['bShowAll']): ?>
    <div class="container show-more js-show-more-box">
        <div class="column-8 pre-1">
            <? if ($arResult['NavPageNomer'] < $arResult['NavPageCount']): ?>
                <div class="container show-more">
                    <div class="column-8 pre-1">
                        <a href="<?= getUrlForPage($arResult, $arResult['NavPageNomer'] + 1); ?>"
                           class="button button--xl button--transparent button--block js-load-more-btn load-more-btn">
                            Показать еще
                        </a>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
<? endif; ?>