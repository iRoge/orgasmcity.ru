<?php


namespace Qsoft\Pvzmap\PVZ;


use Qsoft\Pvzmap\iPvz;

class PickPoint implements iPvz
{
    private function prepareData()
    {
        return unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/PVZ/PickPoint.pvz'));
    }

    public function getData()
    {
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