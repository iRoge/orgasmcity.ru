<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

/**
 * Класс содержит коды ошибок для возникающего при обмене исключения.
 *
 * @package Likee\Exchange
 */
class ExchangeException extends \Exception
{
    /**
     * @var int Код ошибки: "Ошибка авторизации"
     */
    public static $ERR_AUTHORIZE = 10;
    /**
     * @var int Код ошибки: "Метод не найден"
     */
    public static $ERR_NOT_CALLABLE = 11;
    /**
     * @var int Код ошибки: "Не найден инфоблок товаров"
     */
    public static $ERR_NO_PRODUCTS_IBLOCK = 12;
    /**
     * @var int Код ошибки: "Не найден инфоблок торгового предложения"
     */
    public static $ERR_NO_OFFERS_IBLOCK = 13;
    /**
     * @var int Код ошибки: "Неверная структура"
     */
    public static $ERR_INCORRECT_STRUCTURE = 100;
    /**
     * @var int Код ошибки: "Незаполнено поле"
     */
    public static $ERR_EMPTY_FIELD = 101;
    /**
     * @var int Код ошибки: "Обновление/добавление не удалось"
     */
    public static $ERR_CREATE_UPDATE = 102;
    /**
     * @var int Код ошибки: "Неизвестный типа поля"
     */
    public static $ERR_UNKNOWN_FIELD_TYPE = 103;
    /**
     * @var int Код ошибки: "Не существует"
     */
    public static $ERR_NOT_EXIST = 103;
    /**
     * @var int Код ошибки: "Неверная ссылка"
     */
    public static $ERR_INCORRECT_LINK = 111;
    /**
     * @var int Код ошибк: "Заказ не найден"
     */
    public static $ERR_NO_ORDER = 112;
    /**
     * @var int Код ошибки: "Процесс уже запущен"
     */
    public static $ERR_ALREADY_WORK = 113;
    /**
     * @var int Код ошибки: "Обновление/добавление товара не удалось"
     */
    public static $ERR_PRODUCT_ADD_UPDATE = 114;
    /**
     * @var int Код ошибки: "Обновление/добавление торгового предложения не удалось"
     */
    public static $ERR_OFFER_ADD_UPDATE = 115;
    /**
     * @var int Код ошибки: "В настройках сайта запрет на ежечасный импорт"
     */
    public static $ERR_HOURLY_OPTION = 116;
    /**
     * @var int Код ошибки: "Файл пустой"
     */
    public static $ERR_FILE_IS_EMPTY = 117;
    /**
     * @var int Код ошибки: "Несовпадение ID файла и заказа"
     */
    public static $ERR_MISSING_ID_ORDER = 118;

    /**
     * Конструктор класса
     *
     * @param string $message Сообщение ошибки
     * @param int $code Код ошибки
     */
    public function __construct($message = '', $code = 0)
    {
        return parent::__construct($message, $code);
    }
}
