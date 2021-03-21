<?
include("config.php");

$start_time = time();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//fda2000 MS
$exclude = array();
CModule::IncludeModule('catalog');
CModule::IncludeModule('fire.main');
$profile_id = Fire_Settings::getOption('SETTINGS_OFFERS_IMPORT_PROFILE');
$ar_profile = CCatalogImport::GetByID($profile_id);
if(!$ar_profile)
	die("No profile");
parse_str($ar_profile["SETUP_VARS"], $profile_params);
//$OFFERS_IBLOCK_ID = Fire_Settings::getOption('SETTINGS_OFFERS_IBLOCK');
$OFFERS_IBLOCK_ID = intval($profile_params["IBLOCK_ID"]);
if($OFFERS_IBLOCK_ID <= 0)
	die("No IBLOCK ID");
if(Fire_Settings::getOption('SELFSHOP_TYPE')) {
	$rsItems = CIBlockElement::GetList(array(), array("!IBLOCK_SECTION_ID" => false,"IBLOCK_ID" => $OFFERS_IBLOCK_ID), false, false, array('XML_ID'));
	while($arItem = $rsItems->GetNext())
		$exclude[$arItem['XML_ID']] = $arItem['XML_ID'];
}
//
$catalog_props = [];
$res = CIBlock::GetProperties($OFFERS_IBLOCK_ID);
while($arr = $res->Fetch())
	$catalog_props[mb_strtoupper($arr['CODE'])] = $arr['ID'];

$import_price_type = Fire_Settings::getOption('IMPORT_PRICE_TYPE');
if ($import_price_type != 1 and $import_price_type != 2)
	$import_price_type = 1;
$import_nacenka = Fire_Settings::getOption('IMPORT_NACENKA');
if(!is_array($import_nacenka))
	$import_nacenka = array('0'=>(float)$import_nacenka);
krsort($import_nacenka, SORT_NUMERIC);
$import_rrc = Fire_Settings::getOption('IMPORT_PRODUCT_RRC');
$import_url = Fire_Settings::getOption('SETTINGS_OFFERS_IMPORT_PRICE_URL');
$shipping24 = Fire_Settings::getOption('IMPORT_PRODUCT_SHIPPING24');
$minstock = (int)Fire_Settings::getOption('IMPORT_PRODUCT_MINSTOCK');
set_time_limit (0);

$BaseID = CCatalogGroup::GetBaseGroup();
$BaseID = $BaseID['ID'];
if(!$BaseID)
	die('No Base Price!');

$import_data = fopen($import_url, 'r');
if(!$import_data)
	die('Error get feed!');
$upd = $head = [];
while (($data = fgetcsv($import_data, 1000, ";")) !== false) {
	if(!$head) {
		$head = $data;
		if(array_search('sku', $head)===false)
			$head = ['prodid', 'sku', 'qty', 'shippingdate', 'currency', 'price', 'basewholeprice', 'p5s_stock', 'SuperSale', 'StopPromo'];
		else
			continue;
	}
	$data = array_combine($head, $data);
	if(isset($data['WholePrice']))
		$data['basewholeprice'] = $data['WholePrice'];
	
	//fda2000 MS
	if($exclude[$data['sku']])
		continue;
	//
	$price = $import_price_type!=1? $data['basewholeprice'] : $data['price'];
	foreach($import_nacenka as $p=>$d)
		if((float)$p<=$price) {
			$price+= $price * $d/100;
			break;
		}
	if($import_rrc && $data['StopPromo'] && $price<$data['price']) //fda2000 RRC
		$price = $data['price'];
	$price = round($price, 2);
	$data['price'] = $price;
	if($shipping24) {
		$t = MakeTimeStamp($data['shippingdate']);
		if(!$data['shippingdate'] || !$t || ($t-time())/60/60 > 28)
			$data['qty'] = 0;
	}
	if($minstock>0 && $data['qty']<$minstock)
		$data['qty'] = 0;
	
	$upd[$data['sku']] = $data;
}
@fclose($import_data);

