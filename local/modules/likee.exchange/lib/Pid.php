<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Qsoft\Feed\Feed;

/**
 * Класс для работы с pid файлом (идентификатором процесса)
 *
 * @package Likee\Exchange
 */
class Pid
{
    /**
     * @var string Pid файл
     */
    protected $filename;
    /**
     * @var bool Процесс уже существует
     */
    public $already_running = false;

    /**
     * Конструктор класса
     *
     * @param string $directory Директория pid файла
     * @param string|bool $file Pid файл или false
     */
    public function __construct($directory, $file = false)
    {

        if (!$file) {
            $file = basename($_SERVER['PHP_SELF']);
        }

        $this->filename = $directory . '/' . $file . '.pid';

        if (is_writable($this->filename) || is_writable($directory)) {
            if (file_exists($this->filename)) {
                $pid = (int)trim(file_get_contents($this->filename));
                if (posix_kill($pid, 0)) {
                    $this->already_running = true;
                }
            }
        } else {
            die("Cannot write to pid file '$this->filename'. Program execution halted.\n");
        }

        if (!$this->already_running) {
            $pid = getmypid();
            file_put_contents($this->filename, $pid);
        }
    }

    /**
     * Деструктор класса
     */
    public function __destruct()
    {

        if (!$this->already_running && file_exists($this->filename) && is_writeable($this->filename)) {
            unlink($this->filename);
        }
    }
}
