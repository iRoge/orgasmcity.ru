<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Bitrix\Main\Context;

/**
 * Класс для работы с процессом импорта. Содержит методы для проверки и запуска процесса импорта.
 *
 * @package Likee\Exchange
 */
class Process
{
    /**
     * @private string Ключ безопасности
     */
    private $secure;
    /**
     * @private string Формат запроса
     */
    private $format;
    /**
     * @private string Название класса
     */
    private $class;
    /**
     * @private string Версия
     */
    private $version;
    /**
     * @private string Название метода
     */
    private $method;

    /**
     * @public array $headers Заголовки xml/json
     */
    public static $headers = [
        'xml' => [
            'Content-Type: text/xml'
        ],
        'json' => [
            'Content-Type: text/json'
        ]
    ];

    /**
     * @private Result $process Экземпляр класса Result
     */
    public $result;

    /**
     * Конструктор класса
     *
     * @param string $class Название класса
     * @param string $version Версия
     * @param string $method Название метода
     */
    public function __construct($class, $version, $method)
    {
        $request = Context::getCurrent()->getRequest();
        $this->class = $class;
        $this->version = $version;
        $this->method = $method;
        $this->secure = $request->get('secure');
        $this->format = $request->get('format') ?: 'xml';

        $this->result = new Result();
    }

    /**
     * Запускает процесс импорта
     */
    public function process()
    {
        @set_time_limit(0);
        ini_set('memory_limit', '14336M');
        $pid = new Pid('/tmp', '1c_exchange');
        $time = microtime(true);

        if ($pid->already_running) {
            $this->result->setData([
                'status' => 'error',
                'text' => 'Отказ. Импорт уже идет',
                'code' => ExchangeException::$ERR_ALREADY_WORK,
            ]);
        } else {
            try {
                //$this->checkAccess();
                $class = "\\Likee\\Exchange\\Task\\" . $this->class;
                $method = $this->method;
                $this->checkCallable($class . '::' . $method);
                $class = new $class;
                $this->result = $class->$method();
                unset($class);
            } catch (\Exception $e) {
                $this->result->setData([
                    'status' => 'error',
                    'text' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
            }
        }

        $this->result->setTime(microtime(true) - $time);
    }

    /**
     * Проверяет авторизацию
     *
     * @throws ExchangeException
     */
    protected function checkAccess()
    {
        $arConfig = Config::get();
        $md5 = md5(date('Y.m.d') . $arConfig['KEY']);

        if ($md5 != $this->secure) {
            throw new ExchangeException('Ошибка авторизации', ExchangeException::$ERR_AUTHORIZE);
        }
    }

    /**
     * Проверяет существование класса и метода
     *
     * @param string $callable Класс::метод
     * @throws ExchangeException
     */
    protected function checkCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new ExchangeException('Класс или метод "' . $callable . '" не найдены', ExchangeException::$ERR_NOT_CALLABLE);
        }
    }

    /**
     * Возвращает заголовок в зависимости от формата
     *
     * @return string Заголовок
     */
    public function getHeaders()
    {
        return self::$headers[$this->format];
    }

    /**
     * Возвращает форматированые данные из $result
     *
     * @return string Данные в формате JSON или XML
     */
    public function getResult()
    {
        if ($this->format == 'json') {
            return json_encode($this->result->getData(), JSON_PRETTY_PRINT);
        } else {
            $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><xml></xml>");
            Helper::array2xml($this->result->getData(), $xml);
            $domxml = new \DOMDocument('1.0');
            $domxml->preserveWhiteSpace = false;
            $domxml->formatOutput = true;
            /* @var $xml SimpleXMLElement */
            $domxml->loadXML($xml->asXML());
            return $domxml->saveXML();
        }
    }
}
