<?
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

//Класс для работы с dadata
class Dadata
{

    private $base_url = "https://cleaner.dadata.ru/api/v1/";
    private $token;
    private $handle;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function init()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $this->token,
            "X-Secret: fff4fdba3305081a999a5334e73ed7b00a11e9e0"
        ));
        curl_setopt($this->handle, CURLOPT_POST, 1);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    private function executeRequest($url, $fields)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        if ($fields != null) {
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            curl_setopt($this->handle, CURLOPT_POST, 0);
        }
        $result = $this->exec();
        $result = json_decode($result, true);
        return $result;
    }

    private function exec()
    {
        $result = curl_exec($this->handle);
        $info = curl_getinfo($this->handle);
        if ($info['http_code'] == 429) {
            throw new TooManyRequests();
        } elseif ($info['http_code'] != 200) {
            throw new Exception('Request failed with http code ' . $info['http_code'] . ': ' . $result);
        }
        return $result;
    }

    public function clean($name, $value)
    {
        $url = $this->base_url . "clean/$name";
        $fields = array($value);
        $response = $this->executeRequest($url, $fields);
        return $response[0];
    }
}

//Класс для синхронизации местоположений с dadata
class DadataSync
{
    /** @var Dadata */
    //Объект dadata
    private $dadata;
    //Массив всех местоположений из базы
    private $arAllLocation;
    //Структурированный массив всех местоположений из базы
    private $arAllLocationStructure;
    //Массив значений округов
    private $arCountryDistrict;
    //Массив с полными названиями всех местоположений из базы
    private $arAllLocationList;
    //Массив внешних сервисов
    private $arExternalServices;
    //Счетчик запросов в dadata
    private $dadataQueryCount = 0;

    public function startSync()
    {
//        if (!empty($_GET['str'])) {
//            echo 'Запрос из строки<br>';
//            $this->dadata = new Dadata(DADATA_TOKEN);
//            $this->dadata->init();
//            echo '<pre>';
//            print_r($this->getDadataInfo($_GET['str']));
//            echo '</pre>';
//            $this->dadata->close();
//            exit;
//        }
        echo 'Старт синхронизции с dadata';
        $this->log('Старт синхронизции с dadata');
        $this->loadAllLocation();
        $this->getLocationList(0,5000);
        $this->loadExternalServices();

        $this->dadata = new Dadata(DADATA_TOKEN);
        $this->dadata->init();

        foreach ($this->arAllLocationList as $location) {
            $this->log('==========================');
            $this->log('ID: '. $location['ID']);
            $dadataInfo = $this->getDadataInfo($location['NAME']);
//            print_r($dadataInfo);
            $this->setExternalProps($location['ID'], $dadataInfo);
            $this->log('==========================');
        }

        $this->dadata->close();

        $this->log('Выполнено запросов к Dadata: ' . $this->dadataQueryCount);
        $this->log('Конец синхронизции с dadata');
    }

    private function loadAllLocation()
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
            'select' => array(
                '*',
                'NAME_RU' => 'NAME.NAME',
                'TYPE_CODE' => 'TYPE.CODE',
            )
        ));
        while ($item = $res->fetch()) {
            $result[$item['ID']] = $item;
        }
        return $this->arAllLocation = $result;
    }

    private function loadExternalServices()
    {
        global $DB;
        $rsData = $DB->Query("SELECT * FROM `b_sale_loc_ext_srv`");
        while ($res = $rsData->fetch()) {
            $externalServices[$res['CODE']] = $res['ID'];
        }
        return $this->arExternalServices = $externalServices;
    }

    //Формирует массив с полными названиями местоположений из базы
    private function getLocationList($from, $to)
    {
        $i = 0;
        foreach ($this->arAllLocation as $item) {
            if ($item['DEPTH_LEVEL'] <= 2) {
                continue;
            }
            if ($i < $from || $i > $to) {
                $i++;
                continue;
            }

            $this->arAllLocationStructure[$item['ID']][$item['TYPE_CODE']]['ID'] = $item['ID'];
            $this->arAllLocationStructure[$item['ID']][$item['TYPE_CODE']]['NAME'] = $item['NAME_RU'];

            $level = $item['DEPTH_LEVEL'];
            $parent = $item['PARENT_ID'];
            $str = $item['NAME_RU'];
            while ($level != '1') {
                $this->arAllLocationStructure[$item['ID']][$this->arAllLocation[$parent]['TYPE_CODE']]['ID'] = $this->arAllLocation[$parent]['ID'];
                $this->arAllLocationStructure[$item['ID']][$this->arAllLocation[$parent]['TYPE_CODE']]['NAME'] = $this->arAllLocation[$parent]['NAME_RU'];
                if ($this->arAllLocation[$parent]['TYPE_CODE'] != 'COUNTRY_DISTRICT') {
                    $str = $this->arAllLocation[$parent]['NAME_RU'] . ' ' . $str;
                }
                $level = $this->arAllLocation[$parent]['DEPTH_LEVEL'];
                $parent = $this->arAllLocation[$parent]['PARENT_ID'];
            }
            $res[] = [
                'NAME' => $str,
                'ID' => $item['ID'],
            ];
            $i++;
        }
        return $this->arAllLocationList = $res;
    }

    private function getDadataInfo($locationStr)
    {
        usleep(200000); //ждем 0.2 секунды, чтобы не превысить 10 запросов в секунду
        $this->log('Запрашиваем: ' . $locationStr);
        $result = $this->dadata->clean("address", $locationStr);
        $this->log('Качество ответа: ' . $result['qc']);
        $this->log($result, false, $result['qc']);
        $this->dadataQueryCount++;
        return $result;
    }

    private function setExternalProps($idLocation, $arDadataProps)
    {
        $this->log('Проверяем местоположение с ID ' . $idLocation);
//        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
//            'filter' => array('ID' => array($idLocation),
//            ),
//            'select' => array(
//                'EXTERNAL.*',
//                'EXTERNAL.SERVICE.CODE'
//            )
//        ));
//        while ($item = $res->fetch()) {
//            $arCurrentExternalValue[$item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_CODE']] = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
//        }
//        foreach ($this->arExternalServices as $externalService => $externalServiceId) {
//            if (empty($arCurrentExternalValue[$externalService]) && !empty($arDadataProps[$externalService])) {
//                $arExternalServicesData[] = [
//                    'SERVICE_ID' => $externalServiceId, // ID сервиса
//                    'XML_ID' => $arDadataProps[$externalService] // значение
//                ];
//            }
//        }
        $res = \Bitrix\Sale\Location\LocationTable::update($idLocation, array(
            'LONGITUDE' => $arDadataProps['geo_lon'],
            'LATITUDE' => $arDadataProps['geo_lat'],
        ));
//        if (!empty($arExternalServicesData)) {
//            $this->log('Местоположение с ID ' . $idLocation . ' дополнено. Количество новых полей: ' . count($arExternalServicesData));
//        } else {
//            $this->log('Местоположение с ID ' . $idLocation . ' уже заполнено');
//        }
    }

    private function log($message, $error = false, $qc = false)
    {
        if (!$error) {
            qsoft_logger($message, 'qc_' . $qc . '.log', '/local/logs/dadataSync/' . date('Y.m.d') . '/');
        } else {
            qsoft_logger($message, 'ERRORS.log', '/local/logs/dadataSync/' . date('Y.m.d') . '/');
        }
    }
}

$sync = new DadataSync();
$sync->startSync();
