<?php
/**
 * Project: zenden
 * Date: 04.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Exchange;

/**
 * Класс для работы с XML. Содержит методы для чтения XML.
 *
 * @package Likee\Exchange
 */
class XMLReader
{
    /**
     * @var string Имя файла
     */
    protected $file;
    /**
     * @var array Колбэк функции
     */
    private $callbacks = [];
    /**
     * @var array Раскрытые узлы
     */
    protected $expandedNodes = [];
    /**
     * @var array Переключаемые узлы
     */
    protected $triggableNodes = [];

    /**
     * Конструктор класса
     *
     * @param string $file XML файл
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Добавляет колбэк к событию
     *
     * @param string $eventType Тип события
     * @param callable $callback Колбэк функция
     */
    public function on($eventType, Callable $callback)
    {
        if (!array_key_exists($eventType, $this->callbacks)) $this->callbacks[$eventType] = [];
        $this->callbacks[$eventType][] = $callback;
        $this->triggableNodes[] = $eventType;
    }

    /**
     * Вызывает колбэк на событие
     *
     * @param string $eventType Тип события
     * @param array $arguments Параметры колбэка
     */
    private function trigger($eventType, $arguments = [])
    {
        if (array_key_exists($eventType, $this->callbacks)) {
            foreach ($this->callbacks[$eventType] as $callback) {
                call_user_func_array($callback, $arguments);
            }
        }
    }

    /**
     * Читает XML файл
     */
    public function read()
    {
        $reader = new \XMLReader();
        $reader->open("file://{$this->file}");

        while ($reader->next()) {
            if ($reader->nodeType === \XmlReader::ELEMENT) {
                if (in_array($reader->name, $this->expandedNodes)) {
                    $reader->read();
                }

                if (in_array($reader->name, $this->triggableNodes)) {
                    $this->trigger($reader->name, [$this, $reader->readOuterXml()]);
                }
            }
        }

        $reader->close();
    }

    /**
     * Геттер $expandedNodes
     *
     * @return array Узлы
     */
    public function getExpandedNodes()
    {
        return $this->expandedNodes;
    }

    /**
     * Сеттер $expandedNodes
     *
     * @param array $expandedNodes Узлы
     */
    public function setExpandedNodes($expandedNodes)
    {
        $this->expandedNodes = $expandedNodes;
    }
}