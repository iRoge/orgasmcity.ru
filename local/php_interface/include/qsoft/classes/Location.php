<?

namespace Qsoft;

use Bitrix\Main\Loader;
use Bitrix\Sale\Location\LocationTable;
use CPHPCache;
use SxGeo;

class Location
{
    // константы
    private const LOCATION_TYPE = 5;
    // значения по умолчанию
    private $DEFAULT_LOCATION; // местоположение
    public $isStranger = false; // флаг нового пользователя
    // название местоположения (город)
    private $name = false;
    // регион
    private $region = false;
    // страна
    private $country = false;
    // код местоположения
    public $code = false;
    /**
     * @var CPHPCache
     */
    private $cache;
    // телефон, привязанный к месту
    public $phone = false;
    // имя хоста
    public $hostName = false;

    // при создании экземпляра класс
    public function __construct()
    {
        Loader::includeModule("sale");
        Loader::includeModule("catalog");
        $this->setDefault();
        $this->initCache();
        $this->getHostName();
        $this->getLocationCode();
    }

    private function getHostName()
    {
        $this->hostName = $_SERVER['HTTP_HOST'];
    }

    private function setDefault()
    {
        $this->DEFAULT_LOCATION = array(
            "ID" => 129,
            "CODE" => "0000073738",
            "NAME_RU" => "Москва",
        );
    }

