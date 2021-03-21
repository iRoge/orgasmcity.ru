<?
include("config.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
//Импорт цветов

@set_time_limit(0);
$start_time = time();
// подключаем модули
CModule::IncludeModule('highloadblock');
CModule::IncludeModule('fire.main');
$color_hl = Fire_Settings::getOption('SETTINGS_COLOR_HL');
$import_url = Fire_Settings::getOption('SETTINGS_COLOR_IMPORT_URL');

// необходимые классы
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


$items_hlblock   = HL\HighloadBlockTable::getById($color_hl)->fetch();
//var_dump($hlblock);
$items_entity   = HL\HighloadBlockTable::compileEntity( $items_hlblock );
$items_entity_data_class = $items_entity->getDataClass();


$import_data = file_get_contents($import_url);
if ($import_data !== false)
{
	$fp = @fopen($_SERVER["DOCUMENT_ROOT"]."/import/colors.csv","wb");
	if ($fp)
	{
		@fwrite($fp, $import_data);
		@fclose($fp);		
	}
	else
		exit();
}
else
	exit();

$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/import/colors.csv", "r");
$items_added = 0;

$itemsGetListParams = Array(
    "select" => Array("ID", "UF_XML_ID"),
    "filter" => Array(),
	"order" => Array(
		"ID" => "ASC",
	),
    "limit" => 1,
);

if ($handle)
{
	$i = 0;
	while (($data = fgetcsv($handle, 1000, ";")) !== false)
	{
		$i++;
		if ($i == 1)
			continue;

		$name = trim($data[0]);
		$xml_id = trim($data[1]);

		if ($name == "" or $xml_id == "")
			continue;

		$itemsGetListParams["filter"] = array(
			"UF_XML_ID" => $xml_id,
		);
		$rsData = $items_entity_data_class::getList($itemsGetListParams);
		$rsData = new CDBResult($rsData);
	
		if ($arRes = $rsData->Fetch())
		{
			continue;			
		}

		//fda2000 add image
		$file = $temp_file = NULL;
		if($url = trim($data[2])) {
			if($temp_file = tempnam(sys_get_temp_dir(), 'ColorUpload')) {
				file_put_contents($temp_file, file_get_contents($url));
				if($size=mime_content_type($temp_file))
					$file = array(
						'name'=> basename($url),
						'tmp_name'=> $temp_file,
						'size'=>filesize($temp_file),
						'type'=>$size
					);
			}
		}
		$item_result = $items_entity_data_class::add(array(
			'UF_NAME'	=> $name,
			'UF_XML_ID' => $xml_id,
			'UF_FILE' => $file
		));
		if($temp_file)
			unlink($temp_file);
		//
		
//		$items_added++;

//		if ($i > 10)
//			break;		
//		if ($items_added > 50)
//			break;
	}
	fclose($handle);	
}
@copy($_SERVER["DOCUMENT_ROOT"]."/import/colors.csv", $_SERVER["DOCUMENT_ROOT"]."/import/colors/colors_".date("d.m.Y H i").".csv");		

$timestamp = time() - 7 * 86400;
$dir_path = $_SERVER["DOCUMENT_ROOT"]."/import/colors/";
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
$end_time = time();
echo "скрипт работал ".($end_time - $start_time)." секунд";

?>