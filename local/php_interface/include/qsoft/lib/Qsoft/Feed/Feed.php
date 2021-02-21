<?php


namespace Qsoft\Feed;

use Bitrix\Sale\Compatible\CDBResult;
use CIBlockElement;
use CIBlockSection;

class Feed
{
    private $feed_iblock;
    private $feeds;
    private $runned_feed; // код фида который в данный момент выполняется или будет запущен
    private $runned = false; // флаг который определяет что в данный момент выполняется фид
    private $queue;
    private $start_day; // Время начала текущего дня

    private const DATE_FORMAT = 'd.m.Y H:i:s';

    public const FEED_STATUSES = [
        'RUNNING' => 'Выполняется',
        'WAIT_RUN' => 'Ждет следующего запуска', //Время запуска еще не пришло
        'IN_QUEUE' => 'В очереди', //Время запуска пришло, но выполняется другой feed
    ];

    public function __construct()
    {
        $this->start_day = strtotime('00:00:00');
    }

    /**
     * Переодически проверяет выполняется ли какой-либо фид.
     * Если ничего не выполняется запускает готовый к запуску фид.
     */
    public function checkAutoRunFeed(): void
    {
        $this->getFeeds();
        $this->updateFeedsStatuses();
        $this->checkQueue();
        $this->runFeed();
    }

    /**
     * Ставит в очередь фиды у которых в настройках указано
     * "Запускать после ночной выгрузки"
     */
    public function runFeedAfterImport(): void
    {
        $this->getFeeds(['PROPERTY_FC_UPDATE_IMPORT_VALUE' => 'Да']);

        foreach ($this->feeds as $arFeed) {
            $this->addInQueue($arFeed);
        }
    }

    /**
     * @param array $filter
     * Получает список правил создания фидов по заданному фильтру
     */
    private function getFeeds(array $filter = [])
    {
        $arOrder = [];
        $arFilter = [
            'ACTIVE' => 'Y',
            'IBLOCK_CODE' => IBLOCK_FEEDS,
        ];
        $arSelect = [
            'ID',
            'NAME',
            'CODE',
            'IBLOCK_CODE',
            'IBLOCK_ID',
            'IBLOCK_SECTION_ID',
            'PROPERTY_FC_UPDATE_TIME',
            'PROPERTY_FC_UPDATE_PERIOD',
            'PROPERTY_RUN_STATUS',
            'PROPERTY_LAST_RUN',
            'PROPERTY_NEXT_RUN'
        ];

        if (count($filter)) {
            foreach ($filter as $key => $value) {
                $arFilter[$key] = $value;
            }
        }

        /** @var CDBResult $rsFeeds */
        $rsFeeds = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        $this->processFeeds($rsFeeds);
    }

    /**
     * @param $res CDBResult
     * Производит обработку полученных правил создания фидов.
     * Определяет запущенный, и ставит в очередь все остальные
     */
    private function processFeeds($res): void
    {
        $arFeeds = [];
        while ($arFeed = $res->Fetch()) {
            if ($arFeed['PROPERTY_RUN_STATUS_VALUE'] == self::FEED_STATUSES['RUNNING']) {
                $this->runned_feed = $arFeed['ID'];
                $this->runned = true;
            } elseif ($arFeed['PROPERTY_RUN_STATUS_VALUE'] == self::FEED_STATUSES['IN_QUEUE']) {
                $this->queue[] = $arFeed['CODE'];
            }

            $arFeeds[$arFeed['CODE']] = $arFeed;
        }

        $this->feeds = $arFeeds;
    }

    /**
     * Метод обновляет статусы фидов в БД
     */
    private function updateFeedsStatuses(): void
    {
        foreach ($this->feeds as $arFeed) {
            $this->checkNextRun($arFeed);
            CIBlockElement::SetPropertyValuesEx(
                $arFeed['ID'],
                $this->feed_iblock['ID'],
                [
                    'RUN_STATUS' => $arFeed['PROPERTY_RUN_STATUS_VALUE'],
                    'LAST_RUN' => $arFeed['PROPERTY_LAST_RUN_VALUE'],
                    'NEXT_RUN' => $arFeed['PROPERTY_NEXT_RUN_VALUE'],
                ]
            );
        }
    }

