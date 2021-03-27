<?

use Bitrix\Main\Application;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Qsoft\Sailplay\Tasks\SyncUserTask;
use Qsoft\Sailplay\Tasks\TaskManager;
use Qsoft\Sailplay\Tasks\TaskManagerException;
use Qsoft\Sailplay\Tasks\TaskRouter;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Project: Site
 * Date: 2017-03-29
 * Time: 21:30:49
 */
class QsoftBonusesHistoryComponent extends CBitrixComponent implements Controllerable
{
    private const HISTORY_TABLE = 'qsoft_sp_user_history';
    private const STATUS_UF = 'UF_SAILPLAY_STATUS';
    private const POINTS_UF = 'UF_SAILPLAY_POINTS';
    private const EMPTY_STATUS = 'Неизвестен';
    private const STORE_DEPARTMENT_ID = 4943;

    public function onPrepareComponentParams($arParams)
    {
        global $USER;

        if (array_key_exists('USER_ID', $arParams)) {
            $arParams['USER_ID'] = intval($arParams['USER_ID']);
        } else {
            $arParams['USER_ID'] = intval($USER->GetID());
        }

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 31536000;
        }
        return $arParams;
    }

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'updateHistory' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ])
                ],
            ],
        ];
    }

    public function updateHistoryAction()
    {
        $this->updateHistory();

        return [];
    }

    public function executeComponent()
    {
        if ($this->StartResultCache()) {
            $cacheManager = Application::getInstance()->getTaggedCache();
            $cacheManager->registerTag('SP_BONUSES_HISTORY' . $this->arParams['USER_ID']);
            $this->getPoints();
            $this->getStatus();
            $this->arResult['ITEMS'] = [];
            $history = $this->getHistory();

            foreach ($history as $arItem) {
                $iOrderId = intval($arItem['order_num']);

                if ($arItem['action'] == 'sharing') {
                    $arItem['name'] = 'Рассказать о покупке';
                }

                $item = [
                    'ACTION' => strtoupper($arItem['action']),
                    'NAME' => $arItem['name'],
                    'ORDER_ID' => $iOrderId,
                    'DATE' => date('d.m.Y', strtotime($arItem['action_date'])),
                ];

                if (isset($arItem['store_department_id']) && $arItem['store_department_id'] != self::STORE_DEPARTMENT_ID) {
                    $item['ACTION'] = 'PURCHASE_RETAIL';
                }
                if (isset($arItem['debited_points_delta']) && $arItem['debited_points_delta'] != 0) {
                    $item['DELTA']['DEBITED'] = intval($arItem['debited_points_delta']);
                }

                $item['DELTA']['VALUE'] = intval($arItem['points_delta']);
                if (strpos($arItem['points_delta'], '-') !== false) {
                    $item['DELTA']['SIGN'] = 'minus';
                    if ($item['ACTION'] === 'EXTRA') {
                        unset($item['ACTION']);
                    }
                } else {
                    $item['DELTA']['SIGN'] = 'plus';
                }
                $this->arResult['ITEMS'][] = $item;
            }

            $this->includeComponentTemplate();
        }
    }

    public function getActionsDescription()
    {
        return [
            'PURCHASE' => 'Покупка в Интернет-магазине',
            'PURCHASE_RETAIL' => 'Покупка в розничном магазине',
            'EXTRA' => 'Начисление бонусов',
            'REGISTRATION' => 'Регистрация'
        ];
    }


    private function getHistory()
    {
        global $DB;

        $dbRes = $DB->Query("
                    SELECT `history`
                    FROM `" . self::HISTORY_TABLE ."`
                    WHERE `user_id` = " . $this->arParams['USER_ID'] . ";");

        $history = [];
        while ($entry = $dbRes->Fetch()) {
            $history[] = json_decode($entry['history'], true);
        }
        return $history;
    }

    private function getPoints()
    {
        $points = 0;
        $dbRes = UserTable::getList([
            'select' => [self::POINTS_UF],
            'filter' => ['ID' => $this->arParams['USER_ID']],
            'limit' => 1
        ]);
        $user = $dbRes->Fetch();
        if ($user) {
            $points = intval($user[self::POINTS_UF]);
        }
        
        $this->arResult['USER_BONUSES'] = $points;
        $this->arResult['USER_BONUSES_ENDING'] = $this->getNumEnding($points);
    }

    private function getStatus()
    {
        $dbRes = UserTable::getList([
            'select' => [self::STATUS_UF],
            'filter' => ['ID' => $this->arParams['USER_ID']],
            'limit' => 1
        ]);
        $user = $dbRes->Fetch();
        if ($user) {
            $status = $user[self::STATUS_UF];
        }

        $this->arResult['USER_STATUS'] = !empty($status) ? $status : self::EMPTY_STATUS;
    }

    private function updateHistory()
    {
        global $USER;

        $dbUser = CUser::GetByID($USER->GetID());
        $user = $dbUser->Fetch();
        $taskManager = new TaskManager();
        $taskRouter = new TaskRouter();
        if ($user) {
            $taskManager->setUser($user['ID']);
            try {
                $tasks = $taskManager->getTasks();
                $defaultData = $taskManager->getDefaultData();
            } catch (TaskManagerException $e) {
            }
            // регистрацию всегда выполняем первой
            if (array_key_exists('register', $tasks)) {
                $task = $taskRouter->getTask('register');
                if ($task) {
                    $task->setUser($user);
                    $task->setData($tasks['register']);
                    $task->setDefaultData($defaultData);
                    $task->execute();
                    unset($tasks['register']);
                }
            }
            foreach ($tasks as $taskClass => $taskData) {
                $task = $taskRouter->getTask($taskClass);
                if ($task) {
                    $task->setUser($user);
                    $task->setData($taskData);
                    $task->setDefaultData($defaultData);
                    $task->execute();
                }
            }

            try {
                $taskManager->clearTasks();
            } catch (TaskManagerException $e) {
            }
            
                $status = new SyncUserTask();
                $status->setUser($user);
                $status->runWithData();
        }
    }

    private function getNumEnding($number, $endingArray = ['', 'а', 'ов'])
    {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $endingArray[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $endingArray[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $endingArray[1];
                    break;
                default:
                    $ending = $endingArray[2];
            }
        }
        return $ending;
    }
}
