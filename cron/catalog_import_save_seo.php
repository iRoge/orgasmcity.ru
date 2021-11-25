<?
include("config.php");
//Максимальный уровень глубины вложенности разделов для CSV-экспорта/импорта:  3=>5
//Доступные поля групп +ACTIVE SORT XML_ID CODE

$start_time = time();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'].'/upload/tmp');
CModule::IncludeModule('fire.main');
$catalog_url = Fire_Settings::getOption('SETTINGS_CATALOG_IMPORT_URL');
if(!$catalog_url) {
	require('catalog_import_save_seo_nocat.php');
	die;
}
$deactivate = Fire_Settings::getOption('IMPORT_PRODUCT_DEACTIVATE');
$deactivate = $deactivate? date('d.m.Y', time()-60*60*24) : '';
$siteID = Fire_Settings::getOption('SETTINGS_SITE');  // your site ID - need for language ID
$profile_id = Fire_Settings::getOption('SETTINGS_PRODUCTS_IMPORT_PROFILE');
$import_url = Fire_Settings::getOption('SETTINGS_PRODUCTS_IMPORT_URL');

if (preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
{
	define('SITE_ID', $siteID);
}
else
{
	die('No defined site - $siteID');
}

if (!defined('LANGUAGE_ID') || preg_match('/^[a-z]{2}$/i', LANGUAGE_ID) !== 1)
	die('Language id is absent - defined site is bad');

set_time_limit (0);

if (!defined("CATALOG_LOAD_NO_STEP"))
	define("CATALOG_LOAD_NO_STEP", true);

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")) {
	$ar_profile = CCatalogImport::GetByID($profile_id);
	if (!$ar_profile)
		die("No profile");
	parse_str($ar_profile["SETUP_VARS"], $profile_params);

	$IBLOCK_ID = intval($profile_params["IBLOCK_ID"]);
	if ($IBLOCK_ID <= 0)
		die("No IBLOCK ID");
	
	if(array_search('IC_XML_ID0', $profile_params)===false)
		die("Wrong import profile");
	
	$catalog = $catalog_base = $catalog_base_all = $catalog_props = $profile_fileds = $secCodes = $addedXML = [];
	
	for($i=0; $i<1000; $i++)
		if(!$profile_params['field_'.$i])
			break;
		else {
			$val = $profile_params['field_'.$i];
			$add = '';
			while(array_search($val.($add? '+'.$add : $add), $profile_fileds)!==false)
				$add++;
			$profile_fileds[$i] = $val.($add? '+'.$add : $add);
		}
	
	$res = CIBlock::GetProperties($IBLOCK_ID);
	while($arr = $res->Fetch())
		$catalog_props[mb_strtoupper($arr['CODE'])] = $arr['ID'];
	
	$Select = ['ID', 'XML_ID', 'CODE', 'IBLOCK_SECTION_ID', 'NAME', 'ACTIVE', 'SORT'];
	$db = CIBlockSection::GetList([], ['IBLOCK_ID'=>$IBLOCK_ID], false, $Select);
	while($sect = $db->GetNext(true, false)) {
		$catalog_base_all[$sect['ID']] = $sect;
		$secCodes[(int)$sect['IBLOCK_SECTION_ID']][$sect['CODE']] = true;
	}
	foreach($catalog_base_all as $sect)
		if($xml_id=$sect['XML_ID']) {
			$parents = [];
			$parent = $sect;
			do
				$parents[] = $parent;
			while($parent['IBLOCK_SECTION_ID'] && ($parent=$catalog_base_all[$parent['IBLOCK_SECTION_ID']]));
			$i = 0;
			foreach(array_reverse($parents) as $parent)
				$sect+= [
					'IC_XML_ID'.$i => $parent['XML_ID'],
					'IC_GROUP'.$i => $parent['NAME'],
					'IC_ACTIVE'.$i => $parent['ACTIVE'],
					'IC_SORT'.$i => $parent['SORT'],
					'IC_CODE'.$i++ => $parent['CODE']
				];
			$sect = array_diff_key($sect, array_flip($Select));
			$catalog_base[$xml_id] = $sect;
		}

	$import_data = fopen($catalog_url, 'r');
	$head = [];
	if($import_data)
		while (($str_data = fgets($import_data)) !== false) {
			$csv_data = str_getcsv($str_data, ';');
			if(!$head) {
				$head = $csv_data;
				if(array_search('id', $head)===false || array_search('parentId', $head)===false || array_search('name', $head)===false)
					$head = array('id','parentId','name','sort');
				else
					continue;
			}
			$csv_data = array_combine($head, $csv_data);
			$catalog[$csv_data['id']] = $csv_data;
		}
	if(!$catalog)
		die('No catalog in URL');
	foreach($catalog as $key=>$sect)
		if(!$catalog_base[$key]) {
			$parents = [];
			$parent = $sect;
			do
				if($add=$catalog_base[$parent['id']]) {
					$sect+= $add;
					break;
				} else
					$parents[] = $parent;
			while($parent['parentId'] && ($parent=$catalog[$parent['parentId']]));
			$i = 0;
			while(isset($sect['IC_XML_ID'.$i]))
				$i++;
			foreach(array_reverse($parents) as $parent)
				$sect+= [
					'IC_XML_ID'.$i => $parent['id'],
					'IC_GROUP'.$i => $parent['name'],
					'IC_ACTIVE'.$i => '',
					'IC_SORT'.$i => $parent['sort'],
					'IC_CODE'.$i++ => ''
				];
			$sect = array_diff_key($sect, array_flip($head));
			$catalog_base[$key] = $sect;
		}
	unset($catalog_base_all);
	unset($catalog);
	
	if(!$catalog_base)
		die('No catalog');
	
	$success = false;
	$head = $head_remap = [];
    $context = stream_context_create(array(
        'http'=>array(
            'timeout' => 300
        )
    ));
	$import_data = fopen($import_url,'r', false, $context);
	$data_file = $_SERVER["DOCUMENT_ROOT"].$profile_params['URL_DATA_FILE'];
	//fda2000 update flags
	$new = $bestseller = array();
	$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"new"));
	while($enum_fields = $property_enums->GetNext(true, false))
		$new[$enum_fields["VALUE"]] = $enum_fields["ID"];
	$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"bestseller"));
	while($enum_fields = $property_enums->GetNext(true, false))
		$bestseller[$enum_fields["VALUE"]] = $enum_fields["ID"];
	//
	if ($import_data) {
		$fp = @fopen($data_file,"wb");
		if ($fp) {
			$xmls = $elCodes = [];
			$rsItems = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, array('ID', 'CODE', 'XML_ID', 'IBLOCK_ID', 'PROPERTY_new', 'PROPERTY_bestseller', 'PROPERTY_img_status'));
			while($arr = $rsItems->GetNext(true, false)) {
				$xmls[$arr['XML_ID']] = $arr;
				$elCodes[$arr['CODE']] = true;
			}
			
			//Remove double codes
			class MyClass {
				function OnStartIBlockElementAdd(&$arFields) {
					global $elCodes;
					if($code=$arFields['CODE']) {
						$i = '';
						while($elCodes[$code.$i])
							$i++;
						$arFields['CODE'] = $code.$i;
						$elCodes[$arFields['CODE']] = true;
					}
				}
				function OnBeforeIBlockSectionAdd(&$arFields) {
					global $secCodes;
					if($code=$arFields['CODE']) {
						$i = '';
						while($secCodes[(int)$arFields['IBLOCK_SECTION_ID']][$code.$i])
							$i++;
						$arFields['CODE'] = $code.$i;
						$secCodes[(int)$arFields['IBLOCK_SECTION_ID']][$arFields['CODE']] = true;
					}
				}
			}
			AddEventHandler('iblock', 'OnStartIBlockElementAdd', Array('MyClass', 'OnStartIBlockElementAdd'));
			AddEventHandler('iblock', 'OnBeforeIBlockSectionAdd', Array('MyClass', 'OnBeforeIBlockSectionAdd'));
			
			while (($str_data = fgets($import_data)) !== false) {
				$csv_data = str_getcsv($str_data, ';');
				if(!$head) {
					$head = $csv_data;
					if($profile_params['first_names_r']==='Y')
						fputcsv($fp, $profile_fileds, ';');
					$cont = true;
					if(array_search('prodid', $head)===false || array_search('categoryId', $head)===false || array_search('name', $head)===false) {
						$head = array('prodid','vendor_id','vendor_code','name','description','img1','img2','img3','img4','img5','img6','img7','img8','img9','img10','batteries','pack','material','length','diameter','collection','categoryId','bestseller','new','function','addfunction','vibration','volume','modelyear','infoprice','img_status');
						$cont = false;
					}
					$remap = [
						'prodid'=>'IE_XML_ID',
						'name'=>'IE_NAME',
						'description'=>'IE_DETAIL_TEXT',
						'addfunction'=>'add_function',
						'modelyear'=>'year',
						'infoprice'=>'price',
						'vendor_code'=>'article',
						'vendor_id'=>'vendor'
					];
					for($i=0; $i<20; $i++)
						$remap['img'.$i] = 'pics';
					foreach($head as $key=>$val)
						$head_remap[$key] = $remap[$val]? $remap[$val] : $val;
					foreach($head_remap as $key=>$val)
						if($id=$catalog_props[mb_strtoupper($val)]) {
							$val = 'IP_PROP'.$id;
							$add = '';
							while(array_search($val.($add? '+'.$add : $add), $head_remap)!==false)
								$add++;
							$head_remap[$key] = $val.($add? '+'.$add : $add);
						}
					
					if($cont)
						continue;
				}
				
				$csv_data_remap = array_combine($head_remap, $csv_data);
				$csv_data = array_combine($head, $csv_data);
				
				$xml_id = trim($csv_data_remap['IE_XML_ID']);
				if ((int)$xml_id<1)
					die('Wrong format file!');

				if ($arItem = $xmls[$xml_id]) {
					//fda2000 update flags
					$upd = array();
					$val = $bestseller[trim($csv_data['bestseller'])];
					if($val && $arItem['PROPERTY_BESTSELLER_ENUM_ID']!=$val)
						$upd['bestseller'] = $val;
					$val = $new[trim($csv_data['new'])];
					if($val && $arItem['PROPERTY_NEW_ENUM_ID']!=$val)
						$upd['new'] = $val;
					$val = trim($csv_data['img_status']);
					if($val && $arItem['PROPERTY_IMG_STATUS_VALUE']!=$val)
						$upd['img_status'] = $val;
					if($upd)
						CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], $upd);
					//
				} else {
					if(!$csv_data_remap['IE_DETAIL_TEXT_TYPE']) {
						$csv_data_remap['IE_DETAIL_TEXT_TYPE'] = 'html';
						if(mb_strpos($csv_data_remap['IE_DETAIL_TEXT'], "\n")!==false && mb_stripos($csv_data_remap['IE_DETAIL_TEXT'], '<br>')===false)
							$csv_data_remap['IE_DETAIL_TEXT_TYPE'] = 'text';
					}
					if($csv_data_remap['IE_DETAIL_TEXT_TYPE']=='text')
						$csv_data_remap['IE_DETAIL_TEXT'] = str_replace("\t", "\n", $csv_data_remap['IE_DETAIL_TEXT']);
					$csv_data_remap['IE_ACTIVE'] = $deactivate? 'N' : 'Y';
					$csv_data_remap['IE_ACTIVE_TO'] = $deactivate;
					$csv_data_remap['IE_DETAIL_PICTURE'] = $csv_data['img1'];

					if($catalog_base[$csv_data['categoryId']])
						$csv_data_remap+= $catalog_base[$csv_data['categoryId']];

					$data = [];
					foreach($profile_fileds as $val)
						$data[] = $csv_data_remap[$val];

					fputcsv($fp, $data, ';');
					$addedXML[$xml_id] = $xml_id;
				}
			}
			@fclose($fp);
			$success = true;
		}
	}
	if ($success === false)
		exit;

	$strFile = CATALOG_PATH2IMPORTS.$ar_profile["FILE_NAME"]."_run.php";
	if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
	{
		$strFile = CATALOG_PATH2IMPORTS_DEF.$ar_profile["FILE_NAME"]."_run.php";
		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
		{
			die("No import script");
		}
	}

	$bFirstLoadStep = true;

	$arSetupVars = array();
	$intSetupVarsCount = 0;
	if ($ar_profile["DEFAULT_PROFILE"] != 'Y')
	{
		parse_str($ar_profile["SETUP_VARS"], $arSetupVars);
		if (!empty($arSetupVars) && is_array($arSetupVars))
		{
			$intSetupVarsCount = extract($arSetupVars, EXTR_SKIP);
		}
	}

	global $arCatalogAvailProdFields;
	$arCatalogAvailProdFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_ELEMENT);
	global $arCatalogAvailPriceFields;
	$arCatalogAvailPriceFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_CATALOG);
	global $arCatalogAvailValueFields;
	$arCatalogAvailValueFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_PRICE);
	global $arCatalogAvailQuantityFields;
	$arCatalogAvailQuantityFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_PRICE_EXT);
	global $arCatalogAvailGroupFields;
	$arCatalogAvailGroupFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_SECTION);

	global $defCatalogAvailProdFields;
	$defCatalogAvailProdFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_ELEMENT);
	global $defCatalogAvailPriceFields;
	$defCatalogAvailPriceFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_CATALOG);
	global $defCatalogAvailValueFields;
	$defCatalogAvailValueFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_PRICE);
	global $defCatalogAvailQuantityFields;
	$defCatalogAvailQuantityFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_PRICE_EXT);
	global $defCatalogAvailGroupFields;
	$defCatalogAvailGroupFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_SECTION);
	global $defCatalogAvailCurrencies;
	$defCatalogAvailCurrencies = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_CURRENCY);

	CCatalogDiscountSave::Disable();
	include($_SERVER["DOCUMENT_ROOT"].$strFile);
	CCatalogDiscountSave::Enable();

	CCatalogImport::Update($profile_id, array(
		"=LAST_USE" => $DB->GetNowFunction()
		)
	);
	
	//remove items no photo
	if($addedXML) {
		$rsItems = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID, 'XML_ID'=>$addedXML, 'PROPERTY_pics'=>false], false, false, array('ID'));
		while($arItem = $rsItems->Fetch())
			CIBlockElement::Delete($arItem['ID']);
	}
	//
	@copy($_SERVER["DOCUMENT_ROOT"]."/import/catalog_save_seo.csv", $_SERVER["DOCUMENT_ROOT"]."/import/catalog_save_seo/catalog_".date("d.m.Y H i").".csv");
	
	$timestamp = time() - 7 * 86400;
	$dir_path = $_SERVER["DOCUMENT_ROOT"]."/import/catalog_save_seo/";
	if ($handle = opendir($dir_path))
	{
		while (false !== ($entry = readdir($handle)))
		{
			if (is_file($dir_path.$entry))
			{
				if (filemtime($dir_path.$entry) < $timestamp)
					@unlink($dir_path.$entry);
			}
		}
		closedir($handle);
	}
	$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/cron/logs/catalog_import_date.txt","wb");
	if ($fp)
	{
		@fwrite($fp, date("d.m.Y H:i:s"));
		fclose($fp);
	}
}
$end_time = time();
echo "work_time ".intval(($end_time - $start_time)/60)." minutes\n";
?>