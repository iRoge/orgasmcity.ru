<?php
/**
 * User: Azovcev Artem
 * Date: 23.03.17
 * Time: 20:34
 */

namespace Likee\Site;

use Bitrix\Main\Loader;
use Sailplay\Partner;
use Sailplay\Partner\Client;

Loader::includeModule('sailplay.integration');

/**
 * Класс для работы с SailPlay api.
 *
 * @package Likee\Site
 */
class SailPlayApi extends Partner\SailplayApi
{
    /**
     * Экземпляр Partner\ServerCall
     *
     * @var Partner\ServerCall
     */
    private $serverCall;

    /**
     * Параметры партнера
     *
     * @var Partner\PartnerOptions
     */
    protected $options;

    /**
     * Конструктор класса SailPlayApi
     *
     */
    public function __construct()
    {
        $obPartnerLib = \SailplayPartnerModule::getPartnerLib();
        $this->serverCall = new Partner\ServerCall();
        $this->options = $obPartnerLib->getOptions();
    }

    /**
     * Обращение к api SailPlay
     *
     * @param string $version Версия api
     * @param string $method Вызываемый метод
     * @param array $params Параметры вызываемого метода
     * @param bool $post Тип запроса POST
     * @return array Результат выполнения метода
     */
    private function call($version, $method, $params, $post = false)
    {
        return $this->serverCall->invokeMethod($version, $method, $params, $post);
    }

    /**
     * Возвращает информацию о заказе
     *
     * @param integer $orderId Id заказа
     * @param integer $iSailPlayUserId Id пользователя SailPlay
     * @return array Информация о заказе
     */
    public function getPurchaseInfo($orderId, $iSailPlayUserId = null)
    {
        $arParams = array(
            'token' => $this->options->getApiToken(),
            'store_department_id' => $this->options->getDepartmentId(),
            'order_num' => $orderId
        );

        if (!empty($iSailPlayUserId)) {
            $arParams['user_id'] = $iSailPlayUserId;
        }

        $response = $this->call('v2', 'purchases/get/', $arParams);

        return $response;
    }

    /**
     * Расчет допустимого количества начисляемых и списываемых баллов
     *
     * @param Partner\Client $client Экземпляр клиента Partner\Client
     * @param string $sPositions Позиции заказа
     * @return array
     * @deprecated
     */
    public function pointsCalc(Partner\Client $client, $sPositions)
    {
        $response = $this->call('v2', 'points/calc/',
            array(
                $client->getSchemeField() => $client->getIdentifier(),
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'positions' => $sPositions
            )
        );

        return $response;
    }

    /**
     * Расчет чека / корзины
     *
     * @param Partner\Client|bool $client Экземпляр клиента
     * @param string $sPositions Позиции заказа
     * @return array Результат расчета
     */
    public function marketingActionsCalc($client, $sPositions)
    {
        $arPositions = json_decode($sPositions, true);

        foreach ($arPositions as &$arPosition) {
            if (!isset($arPosition['discount_points']))
                $arPosition['discount_points'] = 0;
        }
        unset($arPosition);

        $sPositions = json_encode($arPositions);

        $arParams = array(
            'token' => $this->options->getApiToken(),
            'store_department_id' => $this->options->getDepartmentId(),
            'cart' => $sPositions
        );

        if (!empty($client) && is_object($client)) {
            $arParams[$client->getSchemeField()] = $client->getIdentifier();
        }

        $response = $this->call('v2', 'marketing-actions/calc/', $arParams);

        return $response;
    }

    /**
     * Отменяет подписку клиента
     *
     * @param Partner\Client $client Экземпляр клиента
     * @param bool $bEmail Отписаться от рассылок по почте
     * @param bool $bPhone Отписаться от рассылок по смс
     * @return array Ответ сервера
     */
    public function unsubscribe(Partner\Client $client, $bEmail = false, $bPhone = false)
    {
        $arList = [];

        if ($bEmail)
            $arList[] = 'email_all';

        if ($bPhone)
            $arList[] = 'sms_all';

        $response = $this->call('v2', 'users/unsubscribe/',
            array(
                $client->getSchemeField() => $client->getIdentifier(),
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'unsubscribe_list' => implode(',', $arList)
            )
        );

        return $response;
    }


    /**
     * Подписать клиента на рассылку
     * @param Partner\Client $client Экземпляр клиента
     * @param bool $bEmail Подписаться на рассылоку по почте
     * @param bool $bPhone Подписаться на рассылоку по смс
     * @return array Ответ сервера
     */
    public function subscribe(Partner\Client $client, $bEmail = false, $bPhone = false)
    {
        $arList = [];

        if ($bEmail)
            $arList[] = 'email_all';

        if ($bPhone)
            $arList[] = 'sms_all';

        $response = $this->call('v2', 'users/subscribe/',
            array(
                $client->getSchemeField() => $client->getIdentifier(),
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'subscribe_list' => implode(',', $arList)
            )
        );

        return $response;
    }

    /**
     * Возвращает информацию о клиенте с историей
     *
     * @param Client $client Экземпляр клиента
     * @return array  Информация о клиенте
     */
    public function getClientInfoWithHistory(Client $client)
    {
        $response = $this->call('v2', 'users/info/',
            array(
                $client->getSchemeField() => $client->getIdentifier(),
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'extra_fields' => 'auth_hash',
                'fields' => 'user_points',
                'history' => 1
            ));
        return $response;
    }

    /**
     * Возвращает информацию о клиенте с подписками
     *
     * @param Client $client Экземпляр клиента
     * @return array  Информация о клиенте
     */
    public function getClientInfoWithSubscriptions(Client $client)
    {
        $response = $this->call('v2', 'users/info/',
            array(
                $client->getSchemeField() => $client->getIdentifier(),
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'extra_fields' => 'auth_hash',
                'fields' => 'user_points',
                'subscriptions' => 1
            ));
        return $response;
    }

    /**
     * Объединяет историю и теги двух клиентов в один
     *
     * @param array $arFields Поля для объединения
     * @return array Результат объединения
     */
    public function usersMerge($arFields = [])
    {
        $response = $this->call('v2', 'users/merge/',
            array_merge(
                $arFields,
                array(
                    'token' => $this->options->getApiToken(),
                    'store_department_id' => $this->options->getDepartmentId(),
                )
            )
        );

        return $response;
    }

    /**
     * Обновляет информацию о клиенте
     *
     * @param Client $client Экземпляр клиента
     * @param array $arFields Обновляемые поля
     * @return array Результат обновления
     */
    public function updateClient(Client $client, $arFields = [])
    {
        $response = $this->call('v2', 'users/update/',
            array_merge(
                $arFields,
                array(
                    $client->getSchemeField() => $client->getIdentifier(),
                    'token' => $this->options->getApiToken(),
                    'store_department_id' => $this->options->getDepartmentId(),
                )
            )
        );

        return $response;
    }

    /**
     * Добавляет клиента с пустыми полями
     *
     * @param integer $iUserId Id добавляемого клиента
     * @return array Результат добавления
     */
    public function addEmptyClient($iUserId)
    {
        $response = $this->call('v2', 'users/add/',
            array(
                'origin_user_id' => $iUserId,
                'token' => $this->options->getApiToken(),
                'store_department_id' => $this->options->getDepartmentId(),
                'extra_fields' => 'auth_hash',
            )
        );

        return $response;
    }
}