<?
include("config.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/cron/logs/orders_status_date.txt","wb");
if ($fp)
{
	@fwrite($fp, date("d.m.Y H:i:s"));
	fclose($fp);
}
$log_error = "";
$ChangeStatus = array();

CModule::IncludeModule('sale');
CModule::IncludeModule('fire.main');

if(Fire_Settings::getOption('P5S_SHIPPING_MODEL')=='SELF')
	return;
$import_url = Fire_Settings::getOption('SETTINGS_ORDER_STATUS_URL');
$arFilter = array(
	"STATUS_ID" => array("DS", "PR", "WC", "IR", "RS", "SP", "IC", "SC", "WP"),
	"CANCELED" => "N"
);

$curl_opt = array(
	"ApiKey" => trim(Fire_Settings::getOption('P5S_API_KEY'))
);
$rsOrders = CSaleOrder::GetList(array(), $arFilter);
$i = 0;
$status_arr = array(
	"1" => "DS", 
	"2" => "PR", 
	"3" => "WC",
	"4" => "IR",
	"5" => "RS",
	"6" => "SP",
	"7" => "F",
	"8" => "C",
	"9" => "IC",
	"10" => "AC",
	"11" => "SC",
	"12" => "WP",
	"13" => "DE",
);
while ($arOrder = $rsOrders->GetNext())
{
	$dbProps = CSaleOrderPropsValue::GetList(
		array("SORT" => "ASC"),
		array(
			"ORDER_ID" => $arOrder["ID"],
			"CODE" => "P5S_ID"
		)
	);
	if ($arProp = $dbProps->Fetch())
	{
		if ($arProp["VALUE"] != "")
		{
//			if ($i > 0)
//				$curl_opt["orderID"] .= ',';
			$curl_opt["orderID"] = $arProp["VALUE"];
			$i++;
			//$curl_opt["ExtOrderID"] = $arOrder["ID"];
		}
	$error = "";
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $import_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_opt);
	
	ini_set('default_socket_timeout', 300);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	try
	{
		$response = @curl_exec($ch);
		
		if (!empty($response))
		{
			try
			{
				$xml = @new SimpleXMLElement($response);
				$status = intval($xml->ResultStatus);
				if ($status == 1)
				{
					$order_id = intval($xml->Orders->Orders_Item->ExtOrderID);
					if ($order_id == $arOrder["ACCOUNT_NUMBER"])
					{
						$order_status = intval($xml->Orders->Orders_Item->status);
						if ($status_arr[$order_status] != "" and $status_arr[$order_status] != $arOrder["STATUS_ID"])
						{
							//echo $status_arr[$order_status]."<br>";
							CSaleOrder::StatusOrder($arOrder["ID"], $status_arr[$order_status]);
							if (in_array($order_status, array(8,10,13)))
							{
								CSaleOrder::CancelOrder($arOrder["ID"], "Y");
							}
							//fda2000 set shipment
							if(in_array($order_status, array(6, 7, 11))) {
								$order = Bitrix\Sale\Order::load($arOrder['ID']);
								foreach($order->getShipmentCollection()->getNotSystemItems() as $shipment)
										$shipment->setFields([
											'DEDUCTED'		=>'Y',
											'STATUS_ID'		=>'DF',
											'ALLOW_DELIVERY'=>'Y'
										]);
								if($order_status==7)
									if(!$order->isPaid())
										foreach($order->getPaymentCollection() as $Payment)
											$Payment->setPaid('Y');
								$order->save();
								if($order_status==7 && !$order->isPaid())
									CSaleOrder::PayOrder($arOrder['ID'], 'Y');
							}
							//
						}
					}
				}
				else
				if ($status == 21 || $status == 20) {
					$error = "Заказ не найден";
					$ChangeStatus[$arOrder["ID"]] = $arOrder["ID"];
				}
			}
			catch (Exception $e)
			{
				$title = NULL;
				preg_match('/<title>([^<]+)<\/title>/i', $response, $matches);
				if($matches)
					$title = $matches[1];
				if(!$title) {
					preg_match('/<h1>([^<]+)<\/h1>/i', $response, $matches);
					if($matches)
						$title = $matches[1];
				}
				$error = 'Получен некорректный ответ от сервера';
				if($title) {
					$error.= ': '.$title;
					$log_error.= $error.", Bitrix ID - ".$arOrder["ID"].", P5S ID - ".$curl_opt["orderID"]."\n";
				} else {
					//$log_error .= $e."\n";
					$log_error .= $error.': '.$response."\n";
					//$error = $e;
					$log_error.= ", Bitrix ID - ".$arOrder["ID"].", P5S ID - ".$curl_opt["orderID"]."\n";
				}
				echo htmlspecialcharsbx($e);
			}
		}
		else
			$log_error .= "Получен пустой ответ от сервера, Bitrix ID - ".$arOrder["ID"].", P5S ID - ".$curl_opt["orderID"]."\n";
	}
	catch (Exception $e)
	{
		$log_error .= $e.", ";
		$log_error .= "Bitrix ID - ".$arOrder["ID"].", P5S ID - ".$curl_opt["orderID"]."\n";
		$error = $e;
		echo $e."<br>";
	}
	if ($error != "")
	{
			$db_props = CSaleOrderProps::GetList(
				array(),
				array(
					"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"],
					"CODE" => "P5S_ERROR",
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
					"VALUE" => $error,
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
					if ($arVals["VALUE"] != "") 
					{
						$arFields["VALUE"] = $arVals["VALUE"]."\n".$arFields["VALUE"]; //добавляем текст к уже имеющемуся
					}
					CSaleOrderPropsValue::Update($arVals["ID"],$arFields);
				}
				else
				{
					
					CSaleOrderPropsValue::Add($arFields);
				}
			}	
	}
	curl_close($ch);
	}
}
if ($log_error != "")
{
	$log_error = date("d.m.Y H:i:s")."\n".$log_error;
	$name = $_SERVER["DOCUMENT_ROOT"]."/cron/orders_status_logs/".date("Y-m-d").".txt";
	if(!file_exists($name))
		$log_error = "Если вы получаете такие сообщения 2 и более дней подряд, обратитесь в тех.поддержку проекта Огонь\n".$log_error;
	$fp = @fopen($name,"ab");
	if ($fp)
	{
		@fwrite($fp, $log_error);
	}
}

//fda2000
if($ChangeStatus) {
	$ERROR_MAIL = !Fire_Settings::getOption('P5S_NO_ERROR_MAIL')? Fire_Settings::getOption('MONITORING_EMAIL') : NULL;
	if($ERROR_MAIL) {
		$email_from = COption::GetOptionString("main", "email_from");
		$server_name = COption::GetOptionString("main", "server_name");
		$EOL = CAllEvent::GetMailEOL(); // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
		$headers   = "From: ".$email_from.$EOL;
		$headers  .= 'MIME-Version: 1.0'.$EOL;
		$headers .= 'Content-type: text/plain; charset=utf-8';
		$content = '';
		foreach($ChangeStatus as $ID)
			if(CSaleOrder::StatusOrder($ID, 'ZF'))
				$content.= '
Заказ №'.$ID.' - http://'.$server_name.'/bitrix/admin/sale_order_view.php?ID='.$ID;
		if($content)
			bxmail($ERROR_MAIL, 'Не найдены заказы у поставщика после обновления статуса', 'Следующие заказы не найдены у поставщика после обновления статуса:'.$content, $headers);
	}
}
//
?>