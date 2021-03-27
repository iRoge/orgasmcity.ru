<?php


namespace Qsoft\Pvzmap\PVZ;

use COption;
use Qsoft\Pvzmap\iPvz;
use Qsoft\Pvzmap\PVZFactory;

class FivePost implements iPvz
{

/* закомментировано до лучших времен
    private function loadData($i)
    {
        //Получение данных вернуть ответ если нет или выдать ошибку

        $token = $this->getToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api-omni.x5.ru/api/v1/pickuppoints/query');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token, 'content-type:application/json'));
        $data = json_encode(['pageSize' => 1000, 'pageNumber' => $i]);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $data
        );

        $response = curl_exec($ch);

        curl_close($ch);
        //myLog($response);
        return !empty($response) ? $response : false;
    }

    private function prepareData()
    {
        //Подготовка данных к нужному формату удаление ненужной инфы. Вернуть json.
        $resEmpty = false;
        $i = 0;
        $arReturn = [];
        while (!$resEmpty) {
            $arPVZ = json_decode($this->loadData($i), JSON_OBJECT_AS_ARRAY);
            foreach ($arPVZ['content'] as $pvz) {
                unset($pvz['contractorMap']);
                $city = str_replace(' г', '', $pvz['address']['city']);
                $city = strtoupper($city);
                $city = trim($city);
                $arReturn[$city][] = $pvz;
            }
            $resEmpty = $arPVZ['empty'];
            //$resEmpty = true;
            $i++;
            //myLog($arPVZ);
        }
        return $arReturn;
    }
*/
    //временная функция получения ПВЗ
    private function prepareData()
    {
        $arFiltredPVZ = [];
        foreach (PVZFactory::loadPVZ() as $pvz) {
            $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
            $hidePostamat[$pvz['CLASS_NAME']] = $pvz['HIDE_POSTAMAT'];
        }
        $hideOnlyPrepayment = $hideOnlyPrepayment['FivePost'];
        $hidePostamat = $hidePostamat['FivePost'];
        $arPVZ = unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/5POST.pvz'));
        foreach ($arPVZ as $city => $arPVZInCity) {
            foreach ($arPVZInCity as $numPVZ => $pvz) {
                if ($hideOnlyPrepayment == 'Y' && !($pvz['cashAllowed'] || $pvz['cardAllowed'])) {
                    continue;
                }
                if ($hidePostamat == 'Y' && ($pvz['type'] === 'POSTAMAT')) {
                    continue;
                }
                $arFiltredPVZ[$city][] = $pvz;
            }
        }
        return $arFiltredPVZ;
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
            'https://api-omni.x5.ru/jwt-generate-claims/rs256/1?apikey=' . COption::GetOptionString("likee", "apikey_5post", '')
        );
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            'subject=OpenAPI&audience=A122019!&partnerId'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        $arToken = json_decode($response, JSON_OBJECT_AS_ARRAY);
        return $arToken['jwt'];
    }
}
