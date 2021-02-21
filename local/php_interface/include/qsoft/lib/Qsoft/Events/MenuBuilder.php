<?php

namespace Qsoft\Events;

use Bitrix\Main\GroupTable;

class MenuBuilder
{
    private const ALLOWED_MENU_ITEM_MAP = [
        'content_editor' => [
            'likee_options',
        ],
        
        'online_try_on' => [
            'vendor_code_to_csv',
            'online_try_on_set',
            'online_try_on_reset',
        ],
    ];
    
    private static $userGroups = [];
    private static $userAllowedMenuItems = [];
    
    public static function handleEvent(array &$globalMenu, array &$moduleMenu)
    {
        if (self::identifyUser()) {
            self::$userAllowedMenuItems = self::getUserAllowedMenuItems();
            self::removeExcessMenuItems($moduleMenu);
        }
    }

    private static function identifyUser(): bool
    {
        global $USER;

        if ($USER->isAdmin()) {
            return false;
        }

        $userGroupsID = $USER->GetUserGroupArray();
        $processingGroupsName = array_keys(self::ALLOWED_MENU_ITEM_MAP);
        $groupList = GroupTable::getList([
            'select' => ['ID', 'STRING_ID'],
            'filter' => ['=STRING_ID' => $processingGroupsName],
        ]);
        
        while ($group = $groupList->fetch()) {
            if (in_array($group['ID'], $userGroupsID)) {
                self::$userGroups[$group['ID']] = $group['STRING_ID'];
            }
        }

        return !empty(self::$userGroups);
    }

    private static function getUserAllowedMenuItems(): array
    {
        if (empty(self::$userGroups)) {
            return [];
        }
        
        $allowedItems = [];
        foreach (self::ALLOWED_MENU_ITEM_MAP as $userGroup => $menuItems) {
            if (in_array($userGroup, self::$userGroups)) {
                $allowedItems = array_merge($allowedItems, $menuItems);
            }
        }

        return $allowedItems;
    }

    private static function removeExcessMenuItems(array &$menu)
    {
        foreach ($menu as $itemKey => $menuItem) {
            switch ($menuItem['parent_menu']) {
                case 'global_menu_marketplace':     // Удаление раздела меню "Marketplace"
                case 'global_menu_marketing':       // Удаление раздела меню "Маркетинг"
                case 'global_menu_store':           // Удаление раздела меню "Магазин"
                    unset($menu[$itemKey]);
                    break;

                case 'global_menu_content':         // Удаление пунктов "Структура сайта" и "Инфоблоки" раздела "Контент"
                    if (in_array($menuItem['section'], ['fileman', 'iblock'])) {
                        unset($menu[$itemKey]);
                    }
                    break;

                case 'global_menu_services':        // Удаление лишних пунктов меню раздела "Сервисы"
                    if ($menuItem['section'] != 'Respect' || empty(self::$userAllowedMenuItems)) {
                        unset($menu[$itemKey]);
                        break;
                    }

                    $respectMenu = self::getTidyRespectServiceMenu($menuItem);

                    if (empty($respectMenu)) {
                        unset($menu[$itemKey]);
                        break;
                    }

                    $menu[$itemKey] = $respectMenu;
                    break;
            }
        }
    }

    private static function getTidyRespectServiceMenu(array $menu): array
    {
        foreach ($menu['items'] as $key => $item) {
            if (array_key_exists('items', $item)) {
                $menu['items'][$key] = self::getTidyRespectServiceMenu($item);
                continue;
            }

            if (preg_match('/([\w,\s-]+)\.php/u', $item['url'], $name) != 1) {
                continue;
            }

            if (!in_array($name[1], self::$userAllowedMenuItems)) {
                unset($menu['items'][$key]);
            }
        }

        if (empty($menu['items'])) {
            return [];
        }

        return $menu;
    }
}
