<?php


namespace Qsoft\Pvzmap;

interface iPvz
{
    /**
     * Возвращает json службы доставки для использования в js
     * @return string
     */
    public function getData();


    /**
     * Возвращает массив службы доставки для дополнительной обработки
     * @return array
     */
    public function getArray();

    public function getArrayByCity($city, $arPVZ);
}
