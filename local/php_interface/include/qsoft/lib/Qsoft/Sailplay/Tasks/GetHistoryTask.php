<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Application;
use Bitrix\Main\ObjectException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use CUser;
use Qsoft\Sailplay\SailPlayApi;
use stdClass;

class GetHistoryTask extends AbstractTask
{
    private const BASE_LOG_PATH = '/local/logs/sailplay/getHistory/';
    private const CACHE_TAG = 'SP_BONUSES_HISTORY';
    private const HISTORY_TABLE = 'qsoft_sp_user_history';

    private $history;

    protected $taskName = 'getHistory';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $this->getUser();
    }

    private function getUser()
    {
        $user = $this->loadOrRegisterUser();
        if (!$user) {
            $this->log('Error: user not found');
            return;
        }

        $this->updateIfNeeded($user);

        $history = $user->history;
        $points = $user->points->confirmed;

        $points = intval($points);

        $this->updateHistory($history);
        $this->updatePoints($points);
        $this->clearCache(self::CACHE_TAG . $this->user['ID']);
    }

    private function updateHistory(array $history)
    {
        $this->log("Updating user history");
        global $DB;

        if (empty($history)) {
            $this->log('Error: empty history');
            return;
        }

        $id = $this->user['ID'];
        $table = self::HISTORY_TABLE;
        $DB->Query("DELETE FROM `{$table}` WHERE `user_id` = {$id}");

        $sql = "INSERT INTO `{$table}` (`user_id`, `history`) VALUES ";

        foreach ($history as $entry) {
            $entry = $DB->ForSql(json_encode($entry, JSON_UNESCAPED_UNICODE));
            $sql .= "({$id}, '{$entry}'),";
        }

        $sql = rtrim($sql, ',');
        $DB->Query($sql);

        $this->log("History updated");
    }

    private function updatePoints(int $points)
    {
        $this->log("Updating user points");
        $cUser = new CUser();
        if ($cUser->Update($this->user['ID'], [
                'UF_SAILPLAY_POINTS' => $points
            ])
        ) {
            $this->log("Points updated");
        } else {
            $this->log("Update failed");
            $this->log($cUser->LAST_ERROR);
        }
    }

    private function updateIfNeeded(stdClass $user)
    {
        global $USER;
        $this->log("Checking if was updated in Sailplay");
        $sailplayUser = [
            'EMAIL' => $user->email,
            'PERSONAL_PHONE' => !empty($user->phone) ? $this->formatPhone($user->phone) : null,
            'PERSONAL_BIRTHDAY' => is_set($user->birth_date) ? $this->formatDate($user->birth_date) : null,
            'NAME' => $user->first_name,
            'SECOND_NAME' => $user->middle_name,
            'LAST_NAME' => $user->last_name,
            'PERSONAL_GENDER' => is_set($user->sex) ? $this->parseGender($user->sex) : null
        ];

        $diff = array_diff($sailplayUser, $this->user);

        if (!empty($diff)) {
            $this->log("Updating user on site");
            if (!$USER->Update($this->user['ID'], $diff)) {
                $this->log($user->LAST_ERROR);
            }
        }
    }

    private function formatDate(string $date): ?Date
    {
        try {
            return new Date($date, 'Y-m-d');
        } catch (ObjectException $e) {
            return null;
        }
    }

    private function parseGender(int $gender)
    {
        return $gender === 1 ? 'M' : 'F';
    }

    private function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return $phone;
        }
        $phone = preg_replace('/[^0-9]+/', '', $phone);
        $phone = '+7' . substr($phone, 1);

        return preg_replace('/^\+7(\d{3})(\d{3})(\d{2})(\d{2})$/', '+7 ($1) $2-$3-$4', $phone);
    }
}
