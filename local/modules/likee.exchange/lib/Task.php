<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

/**
 * Абстрактный класс задачи импорта. Содержит необходимые для задачи свойства и загружает необходимые модули.
 *
 * @package Likee\Exchange
 */
abstract class Task
{

    /**
     * @public string Узел XML
     */
    public $node = '';
    /**
     * @public array Данные
     */
    public $data = [];
    /**
     * @public string XML файл
     */
    public $xml = '';

    /**
     * @public Result $result Экземпляр класса Result
     */
    public $result;

    /**
     * @public XMLReader $reader Экземпля класса XMLReader
     */
    public $reader;

    /**
     * @public array $config Настройки модуля
     */
    public $config;

    /**
     * @protected трекер БД
     */
    protected $tracker;
    /**
     * @protected количество запросов с прошлой записи в лог
     */
    protected $query_num;
    /**
     * @protected время с прошлой записи в лог
     */
    protected $log_time;

    /**
     * Конструктор класса
     */
    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');
        Loader::includeModule('sale');
        Loader::includeModule('catalog');

        $connection = Application::getConnection();
        $connection->query('SET wait_timeout=14400;');
        $this->tracker = $connection->getTracker();
        $this->query_num = $this->tracker->getCounter();
        $this->log_time = microtime(true);

