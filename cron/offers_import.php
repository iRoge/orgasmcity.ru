<?
include("config.php");

$start_time = time();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
ini_set('memory_limit', '2048M');
//fda2000 MS
CModule::IncludeModule('catalog');
CModule::IncludeModule('fire.main');
$OFFERS_IBLOCK_ID = Fire_Settings::getOption('SELFSHOP_TYPE')? Fire_Settings::getOption('SETTINGS_OFFERS_IBLOCK') : NULL;
$exclude = $restore = array();
if($OFFERS_IBLOCK_ID) {
	$rsItems = CIBlockElement::GetList(array(), array("!IBLOCK_SECTION_ID" => false,"IBLOCK_ID" => $OFFERS_IBLOCK_ID), false, false, array('ID', 'XML_ID', 'CATALOG_QUANTITY'));
	while($arItem = $rsItems->GetNext()) {
		$exclude[$arItem['XML_ID']] = $arItem['XML_ID'];
		$restore[$arItem['ID']] = $arItem['CATALOG_QUANTITY'];
	}
}
//
$siteID = Fire_Settings::getOption('SETTINGS_SITE');  // your site ID - need for language ID
$profile_id = Fire_Settings::getOption('SETTINGS_OFFERS_IMPORT_PROFILE');
$import_url = Fire_Settings::getOption('SETTINGS_OFFERS_IMPORT_STOCK_URL');
$import_nacenka = Fire_Settings::getOption('IMPORT_NACENKA');
$shipping24 = Fire_Settings::getOption('IMPORT_PRODUCT_SHIPPING24');
$minstock = (int)Fire_Settings::getOption('IMPORT_PRODUCT_MINSTOCK');
if(!is_array($import_nacenka))
	$import_nacenka = array('0'=>(float)$import_nacenka);
krsort($import_nacenka, SORT_NUMERIC);
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

