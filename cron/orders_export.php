<?
include("config.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('fire.main');

$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/cron/logs/orders_export_date.txt","wb");
if ($fp)
{
	@fwrite($fp, date("d.m.Y H:i:s"));
	fclose($fp);
}
$log_error = "";

$api_key = trim(Fire_Settings::getOption('P5S_API_KEY'));
$Model = Fire_Settings::getOption('P5S_SHIPPING_MODEL');
$ERROR_MAIL = !Fire_Settings::getOption('P5S_NO_ERROR_MAIL')? Fire_Settings::getOption('MONITORING_EMAIL') : NULL;
$export_url = ($Model=='SELF'? Fire_Settings::getOption('SETTINGS_ORDER_EXPORT_NODS_URL') : Fire_Settings::getOption('SETTINGS_ORDER_EXPORT_URL'));
$PackDelivery = Fire_Settings::getOption('P5S_PACK_DELIVERY');

CModule::IncludeModule('sale');

$xmlDelivery = false;
//Соответствие доставок Битрикса доставкам P5S
$delivery_arr = array(
	'SELF_PICKUP' => 4,
	'PVZ_PICKPOINT_FREE' => 5,
	'PVZ_PICKPOINT' => 5,
	'COURIER_MOSCOW_FREE' => 1,
	'COURIER_MOSCOW' => 1,
	'DPD_FREE' => 8,
	'DPD' => 8,
	'RUSPOST_FREE' => 2,
	'RUSPOST' => 2,
	'CDEK_FREE' => 10,
	'CDEK' => 10,
	'PVZ_CDEK_FREE' => 11,
	'PVZ_CDEK' => 11
);
$arFilter = array(
	"STATUS_ID" => "ZS",
	"CANCELED" => "N",
);
$rsOrders = CSaleOrder::GetList(array(), $arFilter);
$xmlDelivery = false;
while ($arOrder = $rsOrders->GetNext())
{
	//fda2000 XML_ID
	if($xmlDelivery===false) {
		$xmlDelivery = [];
		$res = \Bitrix\Sale\Delivery\Services\Table::getList(['select'=>['ID', 'XML_ID']]);
		while($arDelivery=$res->fetch())
			$xmlDelivery[$arDelivery['ID']] = $arDelivery['XML_ID'];
	}
	$dsDelivery = $delivery_arr[$xmlDelivery[$arOrder['DELIVERY_ID']]];
	//
	
	$dbProps = CSaleOrderPropsValue::GetList(
		array("SORT" => "ASC"),
		array(
			"ORDER_ID" => $arOrder["ID"],
		)
	);
	$arOrder["LOCATION_NAME"] = "";
	while ($arProp = $dbProps->Fetch())
	{
		if ($arProp["VALUE"] != "")
		{
			if ($arProp["CODE"] == "LOCATION")
			{
				$res = \Bitrix\Sale\Location\LocationTable::getList(array(
					'filter' => array(
						'=ID' => $arProp["VALUE"], 
						'=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
						'=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
					),
					'select' => array(
						'I_ID' => 'PARENTS.ID',
						'I_NAME_RU' => 'PARENTS.NAME.NAME',
						'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
				//        'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
					),
					'order' => array(
						'PARENTS.DEPTH_LEVEL' => 'asc'
					)
				));
				while($item = $res->fetch())
				{
					if (in_array($item["I_TYPE_CODE"], array("CITY", "VILLAGE", "REGION")))
					{
						if ($arOrder["LOCATION_NAME"] != "")
							$arOrder["LOCATION_NAME"] .= " ";
						$arOrder["LOCATION_NAME"] .= $item["I_NAME_RU"];
					}
				}

/*
				if ($arLocs = CSaleLocation::GetByID($arProp["VALUE"], LANGUAGE_ID))
				{
					//print_r($arLocs);
					$arOrder["PROPS"]["LOCATION_CITY"] = $arLocs["CITY_NAME"];
				}
*/
			}
			else
				$arOrder["PROPS"][$arProp["CODE"]] = $arProp["VALUE"];
		}
	}
	if ($arOrder["PROPS"]["CITY"] != "")
		$arOrder["LOCATION_NAME"] .= " ".$arOrder["PROPS"]["CITY"];
	//echo $arOrder["DATE_INSERT"]."<br>";
	
	//fda2000 MS
	if(!$dsDelivery) {
		CModule::IncludeModule('catalog');
		$SHOP_IBLOCK_ID = Fire_Settings::getOption('SETTINGS_SHOP_IBLOCK');
		if($SHOP_IBLOCK_ID) {
			$rsItems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SHOP_IBLOCK_ID, 'PROPERTY_DELIVERY_ID'=>$arOrder["DELIVERY_ID"]), array('ID', 'IBLOCK_ID'));
			if($arItem = $rsItems->GetNext()) {
				$prefix = 'ORDER_';
				$rsItems = CIBlockElement::GetProperty($arItem['IBLOCK_ID'], $arItem['ID'], array(), array('CODE'=>$prefix.'%'));
				while($arItem = $rsItems->GetNext())
					if($arItem['CODE']=='ORDER_DELIVERY_ID') {
						$arOrder['DELIVERY_ID'] = $arItem['VALUE'];
						$dsDelivery = $delivery_arr[$xmlDelivery[$arOrder['DELIVERY_ID']]];
					} elseif($arItem['CODE']=='ORDER_LOCATION_NAME')
						$arOrder["LOCATION_NAME"] = $arItem['VALUE'];
					else
						$arOrder["PROPS"][mb_substr($arItem['CODE'], mb_strlen($prefix))] = $arItem['VALUE'];
			}
		}
	}
	//
	$curl_opt = array(
		"ApiKey" => $api_key,
		"TestMode" => 0,
		"ExtOrderID" => $arOrder["ACCOUNT_NUMBER"],
		"ExtOrderPaid" => ($arOrder["PAYED"] == "Y") ? 1 : 0,
		"ExtDeliveryCost" => intval($arOrder["PRICE_DELIVERY"]),
		"dsDelivery" => $dsDelivery,
		"packType" => $PackDelivery[$arOrder["DELIVERY_ID"]]? 2 : 1,
		"dsFio" => $arOrder["PROPS"]["NAME"]." ".$arOrder["PROPS"]["LAST_NAME"],
		"dsMobPhone" => $arOrder["PROPS"]["PHONE"],
		"dsEmail" => $arOrder["PROPS"]["EMAIL"],
//		"dsCity" => ($arOrder["PROPS"]["LOCATION_CITY"] != "") ? $arOrder["PROPS"]["LOCATION_CITY"] : $arOrder["PROPS"]["CITY"],
		"dsCity" => $arOrder["LOCATION_NAME"],
		"dsComments" => $arOrder["USER_DESCRIPTION"]." ".$arOrder["PROPS"]["ADDITIONAL_INFO"]." ".$arOrder["COMMENTS"],
		"ExtDateOfAdded" => ConvertDateTime($arOrder["DATE_INSERT"], "YYYY-MM-DD HH:MI:SS"),
	);
	if ($dsDelivery == 5 or $dsDelivery == 11) // PickPoint
		$curl_opt["dsPickPointID"] = $arOrder["PROPS"]["PICKPOINT_ID"];
	
	if ($dsDelivery == 2 or $dsDelivery == 1 or $dsDelivery == 8 or $dsDelivery == 10) // Почта, Курьер по Москве, Курьер по Питеру, СДЕК
	{
		$curl_opt["dsStreet"] = $arOrder["PROPS"]["STREET"];
		$curl_opt["dsHouse"] = $arOrder["PROPS"]["HOUSE"];
		$curl_opt["dsFlat"] = $arOrder["PROPS"]["FLAT"];

		if ($dsDelivery == 2) // Почта
		{
			$curl_opt["dsPostcode"] = $arOrder["PROPS"]["INDEX"];
		}
		
	}
	
	if($Model=='SELF')
		$curl_opt = array(
			"ApiKey" => $api_key,
			"TestMode" => 0
		);

	$dbBasketItems = CSaleBasket::GetList(
			array(
					"NAME" => "ASC",
					"ID" => "ASC"
				),
			array(
					"ORDER_ID" => $arOrder["ID"]
				),
			false,
			false,
			array()
		);
	$i = 0;
	$BasketItems = array();
	while ($arBasketItem = $dbBasketItems->GetNext())
		$BasketItems[] = $arBasketItem;
	
	//fda2000 partial payed order
	if(!$curl_opt['ExtOrderPaid']) {
		$order = \Bitrix\Sale\Order::load($arOrder["ID"]);
		$paid = $order->getSumPaid();
		if($paid) {
			$sum = 0;
			foreach($BasketItems as $arBasketItem)
				$sum+= $arBasketItem['PRICE']*$arBasketItem["QUANTITY"];
			
			$disc = min(1, $paid/$sum);
			foreach($BasketItems as $key=>$arBasketItem)
				$BasketItems[$key]['PRICE']-= $disc*$arBasketItem['PRICE'];
			
			if($paid>$sum)
				$curl_opt['ExtDeliveryCost']-= min($curl_opt['ExtDeliveryCost'], $paid-$sum);
			
			$curl_opt['dsComments'].= ' Already paid='.$paid;
		}
	}
	//
	foreach($BasketItems as $arBasketItem) {
		$db_res = CSaleBasket::GetPropsList(
				array(
						"SORT" => "ASC",
						"NAME" => "ASC"
					),
				array(
					"BASKET_ID" => $arBasketItem["ID"],
					"CODE" => "PRODUCT.XML_ID"
				)
			);
		if ($ar_res = $db_res->Fetch())
		{
			if (($pos = mb_strpos($ar_res["VALUE"], "#")) !== false)
				$product_xml_id = mb_substr($ar_res["VALUE"], $pos+1);
			else
				$product_xml_id = $ar_res["VALUE"];
			if ($i > 0)
				$curl_opt["order"] .= ",";
			$curl_opt["order"] .= $product_xml_id."-".$arBasketItem["QUANTITY"];
			if($Model!='SELF')
				$curl_opt["order"].= "-".round($arBasketItem["PRICE"],2);
		}
		$i++;
	}
