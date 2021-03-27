<?php

/**
 * User: Azovcev Artem
 * Date: 07.12.16
 * Time: 15:53
 */

namespace Likee\Site\Events;

use Bitrix\Main\UserTable;

/**
 * Класс для обработки событий пользователя. Обрабатывает перед регистрацией, перед обновлением, после регистрации и авторизации.
 *
 * @package Likee\Site\Events
 */
class User
{
    public static function onBeforeUserUpdate(&$arFields)
    {
        global $APPLICATION;
        if (!empty($arFields['PERSONAL_BIRTHDAY'])) {
            CheckFilterDates($arFields['PERSONAL_BIRTHDAY'], date('d.m.Y'), $bPersonalDate, $bNowDate, $bDiffDate);
            if ($bPersonalDate == 'Y') {
                $APPLICATION->ThrowException('Неверный формат даты рождения');
                return false;
            }
            if ($bDiffDate == 'Y') {
                $APPLICATION->ThrowException('Дата рождения не может быть больше текущего момента');
                return false;
            }
        }
        if (!empty($arFields["PERSONAL_PHONE"])) {
            if (!self::checkUserField($arFields, "PERSONAL_PHONE", "Пользователь с таким номером телефона (".$arFields["PERSONAL_PHONE"].") уже существует.")) {
                return false;
            }
        }
        if (!empty($arFields["EMAIL"])) {
            $arFields["LOGIN"] = $arFields["EMAIL"];
        }
    }

    /**
     * Перенаправляет пользователя на главную страницу после логаута, удаляет куки
     */
    public static function onAfterUserLogout()
    {
        global $APPLICATION;
        global $LOCATION;
        if (strpos($APPLICATION->GetCurDir(), '/personal/') === 0) {
            LocalRedirect('/');
        }
        setcookie("favorites_count", '', time()-3600, '/');
        setcookie("user_fio", '', time()-3600, '/', $LOCATION->getCurrentHost());
        setcookie("user_phone", '', time()-3600, '/', $LOCATION->getCurrentHost());
        setcookie("user_email", '', time()-3600, '/', $LOCATION->getCurrentHost());
    }

    /**
     * Проверка данных перед регистрацией пользователя
     * @param array $arFields Пользователь
     * @return bool|null false если произошла ошибка
     */
    public static function onBeforeUserRegister(&$arFields)
    {
        global $APPLICATION;

        $bPartner = \CSite::InDir(SITE_DIR . 'partnerships/index.php');

        $arFields['LOGIN'] = $arFields['EMAIL'];

        if (!empty($arFields['PERSONAL_BIRTHDAY'])) {
            CheckFilterDates($arFields['PERSONAL_BIRTHDAY'], date('d.m.Y'), $bPersonalDate, $bNowDate, $bDiffDate);

            if ($bPersonalDate == 'Y') {
                $APPLICATION->ThrowException('Неверный формат даты рождения');
                return false;
            }

            if ($bDiffDate == 'Y') {
                $APPLICATION->ThrowException('Дата рождения не может быть больше текущего момента');
                return false;
            }
        }

        if (!$bPartner && !isset($_POST['agreement'])) {
            $APPLICATION->ThrowException('Вы не согласились с политикой конфиденциальности');
            return false;
        }

        if ($bPartner) {
            $arFIO = array_filter(array_map('trim', explode(' ', $arFields['NAME'])));

            if (count($arFIO) !== 3) {
                $APPLICATION->ThrowException('ФИО указано не верно');
                return false;
            }

            $arFields['LAST_NAME'] = $arFIO[0];
            $arFields['NAME'] = $arFIO[1];
            $arFields['SECOND_NAME'] = $arFIO[2];

            $arFields['ACTIVE'] = 'N';

            if (!in_array(\Likee\Site\User::GROUP_PARTNER, $arFields['GROUP_ID'])) {
                $arFields['GROUP_ID'][] = \Likee\Site\User::GROUP_PARTNER;
            }
        }

        if (!empty($arFields["PERSONAL_PHONE"])) {
            if (!self::checkUserField($arFields, "PERSONAL_PHONE", "Пользователь с таким номером телефона (".$arFields["PERSONAL_PHONE"].") уже существует.")) {
                return false;
            }
        }
        if (!empty($arFields["EMAIL"])) {
            $arFields["LOGIN"] = $arFields["EMAIL"];
        }
    }
    private static function checkUserField($arFields, $field, $errorText)
    {
        global $APPLICATION;
        $dbRes = UserTable::getList(array(
            "filter" => array(
                $field => $arFields[$field],
            ),
            "select" => array(
                "ID",
                $field,
            )
        ));
        while ($arItem = $dbRes->Fetch()) {
            if ($arItem["ID"] != $arFields["ID"]) {
                $APPLICATION->ThrowException($errorText);
                return false;
            }
        }
        return true;
    }

    /**
     * Объеденяет избранное пользователя после авторизации
     * Обновляет счетчик в шапке
     */
    public static function onAfterUserAuthorize()
    {
        global $USER;
        $idUser = $USER->GetID();
        $rsUser = $USER->GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arFavoritesId = array_flip($arUser['UF_FAVORITES']);
        if (isset($_COOKIE['favorites'])) {
            $arFavoritesIdCookie = unserialize($_COOKIE['favorites']);
            foreach ($arFavoritesIdCookie as $FavoriteIdCookie) {
                $arFavoritesId[$FavoriteIdCookie] = $FavoriteIdCookie;
            }
            $arFavoritesId = array_flip($arFavoritesId);
            $USER->Update($idUser, array("UF_FAVORITES" => $arFavoritesId));
            setcookie("favorites", '', time()-3600, '/');
        }
        setcookie("favorites_count", count($arFavoritesId), 0, '/');
    }
}
