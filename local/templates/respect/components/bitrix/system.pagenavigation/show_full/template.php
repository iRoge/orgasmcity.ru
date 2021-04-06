<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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

/**
 * @param array $arResult
 * @param int $iPage
 * @return string
 */

$arSortProductNumber = array(
    36 => array("NAME" => 36),
    48 => array("NAME" => 48),
    72 => array("NAME" => 72),
    96 => array("NAME" => 96),
);
$sizen='SIZEN_' . $arResult['NavNum'];
if (empty($arResult['NavNum'])) {
    global $APPLICATION;
    $url=$APPLICATION->GetCurPageParam();
    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $arr_params=array_keys($query_params);
    foreach ($arr_params as $param) {
        if (strpos($param, "SIZEN")!==false) {
            $sizen=$param;
        }
    }
}
if (!empty($_REQUEST["SIZEN_1"]) && $arSortProductNumber[$_REQUEST["SIZEN_1"]]) {
    setcookie("CATALOG_SORT_TO", $_REQUEST["SIZEN_1"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
    $arSortProductNumber[$_REQUEST["SIZEN_1"]]["SELECTED"] = "Y";
    $arParams["PAGE_ELEMENT_COUNT"] = $_REQUEST["SIZEN_1"];
    $arResult['NavPageSize']=$_REQUEST["SIZEN_1"];
} elseif (!empty($_GET[$sizen]) && in_array($_GET[$sizen], array_keys($arSortProductNumber))) {
    $arSortProductNumber[$_GET[$sizen]]["SELECTED"] = "Y";
    setcookie("CATALOG_SORT_TO", $_GET[$sizen], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
    $arParams["PAGE_ELEMENT_COUNT"] = $_GET[$sizen];
    $arResult['NavPageSize']=$_GET[$sizen];
} elseif (!empty($_COOKIE["CATALOG_SORT_TO"]) && $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]) {
    $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]["SELECTED"] = "Y";
    $arParams["PAGE_ELEMENT_COUNT"] = $_COOKIE["CATALOG_SORT_TO"];
    $arResult['NavPageSize']=$_COOKIE["CATALOG_SORT_TO"];
} else {
    $arParams["PAGE_ELEMENT_COUNT"] = 36;
    setcookie("CATALOG_SORT_TO", 36, time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
    $arSortProductNumber[36]["SELECTED"] = "Y";
    $arResult['NavPageSize']=36;
}
if ($arResult['NavPageNomer'] > $arResult['NavPageCount']) {
    $arResult['NavPageNomer']=1;
}
function getUrlForPage($arResult, $iPage, $iPageSize = false)
{
    static $sUrl;

    if (is_null($sUrl)) {
        global $APPLICATION;
        $sUrl = $APPLICATION->GetCurPageParam('PAGEN_' . $arResult['NavNum'] . '=#PAGE#&SIZEN_' . $arResult['NavNum'] . '=#SIZE#', [
            'PAGEN_' . $arResult['NavNum'],
            'SIZEN_' . $arResult['NavNum'],
            'load-more',
            'load_more',
            'nav_string'
        ]);
    }

    if (false === $iPageSize) {
        $iPageSize = $arResult['NavPageSize'];
    }

    return str_replace(['#PAGE#', '#SIZE#'], [$iPage, $iPageSize], $sUrl);
}

function getUrlForSize($arResult, $iPageSize)
{
    static $sUrl;

    if (is_null($sUrl)) {
        global $APPLICATION;
        $sUrl = $APPLICATION->GetCurPageParam('&SIZEN_' . $arResult['NavNum'] . '=#SIZE#', [
            'PAGEN_' . $arResult['NavNum'],
            'SIZEN_' . $arResult['NavNum'],
            'load-more'
        ]);
    }
    
    return str_replace('#SIZE#', $iPageSize, $sUrl);
}

function getShowAllUrl($arResult)
{
    global $APPLICATION;

    $sUrl = $APPLICATION->GetCurPageParam('SHOWALL_' . $arResult['NavNum'] . '=1', ['SHOWALL_' . $arResult['NavNum'], 'PAGEN_' . $arResult['NavNum'], 'load-more']);

    return $sUrl;
}


if (!$arResult['NavShowAlways']) {
    if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false)) {
        return;
    }
}

$bWide = $arResult['NavPageNomer'] > 3 && ($arResult['NavPageCount'] - $arResult['NavPageNomer']) > 2;

if ($arResult['NavPageCount'] < 2) {
    return;
}
?>
<? if ($arResult['bShowAll']) : ?>
<script>
    $('select[name="SIZEN_<?=$arResult["NavNum"]?>"]').ready(function(){
        if ($('select[name="SIZEN_<?=$arResult["NavNum"]?>"]').val() == 0) {
            //console.log('reloading');
            location.reload();
        }
    });
    $('#sizenUrl').on('change', function() {
        //console.log('sizenUrl - ' + $(this).val());
        window.history.pushState('data','Title',$(this).val().replace('&load_more=Y', ''));
    });
    $('#moreUrl').on('click', function() {
        //console.log('moreUrl - ' + $(this).attr('href'));
        window.history.pushState('data','Title',$(this).attr('href').replace('&load_more=Y', ''));
    });
    $('#pagenUrl').on('change', function() {
        //console.log('pagenUrl - ' + $(this).val());
        window.history.pushState('data','Title',$(this).val().replace('&load_more=Y', ''));
    });
