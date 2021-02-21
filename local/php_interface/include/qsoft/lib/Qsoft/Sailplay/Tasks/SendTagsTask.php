<?php


namespace Qsoft\Sailplay\Tasks;

class SendTagsTask extends AbstractTask
{
    private const BASE_LOG_PATH = '/local/logs/sailplay/sendTagsTask/';
    protected $taskName = 'sendTags';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        if ($this->data['type'] = 'order') {
            $this->addTags('Покупка на сайте');
        }
        $this->sendTags();
    }
}