    /**
     *Устанавливает время следующего запуска раз в день
     * @param $arFeed
     */
    private function setNextDayRun(&$arFeed): void
    {
        $update_time = strtotime($arFeed['PROPERTY_FC_UPDATE_TIME_VALUE']) - $this->start_day;

        if (empty($arFeed['PROPERTY_LAST_RUN_VALUE'])) {
            $last_run = $this->start_day - 86400 + $update_time;
            $arFeed['PROPERTY_LAST_RUN_VALUE'] = date(self::DATE_FORMAT, $last_run);
            $next_run = strtotime($arFeed['PROPERTY_LAST_RUN_VALUE']) + 86400;
        } else {
            $next_run = strtotime($arFeed['PROPERTY_NEXT_RUN_VALUE']);
        }

        if (time() >= $next_run) {
            $arFeed['PROPERTY_NEXT_RUN_VALUE'] = date(self::DATE_FORMAT, $this->start_day + $update_time + 86400);
            if (!empty($this->runned_feed)) {
                $arFeed['PROPERTY_RUN_STATUS_VALUE'] = self::FEED_STATUSES['IN_QUEUE'];
            } else {
                $this->runned_feed = $arFeed['CODE'];
            }
        }
    }

    /**
     *Устанавливает время следующего запуска через заданный период
     * @param $arFeed
     */
    private function setNextPeriodRun(&$arFeed): void
    {
        $update_period = strtotime($arFeed['PROPERTY_FC_UPDATE_PERIOD_VALUE']) - $this->start_day;

        if (empty($arFeed['PROPERTY_LAST_RUN_VALUE'])) {
            $last_run = $this->start_day - $update_period;
            $arFeed['PROPERTY_LAST_RUN_VALUE'] = date(self::DATE_FORMAT, $last_run);
        }

        if (!empty($arFeed['PROPERTY_NEXT_RUN_VALUE'])) {
            $next_run = strtotime($arFeed['PROPERTY_NEXT_RUN_VALUE']);
        } else {
            $next_run = strtotime($arFeed['PROPERTY_LAST_RUN_VALUE']) + $update_period;
        }
        $arFeed['PROPERTY_NEXT_RUN_VALUE'] = date(self::DATE_FORMAT, $next_run);

        if (time() >= $next_run) {
            $next_run = strtotime($arFeed['PROPERTY_LAST_RUN_VALUE']) + $update_period;
            if (!empty($arFeed['PROPERTY_FC_UPDATE_TIME_VALUE'])) {
                $update_time = strtotime($arFeed['PROPERTY_FC_UPDATE_TIME_VALUE']) - $this->start_day;
                $update_by_time_today = $this->start_day + $update_time;
                if ($update_by_time_today > time() && $update_by_time_today < $next_run) {
                    $next_run = $update_by_time_today;
                }
            }
            $arFeed['PROPERTY_NEXT_RUN_VALUE'] = date(self::DATE_FORMAT, $next_run);
            if (!empty($this->runned_feed)) {
                $arFeed['PROPERTY_RUN_STATUS_VALUE'] = self::FEED_STATUSES['IN_QUEUE'];
            } else {
                $this->runned_feed = $arFeed['CODE'];
            }
        }
    }

    /**
     * Метод проверяет нужно ли обновить время следующего запуска и определяет нужно ли запустить feed
     * @param $arFeed
     * @return void
     */
    private function checkNextRun(&$arFeed): void
    {
        if (!empty($arFeed['PROPERTY_FC_UPDATE_TIME_VALUE']) && empty($arFeed['PROPERTY_FC_UPDATE_PERIOD_VALUE'])) {
            $this->setNextDayRun($arFeed);
        } elseif (empty($arFeed['PROPERTY_FC_UPDATE_TIME_VALUE']) && empty($arFeed['PROPERTY_FC_UPDATE_PERIOD_VALUE'])) {
            return;
        } else {
            $this->setNextPeriodRun($arFeed);
        }
    }

