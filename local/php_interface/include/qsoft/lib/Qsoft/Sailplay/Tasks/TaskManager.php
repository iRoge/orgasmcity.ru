<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\UserTable;
use Qsoft\Helpers\EventHelper;

class TaskManager
{
    private $user;

    private const USER_FIELD = 'UF_SAILPLAY_TASKS';
    private const LOG_PATH = '/local/logs/sailplay/TaskManager/';
    /**
     * @param int $user
     */
    public function setUser(int $user): void
    {
        $this->user = $user;
    }

    /**
     * @param string $task
     * @param array $data
     * @throws TaskManagerException
     */
    public function addTask(string $task, array $data = []): void
    {
        if (env('SP_TASKS_DISABLED', false)) {
            return;
        }

        if (defined('ADMIN_SECTION') && ADMIN_SECTION === true && $task != 'sendOrder') {
            return;
        }

        if (!isset($this->user)) {
            throw new TaskManagerException("Empty user");
        }
        global $USER;

        $tasksArray = $this->getTasks();
        $taskRouter = new TaskRouter();
        $taskObject = $taskRouter->getTask($task);

        if ($taskObject->add($tasksArray, $data)) {
            $tasksData = serialize(['tasks' => $tasksArray, 'defaultData' => $taskObject->getDefaultData()]);
            $USER->Update($this->user, [self::USER_FIELD => $tasksData]);
            EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
            $this->log('У пользователя ' . $this->user . ' обновлены задачи');
            $this->log($tasksData);
        }
    }

    /**
     * @return array
     * @throws TaskManagerException
     */
    public function getRawData()
    {
        if (!isset($this->user)) {
            throw new TaskManagerException("Empty user");
        }

        $dbUser = UserTable::getList([
            'select' => [self::USER_FIELD],
            'filter' => ['ID' => $this->user],
            'limit' => 1,
        ]);

        $user = $dbUser->fetch();

        if (!$user) {
            return [];
        }

        $tasks = $user[self::USER_FIELD];
        if (!$tasks) {
            return [];
        }

        $tasksArray = unserialize($tasks);

        return is_array($tasksArray) ? $tasksArray : [];
    }

    /**
     * @return array
     * @throws TaskManagerException
     */
    public function getTasks()
    {
        $rawData = $this->getRawData();

        return array_has($rawData, 'tasks') ? $rawData['tasks'] : [];
    }

    public function getDefaultData()
    {
        $rawData = $this->getRawData();

        return array_has($rawData, 'defaultData') ? $rawData['defaultData'] : [];
    }


    /**
     * @throws TaskManagerException
     */
    public function clearTasks()
    {
        if (!isset($this->user)) {
            throw new TaskManagerException("Empty user");
        }

        global $USER;

        $USER->Update($this->user, [self::USER_FIELD => null]);
    }

    public function log($message)
    {
        qsoft_logger($message, date('m.d.Y') . '.log', self::LOG_PATH);
    }
}
