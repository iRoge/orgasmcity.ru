<?php
/**
 * User: Azovcev Artem
 * Date: 05.03.17
 * Time: 0:26
 */
namespace Likee\Site\Tables;

use Bitrix\Main\Entity;

/**
 * Таблица с клиентами из стараго сайта респекта
 *
 * @package Likee\Site\Tables
 * @link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=05748&LESSON_PATH=3913.5062.5748
 */
class CustomersTable extends Entity\DataManager
{
    /**
     * Возвращает имя таблицы пользователей
     *
     * @return string Имя таблицы
     */
    public static function getTableName()
    {
        return 'customers';
    }

    /**
     * Возвращает массив, описывающий поля сущности
     *
     * @return array Массив, описывающий поля сущности
     */
    public static function getMap()
    {
        return array(
            'customerID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'email' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateEmail'),
            ),
            'password' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validatePassword'),
            ),
            'advertisementID' => array(
                'data_type' => 'integer',
            ),
            'discountCardNumber' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDiscountcardnumber'),
            ),
            'bonusActive' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'entered' => array(
                'data_type' => 'datetime',
                'required' => true,
            ),
            'registered' => array(
                'data_type' => 'datetime',
                'required' => true,
            ),
            new Entity\EnumField('registrationType', array(
                'values' => array('pure', 'order', 'dc', 'promo', 'database', 'subscription'),
                'required' => true,
            )),
            'subscribed' => array(
                'data_type' => 'integer',
            ),
            'subscription_code' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateSubscriptionCode'),
            ),
            new Entity\EnumField('sex', array(
                'values' => array('m','f'),
            )),
            'birthDate' => array(
                'data_type' => 'date',
            ),
            'name' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateName'),
            ),
            'phone' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validatePhone'),
            ),
            new Entity\EnumField('searchProvider', array(
                'values' => array('google','yandex','mail.ru'),
            )),
            'searchQuery' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateSearchquery'),
            ),
            'refererDomain' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateRefererdomain'),
            ),
            'referer' => array(
                'data_type' => 'text',
            ),
            'enteredMascotte' => array(
                'data_type' => 'datetime',
                'required' => true,
            ),
            'refererMascotte' => array(
                'data_type' => 'text',
            ),
            'unencryptedPass' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUnencryptedpass'),
            ),
            'sailplay_update_phone' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateSailplayUpdatePhone'),
            ),
            'email_verified' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'verification_code' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateVerificationCode'),
            ),
            'sailplay_update_flag' => array(
                'data_type' => 'integer',
            ),
        );
    }

    /**
     * Возвращает валидаторы для Email
     *
     * @return array Валидаторы
     */
    public static function validateEmail()
    {
        return array(
            new Main\Entity\Validator\Length(null, 150),
        );
    }

    /**
     * Возвращает валидаторы для паролей
     *
     * @return array Валидаторы
     */
    public static function validatePassword()
    {
        return array(
            new Main\Entity\Validator\Length(null, 32),
        );
    }

    /**
     * Возвращает валидаторы для карты скидки
     *
     * @return array Валидаторы
     */
    public static function validateDiscountcardnumber()
    {
        return array(
            new Main\Entity\Validator\Length(null, 14),
        );
    }

    /**
     * Возвращает валидаторы для подписки
     *
     * @return array Валидаторы
     */
    public static function validateSubscriptionCode()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }

    /**
     * Возвращает валидаторы для имени
     *
     * @return array Валидаторы
     */
    public static function validateName()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }

    /**
     * Возвращает валидаторы для телефона
     *
     * @return array Валидаторы
     */
    public static function validatePhone()
    {
        return array(
            new Main\Entity\Validator\Length(null, 20),
        );
    }

    /**
     * Возвращает валидаторы для поля searchQuery
     *
     * @return array Валидаторы
     */
    public static function validateSearchquery()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }

    /**
     * Возвращает валидаторы для поля refererDomain
     *
     * @return array Валидаторы
     */
    public static function validateRefererdomain()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }

    /**
     * Возвращает валидаторы для поля unencryptedPass
     *
     * @return array Валидаторы
     */
    public static function validateUnencryptedpass()
    {
        return array(
            new Main\Entity\Validator\Length(null, 15),
        );
    }

    /**
     * Возвращает валидаторы для поля sailplay_update_phone
     *
     * @return array Валидаторы
     */
    public static function validateSailplayUpdatePhone()
    {
        return array(
            new Main\Entity\Validator\Length(null, 20),
        );
    }

    /**
     * Возвращает валидаторы для поля verification_code
     *
     * @return array Валидаторы
     */
    public static function validateVerificationCode()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }
}