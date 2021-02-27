<?
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../..');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$arResponse['ID'] = '1';
$arResponse['value']='0';

$url = 'https://' . SITE_SERVER_NAME . '/local/php_interface/seo_sitemap_run.php';

do {
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(array(
            'action' => 'sitemap_run',
            'value' => $arResponse['value'],
            'ID' => $arResponse['ID'],
            'pid' => $arResponse['pid'],
            'NS' => $arResponse['NS']
        ))
    ));
    $response = curl_exec($myCurl);
    curl_close($myCurl);
    //парсинг ответа
    $findStart = '(';
    $posStart = strpos($response, $findStart) + 1;
    $findEnd = ')';
    $posEnd = strpos($response, $findEnd);
    $responseParam = substr($response, $posStart, $posEnd - $posStart);
    if ($responseParam != '') {
        list($arResponse['ID'], $arResponse['value'], $arResponse['pid'], $arResponse['NS']) = explode(
            ", ",
            $responseParam
        );
        $arResponse['NS'] = json_decode(str_replace("'", '"', $arResponse['NS']));
    }
    //конец парсинга ответа
} while ($responseParam != '');
