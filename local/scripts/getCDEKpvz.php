<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

while (ob_get_level()) {
    ob_end_flush();
}

use Bitrix\Main\Loader;

class GetCDEKPvz
{
    private $fileLog = "get_CDEK_pvz_logfile.txt";

    private function loadData()
    {
        //Получение данных вернуть ответ если нет или выдать ошибку
        $token = $this->getToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.cdek.ru/v2/offices');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return !empty($response) ? $response : false;
    }

    public function prepareData()
    {
        orgasm_logger('Старт получения ПВЗ CDEK', $this->fileLog);
        Loader::includeModule('qsoft.pvzmap');
        //Подготовка данных к нужному формату удаление ненужной инфы. Вернуть json.
        foreach (\Qsoft\Pvzmap\PVZFactory::loadPVZ() as $pvz) {
            $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
            $hidePostamat[$pvz['CLASS_NAME']] = $pvz['HIDE_POSTAMAT'];
        }
        $hideOnlyPrepayment = $hideOnlyPrepayment['CDEK'];
        $hidePostamat = $hidePostamat['CDEK'];

        $arPVZ = json_decode($this->loadData(), JSON_OBJECT_AS_ARRAY);
        $arReturn = [];
        $countPVZ = 0;

        foreach ($arPVZ['pvz'] as $pvz) {
            if ($hideOnlyPrepayment == 'Y' && (!($pvz['haveCash'] || $pvz['haveCashless']))) {
                continue;
            }
            if ($hidePostamat == 'Y' && $pvz['type'] == 'POSTAMAT') {
                continue;
            }
            if (!$pvz['isHandout']) {
                continue;
            }

            switch ($pvz['regionName']) {
                case 'Московская обл.':
                    $city = 'МОСКВА';
                    break;
                case 'Ленинградская обл.':
                    $city = 'САНКТ-ПЕТЕРБУРГ';
                    break;
                default:
                    $city = mb_strtoupper($pvz['city']);

                    if (mb_strpos($city, ',') || mb_strpos($city, '(')) {
                        preg_match('/(.+)(\s\(|,)/U', $city, $matches);
                        $city = $matches[1];
                    }
            }

            $arReturn[$city][] = $pvz;
            $countPVZ++;
        }
        if ($countPVZ > 1000) {
            orgasm_logger('Получение завершено', $this->fileLog);
            orgasm_logger('Запись в файл', $this->fileLog);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/CDEK.pvz', serialize($arReturn));
            orgasm_logger('Файл создан ' . $_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/CDEK.pvz', $this->fileLog);
        } else {
            orgasm_logger('HUOMIOTA!!! Получено мало (' . $countPVZ . ') ПВЗ, ожидалось более 1000. Файл не обновлен!', $this->fileLog);
        }

        return $countPVZ;
    }

    private function getToken()
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            'http://api.cdek.ru/v2/oauth/token'
        );
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "grant_type=" . "client_credentials"
            . "&client_id=" . COption::GetOptionString("likee", "login_cdek", '')
            . "&client_secret=" . COption::GetOptionString("likee", "password_cdek", '')
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        $arToken = json_decode($response, JSON_OBJECT_AS_ARRAY);

        return $arToken['access_token'];
    }
}

$arCDEKPvz = new GetCDEKPvz();
$countPVZ = $arCDEKPvz->prepareData();
if ($countPVZ > 1000) {
    echo 'Обновлено сейчас (получено ' . $countPVZ . ' ПВЗ)' . PHP_EOL;
} else {
    echo 'HUOMIOTA!!! Файл не обновлен! Получено мало (' . $countPVZ . ') ПВЗ, ожидалось более 1000.' . PHP_EOL;
}

