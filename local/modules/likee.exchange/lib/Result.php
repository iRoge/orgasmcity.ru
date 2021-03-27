<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;
/**
 * Класс для работы с результатом обмена с 1с. Хранит результат обмена. Содержит геттеры, сеттеры результата обмена и метод установки времени.
 *
 * @package Likee\Exchange
 */
class Result
{
    /**
     * @var array Результат выборки
     */
    private $data = [];

    /**
     * Конструктор класса
     */
    public function __construct()
    {

    }

    /**
     * Сеттер $data
     *
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Устанавливает время запроса
     *
     * @param float $fTime Время
     */
    public function setTime($fTime)
    {
        $this->data['time'] = $fTime;
    }

    /**
     * Геттер $data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
