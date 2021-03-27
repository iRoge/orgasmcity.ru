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
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;

/**
 * Класс для работы с таблицей ошибок очереди заказов. Содержит методы для добавления, удаления и обновления данных в таблице ошибок.
 *
 * @package Likee\Exchange\Tables
 * @link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=05748&LESSON_PATH=3913.5062.5748
 *
 */
class OrderQueueErrorTable extends DataManager
{
    /**
     * Возвращает название таблицы
     *
     * @return string Название таблицы
     */
    public static function getTableName()
    {
        return 'b_likee_1c_order_queue_error';
    }

    /**
     * Возвращает текущий файл
     *
     * @return string Имя фала
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
            new IntegerField('QUEUE_ID', [
                'title' => 'Номер в очереди'
            ]),
            new StringField('QUERY', [
                'title' => 'Запрос'
            ]),
            new StringField('ANSWER', [
                'title' => 'Ответ'
            ]),
            new DatetimeField('DATE', [
                'title' => 'Дата запроса'
            ]),
            new ReferenceField(
                'QUEUE',
                '\Likee\Exchange\Tables\OrderQueueTable',
                ['=this.QUEUE_ID' => 'ref.ID']
            )
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