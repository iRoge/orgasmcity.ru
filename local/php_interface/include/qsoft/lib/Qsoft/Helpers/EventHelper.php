<?php

namespace Qsoft\Helpers;

use Bitrix\Main\EventManager;

class EventHelper
{
    /**
     * Удаляет события
     *
     * @param array $eventsListKill Массив с именами событий
     * @param string $moduleid Ид модуля инициатора события
     */
    public static function killEvents($eventsListKill, $moduleid)
    {
        $eventManager = EventManager::getInstance();
        foreach ($eventsListKill as $event) {
            $i = 0;
            do {
                $isAllDead = $eventManager->removeEventHandler($moduleid, $event, $i);
                $i++;
            } while ($isAllDead != false);
        };
    }
}
