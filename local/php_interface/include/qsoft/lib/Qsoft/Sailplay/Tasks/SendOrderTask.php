<?php


namespace Qsoft\Sailplay\Tasks;

use Qsoft\Sailplay\SailPlayApi;

class SendOrderTask extends AbstractTask
{
    private const BASE_LOG_PATH = '/local/logs/sailplay/sendOrderTask/';
    protected $taskName = 'sendOrder';

    public function __construct()
    {
        $this->logPath = self::BASE_LOG_PATH;
        $this->logFile = date('Y.m.d') . '.log';
    }

    public function execute()
    {
        $this->sendOrder();
    }
    
    private function sendOrder()
    {
        if (empty($this->data)) {
            return;
        }

        foreach ($this->data as $orderInTask) {
            $phone = $this->getPhoneForSendOrder($orderInTask['order_phone'], $orderInTask['order_email']);
            if ($phone) {
                $this->log("Отправка заказа для " . $phone);
                $this->log("Тип заказа " . $orderInTask['type_order']);
                $this->log("Заказ номер " . $orderInTask['order_num']);
                $this->log("Корзина " . $orderInTask['cart']);
                $order = SailplayApi::addPurchases(
                    $phone,
                    $orderInTask['cart'],
                    $orderInTask['order_num'],
                    $orderInTask['l_date'],
                    'phone'
                );
            }
            if ($order) {
                $this->log('Заказ ' . $orderInTask['type_order'] . ' отправлен');
            } else {
                $this->log('Ошибка отправки заказа ' . $orderInTask['type_order']);
            }
        }
    }

    private function getPhoneForSendOrder($orderPhone, $orderEmail)
    {
        $phone = $this->getPhone(true);

        if ($phone) {
            $this->log('Ищем в Sailplay пользователя ' . $phone);
            $spUser = SailPlayApi::getUserByPhone($phone);
            if (!$spUser) {
                $this->log('Пользователь ' . $phone . ' не найден');
                $this->log('Ищем в Sailplay пользователя ' . $this->user['EMAIL']);
                $spUser = SailPlayApi::getUserByMail($this->user['EMAIL']);
                if ($spUser->user_phone) {
                    $this->log('Нашли в Sailplay у пользователя ' . $this->user['EMAIL'] . ' телефон ' . $spUser->user_phone);
                    $phone = $spUser->user_phone;
                } else {
                    if ($spUser) {
                        $this->log('Добавляем в Sailplay пользователю ' . $this->user['EMAIL'] . ' телефон ' . $phone);
                        SailPlayApi::addUserPhone($this->user['EMAIL'], $phone);
                    }
                }
            }
        } else {
            $this->log('Телефон для отправки заказа профиле не найден');
            if ($orderPhone) {
                $this->log('Ищем в Sailplay пользователя с телефоном из заказа ' . $orderPhone);
                $spUser = SailPlayApi::getUserByPhone($phone);
                if (!$spUser) {
                    $this->log('Пользователь ' . $orderPhone . ' не найден');
                    $this->log('Ищем в Sailplay пользователя с email из заказа' . $orderEmail);
                    $spUser = SailPlayApi::getUserByMail($orderEmail);
                    if ($spUser->user_phone) {
                        $this->log('Отправляем заказ в Sailplay пользователю ' . $orderEmail . ' по телефону ' . $spUser->user_phone);
                        $phone = $spUser->user_phone;
                    }
                }
            }
        }
        return $phone;
    }
}
