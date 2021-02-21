<?php


namespace Qsoft\Logger;

use Exception;

class Logger
{
    private $logDirPath = '/local/logs/';
    private $logFilePath;
    private $savedMessages;

    public function __construct($logFile)
    {
        $this->logFilePath = $_SERVER['DOCUMENT_ROOT'] . $this->logDirPath . $logFile;
    }

    /**
     * @param $exception Exception
     * @param $line_break string Указывается перенос строки по умолчанию PHP_EOL
     * @return string
     */
    public function getExceptionInfo($exception, $line_break = PHP_EOL)
    {
        $string = '==================================================' . $line_break
            . $exception->getMessage() . ' '
            . 'в строке' . $exception->getLine() . $line_break
            . '==================================================' . $line_break;

        return $string;
    }

    /**
     * @param $exception Exception
     * @param $line_break string Указывается перенос строки по умолчанию PHP_EOL
     */
    public function writeExceptionIntoFile($exception, $line_break = PHP_EOL)
    {
        $date = date('d.m.Y H:i:s') . $line_break;
        $log_mess = $date . $this->getExceptionInfo($exception, $line_break);
        file_put_contents($this->logFilePath, $log_mess, FILE_APPEND);
    }

    /**
     * @param $message
     * @param $line_break string Указывается перенос строки по умолчанию PHP_EOL
     */
    public function writeLogMessage($message, $line_break = PHP_EOL)
    {
        file_put_contents($this->logFilePath, $message . $line_break, FILE_APPEND);
    }

    /**
     * Используется для временного сохранения лог-сообщений чтобы потом записать их файл или вывести пользователю
     * @param string $message Сообщение
     * @param string $key Ключ группировки сообщений
     * @param string $key_message Ключ сообщения в группе
     */
    public function addSavedMessage($message, $key, $key_message = '')
    {
        if (!empty($key_message)) {
            $this->savedMessages[$key][$key_message] = $message;
        } else {
            $this->savedMessages[$key][] = $message;
        }
    }

    public function getSavedMessages()
    {
        return $this->savedMessages;
    }

    public function writeSavedMessagesIntoFile($line_break = PHP_EOL)
    {
        foreach ($this->savedMessages as $group) {
            foreach ($group as $message) {
                $this->writeLogMessage($message, $line_break);
            }
        }
    }

    public function pasteSeparator($line_break = PHP_EOL)
    {
        $this->writeLogMessage('==================================================', $line_break);
    }

    public function num2word($num, $words)
    {
        $num = $num % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1:
                return ($words[0]);
            case 2:
            case 3:
            case 4:
                return ($words[1]);
            default:
                return ($words[2]);
        }
    }
}
