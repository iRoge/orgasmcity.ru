<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\EnumField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;

/**
 * Класс для работы с таблицей очереди заказов. Содержит методы для добавления, удаления и обновления записей в таблице очереди заказов.
 *
 * @package Likee\Exchange\Tables
 * @link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=05748&LESSON_PATH=3913.5062.5748
 */
class OrderQueueTable extends DataManager
{
    /**
     * Статус - Заказ не обработан
     */
    const STATUS_NEW = 'N';
    /**
     * Статус - Ошибка обработки заказа
     */
    const STATUS_ERROR = 'E';
    /**
     * Статус - Заказ успешно обработан
     */
    const STATUS_SUCCESS = 'S';
    /**
     * Статус - Шлюз не обработан
     */
    const STATUS_PAYMENT_NEW = 'PN';
    /**
     * Статус - Шлюз успешно обработан
     */
    const STATUS_PAYMENT_SUCCESS = 'PS';
    /**
     * Статус - Шлюз успешно обработан
     */
    const STATUS_PAYMENT_ERROR = 'PE';

    /**
     * Возвращает название таблицы
     *
     * @return string Название таблицы
     */
    public static function getTableName()
    {
        return 'b_likee_1c_order_queue';
    }

    /**
     * Возвращает текущий файл
     *
     * @return string имя файла
     */
    public static function getFile()
    {
        return __FILE__;
    }

    /**
     * Возвращает массив, описывающий поля сущности
     *
     * @return array Массив, описывающий поля сущности
     */
    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new IntegerField('ORDER_ID', [
                'title' => 'ID заказа'
            ]),
            new StringField('ORDER_NUMBER', [
                'title' => 'Номер заказа в шлюзе'
            ]),
            new StringField('PAYMENT_SYSTEM', [
                'title' => 'Платежная система'
            ]),
            new EnumField('STATUS', [
                'values' => array_keys(self::getStatusesDescription()),
                'title' => 'Статус'
            ]),
            new IntegerField('ATTEMPTS', [
                'title' => 'Количество попыток'
            ]),
            new DatetimeField('DATE_INSERT', [
                'title' => 'Дата добавления'
            ]),
            new DatetimeField('DATE_ATTEMPT', [
                'title' => 'Дата попытки'
            ]),
            new ReferenceField(
                'ORDER',
                '\Bitrix\Sale\Internals\OrderTable',
                ['=this.ORDER_ID' => 'ref.ID']
            ),
            new IntegerField('PRIORITET', [
                'title' => 'приоритет'
            ]),
        ];
    }

    /**
     * Возвращает описание статусов
     *
     * @return array
     */
    public static function getStatusesDescription()
    {
        return [
            self::STATUS_NEW => 'Заказ не обработан',
            self::STATUS_ERROR => 'Ошибка обработки заказа',
            self::STATUS_SUCCESS => 'Заказ успешно обработан',
            self::STATUS_PAYMENT_NEW => 'Шлюз не обработан',
            self::STATUS_PAYMENT_ERROR => 'Ошибка отправки шлюза',
            self::STATUS_PAYMENT_SUCCESS => 'Шлюз успешно отправлен',
        ];
    }
    /**
     * Добавляет данные в таблицу
     *
     * @param array $data данные
     * @return \Bitrix\Main\Entity\AddResult
     */
    public static function add(array $data)
    {
        $result = parent::add($data);
        return $result;
    }
    /**
     * Обновляет данные в таблице
     *
     * @param mixed $primary Ключ
     * @param array $data Данные
     * @return \Bitrix\Main\Entity\UpdateResult
     */
    public static function update($primary, array $data)
    {
        $result = parent::update($primary, $data);
        return $result;
    }
    /**
     * Удаляет данные из таблицы
     *
     * @param integer $primary Ключ
     * @return \Bitrix\Main\Entity\DeleteResult
     */
    public static function delete($primary)
    {
        $result = parent::delete($primary);
        return $result;
    }
}
