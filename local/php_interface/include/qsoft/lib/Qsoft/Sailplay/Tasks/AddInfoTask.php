<?php


namespace Qsoft\Sailplay\Tasks;

use Qsoft\Sailplay\SailPlayApi;
use CEvent;

class AddInfoTask extends AbstractTask
{

    protected $taskName = 'addInfo';

    protected const BASE_LOG_PATH = '/local/logs/sailplay/addInfo/';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $this->log('Старт addInfo таска');
        if (isset($this->data['new']['EMAIL'])) {
            $phone = $this->getPhone();
            $res= SailPlayApi::addUserEmail($phone, $this->data['new']['EMAIL']);
            if ($res['DUBLICATE']) {
                $this->log('Обнаружен пользователь с таким EMAIL в Sailplay');
                $currentUserSailpay = SailPlayApi::getUserByPhone($phone);
                $this->sendMail($currentUserSailpay, $res['DUBLICATE'], 'email');
            }
            if ($res['OK'] && !isset($res['ERROR'])) {
                $this->log('ok add email');
            }
        }
        if (isset($this->data['new']['PHONE'])) {
            $email = $this->user['EMAIL'];
            $res = SailPlayApi::addUserPhone($email, $this->data['new']['PHONE']);
            if ($res['DUBLICATE']) {
                $this->log('Обнаружен пользователь с таким PHONE в Sailplay');
                $currentUserSailpay = SailPlayApi::getUserByMail($email);
                $this->sendMail($currentUserSailpay, $res['DUBLICATE'], 'phone');
            }
            if ($res['OK'] && !isset($res['ERROR'])) {
                $this->log('ok add phone');
            }
        }
        $this->log('Конец addInfo таска');
    }

    private function sendMail($userSailplay1, $userSailplay2, $error)
    {
        $this->log("Duplicate users. Mail send.");
        CEvent::Send('DUPLICATE_USER_SAILPLAY', SITE_ID, [
            'ID' => $this->user['ID'],
            'PHONE' => $this->getPhone(),
            'EMAIL' => $this->user['EMAIL'],
            'NAME' => $this->user['NAME'],
            'LAST_NAME' => $this->user['LAST_NAME'],
            'SECOND_NAME' => $this->user['SECOND_NAME'],

            'SP_ID_1' => $userSailplay1->id,
            'SP_PHONE_1' => $userSailplay1->phone,
            'SP_NAME_1' => $userSailplay1->first_name,
            'SP_LAST_NAME_1' => $userSailplay1->last_name,
            'SP_SECOND_NAME_1' => $userSailplay1->middle_name,

            'SP_ID_2' => $userSailplay2->id,
            'SP_PHONE_2' => $userSailplay2->phone,
            'SP_NAME_2' => $userSailplay2->first_name,
            'SP_LAST_NAME_2' => $userSailplay2->last_name,
            'SP_SECOND_NAME_2' => $userSailplay2->middle_name,

            'NEW_INFO_TEXT' => $error
        ]);
    }
}
