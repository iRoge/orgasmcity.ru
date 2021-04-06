<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (\Bitrix\Main\Loader::includeModule('likee.site'))
    $arResult = \Likee\Site\Helpers\Menu::menuTreeBuild($arResult);

try {
	$colorsIblockId = \Likee\Site\Helpers\IBlock::getIBlockId('COLORS');
	
	$arSelect = Array("ID", "NAME", 'IBLOCK_ID','PROPERTY_COLOR');
	$arFilter = Array("IBLOCK_ID"=>$colorsIblockId, "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>5000), $arSelect);
	while($ob = $res->Fetch())
	{
		$arColors[$ob['ID']] = $ob;
	}

	foreach ($arResult as $key=>$item){
		if($item['PARAMS']['PROPS']['UF_TEXT_C']){
			$arResult[$key]['PARAMS']['PROPS']['UF_TEXT_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_TEXT_C']];
		}
		if($item['PARAMS']['PROPS']['UF_BG_C']){
			$arResult[$key]['PARAMS']['PROPS']['UF_BG_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_BG_C']];
		}
	}
	
} catch (\Exception $e) {}