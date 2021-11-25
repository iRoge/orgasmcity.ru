<?
include("config.php");
//Импорт производителей

$start_time = time();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('fire.main');
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

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock"))
{

//	define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");	
	if ($ar_profile = CCatalogImport::GetByID($profile_id))
	{
		parse_str($ar_profile["SETUP_VARS"], $profile_params);
		//$dump = print_r($profile_params, true);		
		//AddMessage2Log($profile_params["IBLOCK_ID"]);		
		//AddMessage2Log($profile_params["IBLOCK_ID"]);
	}

	//$dump = print_r($ar_profile, true);

	if (!$ar_profile) die("No profile");
	
	$IBLOCK_ID = intval($profile_params["IBLOCK_ID"]);
	if ($IBLOCK_ID <= 0)
		die("No IBLOCK ID");
	
	if(array_search('IC_XML_ID0', $profile_params)!==false)
		die("Wrong import profile");

	$success = false;
    $context = stream_context_create(array(
        'http'=>array(
            'timeout' => 300
        )
    ));
	$import_data = fopen($import_url, 'r', false, $context);
	$data_file = $_SERVER["DOCUMENT_ROOT"]."/import/catalog_save_seo.csv";
	
	//fda2000 update flags
	$new = $bestseller = $addedXML = array();
	$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"new"));
	while($enum_fields = $property_enums->GetNext())
		$new[$enum_fields["VALUE"]] = $enum_fields["ID"];
	$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"bestseller"));
	while($enum_fields = $property_enums->GetNext())
		$bestseller[$enum_fields["VALUE"]] = $enum_fields["ID"];
	//
	
	if ($import_data)
	{
		$fp = @fopen($data_file,"wb");
		if ($fp)
		{
			$i = 0;
			while (($str_data = fgets($import_data)) !== false)
			{
				$csv_data = str_getcsv($str_data, ';');
				
				//fda2000 detail image + deactivate
				array_splice($csv_data, 32, 0, ($i? ($deactivate? 'N' : 'Y') : 'active')); 
				array_splice($csv_data, 33, 0, ($i? $deactivate: 'active_to'));
				array_splice($csv_data, 34, 0, ($i? $csv_data[5] : 'detail_picture'));
				//
				$i++;
				if ($i == 1)
					fputcsv($fp, $csv_data, ';');
				else
				{
					$xml_id = trim($csv_data[0]);
					if ($xml_id != "")
					{
						$csv_data[4] = str_replace("\t", "\n", $csv_data[4]);//fda2000 description
						$arFilter = array(
							"XML_ID" => $xml_id,
							"IBLOCK_ID" => $IBLOCK_ID,
						);
						$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", 'IBLOCK_ID', 'PROPERTY_new', 'PROPERTY_bestseller', 'PROPERTY_img_status'));
						if (!$arItem = $rsItems->GetNext())
						{
							//AddMessage2Log($xml_id);
							fputcsv($fp, $csv_data, ';');
							$addedXML[$xml_id] = $xml_id;
						} else {
							//fda2000 update flags
							$upd = array();
							//Считаем что признаки в CSV всегда на фиксированных позициях, а также что список уже заполнен нужными значениями
							$val = $bestseller[trim($csv_data[24])];
							if($val && $arItem['PROPERTY_BESTSELLER_ENUM_ID']!=$val)
								$upd['bestseller'] = $val;
							$val = $new[trim($csv_data[25])];
							if($val && $arItem['PROPERTY_NEW_ENUM_ID']!=$val)
								$upd['new'] = $val;
							$val = trim($csv_data[35]);
							if($val && $arItem['PROPERTY_IMG_STATUS_VALUE']!=$val)
								$upd['img_status'] = $val;
							if($upd)
								CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], $upd);
							//
						}
					}
				}
				//if ($i > 1000)
					//break;
			}
			@fclose($fp);
			$success = true;
		}
	} else {
		die('Wrong fopen');
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