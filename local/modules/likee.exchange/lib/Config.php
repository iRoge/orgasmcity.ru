<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Exchange;

use Bitrix\Main\Config\Option;

/**
 * Класс с настройками модуля. Позволяет получить текущие настройки или задать новые.
 *
 * @package Likee\Exchange
 */
class Config
{
    /**
     * возвращает настройки модуля
     *
     * IBLOCK_ID - id инфоблока товаров,
     *
     * OFFERS_IBLOCK_ID - id инфоблока торговых предложений,
     *
     * PATH - путь до каталога 1с,
     *
     * KEY - ключ для api синхронизации с каталогом основной 1с,
     *
     * API - api для синхронизации с каталогом основной 1с,
     *
     * LOGIN - имя пользователя в основной 1с
     *
     * PASSWORD - пароль в основной 1с
     *
     * ACTIVE2 - активность дополнительной 1с
     *
     * KEY2 - ключ для api синхронизации с каталогом дополнительной 1с,
     *
     * API2 - api для синхронизации с каталогом дополнительной 1с,
     *
     * LOGIN2 - имя пользователя в дополнительной 1с
     *
     * PASSWORD2 - пароль в дополнительной 1с
     *
     * PROFILES - профиль синхронизации 1с, deprecated
     *
     * @return array Настройки
     */
    public static function get()
    {
        return [
            'IBLOCK_ID' => Option::get('likee.exchange', 'IBLOCK_ID', 0),
            'OFFERS_IBLOCK_ID' => Option::get('likee.exchange', 'OFFERS_IBLOCK_ID', 0),
            'PATH' => Option::get('likee.exchange', 'PATH', '/upload/1c_catalog/'),
            'KEY' => Option::get('likee.exchange', 'KEY', ''),
            'API' => Option::get('likee.exchange', 'API', ''),
            'LOGIN' => Option::get('likee.exchange', 'LOGIN', ''),
            'PASSWORD' => Option::get('likee.exchange', 'PASSWORD', ''),
            'ACTIVE2' => Option::get('likee.exchange', 'ACTIVE2', false),
            'KEY2' => Option::get('likee.exchange', 'KEY2', ''),
            'API2' => Option::get('likee.exchange', 'API2', ''),
            'LOGIN2' => Option::get('likee.exchange', 'LOGIN2', ''),
            'PASSWORD2' => Option::get('likee.exchange', 'PASSWORD2', ''),
            'PROFILES' => unserialize(Option::get('likee.exchange', 'PROFILES', '')),
        ];
    }

    /**
     * Устанавливает настройки
     *
     * @param array $arOptions Настройки
     */
    public static function set($arOptions)
    {
        foreach ($arOptions as $key => $option) {
            Option::set('likee.exchange', $key, $option);
        }
    }
}