//var_dump($curl_opt);die;
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $export_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_opt);
	$prop_values = array();
	try
	{
		$response = curl_exec($ch);	
		?><pre><?
		echo htmlspecialcharsbx($response);
		?></pre><?
		
		$error_arr = array(
		"2" => "Bad key, Проверьте корректность Вашего ApiKey",
		"3" => "Bad order request, Не корректные данные в поле order",
		"4" => "Order do not placed. Some items not at stock OR some problem in aID., Заказ не размещен, Либо каких-то товаров недостаточное количество на нашем складе, либо какие-то aID не найдены в нашей системе.",
		"5" => "TestMode. Data was checked. Order have NOT placed. Включен тестовый режим. Данные проверены, но заказ не размещается.",
		"6" => "Попытка размещения Drop Shipping заказа из под оптового аккаунта не имеющего статус Drop Shipping. Уточните у нашего менеджера - подписан ли Ваш «Договор Прямой Поставки» и зачислен ли депозит на Ваш аккаунт.",
		"7" => "Внутренний номер DS-заказа (ExtOrderID) не уникален.",
		"8" => "Не задан внутренний номер заказа (ExtOrderID).",
		"9" => "Не корректный формат даты размещения заказа ExtDateOfAdded. Корректный формат - YYYY-MM-DD HH:MM:SS.",
		"10" => "Не указан статус оплаты заказа (ExtOrderPaid).",
		"11" => "Не корректно указана стоимость доставки ExtDeliveryCost. Значение может быть только числом.",
		"12" => "Стоимость доставки ExtDeliveryCost не указанa.",
		"13" => "Не выбран способ доставки заказа dsDelivery.",
		"14" => "ФИО покупателя (dsFio) - обязательны для заполнения!",
		"15" => "Телефон покупателя (dsMobPhone) - обязателен для заполнения!",
		"16" => "Email покупателя (dsEmail) - обязателен для заполнения!",
		"17" => "Не известный метод доставки. Вероятно вы указали в поле dsDelivery, значение не соответствующее ни одному из обрабатываемых нами.",
		"18" => "В случае доставки Почтой России, название населенного пункта (dsCity) обязательно для заполнения!",
		"19" => "В случае доставки через PickPoint, индентификатор постомата или ПВЗ (dsPickPointID) обязателен!",
		);
		if (!empty($response))
		{
			$prop_values["P5S_RESPONSE"] = $response;
			try
			{	
				$xml = new SimpleXMLElement($response);
				//var_dump($xml);
				$status = intval($xml->ResultStatus);
				//foreach ($xml as $key => $value)
					//echo $key." ".$value."<br>";
				//echo $status;
				
				if ($status == 1) // Выгрузка прошла успешно
				{
					if (CSaleOrder::StatusOrder($arOrder["ID"], "DS")) //Передан в службу доставки
					{
						$prop_values["P5S_ID"] = intval($xml->orderID);
					}
					echo "Номер заказа - ".$xml->orderID;
				}
				else
				{
					?>Ошибка<br><?
					if (isset($error_arr[$status]))
					{
						$prop_values["P5S_ERROR"] = $error_arr[$status];
						echo $error_arr[$status]."<br>";
					}
					CSaleOrder::StatusOrder($arOrder["ID"], "ZE"); //Подтвержден, ошибка при выгрузке
					
					//fda2000
					if($ERROR_MAIL) {
						$email_from = COption::GetOptionString("main", "email_from");
						$server_name = COption::GetOptionString("main", "server_name");
						$EOL = CAllEvent::GetMailEOL(); // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
						$headers   = "From: ".$email_from.$EOL;
						$headers  .= 'MIME-Version: 1.0'.$EOL;
						$headers .= 'Content-type: text/plain; charset=utf-8';
						bxmail(
							$ERROR_MAIL, 
							'=?koi8-r?B?'.base64_encode(iconv("UTF-8", "KOI8-R//IGNORE", 'Выгрузка заказа с ошибкой: '.$server_name)).'?=', 
'Выгрузка заказа №'.$arOrder["ID"].' с ошибкой "'.($error_arr[$status]? $error_arr[$status] : $status).'"
'.$xml->ResultStatusMsg.'
'.$xml->timestamp.'
http://'.$server_name.'/bitrix/admin/sale_order_view.php?ID='.$arOrder["ID"].'
', 
							$headers
						);
					}
					//
					
				}
			}
			catch (Exception $e)
			{
				$log_error .= $e."\n";
				$prop_values["P5S_ERROR"] = $e;
				echo htmlspecialcharsbx($e);
			}
		}
		else
			$log_error .= "empty response\n";		
	}
	catch (Exception $e)
	{ 
		$log_error .= $e."\n";
		echo $e."<br>";
		$prop_values["P5S_ERROR"] = $e;
	}
	if (!empty($prop_values))
	{
		foreach ($prop_values as $prop_code => $prop_value)
		{
			$db_props = CSaleOrderProps::GetList(
				array(),
				array(
					"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"],
					"CODE" => $prop_code,
				),
				false,
				false,
				array()
			);
			if ($props = $db_props->Fetch())
			{
				$arFields = array(
					"ORDER_ID" => $arOrder["ID"],
					"ORDER_PROPS_ID" => $props["ID"],
					"VALUE" => $prop_value,
					"NAME" => $props["NAME"],
					"CODE" => $props["CODE"],
				);
				$db_vals = CSaleOrderPropsValue::GetList(
					array("SORT" => "ASC"),
					array(
						"ORDER_ID" => $arOrder["ID"],
						"ORDER_PROPS_ID" => $props["ID"],
					)
				);							
				if ($arVals = $db_vals->Fetch()) // если такое свойство заказа уже заполнено
				{
					if (($prop_code == "P5S_ERROR") and $arVals["VALUE"] != "") 
					{
						$arFields["VALUE"] = $arVals["VALUE"]."\n".$arFields["VALUE"]; //добвляем текст к уже имеющемуся
					}
					CSaleOrderPropsValue::Update($arVals["ID"],$arFields);
/*					
					?><pre><?
					print_r($arFields);
					?></pre><?
*/					
				}
				else
				{
					
					CSaleOrderPropsValue::Add($arFields);
/*					
					?><pre><?
					print_r($arFields);
					?></pre><?					
*/					
				}
			}
		}
	}
	curl_close($ch);								
}								
if ($log_error != "")
{
	$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/cron/orders_export_logs/".date("Y-m-d").".txt","ab");
	if ($fp)
	{
		@fwrite($fp, $log_error);
	}
}
?>