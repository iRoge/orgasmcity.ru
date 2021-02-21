<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Application;
use Bitrix\Main\ObjectException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use CUser;
use Qsoft\Sailplay\SailPlayApi;
use stdClass;

class SyncUserTask extends AbstractTask
{
    private const BASE_LOG_PATH = '/local/logs/sailplay/syncUser/';
    private const CACHE_TAG = 'SP_BONUSES_HISTORY';
    private const HISTORY_TABLE = 'qsoft_sp_user_history';
    private const GOLD = 'Золотой статус';
    private const PLATINUM = 'Платиновый статус';
    private const SILVER = 'Серебряный статус';
    private const BRONZE = 'Бронзовый статус';
    private const NONE = 'Неизвестен';

    protected $taskName = 'syncUser';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $spUser = $this->loadOrRegisterUser();
        if (!$spUser) {
            $this->log('Пользователь в Sailplay не найден и ошибка создания нового');
            return;
        }

        $history = $spUser->history;
        $points = $spUser->points->confirmed;
        $points = intval($points);

        if (!empty($spUser->phone)) {
            $tags = SailPlayApi::userGetTags($spUser->phone, [self::PLATINUM, self::GOLD, self::SILVER, self::BRONZE], 'phone');
        } else {
            $tags = SailPlayApi::userGetTags($spUser->email, [self::PLATINUM, self::GOLD, self::SILVER, self::BRONZE], 'email');
        }
        if (!$tags) {
            $tags = [];
        }
        $this->log("User tags: ");
        $this->log($tags);

        $this->updateStatusOnSite($tags);
        $this->updateHistoryOnSite($history);
        $this->updatePointsOnSite($points);
        $this->clearCache(self::CACHE_TAG . $this->user['ID']);
        $this->updateInfoOnSite($spUser);
        $this->updateEmptyInfoOnSailplay($spUser);
    }

    private function updateHistoryOnSite(array $history)
    {
        global $DB;
        $id = $this->user['ID'];
        $this->log('Обновляем историю пользователя ' . $id . ' на сайте из Sailplay');

        if (empty($history)) {
            $this->log('История пуста');
            return;
        }

        $table = self::HISTORY_TABLE;
        $DB->Query("DELETE FROM `{$table}` WHERE `user_id` = {$id}");

        $sql = "INSERT INTO `{$table}` (`user_id`, `history`) VALUES ";

        foreach ($history as $entry) {
            $entry = $DB->ForSql(json_encode($entry, JSON_UNESCAPED_UNICODE));
            $sql .= "({$id}, '{$entry}'),";
        }

        $sql = rtrim($sql, ',');
        $DB->Query($sql);

        $this->log('История обновлена');
    }

    private function updatePointsOnSite(int $points)
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

    private function updateStatusOnSite($tags)
    {
        if (empty($tags)) {
            $status = self::NONE;
        } else {
            $status = array_pop($tags);
            $status = $status->name;
        }

        $status = rtrim($status, ' статус');

        $cUser = new CUser();
        if ($cUser->Update($this->user['ID'], [
            'UF_SAILPLAY_STATUS' => $status
        ])
        ) {
            $this->log("User status set to {$status}");
        } else {
            $this->log("User status was not changed");
        }
    }
    //получает всю информацию из СП и заменяет на сайте
    private function updateInfoOnSite($spUser)
    {
        global $USER;
        $this->log("Checking if was updated in Sailplay");
        $sailplayUser = [
            'EMAIL' => $spUser->email,
            'PERSONAL_PHONE' => !empty($spUser->phone) ? $this->formatPhone($spUser->phone) : null,
            'PERSONAL_BIRTHDAY' => is_set($spUser->birth_date) ? $this->formatDate($spUser->birth_date) : null,
            'NAME' => $spUser->first_name,
            'SECOND_NAME' => $spUser->middle_name,
            'LAST_NAME' => $spUser->last_name,
            'PERSONAL_GENDER' => is_set($spUser->sex) ? $this->parseGender($spUser->sex) : null
        ];

        $this->log($sailplayUser);
        $this->log($this->user);
        $diff = array_diff($sailplayUser, $this->user);

        if (!empty($diff['EMAIL'])) {
            if (preg_match('`.*@rshoes.ru`i', $diff['EMAIL']) && !empty($this->user['EMAIL'])) {
                unset($diff['EMAIL']);
            }
        }

        if (!empty($diff)) {
            $this->log("Updating user on site");
            $this->log($diff);

            if (!$USER->Update($this->user['ID'], $diff)) {
                $this->log($USER->LAST_ERROR);
            }
            $this->log('Пользователь обновлен на сайте');
        }
    }

    private function updateEmptyInfoOnSailplay($spUser)
    {
        $this->log('Заполняем пустые поля в Sailplay');
        $siteUser = SailPlayApi::parseUserData($this->user);

        foreach ($siteUser as $key => $value) {
            if (empty($value) || !empty($spUser->$key)) {
                unset($siteUser[$key]);
            }
        }

        if (!empty($siteUser['new_email'])) {
            if (!empty($spUser->email) && !preg_match('`.*@rshoes.ru`i', $spUser->email)) {
                unset($siteUser['new_email']);
            } else {
                if ($spUser->email == $siteUser['new_email'] || preg_match('`.*@rshoes.ru`i', $siteUser['new_email'])) {
                    unset($siteUser['new_email']);
                }
            }
        }

        if (!empty($siteUser['new_phone'])) {
            if (!empty($spUser->phone)) {
                unset($siteUser['new_phone']);
            } else {
                if (SailPlayApi::getUserByPhone($siteUser['new_phone'])) {
                    unset($siteUser['new_phone']);
                }
            }
        }

        $this->log($siteUser);
        if (!empty($siteUser)) {
            if (!empty($spUser->phone)) {
                $update = SailPlayApi::addUserInfo($spUser->phone, 'phone', $siteUser);
            } else {
                $update = SailPlayApi::addUserInfo($spUser->email, 'email', $siteUser);
            }
            $this->log($update);
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
