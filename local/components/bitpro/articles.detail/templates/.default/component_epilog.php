<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->AddChainItem("Статьи", "/articles/");
$APPLICATION->AddChainItem($arResult['ITEM']["NAME"], "");

if($arResult['ITEM']['SEO']['ELEMENT_META_TITLE']){
    $APPLICATION->SetPageProperty("title",$arResult['ITEM']['SEO']['ELEMENT_META_TITLE']);
}

if($arResult['ITEM']['SEO']['ELEMENT_META_DESCRIPTION']){
    $APPLICATION->SetPageProperty("description",$arResult['ITEM']['SEO']['ELEMENT_META_DESCRIPTION']);
}

if($arResult['ITEM']['SEO']['ELEMENT_META_KEYWORDS']){
    $APPLICATION->SetPageProperty("keywords",$arResult['ITEM']['SEO']['ELEMENT_META_KEYWORDS']);
}

/*if($arResult['ITEM']['SEO']['ELEMENT_PAGE_TITLE']){
    $APPLICATION->SetTitle($arResult['ITEM']['SEO']['ELEMENT_PAGE_TITLE']);
}
else {
    $APPLICATION->SetTitle($arResult['ITEM']['NAME']);
}*/


?>