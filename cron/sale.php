<?
include("config.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);

$start_time = time();
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('fire.main');

$IBLOCK_ID = Fire_Settings::getOption('SETTINGS_OFFERS_IBLOCK');
$SITE_ID = Fire_Settings::getOption('SETTINGS_SITE');

define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'].'/cron/logs/log_sale.txt');

$sale_items_ids = [];
$skuInfo = CCatalogSku::GetInfoByOfferIBlock($IBLOCK_ID);
$IBLOCK_CATALOG_ID = $skuInfo['PRODUCT_IBLOCK_ID'];
$Prices = CIBlockPriceTools::GetCatalogPrices($IBLOCK_ID, array('BASE'));
$Prices = $Prices['BASE'];

$offersFilter = [
	'IBLOCK_ID' => $IBLOCK_ID,
	'HIDE_NOT_AVAILABLE' => 'Y',
	'CATALOG_AVAILABLE' => 'Y',
	'ACTIVE' => 'Y',
	//'SHOW_PRICE_COUNT' => 1
];
$rsOffers = CIBlockElement::GetList([], $offersFilter, false, false, ['ID', 'PROPERTY_'.$skuInfo['SKU_PROPERTY_ID'], $Prices['SELECT']/*, 'CATALOG_QUANTITY', 'CATALOG_SHOP_QUANTITY_'*/]);
while($arOffer = $rsOffers->GetNext()) {
	$item_id = $arOffer['PROPERTY_'.$skuInfo['SKU_PROPERTY_ID'].'_VALUE'];
	
	$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
		$arOffer['ID'],
		[],//$USER->GetUserGroupArray(),
		"N",
		[],
		$SITE_ID
	);
	
	$calculatePrice = $arOffer['CATALOG_PRICE_'.$Prices['ID']];
	$discountPrice = CCatalogProduct::CountPriceWithDiscount(
		$calculatePrice,
		$arOffer['CATALOG_CURRENCY_'.$Prices['ID']],
		$arDiscounts
	);
	
	$upd = $sale_items_ids[$item_id];
	if($upd)
		$upd['price'] = min($upd['price'], $discountPrice);
	else
		$upd = ['price'=>$discountPrice, 'sale'=>'', 'DiscountID'=>false];
	
	if($calculatePrice!=$discountPrice) {
		$percent = ($calculatePrice-$discountPrice)/$calculatePrice*100;
		if($percent<0.01)
			$percent = 0.01;
		$upd["sale"] = max($upd['sale'], $percent);
		
		foreach($arDiscounts as $Discount)
			if(!$upd['DiscountID'] || array_search($Discount['ID'], $upd['DiscountID'])===false)
				$upd['DiscountID'][] = $Discount['ID'];
	}
	
	$sale_items_ids[$item_id] = $upd;
}
foreach($sale_items_ids as $item_id=>$upd)
	CIBlockElement::SetPropertyValuesEx($item_id, $IBLOCK_CATALOG_ID, $upd);

$end_time = time();
//echo "work_time ".ceil(($end_time - $start_time)/60)." minutes\n";
AddMessage2Log("work_time ".ceil(($end_time - $start_time)/60)." minutes\n");
?>