</script>
<div class="after-all-in-right-catalog js-show-more-box">
    <div class="first-row navigation-row">
        <? if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) : ?>
            <div class="view-mobile"> 
                <div class="load-more-btn-loader"></div>
                <div class="load-more-btn-main">
                    <span id ="moreUrl" data-href="<?= getUrlForPage($arResult, $arResult['NavPageNomer'] + 1); ?>" class="view-all js-load-more-btn load-more-btn" onclick="smartFilter.nextPage(this)">Следующая страница</span>
                </div>
            </div>
        <? endif;
        if ($arResult['NavPageNomer']>1) {
            ?><span data-url="<?= getUrlForPage($arResult, $arResult['NavPageNomer'] - 1); ?>" class="pages-right-catalog" onMouseOver="document.pic.src='/img/arr-left.png'" onMouseOut="document.pic.src='/img/arr-left.png'" onclick="SmartFilter.prototype.goToPageNum(this)">
                <img src="/img/arr-left.png" name="pic" style="height: 12px; margin-top: -2px;"/>
            </span><?
        }
        if ($arResult['NavPageCount']<=7) {
            for ($pageNum = 1; $pageNum <= $arResult['NavPageCount']; $pageNum++) : ?>
                <span data-url="<?= getUrlForPage($arResult, $pageNum); ?>" class="pages-right-catalog<?
                if ($pageNum == $arResult['NavPageNomer']) {
                    print ' active';
                }
                ?>" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$pageNum?></span>
            <? endfor;
        } else {
            if ($arResult['NavPageNomer']-1>2) {?>
                <span data-url="<?= getUrlForPage($arResult, 1); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)">1</span>
                <span>…</span>
                <?for ($pageNum = min(($arResult['NavPageCount']-4), ($arResult['NavPageNomer']-1)); $pageNum <= ($arResult['NavPageNomer']-1); $pageNum++) : ?>
                        <span data-url="<?= getUrlForPage($arResult, $pageNum); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$pageNum?></span>
                <? endfor;?>
                
                <?
            } else {
                if ($arResult['NavPageNomer']<=3) {
                    for ($pageNum = 1; $pageNum <= min(5, $arResult['NavPageCount']); $pageNum++) : ?>
                    <span data-url="<?= getUrlForPage($arResult, $pageNum); ?>" class="pages-right-catalog<?
                    if ($pageNum == $arResult['NavPageNomer']) {
                        print ' active';
                    }
                    ?>" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$pageNum?></span>
                    <? endfor;
                } else {
                    for ($pageNum = 1; $pageNum <= ($arResult['NavPageNomer']-1); $pageNum++) : ?>
                    <span data-url="<?= getUrlForPage($arResult, $pageNum); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$pageNum?></span>
                    <? endfor;
                }
            }
            if ($arResult['NavPageNomer']>3) {
                ?>
             <span data-url="<?= getUrlForPage($arResult, $arResult['NavPageNomer']); ?>" class="pages-right-catalog active" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$arResult['NavPageNomer']?></span>
                <?
            }
            if (($arResult['NavPageNomer']+2)<($arResult['NavPageCount']-1)) {?>
                <?if ($arResult['NavPageNomer']>3) {?>
                <span data-url="<?= getUrlForPage($arResult, ($arResult['NavPageNomer']+1)); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)"><?=($arResult['NavPageNomer']+1)?></span>
                <?}?>
                <span>…</span>
                <span data-url="<?= getUrlForPage($arResult, $arResult['NavPageCount']); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$arResult['NavPageCount']?></span>
                <?
            } else {
                for ($pageNum = ($arResult['NavPageNomer']+1); $pageNum <= $arResult['NavPageCount']; $pageNum++) : ?>
                <span data-url="<?= getUrlForPage($arResult, $pageNum); ?>" class="pages-right-catalog" onclick="SmartFilter.prototype.goToPageNum(this)"><?=$pageNum?></span>
                <? endfor;
            }
        }
        if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) : ?>
            <span data-url="<?= getUrlForPage($arResult, $arResult['NavPageNomer'] + 1); ?>" class="pages-right-catalog" onMouseOver="document.pic.src='/img/bc-right-white.png'" onMouseOut="document.pic.src='/img/arr-right.png'" onclick="SmartFilter.prototype.goToPageNum(this)">
                <img src="/img/arr-right.png" name="pic" style="height: 12px; margin-top: -2px;"/>
            </span>
            <div class="view-desktop"> 
                <div class="load-more-btn-loader"></div>
                <div class="load-more-btn-main">
                    <span id ="moreUrl" data-href="<?= getUrlForPage($arResult, $arResult['NavPageNomer'] + 1); ?>" class="view-all js-load-more-btn load-more-btn view-desktop" onclick="smartFilter.nextPage(this)">Следующая страница</span>
                </div>
            </div>
        <? endif; ?>
        <div class="show-for">
            <p>На странице</p>
            <select class="js-paginate" name="SIZEN_<?=$arResult["NavNum"]?>" onchange="jsPaginate.change(this)">
                <? foreach ($arSortProductNumber as $arSortNumberElementId => $arSortNumberElement) : ?>
                    <option value="<?=$APPLICATION->GetCurPageParam('SIZEN_'.$arResult['NavNum'].'='.$arSortNumberElementId, array('SIZEN_'.$arResult['NavNum']));?>"<?if ($arSortNumberElement["SELECTED"] == "Y") :
                        ?> selected<?
                                   endif;?>><?=$arSortNumberElement["NAME"]?></option>
                <? endforeach; ?>
            </select>
        </div>
    </div>
</div>
<? endif; ?>
