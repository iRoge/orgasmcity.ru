<?php


namespace Qsoft\Pvzmap\PVZ;

use Qsoft\Pvzmap\iPvz;
use Qsoft\Pvzmap\PVZFactory;

class IML implements iPvz
{

    /**
     * Получаем информацию со службы доставки
     * @return string|false
     */
    /*
    private function loadData()
    {
        $url = 'http://list.iml.ru/sd?type=json';
        $response = file_get_contents($url);
        return !empty($response) ? $response : false;
    }

    private function loadPost()
    {
        $url = 'http://list.iml.ru/PostCode?type=json';
        $response = file_get_contents($url);
        return !empty($response) ? $response : false;
    }

    private function prepareData()
    {
        //Подготовка данных к нужному формату удаление ненужной инфы. Вернуть array.
        $arPVZ = json_decode($this->loadData(), JSON_OBJECT_AS_ARRAY);
        $arReturn = [];

        // Чекбокс HIDE_POST
        foreach (PVZFactory::loadPVZ() as $pvz) {
            $hidePost[$pvz['CLASS_NAME']] = $pvz['HIDE_POST'];
            $hideOnlyPrepayment[$pvz['CLASS_NAME']] = $pvz['HIDE_ONLY_PREPAYMENT'];
            $hidePostamat[$pvz['CLASS_NAME']] = $pvz['HIDE_POSTAMAT'];
        }

        // Массив постоматов IML/Почта
        if ($hidePost['IML'] == 'Y') {
            $arrayPVZ = json_decode($this->loadPost(), JSON_OBJECT_AS_ARRAY);
            foreach ($arrayPVZ as $pvz) {
                if ($pvz['RegionIML'] == 'ПОЧТА') {
                    $arHidePost[$pvz['LocationCode']] = $pvz['Name'];
                }
            }
        }

        foreach ($arPVZ as $pvz) {
            if ($hidePost['IML'] == 'Y' && isset($arHidePost[$pvz['Code']])) {
                continue;
            }
            if ($hideOnlyPrepayment['IML'] == 'Y' && (!($pvz['PaymentType'] == 1 || $pvz['PaymentType'] == 2 || $pvz['PaymentType'] == 3))) {
                continue;
            }
            if ($hidePostamat['IML'] == 'Y' && ($pvz['Type'] != 1)) {
                continue;
            }
            $arReturn[$pvz['RegionCode']][] = $pvz;
        }

        return $arReturn;
    }
*/

    //временная функция получения ПВЗ
    private function prepareData()
    {
        return unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/IML.pvz'));
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
}
