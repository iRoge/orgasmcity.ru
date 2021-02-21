<?php


namespace Qsoft\Sailplay\Tasks;

class TaskRouter
{
    private const TASK_MAPPING = [
        'register' => '\Qsoft\Sailplay\Tasks\RegisterTask',
        'getHistory' => '\Qsoft\Sailplay\Tasks\GetHistoryTask',
        'getTags' => '\Qsoft\Sailplay\Tasks\GetTagsTask',
        'update' => '\Qsoft\Sailplay\Tasks\UpdateTask',
        'subscribe' => '\Qsoft\Sailplay\Tasks\SubscribeTask',
        'sendOrder' => '\Qsoft\Sailplay\Tasks\SendOrderTask',
        'addInfo' => '\Qsoft\Sailplay\Tasks\AddInfoTask',
        'syncUser' => '\Qsoft\Sailplay\Tasks\SyncUserTask'
    ];

    public function getTask(string $alias): ?AbstractTask
    {
        $task = self::TASK_MAPPING[$alias] ?: false;

        if ($task) {
            return new $task;
        }

        return null;
    }
}
