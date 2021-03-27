<?php
/**
 * User: Azovcev Artem
 * Date: 12.04.17
 * Time: 11:19
 */

namespace Likee\Site\Helpers;

/**
 * Класс для работы с меню. Содержит метод построения дерева меню.
 *
 * @package Likee\Site\Helpers
 */
class Menu
{
    /**
     * Строит дерево меню
     *
     * @param array $arItems Элементы меню
     * @param int $iDepthLevel Глубина
     * @return array Пункты меню ввиде дерева
     */
    public static function menuTreeBuild($arItems, $iDepthLevel = 1)
    {
        $arTree = [];
        foreach ($arItems as $iKey => $arItem) {
            if ($arItem['DEPTH_LEVEL'] < $iDepthLevel) {
                break;
            }

            if ($arItem['DEPTH_LEVEL'] == $iDepthLevel) {
                if ($arItem['IS_PARENT']) {
                    $arItem['ITEMS'] = self::menuTreeBuild(array_slice($arItems, $iKey + 1), $arItem['DEPTH_LEVEL'] + 1);
                }
                $arTree[] = $arItem;
            }
        }
        return $arTree;
    }
}