        $this->result = new Result();
        $this->reader = new XMLReader($this->getFilePath());
        $this->config = Config::get();
    }

    /**
     * Возвращает путь файла
     *
     * @return string Путь файла
     */
    protected function getFilePath()
    {
        $request = Context::getCurrent()->getRequest();
        $arConfig = Config::get();
        $sPath = $arConfig['PATH'] . 'tempPath/';

        if ($_REQUEST['data']) {
            $file = $_SERVER['DOCUMENT_ROOT'] . $sPath . $this->xml;
            $str = $request->get('data');
            $str = str_replace("'", "", $str);
            file_put_contents($file, $str);
        } else {
            if (mb_substr($sPath, -1) != '/') {
                $sPath .= '/';
            }
            if ($request->get('type') == 'file') {
                $file = $_SERVER['DOCUMENT_ROOT'] . $sPath . $this->xml;
            } else {
                $file = reset($_FILES)['tmp_name'];
            }
        }

        return $file;
    }

    /**
     * Возвращает форматированое название highload блока
     * @param string $name Название блока
     * @return string Форматированое название
     */
    protected function getHighloadBlockName($name)
    {
        return ucfirst(preg_replace('/[^a-zA-Z%\[\]\.\(\)%&-]/s', '', $name));
    }
    /**
     * Разбирает поля и свойства по массива
     * @param array $arItems   Массив со свойствами и полями
     * @param int   $elementId ID торгового предложения
     * @return array $result Массив с полями и свойствами
     */
    protected function magicFieldAndProp($arItems, $elementId = 0)
    {
        $result = [];
        // Собираем поля
        foreach ($arItems as $keyItem => $valItem) {
            if (!isset($this->properties[$keyItem]) && mb_stripos($keyItem, '*multi*') === false) {
                $result['FIELD'][$keyItem] = $valItem;
            }
        }
        // Собираем свойства
        foreach ($this->properties as $keyItem => $valItem) {
            if ($this->properties[$keyItem]['PROPERTY_TYPE'] == 'reference' || $this->properties[$keyItem]['PROPERTY_TYPE'] == 'list') {
                // Если список или справочник проверяем допустимо ли такое значение
                if (empty($arItems[$keyItem]) || isset($this->properties[$keyItem]['VALUES'][$arItems[$keyItem]])) {
                    // Если всё ок, для лист и множественое заносим поле по особому
                    if ($this->properties[$keyItem]['MULTIPLE'] == 'Y') {
                        $arMulti = [];
                        foreach ($arItems as $keyMulti => $valMulti) {
                            // Для полей multiply запись имеет формат [COLOR*multi*000000538] => 000000538
                            // Обратные преобразования для множественного поля
                            if (mb_stripos($keyMulti, $keyItem.'*multi*') !== false) {
                                $arMulti[] = $valMulti;
                            }
                        }
                        $result['PROPERTIES'][$keyItem] = !empty($arMulti) ? $arMulti : '';
                        continue;
                    }
                    if ($this->properties[$keyItem]['PROPERTY_TYPE'] == 'list') {
                        $result['PROPERTIES'][$keyItem] = [
                            "VALUE" => $this->properties[$keyItem]['VALUES'][$arItems[$keyItem]]
                        ];
                        continue;
                    }
                } else {
                    // Не существует значения в справочнике или списке
                    $this->log("Элемент с ID = ".$elementId.". Не существует значения [".$arItems[$keyItem]."] в справочнике или списке. Свойство [".$this->properties[$keyItem]['NAME']."]");
                    continue;
                }
            }
            // Если обычное (не файл) поле проверяем на множественность
            if ($this->properties[$keyItem]['MULTIPLE'] == 'Y' && $this->properties[$keyItem]['PROPERTY_TYPE'] != "file") {
                $arMulti = [];
                foreach ($arItems as $keyMulti => $valMulti) {
                    // Для полей multiply запись имеет формат [COLOR*multi*000000538] => 000000538
                    // Обратные преобразования для множественного поля
                    if (mb_stripos($keyMulti, $keyItem.'*multi*') !== false) {
                        $arMulti[] = $valMulti;
                    }
                }
                $result['PROPERTIES'][$keyItem] = !empty($arMulti) ? $arMulti : '';
                continue;
            } else {
                $result['PROPERTIES'][$keyItem] = $arItems[$keyItem];
            }
        }
        return $result;
    }
    /**
     * Логирует импорт
     * @param string $message - строка для лога
     */
    protected function log($message)
    {
        $time = microtime(true);
        $query_num = $this->tracker->getCounter();
        echo date('d.m.Y H:i:s')." - Query: ".($query_num - $this->query_num)." - ".
            "Time: ".number_format(($time - $this->log_time), 6, ".", "")." - ".
            $message."\n";
        $this->log_time = $time;
        $this->query_num = $query_num;
    }
    /**
     * Архивирует файл в отдельную папку
     * Удаляет исходный файл
     * @return bool True в случае успешного выполнения
     */
    protected function arhivate($file = false)
    {
        if (!$file) {
            $zipFileTemp = $this->xml . '.zip';
            $path = $_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . 'tempPath/' . $this->xml;
            $pathArhives = $_SERVER['DOCUMENT_ROOT'] . '/upload/1c_catalog/archives/' . date("Y.m.d") . '/';
            mkdir($pathArhives, 0775);
            $formatDateTime = "Y.m.d.H-i-s";
            $dateStat = date($formatDateTime);
            $zip = new \ZipArchive();
            if ($zip->open($pathArhives . $zipFileTemp, \ZipArchive::CREATE)) {
                $zip->addFile($path, $this->xml);
                if ($zip->close()) {
                    if (rename($pathArhives . $zipFileTemp, $pathArhives . $dateStat . " - " . $this->xml . ".zip")) {
                        if (unlink($path)) {
                            return true;
                        } else {
                            $this->log("Не удалось удалить исходный файл $this->xml");
                        }
                    } else {
                        $this->log("Не удалось переименовать архив с файлом $this->xml формат (Дата начала - Имя файла)");
                    }
                } else {
                    $this->log("Не удалось сохранить архив с файлом $this->xml");
                }
            } else {
                $this->log("Не удалось создать временный архив с файлом $this->xml");
            }
        } else {
            $zipFileTemp = $file . '.zip';
            $path = $_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . 'tempPath/' . $file;
            $pathArhives = $_SERVER['DOCUMENT_ROOT'] . '/upload/1c_catalog/archives/' . date("Y.m.d") . '/';
            mkdir($pathArhives, 0775);
            $formatDateTime = "Y.m.d.H-i-s";
            $dateStat = date($formatDateTime);
            $zip = new \ZipArchive();
            if ($zip->open($pathArhives . $zipFileTemp, \ZipArchive::CREATE)) {
                $zip->addFile($path, $file);
                if ($zip->close()) {
                    if (rename($pathArhives . $zipFileTemp, $pathArhives . $dateStat . " - " . $file . ".zip")) {
                        if (unlink($path)) {
                            return true;
                        } else {
                            $this->log("Не удалось удалить исходный файл $file");
                        }
                    } else {
                        $this->log("Не удалось переименовать архив с файлом $file формат (Дата начала - Имя файла)");
                    }
                } else {
                    $this->log("Не удалось сохранить архив с файлом $file");
                }
            } else {
                $this->log("Не удалось создать временный архив с файлом $file");
            }
        }
    }
}
