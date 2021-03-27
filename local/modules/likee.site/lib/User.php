<?php
/**
 * Created by PhpStorm.
 * User: Azovcev Artem
 * Date: 08.12.16
 * Time: 15:14
 */

namespace Likee\Site;


use Bitrix\Main\Config\Option;
use Bitrix\Main\UserTable;

/**
 * Класс для работы с пользователем. Содержит методы для определения типа пользователя.
 *
 * @package Likee\Site
 */
class User
{
    /**
     * Группа партнеров
     */
    const GROUP_PARTNER = 8;
    /**
     * Тип плательщика для физ. лица
     */
    const PERSON_TYPE_ID = 1;
    /**
     * Тип плательщика для юр. лица
     */
    const PERSON_TYPE_ID_UR = 2;

    /**
     * Проверяет id пользователя
     *
     * @param integer $iUserId Id пользователя
     * @return integer
     */
    public static function checkUserId($iUserId = null)
    {
        global $USER;

        if (is_null($iUserId))
            $iUserId = $USER->GetID();

        return intval($iUserId);
    }

    /**
     * Проверяет является ли пользователь партнером
     *
     * @param integer $iUserId Id пользователя
     * @return bool True если пользователь является партнером
     */
    public static function isPartner($iUserId = null)
    {
        $iUserId = self::checkUserId($iUserId);
        if ($iUserId <= 0)
            return false;

        $arGroups = \CUser::GetUserGroup($iUserId);
        return in_array(self::GROUP_PARTNER, $arGroups);
    }

    /**
     * Возвращает тип пользователя
     *
     * @param integer $iUserId Id пользователя
     * @return string Тип партнера
     */
    public static function getPersonalTypeId($iUserId = null)
    {
        return self::isPartner($iUserId) ? self::PERSON_TYPE_ID_UR : self::PERSON_TYPE_ID;
    }

    /**
     * Проверяет связку логин и пароль пользователя
     *
     * @param string $sLogin Логин
     * @param string $sPass Пароль
     * @return bool True если вход успешный
     */
    public static function checkPass($sLogin, $sPass)
    {
        $arUser = UserTable::getRow([
            'filter' => [
                '=LOGIN' => $sLogin
            ],
            'select' => ['ID', 'PASSWORD']
        ]);

        if (!$arUser)
            return false;

        $sSalt = '';
        $sUserPass = $arUser['PASSWORD'];

        if (strlen($sUserPass) > 32) {
            $sSalt = substr($sUserPass, 0, strlen($sUserPass) - 32);
            $sUserPass = substr($sUserPass, -32);
        }

        return $sUserPass === md5($sSalt . $sPass);
    }

    /**
     * Возвращает ссылку для восстановления пароля
     * Смена пароля не работает для авторизованных пользователей (компонет system.auth.forgotpasswd)
     *
     * Основная логика метода взята в \CUser::SendUserInfo
     *
     * @param null $iUserId
     * @return string
     */
    public static function getLinkForgotPass($iUserId = null)
    {
        global $DB;

        $iUserId = self::checkUserId($iUserId);

        if ($iUserId <= 0)
            return '';

        $arUser = \CUser::GetByID($iUserId)->Fetch();

        if (!$arUser)
            return '';

        $sSalt = randString(8);
        $sCheckWord = md5(\CMain::GetServerUniqID() . uniqid());

        $sSql = "UPDATE b_user SET " .
            "	CHECKWORD = '" . $sSalt . md5($sSalt . $sCheckWord) . "', " .
            "	CHECKWORD_TIME = " . $DB->CurrentTimeFunction() . ", " .
            "	LID = '" . $DB->ForSql(SITE_ID, 2) . "', " .
            "   TIMESTAMP_X = TIMESTAMP_X " .
            "WHERE ID = '" . $iUserId . "'" .
            "	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";

        $DB->Query($sSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);

        $arQuery = [
            'change_password' => 'yes',
            'lang' => 'ru',
            'USER_CHECKWORD' => $sCheckWord,
            'USER_LOGIN' => $arUser['LOGIN']
        ];

        $sServerName = Option::get('main', 'server_name', $_SERVER['SERVER_NAME']);

        $sLink = 'https://' . $sServerName . '/auth/?' . http_build_query($arQuery);

        return $sLink;
    }
}