if($upd) {
	$el = new CIBlockElement;
	$Dates = array();
	\Bitrix\Catalog\Product\Sku::enableDeferredCalculation();
	
	//remove not exist in import
	$act = $profile_params['outFileAction'];
	if($act=='H' || $act=='D' || $act=='M' || $act=='F') {
		$arProductArray = \Bitrix\Catalog\ProductTable::getDefaultAvailableSettings();
		$filter = ['IBLOCK_ID' => $OFFERS_IBLOCK_ID];
		if($act=='F')
			$filter['=PROPERTY_supplier_VALUE'] = 'p5s';
		$rsItems = CIBlockElement::GetList([], $filter, false, false, array('ID', 'XML_ID'));
		while($arItem = $rsItems->Fetch()) 
			if(!$upd[$arItem['XML_ID']] && !$exclude[$arItem['XML_ID']]) {
				if($act=='D' || $act=='F')
					CIBlockElement::Delete($arItem['ID']);
				elseif($act=='H')
					$el->Update($arItem['ID'], ['ACTIVE' => 'N']);
				elseif($act=='M')
					\Bitrix\Catalog\Model\Product::update($arItem['ID'], $arProductArray);
			}
	}
	//
	$rsItems = CIBlockElement::GetList([], ['=XML_ID' => array_keys($upd), '=IBLOCK_ID' => $OFFERS_IBLOCK_ID], false, false, ['ID', 'IBLOCK_ID', 'XML_ID', 'PROPERTY_CML2_LINK', 'PROPERTY_shipping_date', 'PRICE_'.$BaseID, 'CURRENCY_'.$BaseID, 'QUANTITY', 'PURCHASING_PRICE', 'PURCHASING_CURRENCY', 'PROPERTY_p5s_stock', 'PROPERTY_SuperSale', 'PROPERTY_StopPromo', 'PROPERTY_BasePrice', 'PROPERTY_BasewholePrice']);
	
	while($arItem = $rsItems->fetch())
		if($data = $upd[$arItem['XML_ID']]) {
			$prod = [];
			if($arItem['QUANTITY']!=$data['qty']) {
				if( ($arItem['QUANTITY']>0 && $data['qty']<=0) || ($arItem['QUANTITY']<=0 && $data['qty']>0) )
					$Dates = false;
				$prod['QUANTITY'] = $data['qty'];
			}
			if($arItem['PURCHASING_PRICE']!=$data['basewholeprice'])
				$prod['PURCHASING_PRICE'] = $data['basewholeprice'];
			if($arItem['PURCHASING_CURRENCY']!=$data['currency'])
				$prod['PURCHASING_CURRENCY'] = $data['currency'];
			
			if($prod)
				CCatalogProduct::Update($arItem['ID'], $prod);
			
			if($arItem['PRICE_'.$BaseID]!=$data['price'] || $arItem['CURRENCY_'.$BaseID]!=$data['currency']) {
				$Dates = false;
				CPrice::SetBasePrice($arItem['ID'], $data['price'], $data['currency']);
			}
			
			$props = array();
			if($catalog_props['SHIPPING_DATE'])
				if($arItem['PROPERTY_SHIPPING_DATE_VALUE']!=$data['shippingdate'])
					$props['shipping_date'] = $data['shippingdate'];
			if($catalog_props['P5S_STOCK'])
				if($arItem['PROPERTY_P5S_STOCK_VALUE']!=$data['p5s_stock'])
					$props['p5s_stock'] = $data['p5s_stock'];
			if($catalog_props['SUPERSALE'])
				if($arItem['PROPERTY_SUPERSALE_VALUE']!=$data['SuperSale'])
					$props['SuperSale'] = $data['SuperSale'];
			if($catalog_props['STOPPROMO'])
				if($arItem['PROPERTY_STOPPROMO_VALUE']!=$data['StopPromo'])
					$props['StopPromo'] = $data['StopPromo'];
			if($catalog_props['BASEPRICE'])
				if($arItem['PROPERTY_BASEPRICE_VALUE']!=$data['price'])
					$props['BasePrice'] = $data['price'];
			if($catalog_props['BASEWHOLEPRICE'])
				if($arItem['PROPERTY_BASEWHOLEPRICE_VALUE']!=$data['basewholeprice'])
					$props['BasewholePrice'] = $data['basewholeprice'];
			if($props) {
				CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], $props);
				if($Dates!==false)
					$Dates[$arItem['PROPERTY_CML2_LINK_VALUE']] = 1;
			}
		}
		
		if($Dates) {
			$arr = unserialize(Fire_Settings::getModuleSetting('ClearCacheElements'));
			$arr = is_array($arr)? $arr : array();
			$arr = $arr +$Dates;
			Fire_Settings::setModuleSetting('ClearCacheElements', serialize($arr));
		}
		if($Dates===false) {
			CIBlock::clearIblockTagCache($OFFERS_IBLOCK_ID);
			Fire_Settings::setModuleSetting('ClearCacheElements', '');
		}
	
	\Bitrix\Catalog\Product\Sku::disableDeferredCalculation();
	\Bitrix\Catalog\Product\Sku::calculate();
}

$end_time = time();
echo "work_time ".ceil(($end_time - $start_time)/60)." minutes\n";
?>