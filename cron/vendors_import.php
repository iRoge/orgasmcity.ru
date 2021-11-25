<?
include("config.php");

//������ ��������������



define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!defined('LANGUAGE_ID') || preg_match('/^[a-z]{2}$/i', LANGUAGE_ID) !== 1)
	die('Language id is absent - defined site is bad');

CModule::IncludeModule('fire.main');
$IBLOCK_ID = Fire_Settings::getOption('SETTINGS_VENDOR_IBLOCK');
$profile_id = Fire_Settings::getOption('SETTINGS_VENDOR_IMPORT_PROFILE');
$import_url = Fire_Settings::getOption('SETTINGS_VENDOR_IMPORT_URL');

$siteID = Fire_Settings::getOption('SETTINGS_SITE');  // your site ID - need for language ID
if (preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
{
	define('SITE_ID', $siteID);
}
else
{
	die('No defined site - $siteID');
}

set_time_limit (0);

if (!defined("CATALOG_LOAD_NO_STEP"))
	define("CATALOG_LOAD_NO_STEP", true);

if (CModule::IncludeModule("catalog"))
{
	$ar_profile = CCatalogImport::GetByID($profile_id);
	if (!$ar_profile) die("No profile");

    $context = stream_context_create(array(
        'http'=>array(
            'timeout' => 300
        )
    ));
    $import_data = fopen($import_url, 'r', false, $context);
	$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/import/vendors.csv","wb");
	if ($import_data && $fp) {
		while (($str_data = fgets($import_data)) !== false) {
			$csv_data = str_getcsv($str_data, ';');
			$xml_id = trim($csv_data[0]);
			if($xml_id) {
				$arFilter = array(
					"XML_ID" => $xml_id,
					"IBLOCK_ID" => $IBLOCK_ID,
				);
				$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
				if (!$arItem = $rsItems->GetNext())
					fputcsv($fp, $csv_data, ';');
			}
		}
		@fclose($fp);
	}
	else
		exit();
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
	@copy($_SERVER["DOCUMENT_ROOT"]."/import/vendors.csv", $_SERVER["DOCUMENT_ROOT"]."/import/vendors/vendors_".date("d.m.Y H i").".csv");

	$timestamp = time() - 7 * 86400;
	$dir_path = $_SERVER["DOCUMENT_ROOT"]."/import/vendors/";
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
}
?>