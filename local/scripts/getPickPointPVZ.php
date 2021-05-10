<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://e-solution.pickpoint.ru/api/allpostamatdetaillist');
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4'",
    'Accept: text/html, application/json'
));

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);

if (file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/PickPoint.pvz', serialize($data))) {
    echo 'Обновлено сейчас (получено ' . count($data) . ' ПВЗ)' . PHP_EOL;
} else {
    echo 'Ошибка записи данных в файл' . PHP_EOL;
}
