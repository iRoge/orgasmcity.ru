<? use Bitrix\Main\UserTable as UserTable;
use Qsoft\Sailplay\SailPlayApi;
use Qsoft\Sailplay\Tasks\TaskManager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class QsoftSubscribeManagerComponent extends \CBitrixComponent
{
    private const EMAIL_UF = 'UF_SUBSCRIBE_EMAIL';
    private const SMS_UF = 'UF_SUBSCRIBE_SMS';
    private $user;
    private $subscriptions = [];

    public function executeComponent()
    {
        global $USER;
        if ($USER->IsAuthorized()) {
            $this->getUser();
            $this->getSubscriptions();
        }

        if ($this->request->isPost() && check_bitrix_sessid()) {
            $this->processPost();
        }

        $this->arResult['SUBSCRIPTIONS'] = $this->subscriptions;
        $this->includeComponentTemplate();
    }

    private function getUser()
    {
        global $USER;
        $dbRes = UserTable::getList([
            'filter' => ['ID' => $USER->GetID()],
            'select' => [self::EMAIL_UF, self::SMS_UF, 'PERSONAL_PHONE', 'EMAIL', 'ID']
        ]);

        if ($user = $dbRes->fetch()) {
            $this->user = $user;
        }
        $this->arResult['PHONE'] = $this->user['PERSONAL_PHONE'];
        $this->arResult['MAIL'] = $this->user['EMAIL'];
    }
    private function processPost()
    {
        $sub = ['source' => 'lk'];
        $fields = [];
        if ($this->request->get('subscribe-email') === 'Y') {
            if (!$this->subscriptions['email']) {
                $sub['email'] = 'subscribe';
            }
            $this->subscriptions['email'] = true;
            $fields[self::EMAIL_UF] = 1;
        } else {
            if ($this->subscriptions['email']) {
                $sub['email'] = 'unsubscribe';
            }
            $this->subscriptions['email'] = false;
            $fields[self::EMAIL_UF] = 0;
        }
        if ($this->request->get('subscribe-sms') === 'Y') {
            if (!$this->subscriptions['sms']) {
                $sub['sms'] = 'subscribe';
            }
            $this->subscriptions['sms'] = true;
            $fields[self::SMS_UF] = 1;
        } else {
            if ($this->subscriptions['sms']) {
                $sub['sms'] = 'unsubscribe';
            }
            $this->subscriptions['sms'] = false;
            $fields[self::SMS_UF] = 0;
        }

        if ($fields[self::EMAIL_UF] != $this->user[self::EMAIL_UF] || $fields[self::SMS_UF] != $this->user[self::SMS_UF]) {
            $cuser = new CUser();
            $cuser->Update($this->user['ID'], $fields);
            $this->user[self::EMAIL_UF] = $fields[self::EMAIL_UF];
            $this->user[self::SMS_UF] = $fields[self::SMS_UF];
        }

        $taskManager = new TaskManager();
        $taskManager->setUser($this->user['ID']);
        $taskManager->addTask('subscribe', $sub);
    }

    private function getSubscriptions()
    {
        if (!empty($this->user[self::EMAIL_UF]) || !empty($this->user[self::SMS_UF])) {
            $this->subscriptions['sms'] = !empty($this->user[self::SMS_UF]);
            $this->subscriptions['email'] = !empty($this->user[self::EMAIL_UF]);
        } else {
            if ($_SERVER['REQUEST_URI'] == '/personal/subscribe/') {
                $this->getSubscriptionsSP();
            }
        }
    }

    private function getSubscriptionsSP()
    {
        $phone = $this->user['PERSONAL_PHONE'];

        if (!empty($phone)) {
            $userSp = SailPlayApi::getUserByPhone($phone, false, true);
        } else {
            $userSp = SailPlayApi::getUserByMail($this->user['EMAIL'], false, true);
        }

        $this->subscriptions['sms'] = in_array('sms_all', $userSp->subscriptions);
        $this->subscriptions['email'] = in_array('email_all', $userSp->subscriptions);
    }
}
