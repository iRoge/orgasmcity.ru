<?php


namespace Qsoft\Pvzmap\PVZ;

use COption;
use Qsoft\Pvzmap\iPvz;
use Qsoft\Pvzmap\PVZFactory;

class CDEK implements iPvz
{
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
/*
    private function prepareData()
    {
        //Подготовка данных к нужному формату удаление ненужной инфы. Вернуть json.
        foreach (PVZFactory::loadPVZ() as $pvz) {
            $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
        }
        $hideOnlyPrepayment = $hideOnlyPrepayment['CDEK'];

        $arPVZ = json_decode($this->loadData(), JSON_OBJECT_AS_ARRAY);
        $arReturn = [];

        foreach ($arPVZ['pvz'] as $pvz) {
            if ($hideOnlyPrepayment == 'Y' && (!($pvz['haveCash'] || $pvz['haveCashless']))) {
                continue;
            }
            $city = mb_strtoupper($pvz['city']);

            if (mb_strpos($city, ',') || mb_strpos($city, '(')) {
                preg_match('/(.+)(\s\(|,)/U', $city, $matches);
                $city = $matches[1];
            }

            $arReturn[$city][] = $pvz;
        }

        return $arReturn;
    }
*/
    //временная функция получения ПВЗ
    private function prepareData()
    {
        return unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/CDEK.pvz'));
    }

    public function getData()
    {
        //Обертка  prepareData
        return json_encode($this->prepareData());
    }

    public function getArray()
    {
        return $this->prepareData();
    }
    public function getArrayByCity($city, $arPVZ)
    {
        $city = mb_strtoupper($city);

        return $arPVZ[$city];
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
            ."&client_id=" . COption::GetOptionString("likee", "login_cdek", '')
            ."&client_secret=" . COption::GetOptionString("likee", "password_cdek", '')
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        $arToken = json_decode($response, JSON_OBJECT_AS_ARRAY);

        return $arToken['access_token'];
    }
}
