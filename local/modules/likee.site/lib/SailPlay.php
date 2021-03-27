<?php
/**
 * User: Azovcev Artem
 * Date: 22.03.17
 * Time: 11:15
 *
 * Список того, что было изменено в модуле (не под гитом)
 * 1) \Sailplay\Partner\HashFunction::calculateHash - должен возвращать просто $hashSource (это USER_ID)
 * 2) \Sailplay\Partner\Order::getMarketingActionPositions - была ошибка, метод должен возвращать \Likee\Site\SailPlay::getMarketingActionPositionsForOrder($OrderID)
 * 3) \UserStorageImpl::getBirthDate - была ошибка с форматом даты
 */

namespace Likee\Site;

use Bitrix\Main\Loader;
use Sailplay\Partner;

/**
 * Класс для работы с системой SailPlay
 *
 * @package Likee\Site
 */
class SailPlay
{
    /**
     * Платежная система для SailPlay
     */
    const PAY_SYSTEM_ID = 12;

    /**
     * @var object Экземпляр SailPlayApi
     */
    protected static $obApi;

    /**
     * Возвращает экземпляр SailPlayApi
     *
     * @return Partner\SailplayApi
     */
    public static function getApi()
    {
        if (is_null(self::$obApi)) {
            $obPartnerLib = \SailplayPartnerModule::getPartnerLib();
            self::$obApi = new Partner\SailplayApi(new Partner\ServerCall(), $obPartnerLib->getOptions());
        }

        return self::$obApi;
    }

    /**
     * Возвращает хранилище пользователя
     *
     * @return \UserStorageImpl Экземпляр UserStorageImpl
     */
    protected static function getUserStorage()
    {
        return new \UserStorageImpl(new Partner\Options(new \OptionStorageImpl()));
    }

    /**
     * Возвращает оброботчик клиентов SailPlay
     *
     * @return Partner\ClientProcessor Экземпляр Partner\ClientProcessor
     */
    protected static function getClientProcessor()
    {
        $obPartnerLib = \SailplayPartnerModule::getPartnerLib();

        return new Partner\ClientProcessor(
            self::getApi(),
            new \LoggerImpl(),
            new Partner\SessionContainer(),
            self::getUserStorage(),
            $obPartnerLib->getOptions()
        );
    }

    /**
     * Возвращает клиента SailPlay
     *
     * @param int $iUserId Id клиента
     * @return Partner\Client клиент SailPlay
     */
    protected static function getClient($iUserId)
    {
        return self::getClientProcessor()->getClient($iUserId);
    }

