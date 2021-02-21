<?php

namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Type\DateTime;
use CEvent;
use CUser;
use Qsoft\Sailplay\SailPlayApi;
use Qsoft\Helpers\EventHelper;

class RegisterTask extends AbstractTask
{
    protected $user;
    protected $taskName = 'register';

    private const TAGS = 'Регистрация на сайте';
    private const TAGS_FULL = 'Полная регистрация';
    private const TAGS_ORDER = 'Регистрация при заказе';
    private const TAGS_ONE_CLICK = 'Регистрация при заказе: 1 клик';
    private const TAGS_RESERV = 'Регистрация при заказе: резервирование в магазине';
    private const TAGS_CART = 'Регистрация при заказе: в корзине';


    private const BASE_LOG_PATH = '/local/logs/sailplay/register/';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $check = $this->checkUser();

        switch (count($check)) {
            case 0:
                $this->register();
                break;
            case 1: //обновляем только при регистрации из формы регистрации
                if (!isset($this->data['type_order'])) {
                    $this->update(array_shift($check));
                }
                break;
            case 2:
                $this->sendMail();
                break;
            default:
                break;
        }
        if ($this->data['send_register_tag']) {
            $this->addRegisterTags();
        }
        $this->processSubscriptions();
        $this->sendTags();
    }

    private function checkUser()
    {
        $phone = $this->getPhone();

        if ($phone) {
            $checkByPhone = SailPlayApi::getUserByPhone($phone);
        } else {
            $checkByPhone = false;
        }

        $checkByMail = SailPlayApi::getUserByMail($this->user['EMAIL']);

        $this->log("Check user ['email' => {$this->user['EMAIL']}, 'phone' => {$phone}]");

        $result = [];
        if ($checkByPhone) {
            $this->log("User found by phone. Sailplay ID - {$checkByPhone->id}");
            $result[] = 'phone';
        }

        if ($checkByMail) {
            $this->log("User found by e-mail. Sailplay ID - {$checkByMail->id}");
            $result[] = 'mail';
        }

        if (isset($checkByPhone->id) && isset($checkByMail->id)) {
            if ($checkByPhone->id === $checkByMail->id) {
                $result = ['phone'];
            } else {
                $this->user['CHECK_BY_PHONE'] = $checkByPhone->id;
                $this->user['CHECK_BY_EMAIL'] = $checkByMail->id;
            }
        }

        if (count($result) === 0) {
            $this->log("User not found in Sailplay");
        }

        return $result;
    }

    private function register()
    {
        $phone = $this->getPhone();

        $this->log("Register user #{$this->user['ID']}.");
        $this->log("User data:");
        $this->log(SailPlayApi::parseUserData($this->user));

        if (!$phone) {
            $this->log('Error: empty phone.');
            return;
        }

        $register = SailPlayApi::addUser($phone, $this->user);

        if (!$register) {
            $this->log("Register failed.");
            return;
        }
        $this->log("Sailplay user ID - {$register->id}");
        $this->log('По умолчанию отписываем');
        $this->log(SailPlayApi::userUnsubscribe($phone, ['sms_all', 'email_all']));
    }

    private function update(string $key = 'phone')
    {
        $this->log("Updating user #{$this->user['ID']}");
        $this->log("User data:");
        $this->log(SailPlayApi::parseUserData($this->user));

        switch ($key) {
            case 'phone':
                $value = $this->getPhone();
                break;
            case 'mail':
                $value = $this->user['EMAIL'];
                break;
            default:
                $this->log("Error: invalid key - {$key}");
                return false;
        }
        $update = SailPlayApi::updateUser($key, $value, $this->user);
        if (!$update) {
            $this->log("Update failed");
        }

        global $USER;
        $rsUser = $USER->GetByID($this->user['ID']);
        $arUser = $rsUser->Fetch();
        $arUser['UF_SUBSCRIBE_EMAIL'] ? $sub[] = 'email_all' : '';
        $arUser['UF_SUBSCRIBE_SMS'] ? $sub[] = 'sms_all' : '';

        if (!empty($sub)) {
            $this->log('Обновляем подписки --->');
            $this->log(SailPlayApi::userSubscribe($value, $sub, $key));
            $this->log('<---');
        }
    }

    private function addRegisterTags()
    {
        $this->tags[] = self::TAGS;
        if (isset($this->data['type_order'])) {
            $this->tags[] = self::TAGS_ORDER;
            if ($this->data['type_order'] == '1click') {
                $this->tags[] = self::TAGS_ONE_CLICK;
            } elseif ($this->data['type_order'] == 'reserv') {
                $this->tags[] = self::TAGS_RESERV;
            } elseif ($this->data['type_order'] == 'order') {
                $this->tags[] = self::TAGS_CART;
            }
        } else {
            $this->tags[] = self::TAGS_FULL;
        }
        $this->tags = array_merge($this->tags, $this->getGeoTags());
    }

    private function sendMail()
    {
        $this->log("Duplicate users. Mail send.");
        CEvent::Send('DUPLICATE_USER_SAILPLAY', SITE_ID, [
            'BID' => $this->user['ID'],
            'NAME' => $this->user['NAME'],
            'LAST_NAME' => $this->user['LAST_NAME'],
            'ID1' => $this->user['CHECK_BY_EMAIL'],
            'ID2' => $this->user['CHECK_BY_PHONE'],
            'EMAIL' => $this->user['EMAIL'],
            'PHONE' => $this->getPhone(),
            'SECOND_NAME' => $this->user['SECOND_NAME'],
            'GENDER' => $this->getGenderString($this->user['PERSONAL_GENDER']),
            'BIRTHDAY' => $this->user['PERSONAL_BIRTHDAY']
        ]);
    }

    private function getGenderString($gender = false)
    {
        if ($gender) {
            return $gender === 'M' ? 'мужской' : 'женский';
        } else {
            return 'Неизвестно';
        }
    }

    private function processSubscriptions()
    {
        $phone = $this->getPhone();
        // Если регистрация идет с заказа, то подписка обновится в SubscribeTask
        //здесь только отписываемся, так как по-умолчанию СП на все подписывает
        if (isset($this->data['type_order'])) {
            return;
        }
        if (!isset($this->data['subscribe'])) {
            return;
        }

        $subs = [];

        if ($this->data['subscribe']['sms']) {
            $this->addTags('Подписка на SMS: Полная регистрация.');
            $subs[] = 'sms_all';
        }
        if ($this->data['subscribe']['email']) {
            $this->addTags('Подписка на E-mail: Полная регистрация.');
            $subs[] = 'email_all';
        }

        if (!empty($subs)) {
            SailPlayApi::userSubscribe($phone, $subs);
            $cuser = new CUser();
            $cuser->Update($this->user['ID'], [
               'UF_SP_LAST_TAG' => end($this->tags),
               'UF_SP_SUB_TIME' => new DateTime()
            ]);
            EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');

            /*
             * отписываем от лишнего, если юзер ее не выбрал, так как по-умолчанию СП на все подписывает
             */
            if (!$this->data['subscribe']['sms']) {
                SailPlayApi::userUnsubscribe($phone, ['sms_all']);
            }
            if (!$this->data['subscribe']['email']) {
                SailPlayApi::userUnsubscribe($phone, ['email_all']);
            }
        }
    }
}