    private function getLocationCode()
    {
        // если значение уже установлено, то ничего не делаем
        if ($this->code) {
            return;
        }
        // храним код в сессии
        if (isset($_SESSION["BRANCH"]["LOCATION_CODE"])) {
            if ($this->checkLocationCode($_SESSION["BRANCH"]["LOCATION_CODE"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // наша новая кука
        if (isset($_COOKIE["LOCATION_CODE"])) {
            if ($this->checkLocationCode($_COOKIE["LOCATION_CODE"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // если пользователь не узнан, устанавливаем флаг
        $this->isStranger = true;
        // оставлено для обратной совместимости со старым функционалом
        if (isset($_COOKIE["CURRENT_LOCATION_ID"])) {
            if ($this->checkLocationId($_COOKIE["CURRENT_LOCATION_ID"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // если ничего нет, определяем по IP
        $this->locateUserByIP();
        $this->setExeptionRegionIfDefault();
    }

    // получаем и устанавливаем значения по умолчанию
    public function checkLocationCode($code): bool
    {
        $code = htmlspecialchars($code);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "CODE" => $code,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            return $this->setLocationCode($res);
        }
        return false;
    }

    // устанавливаем код местоположения для экземпляра класса
    private function setLocationCode($arFields)
    {
        $_SESSION["BRANCH"]["LOCATION_CODE"] = $arFields["CODE"];
        if (!$_COOKIE["LOCATION_CODE"] || $_COOKIE["LOCATION_CODE"] != $arFields["CODE"]) {
            // устанавливаем на 30 дней
            setcookie("LOCATION_CODE", $arFields["CODE"], time() - 2592000, "/");
            setcookie("LOCATION_CODE", $arFields["CODE"], time() + 2592000, "/", $this->getCurrentHost());
        }

        // устанавливаем старую куку, чтобы не отвалилась часть функционала, завязанная на ID с неё
        // когда всё переделаем, можно будет убрать
        if (!$_COOKIE["CURRENT_LOCATION_ID"] || $_COOKIE["CURRENT_LOCATION_ID"] != $arFields["ID"]) {
            // устанавливаем на 30 дней
            setcookie("CURRENT_LOCATION_ID", $arFields["ID"], time() - 2592000, "/");
            setcookie("CURRENT_LOCATION_ID", $arFields["ID"], time() + 2592000, "/", $this->getCurrentHost());
        }
        $this->code = $arFields["CODE"];
        $this->name = $arFields["NAME_RU"];
        return true;
    }

    public function getCurrentHost()
    {
        $arCurrentHost = array_reverse(explode('.', $_SERVER['HTTP_HOST']));
        return $arCurrentHost[1] . '.' . $arCurrentHost[0];
    }

    private function checkLocationId($id)
    {
        $id = intval($id);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "ID" => $id,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            return $this->setLocationCode($res);
        }
        return false;
    }

    // получение местоположения по IP
    public function locateUserByIP($return = false)
    {
        if ($res = $this->getLocationByIp($return)) {
            return $res;
        }
        if ($return) {
            return $this->DEFAULT_LOCATION;
        }
        // Если не удалось определить по IP, устанавливаем местоположение по умолчанию
        $this->setLocationCode($this->DEFAULT_LOCATION);
    }

    // получаем IP из всех возможных заголовков
    private function getLocationByIp($return)
    {
        $ip = $this->getIp();
        if (!$ip) {
            return false;
        }
        @mb_internal_encoding("8bit");
        require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/tools/SxGeo.php");
        $SxGeo = new SxGeo($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/tools/SxGeoCity.dat");
        $city = $SxGeo->getCity($ip);
        @mb_internal_encoding("UTF-8");
        $city = $city["city"]["name_ru"];
        if ($city) {
            return $this->checkLocationName($city, $return);
        }
        return false;
    }

    private function getIp()
    {
        $headers = array("HTTP_X_REAL_IP", "HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "REMOTE_ADDR");
        foreach ($headers as $header) {
            $ip = @$_SERVER[$header];
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return false;
    }

    // проверка местоположения по названию
    private function checkLocationName($name, $return)
    {
        $name = htmlspecialchars($name);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "NAME.NAME" => $name,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            if ($return) {
                return $res;
            } else {
                return $this->setLocationCode($res);
            }
        }
        return false;
    }

    private function initCache()
    {
        $this->cache = new CPHPCache();
    }

    public function getParentCodes($code = false, $addFilter = array())
    {
        if (!$code) {
            $code = $this->code;
        }
        $arFilter = array(
            'CODE' => $code,
        );
        // сливаем дополнительный массив с фильтрацией по коду
        $arFilter = array_merge($addFilter, $arFilter);
        // убираем ключи с пустыми значениями
        foreach ($arFilter as $key => $value) {
            if (!$value || empty($value)) {
                unset($arFilter[$key]);
            }
        }
        //получаем все коды от текущего по цепочке вверх
        $res = LocationTable::getList(array(
            'filter' => $arFilter,
            'select' => array(
                'LOC_CODE' => 'PARENTS.CODE',
            ),
            'order' => array(
                'PARENTS.DEPTH_LEVEL' => 'DESC',
            ),
        ));
        $arCodes = array();
        while ($arItem = $res->fetch()) {
            $arCodes[] = $arItem["LOC_CODE"];
        }
        return $arCodes;
    }

    // получаем название города
    public function getName()
    {
        if (!$this->name) {
            $this->getLocationCode();
        }
        return $this->name;
    }

    // получаем название страны
    public function getCountry()
    {
        if (!$this->country) {
            $this->initLocationParent();
        }
        return $this->country;
    }

    public function getRegion($flag = false)
    {
        if (!$this->region) {
            $this->initLocationParent();
        }
        if ($flag) {
            if ($this->region == 'Москва' || $this->region == 'Московская область') {
                return 'Москва и область';
            }
            if ($this->region == 'Ленинградская область') {
                return 'Санкт-Петербург и область';
            }
        }
        return $this->region;
    }

    private function initLocationParent()
    {
        $res = LocationTable::getList(array(
            'filter' => array(
                'CODE' => $this->code,
                'NAME.NAME' => $this->getName(),
                "PARENTS.TYPE.ID" => [1, 3],
                '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ),
            'select' => array(
                'NAME_RU' => 'PARENTS.NAME.NAME',
                'TYPE_CODE' => 'PARENTS.TYPE.ID',
            ),
        ));
        while ($arItem = $res->fetch()) {
            if (intval($arItem["TYPE_CODE"]) == 3) {
                $this->region = $arItem["NAME_RU"];
            }
            if (intval($arItem["TYPE_CODE"]) == 1) {
                $this->country = $arItem["NAME_RU"];
            }
        }
        if (!$this->region) {
            $this->region = $this->getName();
        }
        if (!$this->country) {
            $this->country = $this->getName();
        }
    }

    private function setExeptionRegionIfDefault()
    {
        if ($this->code === $this->DEFAULT_LOCATION['CODE']) {
            $this->exepRegionFlag = true;
        }
    }

    /**
     * Возвращает географические координаты текущего местоположения
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getLocationCoords()
    {
        $arRes = LocationTable::getList([
            'filter' => ['CODE' => $this->code],
            'select' => ['LATITUDE', 'LONGITUDE']
        ])->fetch();

        return $arRes;
    }
}
