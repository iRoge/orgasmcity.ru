<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Application;
use Bitrix\Main\Service\GeoIp\Manager;
use Bitrix\Main\SystemException;
use Qsoft\Sailplay\SailPlayApi;

abstract class AbstractTask
{
    protected $logPath;
    protected $logFile;
    protected $user;
    protected $taskName;
    protected $data;
    protected $defaultData;
    protected $tags = [];

    abstract public function execute();

    public function add(array &$tasksArray, array $data): bool
    {
        if ($this->taskName != 'sendOrder' && $this->checkDuplicate($tasksArray)) {
            return false;
        }

        if ($this->taskName == 'sendOrder') {
            $tasksArray[$this->taskName][$data['order_num']] = $data;
        } else {
            $tasksArray[$this->taskName] = $data;
        }

        return true;
    }

    public function setUser(array $user)
    {
        $this->user = $user;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function setDefaultData(array $data)
    {
        $this->defaultData = $data;
    }

    public function runWithData(array $data = [])
    {
        $this->setData($data);
        $this->setDefaultData($this->getDefaultData());
        $this->execute();
    }

    public function getDefaultData()
    {
        global $LOCATION;
        return [
            'ip' => Manager::getRealIp(),
            'city' => $LOCATION->getName(),
            'region' => $LOCATION->getRegion(),
            'country' => $LOCATION->getCountry(),
        ];
    }

    protected function log($message)
    {
        qsoft_logger('User ip: ' . $this->defaultData['ip'], $this->logFile, $this->logPath);
        qsoft_logger($message, $this->logFile, $this->logPath);
    }

    protected function getPhone($useEmail = false)
    {
        $this->log('Определяем телефон пользователя');
        $phone = $this->user['PERSONAL_PHONE'];
        if (!$phone) {
            $phone = $this->user['PERSONAL_MOBILE'];
            if (!$phone) {
                $this->log('В профиле на сайте телефон не найден');
                if (!empty($this->user['EMAIL']) && $useEmail) {
                    $this->log('Ищем телефон в Sailplay по email из профиля на сайте' . $this->user['EMAIL']);
                    $res = SailplayApi::getUserByMail($this->user['EMAIL']);
                    $phone = $res->phone;
                }
            }
        }
        if (empty($phone)) {
            $this->log('Телефон пользователя не найден');
            return false;
        } else {
            $this->log('Телефон пользователя ' . $phone);
            return $phone;
        }
    }

    protected function sendTags()
    {
        if (empty($this->tags)) {
            return;
        }
        $this->log("Adding Tags for user #{$this->user['ID']}");
        $this->log($this->tags);

        $phone = $this->getPhone();

        if (!$phone) {
            $this->log('Error: empty phone.');
        }
        if ($phone) {
            $tags = SailplayApi::userAddTags($phone, $this->tags);
        } else {
            $tags = SailPlayApi::userAddTags($this->user['EMAIL'], $this->tags, 'email');
        }

        if (!$tags) {
            $this->log("Tags add failed.");
        }

        $this->log("Tags added: {$tags->added_tags_count}");
    }

    protected function addTags($tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $this->tags = array_merge($this->tags, $tags);
    }

    protected function getGeoTags()
    {
        return [
            'Город на сайте: ' . $this->defaultData['city'],
            'Регион на сайте: ' . $this->defaultData['region'],
            'Страна на сайте: ' . $this->defaultData['country']
        ];
    }

    protected function checkDuplicate(array $tasksArray): bool
    {
        return array_key_exists($this->taskName, $tasksArray);
    }

    protected function clearCache($tag)
    {
        try {
            Application::getInstance()->getTaggedCache()->clearByTag($tag);
            $this->log("Cache cleared. Tag {$tag}");
        } catch (SystemException $e) {
            $this->log("Cache clear failed. Tag {$tag}");
            $this->log($e->getMessage());
            $this->log($e->getTrace());
        }
    }

    protected function loadOrRegisterUser()
    {
        $this->log('Поиск пользователя ' . $this->user['ID'] . ' в Sailplay');
        $phone = $this->getPhone();
        if (!empty($phone)) {
            $this->log('Поиск истории пользователя ' . $phone . ' в Sailplay');
            $user = SailPlayApi::getUserByPhone($phone, true);
        }
        if (!empty($this->user['EMAIL']) && !$user) {
            $this->log('По телефону ' . $phone . ' в Sailplay история не найдена');
            $this->log('Поиск истории пользователя ' . $this->user['EMAIL'] . ' в Sailplay');
            $user = SailPlayApi::getUserByMail($this->user['EMAIL'], true);
        }
        if (!$user) {
            $this->log('Пользователь ' . $this->user['ID'] . ' в Sailplay не найден');
            if (!empty($phone)) {
                $this->log('Регистрируем нового пользователя ' . $phone . ' в Sailplay');
                $data['send_register_tag'] = true;
                $registerTask = new RegisterTask();
                $registerTask->setUser($this->user);
                $registerTask->runWithData($data);
                $this->log('Поиск истории пользователя ' . $phone . ' в Sailplay');
                $user = SailPlayApi::getUserByPhone($phone, true);
            } else {
                return false;
            }
        }

        return $user;
    }

    protected function checkDoubleSailplayUsers($identifier1, $identifier2, $identifierType1, $identifierType2)
    {
        if ($identifierType1 == 'phone') {
            $spUser1 = SailPlayApi::getUserByPhone($identifier1);
        } else {
            $spUser1 = SailPlayApi::getUserByMail($identifier1);
        }
        if (!$spUser1) {
            return false;
        }
        if ($identifierType2 == 'phone') {
            $spUser2 = SailPlayApi::getUserByPhone($identifier2);
        } else {
            $spUser2 = SailPlayApi::getUserByMail($identifier2);
        }
        if (!$spUser2) {
            return false;
        }
        if ($spUser1->id == $spUser2->id) {
            return false;
        }
        return true;
    }
}