    /**
     * Метод запускает фид на создание
     */
    private function runFeed(): void
    {
        global $APPLICATION;

        if ($this->runned == false && $this->runned_feed) {
            $runned_feed = $this->feeds[$this->runned_feed];

            CIBlockElement::SetPropertyValuesEx(
                $runned_feed['ID'],
                $this->feed_iblock['ID'],
                [
                    'RUN_STATUS' => self::FEED_STATUSES['RUNNING'],
                ]
            );

            $this->getTemplateName($runned_feed);
            $status = $APPLICATION->IncludeComponent(
                'qsoft:feed',
                $runned_feed['TEMPLATE_NAME'],
                [
                    'NEWS_YANDEX_TURBO' =>  preg_match('/yandex_turbo/i', $runned_feed['TEMPLATE_NAME']),
                    'FEED_SETTINGS_CODE' => $this->runned_feed
                ],
                false
            );

            $runned_feed = $this->feeds[$this->runned_feed];
            $runned_feed['PROPERTY_LAST_RUN_VALUE'] = $status['END_TIME'];
            $runned_feed['PROPERTY_RUNNING_TIME_VALUE'] = $status['DURATION'];
            $this->checkNextRun($runned_feed);
            if ($status['STATUS'] == 'SUCCESS') {
                $runned_feed['PROPERTY_RUN_STATUS_VALUE'] = self::FEED_STATUSES['WAIT_RUN'];
            } else {
                $runned_feed['PROPERTY_RUN_STATUS_VALUE'] = $status['ERRORS'];
            }

            CIBlockElement::SetPropertyValuesEx(
                $runned_feed['ID'],
                $this->feed_iblock['ID'],
                [
                    'RUN_STATUS' => $runned_feed['PROPERTY_RUN_STATUS_VALUE'],
                    'LAST_RUN' => $runned_feed['PROPERTY_LAST_RUN_VALUE'],
                    'NEXT_RUN' => $runned_feed['PROPERTY_NEXT_RUN_VALUE'],
                    'RUNNING_TIME' => $runned_feed['PROPERTY_RUNNING_TIME_VALUE']
                ]
            );
        }
    }

    /**
     * Метод проверяет не пуста ли очередь
     * и берет первый элемент для запуска если ничего не запущено
     */
    private function checkQueue(): void
    {
        if (count($this->queue)) {
            if (empty($this->runned_feed) && $this->runned == false) {
                $this->runned_feed = reset($this->queue);
            }
        }
    }

    /**
     * @param $arFeed
     * Добавляет правило в очередь выполнения
     */
    private function addInQueue($arFeed): void
    {
        CIBlockElement::SetPropertyValuesEx(
            $arFeed['ID'],
            $this->feed_iblock['ID'],
            [
                'RUN_STATUS' => self::FEED_STATUSES['IN_QUEUE'],
            ]
        );
    }

    /**
     * @return bool
     * Проверяет есть ли запущенный фид
     */
    public function checkRunnedFeed(): bool
    {
        $this->getFeeds(['PROPERTY_RUN_STATUS' => self::FEED_STATUSES['RUNNING']]);
        return count($this->feeds);
    }

    /**
     * @param $arFeed
     * Получает название шаблона из раздела правила создания фида
     */
    private function getTemplateName(&$arFeed): void
    {
        $arOrder = [];
        $arFilter = [
            'ID' => $arFeed['IBLOCK_SECTION_ID'],
            'IBLOCK_CODE' => $arFeed['IBLOCK_CODE']
        ];
        $arSelect = [
            'CODE'
        ];

        $rsSection = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
        if ($arSection = $rsSection->Fetch()) {
            $arFeed['TEMPLATE_NAME'] = $arSection['CODE'];
        }
    }

    /**
     * @param $feed_id
     * @return array
     * Получает массив настроек правила создания фидов
     */
    public function getFeedSettings($feed_id): array
    {
        $arFilter = [
            'ID' => $feed_id
        ];

        $this->getFeeds($arFilter);
        $arFeed = reset($this->feeds);
        $this->getTemplateName($arFeed);
        return $arFeed;
    }
}
