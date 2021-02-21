<?php


namespace Qsoft\Sailplay\Tasks;

use Qsoft\Sailplay\SailPlayApi;

class UpdateTask extends AbstractTask
{

    protected $taskName = 'update';

    protected const BASE_LOG_PATH = '/local/logs/sailplay/update/';

    private const REGISTER_TASK_NAME = 'register';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        if (empty($this->data['oldPhone']) && empty($this->data['oldEmail'])) {
            return false;
        }


        $phone = $this->getPhone();
        if (!$phone) {
            $this->log('Error: empty phone.');
        }

        $this->log('Updating user ' . $this->user['ID']);
        $this->log('User data:');
        $this->log($this->user);

        if ($this->data['oldPhone'] === $this->user['PERSONAL_PHONE']) {
            unset($this->user['PERSONAL_PHONE']);
        }

        if ($this->data['oldEmail'] === $this->user['EMAIL']) {
            unset($this->user['EMAIL']);
        }

        if (preg_match('`.*@rshoes.ru`i', $this->user['EMAIL'])) {
            if ($phone) {
                $spUser = SailPlayApi::getUserByPhone($phone);
            } else {
                $spUser = SailPlayApi::getUserByMail($this->data['oldEmail']);
            }
            if ($spUser && !empty($spUser->email)) {
                unset($this->user['EMAIL']);
            }
        }

        if (isset($this->user['PERSONAL_PHONE']) && $this->user['PERSONAL_PHONE'] != $this->data['oldPhone']) {
            if (SailPlayApi::getUserByPhone($this->user['PERSONAL_PHONE'])) {
                $this->log('В SP найден пользователь с телефоном, который указал пользователь, отмена обновления');
                return false;
            }
        }
        if (isset($this->user['EMAIL']) && $this->user['EMAIL'] != $this->data['oldEmail']) {
            if (SailPlayApi::getUserByMail($this->user['EMAIL'])) {
                $this->log('В SP найден пользователь с почтой, которую указал пользователь, отмена обновления');
                return false;
            }
        }
        if ($phone) {
            $update = SailPlayApi::updateUser('phone', $phone, $this->user);
        } elseif (!empty($this->data['oldEmail'])) {
            $email = $this->data['oldEmail'];
            $update = SailPlayApi::updateUser('mail', $email, $this->user);
        }

        if ($update) {
            $this->log('User updated');
        } else {
            $this->log('Update failed');
        }
    }

    public function add(array &$tasksArray, array $data): bool
    {
        if (array_key_exists(self::REGISTER_TASK_NAME, $tasksArray)) {
            return false;
        }

        return parent::add($tasksArray, $data);
    }

    protected function getPhone(): string
    {
        $phone = $this->data['oldPhone'] ?: '';

        return $phone;
    }
}
