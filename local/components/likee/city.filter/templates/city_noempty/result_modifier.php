<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


$arFilter = array('IBLOCK_ID' => 13); 
$rsSect = CIBlockSection::GetList(array('ID' => 'ASC'),$arFilter,true);
while ($arSect = $rsSect->GetNext()){
	if($arSect['ELEMENT_CNT']>0){
		$arResult['VACANCY_CITIES'][$arSect['NAME']] = $arSect['ELEMENT_CNT'];

		//Получаю города, в которых есть вакансии, но которых нет в складах
		foreach($arResult['CITIES'] as $key=>$city){
			if($arSect['NAME']!=$city['CITY_NAME']){
				$arCitiesNoStore[$arSect['NAME']] = $arSect['NAME'];
			}else{
				unset($arCitiesNoStore[$arSect['NAME']]);break;
			}
		}
	}
}
//Удаляю из массива города, в которых нет вакансий
foreach($arResult['CITIES'] as $key=>$city){
	if(!$arResult['VACANCY_CITIES'][$city['CITY_NAME']]){
		unset($arResult['CITIES'][$key]);
	}
}
//Получаю детальную информацию о городах, в которых есть вакансии, но которых не было в массиве
$res2 = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID,/*'COUNTRY_ID'=>1,*/'NAME_RU'=>$arCitiesNoStore),
    'select' => array('ID','CODE','NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
    'order' => array('NAME_RU' => 'asc')
));
while($item2 = $res2->fetch())
{
    $arResult['CITIES'][$item2['ID']] = $item2;
    $arResult['CITIES'][$item2['ID']]['CITY_NAME'] = $item2['NAME_RU'];
}
if($_COOKIE['FILTER_CITY']){
	$GLOBALS['CITY_FILTER']['CITY_NAME']=$arResult['CITIES'][$_COOKIE['FILTER_CITY']]['CITY_NAME'];
}
//Сортируем новый массив
function CustomSort($a, $b){
    return strcmp($a["CITY_NAME"], $b["CITY_NAME"]);
}
usort($arResult['CITIES'], "CustomSort");