<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
foreach ($arResult['SECTIONS'] as $arSection) {

    if ($arSection['ID'] == $arParams['CUR_SECTION_ID']) {
        $arSection['CURRENT'] = 'Y';
    }

    $arSections[] = $arSection;

}
$arResult['SECTIONS'] = $arSections;

