<?php

use Bitrix\Main\Loader;

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

while (ob_get_level()) {
    ob_end_flush();
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://e-solution.pickpoint.ru/api/allpostamatdetaillist');
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
));

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);

Loader::includeModule('qsoft.pvzmap');
//Подготовка данных к нужному формату удаление ненужной инфы. Вернуть json.
foreach (\Qsoft\Pvzmap\PVZFactory::loadPVZ() as $pvz) {
    $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
    $hidePostamat[$pvz['CLASS_NAME']] = $pvz['HIDE_POSTAMAT'];
}
$hideOnlyPrepayment = $hideOnlyPrepayment['PickPoint'];
$hidePostamat = $hidePostamat['PickPoint'];
$arResult = [];
$countPVZ = 0;

foreach ($data as $pvz) {
    if ($hideOnlyPrepayment == 'Y' && !$pvz['Cash'] && $pvz['Card'] !== '1') {
        continue;
    }

    if ($hidePostamat == 'Y' && $pvz['TypeTitle'] == 'АПТ') {
        continue;
    }

    switch ($pvz['Region']) {
        case 'Московская обл.':
            $city = 'МОСКВА';
            break;
        case 'Ленинградская обл.':
            $city = 'САНКТ-ПЕТЕРБУРГ';
            break;
        default:
            $city = mb_strtoupper($pvz['CitiName']);

            if (mb_strpos($city, ',') || mb_strpos($city, '(')) {
                preg_match('/(.+)(\s\(|,)/U', $city, $matches);
                $city = $matches[1];
            }
    }

    $arResult[$city][] = $pvz;
    $countPVZ++;
}

if (file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/PickPoint.pvz', serialize($arResult))) {
    echo 'Обновлено сейчас (получено ' . $countPVZ . ' ПВЗ)' . PHP_EOL;
} else {
    echo 'Ошибка записи данных в файл' . PHP_EOL;
}
