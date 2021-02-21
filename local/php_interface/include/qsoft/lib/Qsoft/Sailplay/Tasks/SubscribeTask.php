<?php


namespace Qsoft\Sailplay\Tasks;

use Bitrix\Main\Type\DateTime;
use CUser;
use Qsoft\Sailplay\SailPlayApi;
use stdClass;

class SubscribeTask extends AbstractTask
{

    private const BASE_LOG_PATH = '/local/logs/sailplay/subscribe/';
    private const REGISTER_TAGS = [
        'Регистрация на сайте',
        'Регистрация при подписке на E-mail',
    ];
    private const SUBSCRIBE_FOOTER_TAG = 'Подписка на E-mail: Подвал';
    private const SUBSCRIBE_DOUBLE_TAG = 'E-mail: Double Opt-In';

    protected $taskName = 'subscribe';
    /*
     * siteStatus:
     * Y - подписан
     * W - есть профиль, не подписан
     * N - нет профиля
     */

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        if (in_array($this->data['source'], ['1click', 'reserv', 'order'])) {
            $this->processSubOrder();
        }

        switch ($this->data['source']) {
            case 'lk':
                $this->processSubLk();
                break;
            case 'footer':
                $this->processSubFooter();
                break;
        }

        $cuser = new CUser();
        $cuser->Update($this->user['ID'], [
            'UF_SP_LAST_TAG' => end($this->tags),
            'UF_SP_SUB_TIME' => new DateTime()
        ]);
    }

    private function processSubOrder()
    {
        $sub = [];
        $phone = $this->getPhone();
        if ($this->data['subscribe']['sms']) {
            $this->log('Подписка ' . $phone . ' на SMS');
            $sub[] = 'sms_all';
            $this->addTags('Подписка на SMS: при оформлении заказа');
            if ($this->data['source'] == '1click') {
                $this->addTags('Подписка на SMS: при оформлении заказа в 1 клик');
            }
            if ($this->data['source'] == 'reserv') {
                $this->addTags('Подписка на SMS: при оформлении заказа резервирования в магазине');
            }
            if ($this->data['source'] == 'order') {
                $this->addTags('Подписка на SMS: при оформлении заказа в корзине');
            }
        }
        if ($this->data['subscribe']['email']) {
            $this->log('Подписка ' . $phone . ' на EMAIL');
            $sub[] = 'email_all';
            $this->addTags('Подписка на E-mail: при оформлении заказа');
            if ($this->data['source'] == '1click') {
                $this->addTags('Подписка на E-mail: при оформлении заказа в 1 клик');
            }
            if ($this->data['source'] == 'reserv') {
                $this->addTags('Подписка на E-mail: при оформлении заказа резервирования в магазине');
            }
            if ($this->data['source'] == 'order') {
                $this->addTags('Подписка на E-mail: при оформлении заказа в корзине');
            }
        }

        if (!empty($sub)) {
            $this->log('Ответ сервера на подписку --->');
            $this->log(SailPlayApi::userSubscribe($phone, $sub));
            $this->log('<---');
            $this->log('Отправка тегов --->');
            $this->sendTags();
            $this->log('<---');
        }
    }


    private function processSubFooter()
    {
        $user = $this->findUserByEmail();

        // первй сценарий
        if (!$user && $this->isSubscribedSite()) {
            if ($this->addUser()) {
                $this->subscribe();
            }

            $this->sendTags();
            return;
        }

        // второй и третий сценарии
        if ($user && $this->isSubscribedSite()) {
            if (!$this->isSubscribedSP($user)) {
                $this->subscribe();
            } else {
                $this->addSubscribeTags();
            }

            $this->sendTags();
            return;
        }
        // 4-9 сценарии
        if (!$this->isSubscribedSite()) {
            // 4 и 7
            if (!$user) {
                if ($this->addUser()) {
                    $this->subscribe(true);
                }

                $this->sendTags();
                return;
            }

            if ($this->isSubscribedSP($user)) {
                // 8
                if (!$this->hasProfileSite()) {
                    $this->addTags(self::REGISTER_TAGS);
                }
                // 5
                $this->addSubscribeTags(true);

                $this->sendTags();
                return;
            } else {
                // 6
                if ($this->hasProfileSite()) {
                    $this->subscribe(true);
                } else {
                    // 9
                    if ($this->addUser()) {
                        $this->subscribe(true);
                    }
                }

                $this->sendTags();
                return;
            }
        }
    }

    /*
    protected function sendTags()
    {
        if (empty($this->tags)) {
            return;
        }
        $this->log("Adding Tags for user #{$this->user['ID']}");
        $this->log($this->tags);

        $tags = SailPlayApi::userAddTags($this->user['EMAIL'], $this->tags, 'email');

        if (!$tags) {
            $this->log("Tags add failed.");
        }

        $this->log("Tags added: {$tags->added_tags_count}");
    }
*/
    private function isSubscribedSite(): bool
    {
        return $this->data['siteStatus'] === 'Y';
    }

    private function hasProfileSite(): bool
    {
        return $this->data['siteStatus'] !== 'N';
    }

    private function isSubscribedSP(?stdClass $user): bool
    {
        if (!$user) {
            return false;
        }
        if (!isset($user->subscriptions)) {
            return false;
        }
        if (!is_array($user->subscriptions)) {
            return false;
        }

        return in_array('email_all', $user->subscriptions);
    }

    private function addUser(): bool
    {
        $this->log('Adding user to sailplay');
        $this->log($this->user);
        $addResult = SailPlayApi::addUser($this->user['EMAIL'], $this->user, 'email');
        if ($addResult) {
            SailPlayApi::updateUser('mail', $this->user['EMAIL'], $this->user);
            $this->log('User added. User id in sailplay - ' . $addResult->id);
            $this->unsubscribeSMS();
            $this->tags = $this->getGeoTags();
            $this->addTags(self::REGISTER_TAGS);

            return true;
        } else {
            return false;
        }
    }

    private function subscribe(bool $double = false)
    {
        $this->log('Subscribe user to email');
        SailPlayApi::userSubscribe($this->user['EMAIL'], ['email_all'], 'email');
        $this->addSubscribeTags($double);
    }
    private function addSubscribeTags(bool $double = false)
    {
        $tags = $double ? [self::SUBSCRIBE_FOOTER_TAG, self::SUBSCRIBE_DOUBLE_TAG] : [self::SUBSCRIBE_FOOTER_TAG];
        $this->tags = array_merge($tags, $this->getGeoTags());
    }
    private function unsubscribeSMS()
    {
        $this->log('Unsubscribe user from sms');
        SailPlayApi::userUnsubscribe($this->user['EMAIL'], ['sms_all'], 'email');
    }

    private function findUserByEmail()
    {
        $this->log('Retrieving user data by e-mail: ' . $this->user['EMAIL']);
        $user = SailPlayApi::getUserByMail($this->user['EMAIL'], false, true);
        $user ? $this->log('User found') : $this->log('User not found');

        return $user;
    }

    private function processSubLk()
    {
        $sub = [];
        $unsub = [];
        if ($this->data['sms'] === 'subscribe') {
            $sub[] = 'sms_all';
            $this->addTags('Подписка на SMS: Личный кабинет');
        } elseif ($this->data['sms'] === 'unsubscribe') {
            $unsub[] = 'sms_all';
            $this->addTags('Отписка на SMS: Личный кабинет');
        }
        if ($this->data['email'] === 'subscribe') {
            $sub[] = 'email_all';
            $this->addTags('Подписка на E-mail: Личный кабинет');
        } elseif ($this->data['email'] === 'unsubscribe') {
            $unsub[] = 'email_all';
            $this->addTags('Отписка на E-mail: Личный кабинет');
        }

        if (!empty($sub)) {
            $this->log('Subscribe user lk');
            $this->log($sub);
            SailPlayApi::userSubscribe($this->user['EMAIL'], $sub, 'email');
        }

        if (!empty($unsub)) {
            $this->log('Unsubscribe user lk');
            $this->log($unsub);
            SailPlayApi::userUnsubscribe($this->user['EMAIL'], $unsub, 'email');
        }
        $this->sendTags();
    }
}
