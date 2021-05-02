<?php


namespace Qsoft\Pvzmap;

//Название лучше нужно придумать
use Bitrix\Sale\Delivery\Services\Base;

class PVZFactory
{
    private static $namespace = 'Qsoft\\Pvzmap\\PVZ\\';
    private static $arPVZ = [];

    /**
     * Возвращает json определенной службы доставки для использования в js.
     * При передаче доп. аргумента возвращает массив для обработки.
     * @param $class_name string
     * @param bool $return_array
     * @return string
     */
    public static function getPVZ($class_name, $return_array = false)
    {
        /** @var iPvz $class */
        $class_name = self::$namespace . $class_name; //Пример new Qsoft\\Pvzmap\\PVZ\\CDEK()
        $class = new $class_name();
        if ($return_array === true) {
            $return = $class->getArray();
        } else {
            $return = $class->getData();
        }
        return $return;
    }

    /**
     * Возвращает json всех доступных служб доставки для текущего заказа для использования в js
     * @return string|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getPVZCollection()
    {
        $arPVZ = self::loadPVZ();

        if (empty($arPVZ)) {
            return false;
        }

        $arReturn['CLASS_MAP'] = self::getClassMap($arPVZ);

        foreach ($arReturn['CLASS_MAP'] as $class_name => $name) {
            $arReturn['PVZ'][$class_name] = self::getPVZ($class_name, true);
        }

        return $arReturn;
    }


    public static function getPVZCollectionByCity($city, $arPVZ)
    {
        $arClasses = array_keys(self::getClassMap(self::loadPVZ())); //Получаем только имена классов

        /** @var iPvz $class */
        foreach ($arPVZ as $class_name => $pvz) {
            if (in_array($class_name, $arClasses)) {
                $full_class_name = self::$namespace . $class_name; //Пример new Qsoft\\Pvzmap\\PVZ\\CDEK
                $class = new $full_class_name();
                $arReturn[$class_name] = $class->getArrayByCity($city, $pvz);
            }
        }

        return $arReturn;
    }

    /**
     * Получает доступные службы доставки
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function loadPVZ()
    {
        return PVZTable::getList([
            'filter' => ['ACTIVE' => 'Y']
        ])->fetchAll();
    }

    /**
     * Возвращает массив для построения меню на карте. Необходимо передать в js.
     * @param $arPVZ
     * @return array
     */
    private static function getClassMap($arPVZ)
    {
        $arReturn = [];

        if (!empty($GLOBALS['ACCEPTED_PVZ'])) {
            $arReturn = $GLOBALS['ACCEPTED_PVZ'];
        } else {
            foreach ($arPVZ as $pvz) {
                $arReturn[$pvz['CLASS_NAME']] = $pvz['NAME'];
            }
        }

        return $arReturn;
    }

    /**
     * Проверяем отдельно каждую ПВЗ на наличие в текущем городе
     * @param $arDeliverySrv array
     * @param $local bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkDeliverySrv($arDeliverySrv)
    {
        if (empty(self::$arPVZ)) {
            self::$arPVZ = self::getClassMap(self::loadPVZ());
        }

        reset(self::$arPVZ);//Сбрасываем указатель чтобы цикл каждый раз отрабатывал
        foreach (self::$arPVZ as $class_name => $PVZ_name) {
            if (strpos($arDeliverySrv['NAME'], $PVZ_name) !== false) { // если строка точно не найдена
                $GLOBALS['ACCEPTED_PVZ'][$class_name] = self::$arPVZ[$class_name]; //Собираем список ПВЗ доступных для данного местоположения
                $GLOBALS['PVZ_PRICES'][$class_name] = $arDeliverySrv['PRICE']; //В отдельный массив собираем цены сервисов доставок
                $GLOBALS['PVZ_IDS'][$class_name] = $arDeliverySrv['ID'];
                return true;
            }
        }
        return false;
    }
}
