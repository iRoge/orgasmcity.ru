<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use CUser;
use Qsoft\Sailplay\SailPlayApi;

class GetTagsTask extends AbstractTask
{
    private const BASE_LOG_PATH = '/local/logs/sailplay/getTags/';
    private const CACHE_TAG = 'SP_BONUSES_HISTORY';

    private const GOLD = 'Золотой статус';
    private const PLATINUM = 'Платиновый статус';
    private const SILVER = 'Серебряный статус';
    private const BRONZE = 'Бронзовый статус';
    private const NONE = 'Неизвестен';

    private $userTags;

    protected $taskName = 'getTags';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $this->getTags();
    }

    private function getTags()
    {
        $this->log("Loading user tags for user {$this->user['ID']}");
        $phone = $this->getPhone();
        if ($phone) {
            $userIdentifier = $phone;
            $userIdentifierType = 'phone';
        } else {
            $this->log('Error: empty phone.');
            $userIdentifier = $this->user['EMAIL'];
            $userIdentifierType = 'email';
        }
        if (empty($userIdentifier)) {
            $this->log('Error: empty email.');
            return;
        }

        $tags = SailPlayApi::userGetTags($userIdentifier, [self::PLATINUM, self::GOLD, self::SILVER, self::BRONZE], $userIdentifierType);

        if (!$tags) {
            $tags = [];
        }
        $this->userTags = $tags;

        $this->log("User tags: ");
        $this->log($tags);

        $this->getStatus();
    }

    private function getStatus()
    {
        if (empty($this->userTags)) {
            $status = self::NONE;
        } else {
            $status = array_pop($this->userTags);
            $status = $status->name;
        }

        $status = rtrim($status, ' статус');
        $this->setStatus($status);
    }

    private function setStatus(string $status)
    {
        $cUser = new CUser();
        if ($cUser->Update($this->user['ID'], [
                'UF_SAILPLAY_STATUS' => $status
            ])
        ) {
            $this->log("User status set to {$status}");
            $this->clearCache(self::CACHE_TAG . $this->user['ID']);
        } else {
            $this->log("User status was not changed");
        }
    }
}
