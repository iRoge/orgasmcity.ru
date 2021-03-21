<?
include("config.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule('fire.main');
$email_to = Fire_Settings::getOption('MONITORING_EMAIL');

if ($email_to == "" && SITE_ID != "")
{
	$rsSites = CSite::GetByID(SITE_ID);
	if ($arSite = $rsSites->Fetch())
	{
		$email_to = $arSite["EMAIL"];
	}
}
if ($email_to != "")
{
	$monitoring_disk = Fire_Settings::getOption('MONITORING_DISK');
	$monitoring_catalog = Fire_Settings::getOption('MONITORING_CATALOG');
	$monitoring_orders = Fire_Settings::getOption('MONITORING_ORDERS');
	$mail_text = "";

	/*
	// Проверка обновлений товаров и торговых предложений
	$arFilter = array(
		"IBLOCK_ID" => array(6),
		"<TIMESTAMP_X" => ConvertTimeStamp(time() - 26 * 3600, "FULL"),
	);
	$cnt = CIBlockElement::GetList(array(), $arFilter, array());
	if ($cnt > 0)
	{
		$mail_text = "По нескольким товарам (".$cnt.") более 24 часов не было обновления\n\n";
	}
	*/


	// Проверка запуска скриптов для выгрузки заказов и обновления статусов, а также для импорта товаров и торговых предложений
	$timestamp = time() - 24 * 3600;
	$dir_path = $_SERVER["DOCUMENT_ROOT"]."/cron/logs/";
	if ($monitoring_catalog == "Y")
	{
		if (!is_file($dir_path."catalog_import_date.txt") || filemtime($dir_path."catalog_import_date.txt") < $timestamp)
		{
			$mail_text .= "Импорт товаров не работает более 24 часов\n\n";
		}
		if (!is_file($dir_path."offers_import_date.txt") || filemtime($dir_path."offers_import_date.txt") < $timestamp)
		{
			$mail_text .= "Импорт торговых предложений не работает более 24 часов\n\n";
		}
	}

	if ($monitoring_orders == "Y")
	{
		if (!is_file($dir_path."orders_export_date.txt") || filemtime($dir_path."orders_export_date.txt") < $timestamp)
		{
			$mail_text .= "Выгрузка заказов не работает более 24 часов\n\n";
		}
		if (!is_file($dir_path."orders_status_date.txt") || filemtime($dir_path."orders_status_date.txt") < $timestamp)
		{
			$mail_text .= "Получение статусов заказов не работает более 24 часов\n\n";
		}
		
		//Проверка корректности ответа сервера при выгрузке заказов и обновлении статусов
		$dir_path = $_SERVER["DOCUMENT_ROOT"]."/cron/";
		//echo $dir_path."orders_export_logs/".date("d.m.Y", time()).".txt";
		$file_path = $dir_path."orders_export_logs/".date("Y-m-d", time() - 86400).".txt";
		if (is_file($file_path))
		{
			$fp = @fopen($file_path, "rb");
			if ($fp)
			{
				$contents = trim(fread($fp, filesize($file_path)));
				fclose($fp);
				if ($contents != "")
					$mail_text .= "Некорректный ответ сервера при выгрузке заказов\n".$contents."\n\n";
			}	
		}
		$file_path = $dir_path."orders_status_logs/".date("Y-m-d", time() - 86400).".txt";
		if (is_file($file_path))
		{
			$fp = @fopen($file_path, "rb");
			if ($fp)
			{
				$contents = trim(fread($fp, filesize($file_path)));
				fclose($fp);
				if ($contents != "")
					$mail_text .= "Некорректный ответ сервера при получении статусов заказов\n".$contents."\n\n";
			}	
		}
	}

	if ($monitoring_disk == "Y")
	{
		// Проверка свободного места на диске
		$df_c = disk_free_space($_SERVER["DOCUMENT_ROOT"]);
		if (($df_c / (1024*1024*1024)) < 5)
			$mail_text .= "Остаток свободного места на сервере ".round($df_c / (1024*1024*1024), 2)." Гб\n\n";	
	}

	if ($mail_text != "")
	{
		$email_from = COption::GetOptionString("main", "email_from");
		$server_name = COption::GetOptionString("main", "server_name");

		//$to      = 'fire-errors@p5s.ru';

		$subject = 'Monitoring alarm: '.$server_name;

		$EOL = CAllEvent::GetMailEOL(); // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
		$headers   = "From: ".$email_from.$EOL;
		//$headers   = "Bcc: izhdesign@mail.ru".$EOL;
		$headers  .= 'MIME-Version: 1.0'.$EOL;
		$headers .= 'Content-type: text/plain; charset=utf-8';

		bxmail($email_to, $subject, $mail_text, $headers);
		/*	
			$arFields = array(
				"TEXT" => $mail_text,
			);
			CEvent::SendImmediate
			(
			   "MONITORING", 
			   SITE_ID, 
			   $arFields
			);
		*/
	}
}
echo "ok";
?>