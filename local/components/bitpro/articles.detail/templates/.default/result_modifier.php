<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$rs=CIBlockElement::GetList(array("SORT" => "ASC"), array("ACTIVE"=>"Y", "ACTIVE_DATE" => "Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]), false, array("nElementID"=>$arResult['ITEM']['ID'], "nPageSize"=>1), array("ID", "NAME", "DETAIL_PAGE_URL"));
while($ar=$rs->GetNext()) $page[] = $ar;
?>