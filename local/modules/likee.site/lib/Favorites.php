<?php
/**
 * User: Azovcev Artem
 * Date: 05.03.17
 * Time: 0:26
 */
namespace Likee\Site;


use Bitrix\Main\Loader;

/**
 *  Класс для работы с избранными товарами. Содержит методы добавления, проверки и получения избранных товаров.
 *
 * @package Likee\Site
 */
class Favorites
{
    /**
     * Название cookie
     */
    const COOKIE_NAME = 'LIKEE_FAVORITES';

    /**
     * Экземпляр объекта
     */
    private static $_instance;

    /**
     * Кэш избранного
     */
    private static $_favoritesCache;

    /**
     * Возвращает экземпляр объекта
     *
     * @return Favorites
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Favorites();
        }

        return self::$_instance;
    }

    /**
     * Возвращает массив id избранных товаров
     *
     * @return array
     */
    public function getIdArray()
    {
        global $USER;

        if (!is_null(self::$_favoritesCache))
            return self::$_favoritesCache;

        if ($USER->isAuthorized()) {
            $arUser = \CUser::GetList(
                $by = 'id',
                $order = 'desc',
                array('ID' => $USER->GetID()),
                array('SELECT' => array('UF_FAVORITES'))
            )->Fetch();

            $arFavorites = $arUser['UF_FAVORITES'];
        } else {
            $arFavorites = json_decode($_COOKIE[self::COOKIE_NAME], true);
        }

        self::$_favoritesCache = self::normalizeArrayValuesByInt($arFavorites);

        return self::$_favoritesCache;
    }

    /**
     * Возвращает количество избранных товаров
     *
     * @return integer количество избранных товаров
     */
    public function count()
    {
        return count($this->getIdArray());
    }

    /**
     * Проверяет находится ли указанный id в избранных товарах
     *
     * @param integer $ID id товара
     * @return bool
     */
    public function isInFavorites($ID)
    {
        $ID = self::checkModelId($ID);
        return in_array($ID, $this->getIdArray());
    }

    /**
     * Добавляет товар в избранное
     *
     * @param integer $ID id товара
     * @return array массив избранных товаров $_favoritesCache
     */
    public function add($ID)
    {
        global $USER, $USER_FIELD_MANAGER;

        $ID = intval($ID);

        if ($ID <= 0)
            return self::$_favoritesCache;

        //добавляем в избранное только модель товара, а не ТП
        $ID = self::checkModelId($ID);

        $arFavorites = $this->getIdArray();
        $arFavorites[] = $ID;
        $arFavorites = self::normalizeArrayValuesByInt($arFavorites);

        if ($USER->isAuthorized()) {
            $USER_FIELD_MANAGER->Update('USER', $USER->GetID(), array(
                'UF_FAVORITES' => $arFavorites
            ));
        } else {
            setcookie(self::COOKIE_NAME, json_encode($arFavorites), time() + 86400 * 365, '/');
        }

        return self::$_favoritesCache = $arFavorites;
    }

    /**
     * Удаляет товар из избранного
     *
     * @param integer $ID id товара
     * @return array массив товаров $_favoritesCache
     */
    public function remove($ID)
    {
        global $USER, $USER_FIELD_MANAGER;

        $arFavorites = $this->getIdArray();

        $ID = self::checkModelId($ID);

        if (in_array($ID, $arFavorites))
            unset($arFavorites[array_search($ID, $arFavorites)]);

        $arFavorites = self::normalizeArrayValuesByInt($arFavorites);

        if (!$USER->isAuthorized()) {
            setcookie(self::COOKIE_NAME, json_encode($arFavorites), time() + 86400 * 365, '/');
        } else {
            $USER_FIELD_MANAGER->Update('USER', $USER->GetID(), array(
                'UF_FAVORITES' => $arFavorites,
            ));
        }
        return self::$_favoritesCache = $arFavorites;
    }

    /**
     * Переносит информацию о избранных товарах из cookie в базу данных
     */
    public function moveFromCookieToUser()
    {
        global $USER, $USER_FIELD_MANAGER;

        if ($USER->isAuthorized()) {
            $arFavorites = json_decode($_COOKIE[self::COOKIE_NAME], true);

            if (empty($arFavorites) || !is_array($arFavorites))
                $arFavorites = [];

            $arUser = \CUser::GetList(
                $by = 'id',
                $order = 'desc',
                array('ID' => $USER->GetID()),
                array('SELECT' => array('UF_FAVORITES')
                ))->Fetch();

            if (!empty($arUser['UF_FAVORITES']) && is_array($arUser['UF_FAVORITES']))
                $arFavorites = array_merge($arFavorites, $arUser['UF_FAVORITES']);

            $arFavorites = self::normalizeArrayValuesByInt($arFavorites);

            $USER_FIELD_MANAGER->Update('USER', $USER->GetID(), array(
                'UF_FAVORITES' => $arFavorites
            ));

            setcookie(self::COOKIE_NAME, '', time(), '/');
        }
    }

    /**
     * Проверка id товара, если это торговое предложение, то вернет id модели
     *
     * @param integer $ID id товара или торгового предложения
     * @return integer id модели
     */
    static public function checkModelId($ID)
    {
        if (Loader::includeModule('catalog')) {
            $arModel = \CCatalogSKU::GetProductInfo($ID);
            if ($arModel)
                $ID = $arModel['ID'];
        }

        return $ID;
    }

    /**
     * Приводит все значения массива к типу Int
     *
     * @param $arValues массив значений для нормализации
     * @return array нормализованный массив
     */
    static public function normalizeArrayValuesByInt($arValues)
    {
        if (empty($arValues) || !is_array($arValues))
            $arValues = [];

        \Bitrix\Main\Type\Collection::normalizeArrayValuesByInt($arValues);

        return $arValues;
    }
}