    /**
     * Возвращает информацию о пользователе в системе SailPlay
     *
     * @param int|null $iUserId Id пользователя
     * @param bool $bHistory Получить историю
     * @return array|bool Массив ошибок|Результат работы
     */
    public static function getUserInfo($iUserId = null, $bHistory = false)
    {
        $iUserId = User::checkUserId($iUserId);

        if ($iUserId <= 0)
            return false;

        if (!Loader::includeModule('sailplay.integration'))
            return false;

        try {
            $obClient = self::getClient($iUserId);
            if ($bHistory) {
                $obApi = new SailPlayApi();
                $arUser = $obApi->getClientInfoWithHistory($obClient);
            } else {
                $arUser = self::getApi()->getClientInfo($obClient);
            }

            return $arUser && is_array($arUser) ? $arUser : false;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Возвращает кол-во бонусов у пользователя
     *
     * @param int|null $iUserId Id пользователя
     * @param string $sType Тип бонусов (spent_extra, confirmed, total, spent, unconfirmed)
     * @return int Количество бонусов
     */
    public static function getUserBonuses($iUserId = null, $sType = 'confirmed')
    {
        $arTypes = [
            'spent_extra',
            'confirmed',    //начисленные и подтвержденные
            'total',        //сумма всех начисленных баллов (подтвержденных и не подтвержденных)
            'spent',        //сумма потраченных клиентом баллов
            'unconfirmed'   //начсленные, не подтвержденные
        ];

        if (!in_array($sType, $arTypes))
            $sType = 'confirmed';

        $arUser = self::getUserInfo($iUserId);
        return intval($arUser['points'][$sType]);
    }

    /**
     * Возвращает кол-во бонусов которые получат при покупке товара
     *
     * @param int $ID Id модели
     * @param int $iQuantity Кол-во товара
     * @return int Кол-во бонусов
     */
    public static function getProductBonuses($ID, $iQuantity = 1)
    {
        $ID = intval($ID);

        if ($ID <= 0)
            return 0;

        if (
            !Loader::includeModule('iblock')
            || !Loader::includeModule('sale')
            || !Loader::includeModule('sailplay.integration')
        )
            return 0;

        /*$arPrice = \CPrice::GetBasePrice($ID);

        if ($arPrice) {
            $iPrice = floatval($arPrice['PRICE']);
        } else {*/
        $arItem = \CIBlockElement::GetList(
            [],
            [
                'ID' => $ID,
                'IBLOCK_ID' => \CIBlockElement::GetIBlockByID($ID)
            ],
            false,
            ['nTopCount' => 1],
            ['ID', 'IBLOCK_ID', 'PROPERTY_MINIMUM_PRICE']
        )->Fetch();

        $iPrice = floatval($arItem['PROPERTY_MINIMUM_PRICE_VALUE']);
        //}

        if ($iPrice <= 0)
            return 0;

        $arData = [
            1 => [
                'sku' => (string)$ID,
                'price' => $iPrice * $iQuantity
            ]
        ];

        try {
            $arResponse = self::getApi()->calculate(json_encode($arData));
            return floatval($arResponse['cart']['total_points']);
        } catch (\Exception $e) {
            return 0;
        }
    }


    /**
     * Возвращает кол-во бонусов которые получит при покупке товаров из корзины
     *
     * @param int $iUseBonuses - сколько бонусов пользователь хочет использовать для платы
     * @return int Кол-во Бонусов
     */
    public static function getBasketBonuses($iUseBonuses = 0)
    {
        global $USER;

        if (!Loader::includeModule('sale'))
            return 0;

        $iFUser = \CSaleBasket::GetBasketUserID(true);

        // если у пользователя нет F_USER значит и нет товаров в корзине
        if ($iFUser <= 0)
            return 0;

        if (!Loader::includeModule('sailplay.integration'))
            return 0;

        try {
            $obPartnerLib = \SailplayPartnerModule::getPartnerLib();
            $sPositions = $obPartnerLib->getMarketingActionPositions();

            if ($iUseBonuses > 0 && $USER->IsAuthorized()) {
                $sPositions = self::addDiscountsPointsToPositions($sPositions, $iUseBonuses);
                $obClient = self::getClient($USER->GetID());
                $obApi = new SailPlayApi();
                $arResponse = $obApi->marketingActionsCalc($obClient, $sPositions);
            } else {
                $arResponse = self::getApi()->calculate($sPositions);
            }

            return floatval($arResponse['cart']['total_points']);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Возвращает кол-во бонусов которые пользователь может потратить на оплату товарв из корзины (заказа)
     *
     * @return int Кол-во Бонусов
     */
    public static function getBasketCanUseBonuses()
    {
        global $USER;

        if (!$USER->IsAuthorized() || !Loader::includeModule('sale'))
            return 0;

        $iFUser = \CSaleBasket::GetBasketUserID(true);

        // если у пользователя нет F_USER значит и нет товаров в корзине
        if ($iFUser <= 0)
            return 0;

        if (!Loader::includeModule('sailplay.integration'))
            return 0;

        try {
            //$obClient = self::getClient($USER->GetID());

            $obPartnerLib = \SailplayPartnerModule::getPartnerLib();
            $sPositions = $obPartnerLib->getMarketingActionPositions();

            $obApi = new SailPlayApi();
            $arResponse = $obApi->marketingActionsCalc(false, $sPositions);

            return floatval($arResponse['cart']['total_discount_points_max']);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Возвращает кол-во бонусов полученных за заказ
     *
     * @param int $iOrderId Id заказа
     * @param string $iSailPlayUserId Id пользоватля SailPlay
     * @return int Кол-во бонусов
     */
    public static function getOrderBonuses($iOrderId, $iSailPlayUserId = null)
    {
        $iOrderId = intval($iOrderId);
        if ($iOrderId <= 0)
            return 0;

        if (!Loader::includeModule('sailplay.integration'))
            return 0;

        try {
            $obApi = new SailPlayApi();
            $arResponse = $obApi->getPurchaseInfo($iOrderId, $iSailPlayUserId);
            return floatval($arResponse['purchase']['points_delta']);

            /*$obPartnerLib = \SailplayPartnerModule::getPartnerLib();
            $sPositions = $obPartnerLib->getMarketingActionPositions($iOrderId);
            $arResponse = self::getApi()->calculate($sPositions);
            return self::caclProductBonuses($arResponse);*/
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Событие сохранения заказа
     *
     * Сбрасывается кеш пользователя
     *
     * Если заказ новый то вызывается обработчик события onOrderAdd модуля sailplay.integration
     *
     * @param \Bitrix\Sale\Order $order Заказ
     * @param bool $bIsNew Новый заказ
     * @param array $arValues
     */
    public static function onOrderAdd($order, $bIsNew, $arValues)
    {
        //очищаем кеш пользователя связанный с SailPlay
        \Bitrix\Main\Application::getInstance()->getTaggedCache()->clearByTag('SAILPLAY_USER_' . $order->getUserId());

        if (!$bIsNew)
            return;

        if (!Loader::includeModule('sale') || !Loader::includeModule('sailplay.integration'))
            return;

        $ID = $order->getId();
        $arOrder = \CSaleOrder::GetByID($ID);

        // Дальше код взят из метода
        \SailplayEventHandlers::onOrderAdd($ID, $arOrder);
    }

    /**
     *  Событие регистрации пользователя
     *
     *  Проверка существует ли пользователь
     *
     *  Создание или обновление данных пользователя
     *
     * @param array $arFields Данные пользователя
     */
    public static function onUserRegister($arFields)
    {
        if (!Loader::includeModule('sailplay.integration'))
            return;

        $iUserId = intval($arFields['USER_ID']);

        if ($iUserId <= 0)
            return;

        $arNewUser = $arUserByPhone = $arUserByEmail = [];

        $obApi = new SailPlayApi();
        $obClientFactory = new Partner\ClientFactory();

        //ищем пользователя по телефону
        try {
            $obClientByPhone = $obClientFactory->createClientByPhone($arFields['PERSONAL_PHONE']);
            $arUserByPhone = self::getApi()->getClientInfo($obClientByPhone);
        } catch (\Exception $e) {
        }

        //ищем пользователя по e-mail
        try {
            $obClientByEmail = $obClientFactory->createClientByEmail($arFields['EMAIL']);
            $arUserByEmail = self::getApi()->getClientInfo($obClientByEmail);
        } catch (\Exception $e) {
        }

        $arSailPlayUserFields = array(
            'last_name' => $arFields['LAST_NAME'],
            'middle_name' => $arFields['SECOND_NAME'],
            'first_name' => $arFields['NAME'],
            'sex' => $arFields['PERSONAL_GENDER'] == 'F' ? 2 : 1,
            'birth_date' => date('Y-m-d', MakeTimeStamp($arFields['PERSONAL_BIRTHDAY']))
        );

        try {
            //если найден пользователь по емайл и телефону, то объединяем их, телефон считается основным
            if (self::checkResponse($arUserByPhone) && self::checkResponse($arUserByEmail)) {

                if ($arUserByPhone['id'] !== $arUserByEmail['id']) {
                    $obApi->usersMerge(array(
                        'from_phone' => $arUserByPhone['phone'],
                        'to_email' => $arUserByEmail['email']
                    ));
                }

                $obClient = $obClientFactory->createClientByPhone($arFields['PERSONAL_PHONE']);
                $obApi->updateClient($obClient, $arSailPlayUserFields);

            } elseif (self::checkResponse($arUserByPhone)) {

                $obClient = $obClientFactory->createClientByPhone($arFields['PERSONAL_PHONE']);
                $obApi->updateClient($obClient, array_merge($arSailPlayUserFields, array('new_email' => $arFields['EMAIL'])));

            } elseif (self::checkResponse($arUserByEmail)) {

                $obClient = $obClientFactory->createClientByEmail($arFields['EMAIL']);
                $obApi->updateClient($obClient, array_merge($arSailPlayUserFields, array('new_phone' => $arFields['PERSONAL_PHONE'])));

            } else {
                //Если ничего не нашли, создаем нового пользователя
                $obPersonalInfo = Partner\PersonalInfo::createFromApiResponse($arSailPlayUserFields);

                $obClient = $obClientFactory->createClientByPhone($arFields['PERSONAL_PHONE']);
                $arNewUser = self::getApi()->addClient($obClient, $obPersonalInfo);

                if (self::checkResponse($arNewUser)) {
                    //добавляем email
                    $obApi->updateClient($obClient, ['add_email' => $arFields['EMAIL']]);
                    //добавляем тег
                    self::getApi()->tagsAdd($obClient, 'Регистрация на сайте', '');
                }
            }

            //#10854 после геристарции подписываем пользователя
            if (!empty($obClient) && is_object($obClient)) {
                $obApi = new SailPlayApi();
                $obApi->subscribe($obClient, true, true);

                $arTags = ['Подписка при регистрации'];
                if (!empty($GLOBALS['SAILPLAY_TAGS']) && is_array($GLOBALS['SAILPLAY_TAGS'])) {
                    $arTags = array_merge($arTags, $GLOBALS['SAILPLAY_TAGS']);
                }

                self::getApi()->tagsAdd($obClient, implode(',', $arTags), '');

                $user = new \CUser;
                $user->Update($iUserId, array("UF_SUBSCRIBE_TAGS" => implode(',', $arTags)));
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Проверяет ответ от SailPay
     *
     * @param mixed $arResponse Ответ SailPay
     * @return bool Возвращает true если ответ не имеет ошибок
     */
    public static function checkResponse($arResponse)
    {
        return !empty($arResponse) && is_array($arResponse) && $arResponse['status'] == 'ok';
    }

    /**
     * Этот метод вызывается внутри модуля sailplay.integration
     *
     * в методе Sailplay\Partner\Order::getMarketingActionPositions()
     *
     * первая часть, до //CUSTOM, то что было в методе Sailplay\Partner\Order::getMarketingActionPositions()\
     *
     * вторая часть добавляет оплату бонусами, для каждого товара надо отправлять кол-во бонусов, на которое он был оплачен
     *
     * @param int|bool $OrderID Id заказа
     * @return string Позиции заказа
     */
    public static function getMarketingActionPositionsForOrder($OrderID = false)
    {
        Loader::includeModule('sale');
        Loader::includeModule('catalog');

        $dbBasketItems = \CSaleBasket::GetList(
            array('ID' => 'ASC'), //
            array_merge(
                array('FUSER_ID' => \CSaleBasket::GetBasketUserID()),
                ($OrderID > 0 ? array('ORDER_ID' => $OrderID) : array('ORDER_ID' => 'NULL'))
            ),
            false,
            false,
            array('ID', 'CALLBACK_FUNC', 'PRODUCT_PROVIDER_CLASS', 'MODULE', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT'));
        $arPositions = array();

        $i = 0;
        while ($arBasketItem = $dbBasketItems->Fetch()) {
            $i++;
            $mxResult = \CCatalogSku::GetProductInfo($arBasketItem['PRODUCT_ID']);

            if (is_array($mxResult)) {
                $arBasketItem['PRODUCT_ID'] = $mxResult['ID'];
            }

            $arPositions[$i] = array(
                'sku' => (string)$arBasketItem['PRODUCT_ID'],
                'price' => $arBasketItem['PRICE'] * $arBasketItem['QUANTITY']
            );
        }

        $sPositions = json_encode($arPositions);

        //CUSTOM
        if ($OrderID) {
            $order = \Bitrix\Sale\Order::load($OrderID);

            $arPayment = \Bitrix\Sale\Payment::getList([
                'filter' => [
                    'ORDER_ID' => $OrderID,
                    'PAY_SYSTEM_ID' => \Likee\Site\SailPlay::PAY_SYSTEM_ID
                ],
                'limit' => 1,
                'select' => ['ID', 'SUM']
            ])->fetch();

            $iUseBonuses = floatval($arPayment['SUM']);

            if ($iUseBonuses > 0) {
                $sPositions = self::addDiscountsPointsToPositions($sPositions, $iUseBonuses);
            }
        }

        return $sPositions;
    }


    /**
     * Добавляет скидку к позициям заказа
     *
     * @param string $sPositions Позиции заказа
     * @param int $iUseBonuses Количество бонусов
     * @return string Позиции с примененнымы бонусами
     */
    private static function addDiscountsPointsToPositions($sPositions, $iUseBonuses = 0)
    {
        $arPositions = json_decode($sPositions, true);

        $obApi = new SailPlayApi();
        $arResponse = $obApi->marketingActionsCalc(false, $sPositions);

        $arPositionsBonuses = []; // кол-во бонусов которые можно использовать для каждого товара
        foreach ($arResponse['cart']['positions'] as $arPosition) {
            $arPositionsBonuses[$arPosition['product']['sku']] = floatval($arPosition['discount_points_max']);
        }

        foreach ($arPositions as $iKey => $arPosition) {
            if (array_key_exists($arPosition['sku'], $arPositionsBonuses)) {
                $iPositionUseBonuses = min($arPositionsBonuses[$arPosition['sku']], $iUseBonuses);

                $iUseBonuses -= $iPositionUseBonuses;

                if ($iPositionUseBonuses > 0)
                    $arPositions[$iKey]['discount_points'] = $iPositionUseBonuses;

                if ($iUseBonuses <= 0)
                    break;
            }
        }

        $sPositions = json_encode($arPositions);

        return $sPositions;
    }

    /**
     * Оформляет подписку на Email
     *
     * @param string $sEmail Email
     * @param array $arTags Тэги
     * @return bool true в случае успешного выполнения
     */
    public static function subscribeByEmail($sEmail, $arTags = [])
    {
        global $USER;

        if (!Loader::includeModule('sailplay.integration'))
            return false;

        $arUser = \CUser::GetList($by = 'ID', $order = 'ASC', [
            '=EMAIL' => $sEmail
        ])->Fetch();

        //если пользователь уже есть, то он уже зарегистрирован в SailPlay
        if ($arUser) {
            $iUserId = intval($arUser['ID']);
        } else {
            $sPass = (new \Bitrix\Main\Type\RandomSequence('subscribe_' . time()))->randString(6);

            $iUserId = $USER->Add([
                'LID' => SITE_ID,
                'ACTIVE' => 'Y',
                'GROUP_ID' => [2, 3, 5],
                'LOGIN' => $sEmail,
                'EMAIL' => $sEmail,
                'PASSWORD' => $sPass
            ]);

            $arTags[] = 'Регистрация на сайте';
        }

        if ($iUserId > 0) {
            $obApi = new SailPlayApi();
            $obClientFactory = new Partner\ClientFactory();

            $arUserByEmail = [];

            //ищем пользователя по e-mail
            try {
                $obClient = $obClientFactory->createClientByEmail($sEmail);
                $arUserByEmail = self::getApi()->getClientInfo($obClient);
            } catch (\Exception $e) {
            }

            try {
                if (! self::checkResponse($arUserByEmail)) {
                    $obClient = self::getClient($iUserId);
                    $obPersonalInfo = Partner\PersonalInfo::createFromUserStorage(self::getUserStorage(), $iUserId);
    
                    $arNewUser = self::getApi()->addClient($obClient, $obPersonalInfo);
                    if (self::checkResponse($arNewUser)) {
                        $obApi->updateClient($obClient, ['add_email' => $sEmail]);
                    }
                }
    
                if (!empty($obClient) && is_object($obClient)) {
                    $obApi->subscribe($obClient, true);
    
                    if (!empty($arTags)) {
                        self::getApi()->tagsAdd($obClient, implode(',', $arTags), '');
                        
                        $user = new \CUser;
                        $user->Update($iUserId, array("UF_SUBSCRIBE_TAGS" => implode(',', $arTags)));
                    }
                }      
            } catch (\Exception $e) {
            }
        }

        return true;
    }

    /**
     * Отменяет подписку на Email
     *
     * @param string $sEmail Email
     * @param array $arTags Тэги
     * @return bool true в случае успешного выполнения
     */
    public
    static function unSubscribeByEmail($sEmail, $arTags = [])
    {
        if (!Loader::includeModule('sailplay.integration'))
            return false;

        $arUser = \CUser::GetList($by = 'ID', $order = 'ASC', [
            '=EMAIL' => $sEmail
        ])->Fetch();

        if (!$arUser)
            return false;

        $obApi = new SailPlayApi();
        $obClientFactory = new Partner\ClientFactory();

        $arUserByEmail = [];

        //ищем пользователя по e-mail
        try {
            $obClient = $obClientFactory->createClientByEmail($sEmail);
            $arUserByEmail = self::getApi()->getClientInfo($obClient);
        } catch (\Exception $e) {
        }

        try {
            if (! self::checkResponse($arUserByEmail)) {
                $obClient = self::getClient($arUser['ID']);
            }
            
            $arResponse = $obApi->unsubscribe($obClient, true, true);

            if ($arResponse['status'] == 'ok' && !empty($arTags))
                self::getApi()->tagsAdd($obClient, implode(',', $arTags), '');

            return $arResponse['status'] == 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверяет подписку по Email
     *
     * @param string $sEmail Email
     * @return bool true в случае успешного выполнения
     */
    public static function isSubscribedByEmail($sEmail)
    {
        $obApi = new SailPlayApi();
        $obClientFactory = new Partner\ClientFactory();

        $arUserByEmail = [];
        
        //ищем пользователя по e-mail
        try {
            $obClient = $obClientFactory->createClientByEmail($sEmail);
            $arUserByEmail = $obApi->getClientInfoWithSubscriptions($obClient);

            if (is_array($arUserByEmail) && in_array('email_all', $arUserByEmail['subscriptions'])) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Проверяет подписку по Email
     *
     * @param string $sEmail Email
     * @param array $arTags arTags
     */
    public static function addTagsByEmail($sEmail, $arTags)
    {
        $obApi = new SailPlayApi();
        $obClientFactory = new Partner\ClientFactory();

        $arUserByEmail = [];

        //ищем пользователя по e-mail
        try {
            $obClient = $obClientFactory->createClientByEmail($sEmail);
            $arUserByEmail = self::getApi()->getClientInfo($obClient);

            if (self::checkResponse($arUserByEmail)) {
                self::getApi()->tagsAdd($obClient, implode(',', $arTags), '');
            }
        } catch (\Exception $e) {
        }
    }

    public static function updateClientIdentifierInfo($iUserId, $arFields)
    {
        try {
            $obApi = new SailPlayApi();
            $obClientFactory = new Partner\ClientFactory();

            if (!empty($arFields['ORIGINAL']['origin_user_id'])) {
                $obClient = $obClientFactory->createClientByOriginId($arFields['ORIGINAL']['origin_user_id']);
            } elseif (!empty($arFields['ORIGINAL']['phone'])) {
                $obClient = $obClientFactory->createClientByPhone($arFields['ORIGINAL']['phone']);
            } else {
                $obClient = self::getClient($iUserId);
            }

            if (!empty($arFields['EMAIL'])) {
                $arNewFields = [
                    'new_email' => $arFields['EMAIL']
                ];

                $obApi->updateClient($obClient, $arNewFields);
            }
            if (!empty($arFields['PHONE'])) {
                $arNewFields = [
                    'new_phone' => $arFields['PHONE']
                ];

                $obApi->updateClient($obClient, $arNewFields);
            }
        } catch (\Exception $e) {
        }
    }
}