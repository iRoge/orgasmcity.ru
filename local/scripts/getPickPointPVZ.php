<?php
$srcUrl = 'https://e-solution.pickpoint.ru/api/allpostamatdetaillist';
/**@var CurlHttpResponse $response **/
$response = CurlHttpClient::create()->
setOption(CURLOPT_ENCODING, '')->
setFollowLocation(true)->
setMaxRedirects(5)->
setTimeout(100000)->
send(
    HttpRequest::create()->
    setHeaderVar('Accept', 'text/html, application/json')->
    setHeaderVar('Accept-Language', 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4')->
    setMethod(HttpMethod::get())->
    setUrl(HttpUrl::parse($srcUrl))
);
$body = $response->getBody();

$Data = json_decode($body, true);
$clear = array(" ", "-");

// Сохраняем в базу данные полученные данные и обновляем те что уже есть.
foreach($Data as $key => $item) {
//	echo "\n\nkey=".$key;
//	Print_r($item);

    $item['CityName_Eng'] = str_replace("'","", strtolower($item['CityName_Eng']));

    //заполняем массив данными
    $NewPoint = array(
        "DeliveryTypeID" => 5, // метод доставки PickPoint
        "pp_Id" => $item['Id'],
        "pp_CitiId" => $item['CitiId'],
        "pp_CitiName" => $item['CitiName'],
        "pp_Region" => $item['Region'],
        "pp_CountryName" => $item['CountryName'],
        "pp_Number" => $item['Number'],
        "pp_Metro" => $item['Metro'],
        "pp_IndoorPlace" => $item['IndoorPlace'],
        "pp_Address" => $item['Address'],
        "pp_House" => $item['House'],
        "pp_PostCode" => $item['PostCode'],
        "pp_Name" => $item['Name'],
        "pp_WorkTime" => $item['WorkTime'],
        "pp_Status" => $item['Status'],
        "pp_TypeTitle" => $item['TypeTitle'],
        "pp_Cash" => $item['Cash'],
        "pp_Card" => $item['Card'],
        "pp_InDescription" => $item['InDescription'],
        "pp_OutDescription" => $item['OutDescription'],
        "pp_WorkTimeSMS" => $item['WorkTimeSMS'],
        "pp_CityName_Eng" => $item['CityName_Eng'],
        "PageUrl" => str_replace($clear,"_", strtolower($item['CityName_Eng']))
    );

    //Проверяем есть ли строчка про этот постомат в базе.
    $res = sql_query("SELECT DeliveryPointID FROM delivery_point WHERE pp_Id='".addslashes($item['Id'])."'");
    if (sql_num_rows($res) == 1) {
        //если строчка есть то UPDATE
        list($DeliveryPointID) = sql_fetch_row($res);
        updateVars("delivery_point", $NewPoint, "DeliveryPointID='".$DeliveryPointID."'");
    } else {
        //если строчки нет то INSERT
        $NewPoint['DateOfAdded'] = "NOW()";
        $DeliveryPointID = putVars("delivery_point", $NewPoint);
    }

    //Обрабатываем картинки, последовательно проверяем каждую
    //Если картинка задана, проверяем загружен ли соответсвующий файл у нас есть нет то загружаем.
    for ($i = 0; $i<3; $i++) {
        $itemKey = 'File'.$i;
        if (isset($item[$itemKey]) && strlen($item[$itemKey]) > 0) {
            $dst = "../../files/delivery_points/".$DeliveryPointID."_".$i.".jpg";
            if (!file_exists($dst)) {
                //FIXME: use real path
                $src = "https://e-solution.pickpoint.ru/api/".$item[$itemKey];
//				echo "copy  $src to $dst\n";
                ShopUtils::fetchFile($src, $dst);
            }
        }
    }
}
?>