if (CModule::IncludeModule("catalog"))
{
	$ar_profile = CCatalogImport::GetByID($profile_id);
	if (!$ar_profile) die("No profile");
	parse_str($ar_profile["SETUP_VARS"], $profile_params);
	$IBLOCK_ID = intval($profile_params["IBLOCK_ID"]);
	if ($IBLOCK_ID <= 0)
		die("No IBLOCK ID");

	$catalog = $catalog_props = $profile_fileds = $head = $head_remap = [];
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

	$BaseID = CCatalogGroup::GetBaseGroup();
	$BaseID = $BaseID['ID'];
	if(!$BaseID)
		die('No Base Price!');

	$success = false;
	$import_data = fopen($import_url, 'r');
	$data_file = $_SERVER["DOCUMENT_ROOT"].$profile_params['URL_DATA_FILE'];
	if ($import_data) {
		$fp = @fopen($data_file, 'wb');
		if ($fp) {
			$import_price_type = Fire_Settings::getOption('IMPORT_PRICE_TYPE');
			if ($import_price_type != 1 and $import_price_type != 2)
				$import_price_type = 1;
			$import_rrc = Fire_Settings::getOption('IMPORT_PRODUCT_RRC');

			$csv_data = '';
			while (($str_data = fgets($import_data)) !== false) {
				$csv_data = str_getcsv($str_data, ';');
				if(!$head) {
					$head = $csv_data;
					$cont = true;
					$head1 = array('prodid', 'sku', 'barcode', 'name', 'qty', 'shippingdate', 'weight', 'color', 'size', 'currency', 'price', 'basewholeprice', 'p5s_stock');
					foreach($head1 as $val)
						if($val!='basewholeprice')
							if(array_search($val, $head)===false)
								$cont = false;
					if(array_search('basewholeprice', $head)===false && array_search('WholePrice', $head)===false)
						$cont = false;
					if(!$cont) {
						$head = $head1;
						$head[] = 'SuperSale';
						$head[] = 'StopPromo';
					}
					$remap = [
						'prodid'=>'CML2_LINK',
						'sku'=>'IE_XML_ID',
						'name'=>'IE_NAME',
						'price'=>'CV_PRICE_'.$BaseID,
						'currency'=>'CV_CURRENCY_'.$BaseID,
						'shippingdate'=>'SHIPPING_DATE',
						'p5s_stock'=>'P5S_STOCK',
						'qty'=>'CP_QUANTITY',
						'weight'=>'CP_WEIGHT',
						'basewholeprice'=>'CP_PURCHASING_PRICE'
					];
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

					if($profile_params['first_names_r']==='Y') {
						$fileds = [];
						foreach($profile_fileds as $val) {
							$pos = array_search($val, $head_remap);
							if($pos!==false)
								$pos = $head[$pos];
							$fileds[] = $pos? $pos : $val;
						}
						fputcsv($fp, $fileds, ';');
					}

					if($cont)
						continue;
				}
				$csv_data_remap = array_combine($head_remap, $csv_data);
				$csv_data = array_combine($head, $csv_data);

				//fda2000 MS
				if($exclude[$csv_data['sku']])
					continue;
				//

				$exclude[$csv_data['sku']] = $csv_data['sku'];
				$csv_data_remap['IP_PROP'.$catalog_props['SUPPLIER']] = 'p5s';

				$opt_price = isset($csv_data['WholePrice'])? $csv_data['WholePrice'] : $csv_data['basewholeprice'];
				$price = $import_price_type!=1? $opt_price : $csv_data['price'];
				foreach($import_nacenka as $p=>$d)
					if((float)$p<=$price) {
						$price+= $price * $d/100;
						break;
					}
				if($import_rrc && $csv_data['StopPromo'] && $price<$csv_data['price']) //fda2000 RRC
					$price = $csv_data['price'];

				/*if($val = $exclude[$data['sku']]) {
					$data[4] = $val[0];
					$price = $val[1];
				}*/
				$price = round($price, 2);
				//

				if($shipping24) {
					$t = MakeTimeStamp($csv_data_remap['IP_PROP'.$catalog_props['SHIPPING_DATE']]);
					if(!$csv_data_remap['IP_PROP'.$catalog_props['SHIPPING_DATE']] || !$t || ($t-time())/60/60 > 28)
						$csv_data_remap['CP_QUANTITY'] = 0;
				}
				if($minstock>0 && $csv_data_remap['CP_QUANTITY']<$minstock)
					$csv_data_remap['CP_QUANTITY'] = 0;

				$csv_data_remap['CV_PRICE_'.$BaseID] = $price;
				$csv_data_remap['CP_PURCHASING_PRICE'] = $opt_price;
				$csv_data_remap['CP_PURCHASING_CURRENCY'] = $csv_data['currency'];
				if($catalog_props['BASEPRICE'])
					$csv_data_remap['IP_PROP'.$catalog_props['BASEPRICE']] = $price;
				if($catalog_props['BASEWHOLEPRICE'])
					$csv_data_remap['IP_PROP'.$catalog_props['BASEWHOLEPRICE']] = $opt_price;

				$data = [];
				foreach($profile_fileds as $val)
					$data[] = $csv_data_remap[$val];

				fputcsv($fp, $data, ';');
			}
			@fclose($fp);
			if($csv_data)
				$success = true;
		}
	}
	if ($success === false)
		exit;

	//remove not exist in import
	if($profile_params['outFileAction']=='F') {
		$rsItems = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID, /*'!XML_ID'=>$exclude,*/ '=PROPERTY_supplier_VALUE'=>'p5s'], false, false, array('ID', 'XML_ID'));
		while($arItem = $rsItems->Fetch())
			if(!$exclude[$arItem['XML_ID']])
				CIBlockElement::Delete($arItem['ID']);
	}
	//

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
	@copy($data_file, $_SERVER["DOCUMENT_ROOT"]."/import/offers/offers_".date("d.m.Y H i").".csv");

	//fda2000 MS Profile deactivate void
	foreach($restore as $ID=>$QUANTITY)
		CCatalogProduct::Update($ID, array('QUANTITY'=>$QUANTITY));
	//

	$timestamp = time() - 7 * 86400;
	$dir_path = $_SERVER["DOCUMENT_ROOT"]."/import/offers/";
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
	$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/cron/logs/offers_import_date.txt","wb");
	if ($fp)
	{
		@fwrite($fp, date("d.m.Y H:i:s"));
		fclose($fp);
	}
}

// Чистим кэш и отправляем запрос на каждую страницу каталога для автогенерации кеша
$CACHE_MANAGER->ClearByTag("catalogAll");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, DOMEN_NAME);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_HEADER, 0);

$output = curl_exec($ch);
curl_close($ch);
$mainSection = CIBlockSection::GetByID(MAIN_SECTION_ID)->GetNext();
$res = CIBlockSection::GetList(
    [
        "SORT" => "ASC",
    ],
    [
        "IBLOCK_ID" => IBLOCK_CATALOG,
        ">LEFT_MARGIN" => $mainSection["LEFT_MARGIN"],
        "<RIGHT_MARGIN" => $mainSection["RIGHT_MARGIN"],
    ],
    false,
    [
        "ID",
        "NAME",
        "SECTION_PAGE_URL",
    ]
);
while ($arItem = $res->GetNext()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, DOMEN_NAME . $arItem['SECTION_PAGE_URL']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $output = curl_exec($ch);
    curl_close($ch);
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, DOMEN_NAME . '/catalog/favorites');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_HEADER, 0);

$output = curl_exec($ch);
curl_close($ch);

$end_time = time();
echo "work_time ".ceil(($end_time - $start_time)/60)." minutes\n";
