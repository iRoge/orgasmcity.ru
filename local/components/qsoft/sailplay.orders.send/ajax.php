<?php
define('ADMIN_SECTION', true);

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Service\GeoIp\Manager;
use Bitrix\Main\UserTable;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Order;
use Qsoft\Helpers\EventHelper;
use Qsoft\Sailplay\SailPlayApi;

class SailplayOrdersSendAjax extends Controller
{

    private $userPhoneSailplay;  //
    private $userEmailSailplay;
    private $userIdSailplay;

    private $currentOrder;
    private $currentOrderId;
    private $currentUserId;

    private $userIdentifier;
    private $userIdentifierType;

    private $orderType;
    private $orderLocation;
    private $orderPhone;
    private $orderEmail;


    private $arCodeProducts;
    private $arGeoTags;

    private $cache;

    public function configureActions()
    {
        if (CModule::IncludeModule("sale")) {
            return [
                'processChunk' => []
            ];
        }
    }

    private function unsetOldOrderInfo()
    {

        unset($this->userPhoneSailplay);  //
        unset($this->userEmailSailplay);
        unset($this->userIdSailplay);

        unset($this->currentOrder);
        unset($this->currentOrderId);
        unset($this->currentUserId);

        unset($this->userIdentifier);
        unset($this->userIdentifierType);

        unset($this->orderType);
        unset($this->orderLocation);
        unset($this->orderPhone);
        unset($this->orderEmail);
    }

    private function initCache()
    {
        $this->cache = new CPHPCache();
    }

    public function processChunkAction($start, $end, $tmpFile)
    {
        $this->initCache();
        $this->log('');
        $this->log('=====================================================');
        $this->log('Идет отправка заказов с ip: ' . Manager::getRealIp());
        $this->log('=====================================================');
        $this->log('');

        $this->loadProductCodes();

        $resArr = file_get_contents($tmpFile);
        $adsInfo = unserialize($resArr);
        $chunk = array_slice($adsInfo, $start, $end);

        foreach ($chunk as $info) {
            $arOrderIds[] = $info['ID'];
        }

        $rsSales = Order::loadByFilter(array(
            'order' => array('ID' => 'DESC'),
            'filter' => array(
                '@ID' => $arOrderIds,
            )
        ));
        foreach ($rsSales as $order) {
            $this->unsetOldOrderInfo();
            $this->currentOrder = $order;
            $this->currentOrderId = $order->getId();
            $this->log('');
            $this->log('');
            $this->log('===================');
            $this->log('Заказ №' . $this->currentOrderId);
            $this->log('===================');
            $this->getOrderInfo();
            //$this->log('Тип текущего заказа ' . $this->orderType);
            // определяем пользователя
            $userId = $order->getUserId();
            $this->currentUserId = $userId;
            $this->log('Пользователь ID ' . $userId);

            if (!$this->checkUserInSailplay($userId)) {
                $this->log('Ошибка проверки пользователя ID ' . $userId . ' в Sailplay, информация в ' . date('Y.m.d') . ' - ERRORS.log');
                $this->log('Переходим к следующему заказу');
                continue;
            }
            // пусть будет на всякий случай
            //if (!$this->checkRegisterTagsInSailplay($userId)) {
            //    $this->log('Ошибка отправки тегов о регистрации на сайте пользователя ID ' . $userId . ', информация в ' . date('Y.m.d') . ' - ERRORS.log');
            //    $this->log('Продолжаем без отправки тегов');
            //}
            $this->log('Пользователь ID ' . $userId . ' проверен в Sailplay');

            /*
            if ($this->orderType == 'RESERVATION') {
                $this->log('Резервирование в Sailplay не отправляем');
                $this->log('Переходим к следующему заказу');
                continue;
            }

            if ($this->currentOrder->getField('STATUS_ID') != 'O') {
                $this->log('Заказ не оплачен');
                $this->log('Переходим к следующему заказу');
                continue;
            }
            $this->log('Статус заказа - оплачен');
            */
            if (!$this->checkOrderInSailplay()) {
                $this->log('Переходим к следующему заказу');
                continue;
            }

            if (!$this->sendOrderInSailplay()) {
                $this->log('Ошибка отправки заказа в Sailplay, информация в ' . date('Y.m.d') . ' - ERRORS.log');
                $this->log('Переходим к следующему заказу');
                continue;
            }
            $result['done'] .= $this->currentOrderId . ',';
        }

        $result['processed'] = $start + $end;

        if ($start + $end >= count($adsInfo)) {
            $this->log('Все заказы обработаны');
            $this->log('');
            $this->log('=====================================================');
            $this->log('Отправка заказов завершена');
            $this->log('=====================================================');
            $this->log('');
            $result['finished'] = true;

            unlink($_REQUEST['tpm_file']);
        }

        return $result;
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


    private function checkUserInSailplay($userId)
    {
        global $USER;
        $this->log('Проверяем пользователя ID ' . $userId . ' в Sailplay');
        $user = UserTable::GetByID($userId);
        $arUser = $user->Fetch();

        $userPhone = $arUser['PERSONAL_PHONE'];
        $userEmail = $arUser['EMAIL'];
        $this->log('Телефон - ' . $userPhone . ' EMAIL - ' . $userEmail);

        if (!empty($userPhone)) {
            $this->log('Ищем в Sailplay телефон ' . $userPhone);
            $res = SailPlayApi::getUserByPhone($userPhone);
            if ($res) {
                $this->log('Пользователь найден');
                $this->userIdentifier = $userPhone;
                $this->userIdentifierType = 'phone';
                $this->userPhoneSailplay = $userPhone;
                $this->userEmailSailplay = $res->email;
                $this->userIdSailplay = $res->id;
                return true;
            }
            $this->log('По телефону ' . $userPhone . ' в Sailplay пользователь не найден');
        } else {
            $this->log('В профиле пользователя на сайте телефон не найден');
        }
        $this->log('Ищем в Sailplay EMAIL из профиля на сайте ' . $userEmail);
        $res = SailPlayApi::getUserByMail($userEmail);
        if ($res) {
            $this->log('По EMAIL пользователь найден');
            $this->userEmailSailplay = $userEmail;
            $this->userIdSailplay = $res->id;
            if (!empty($res->phone)) {
                $this->log('В Sailplay указан телефон пользователя - ' . $res->phone);
                $this->log('Дальнейшая связь с Sailplay по номеру - ' . $res->phone);
                $this->userPhoneSailplay = $res->phone;
                $this->userIdentifier = $res->phone;
                $this->userIdentifierType = 'phone';
                if (empty($userPhone)) {
                    $this->log('Добавляем пользователю в профиль на сайте телефон - ' . $this->formatPhone($res->phone));
                    $USER->Update($userId, ['PERSONAL_PHONE' => $this->formatPhone($res->phone)]);
                    EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                }
                return true;
            }
            $this->log('У найденного по EMAIL в Sailplay пользователя телефон не указан');
            if (!empty($userPhone)) {
                $this->log('Добавляем в Sailplay телефон пользователя из профиля на сайте ' . $userPhone);
                $res = SailPlayApi::addUserPhone($userEmail, $userPhone);
                if ($res) {
                    $this->log('Пользователю ' . $userEmail . ' в Sailplay добавлен номер ' . $userPhone);
                    $this->log('Дальнейшая связь с Sailplay по номеру - ' . $userPhone);
                    $this->userIdentifier = $userPhone;
                    $this->userIdentifierType = 'phone';
                    $this->userPhoneSailplay = $res->phone;
                    $this->userEmailSailplay = $res->email;
                    return true;
                } else {
                    $this->log('---------------', true);
                    $this->log(
                        'Ошибка при добавлении пользователю' . $userEmail . ' в Sailplay телефона из профиля на сайте' . $userPhone,
                        true
                    );
                    $this->log('Заказ №' . $this->currentOrderId . ' пользователь ' . $this->currentUserId, true);
                    $this->log('---------------', true);
                    return false;
                }
            }
            $this->log('Берем номер телефона из контактной информации заказа - ' . $this->orderPhone);
            $this->log('Ищем пользователя в Sailplay - ' . $this->orderPhone);
            $res = SailPlayApi::getUserByPhone($this->orderPhone);
            if ($res) {
                $this->log('Пользователь найден');
                $this->log('Дублирование пользователей Sailplay, информация в ' . date('Y.m.d') . ' - ERRORS.log');
                $this->log('---------------', true);
                $this->log('Дублирование пользователей Sailplay', true);
                $this->log(
                    'Заказ №' . $this->currentOrderId . ' пользователь ' . $arUser['ID'] . ' не обработан',
                    true
                );
                $this->log(
                    'В Sailplay найден пользователь с email ' . $userEmail . ' (из профиля на сайте ' . $arUser['ID'] . ' )',
                    true
                );
                $this->log(
                    'В Sailplay найден пользователь с телефоном ' . $this->orderPhone . ' (из контактной информации заказа ' . $this->currentOrderId . ' , созданного пользователем ' . $userEmail . ')',
                    true
                );
                $this->log('---------------', true);
                return false;
            } else {
                $this->log('В Sailplay пользователь с телефоном ' . $this->orderPhone . ' не найден');
                if (empty($userPhone)) {
                    $this->log('Добавляем пользователю в профиль на сайте телефон из контактной информации заказа - ' . $this->formatPhone($this->orderPhone));
                    $USER->Update($userId, ['PERSONAL_PHONE' => $this->formatPhone($this->orderPhone)]);
                    EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                }
                $this->log('Добавляем в Sailplay телефон пользователя из контактной информации заказа ' . $this->orderPhone);
                $res = SailPlayApi::addUserPhone($userEmail, $this->orderPhone);
                if ($res) {
                    $this->log('Пользователю ' . $userEmail . ' в Sailplay добавлен номер ' . $this->orderPhone);
                    $this->log('Дальнейшая связь с Sailplay по номеру - ' . $this->orderPhone);
                    $this->userIdentifier = $this->orderPhone;
                    $this->userIdentifierType = 'phone';
                    $this->userPhoneSailplay = $res->phone;
                    $this->userEmailSailplay = $res->email;
                    return true;
                } else {
                    $this->log('---------------', true);
                    $this->log(
                        'Ошибка при добавлении пользователю' . $userEmail . ' в Sailplay телефона из контактной информации заказа ' . $this->orderPhone,
                        true
                    );
                    $this->log('Заказ №' . $this->currentOrderId . ' пользователь ' . $this->currentUserId, true);
                    $this->log('---------------', true);
                    return false;
                }
            }
        } else {
            $this->log('По EMAIL ' . $userEmail . ' в Sailplay пользователь не найден');
            $this->log('Берем номер телефона из контактной информации заказа - ' . $this->orderPhone);
            $this->log('Ищем пользователя в Sailplay - ' . $this->orderPhone);
            $res = SailPlayApi::getUserByPhone($this->orderPhone);
            if ($res) {
                $this->log('Пользователь найден');
                $this->log('Дальнейшая связь с Sailplay по номеру - ' . $this->orderPhone);
                $this->userIdentifier = $this->orderPhone;
                $this->userIdentifierType = 'phone';
                $this->userPhoneSailplay = $res->phone;
                $this->userEmailSailplay = $res->email;
                if (empty($userPhone)) {
                    $this->log('Добавляем пользователю в профиль на сайте телефон - ' . $this->formatPhone($res->orderPhone));
                    $USER->Update($userId, ['PERSONAL_PHONE' => $this->formatPhone($res->orderPhone)]);
                    EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                }
                return true;
            } else {
                $this->log('По номеру телефона из контактной информации заказа - ' . $this->orderPhone . ' в Sailplay пользователь не найден');
                if (!empty($this->orderEmail)) {
                    $this->log('Ищем в Sailplay EMAIL из контактной информации заказа - ' . $this->orderEmail);
                    $res = SailPlayApi::getUserByMail($this->orderEmail);
                    if ($res) {
                        $this->log('По EMAIL из контактной информации заказа пользователь найден');
                        $this->userIdSailplay = $res->id;
                        if (!empty($res->phone)) {
                            $this->log('В Sailplay указан телефон пользователя - ' . $res->phone);
                            $this->log('Дальнейшая связь с Sailplay по номеру - ' . $res->phone);
                            $this->userPhoneSailplay = $res->phone;
                            $this->userIdentifier = $res->phone;
                            $this->userIdentifierType = 'phone';
                            if (empty($userPhone)) {
                                $this->log('Добавляем пользователю в профиль на сайте телефон - ' . $this->formatPhone($res->phone));
                                $USER->Update($userId, ['PERSONAL_PHONE' => $this->formatPhone($res->phone)]);
                                EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                            }
                            return true;
                        }
                        $this->log('У найденного по EMAIL из контактной информации заказа в Sailplay пользователя телефон не указан');
                        if (!empty($userPhone)) {
                            $this->log('Добавляем в Sailplay телефон пользователя из профиля на сайте ' . $userPhone);
                            $res = SailPlayApi::addUserPhone($this->orderEmail, $userPhone);
                            if ($res) {
                                $this->log('Пользователю ' . $this->orderEmail . ' в Sailplay добавлен номер ' . $userPhone);
                                $this->log('Дальнейшая связь с Sailplay по номеру - ' . $userPhone);
                                $this->userIdentifier = $userPhone;
                                $this->userIdentifierType = 'phone';
                                $this->userPhoneSailplay = $res->phone;
                                $this->userEmailSailplay = $res->email;
                                return true;
                            } else {
                                $this->log('---------------', true);
                                $this->log(
                                    'Ошибка при добавлении пользователю' . $this->orderEmail . ' в Sailplay телефона из профиля на сайте' . $userPhone,
                                    true
                                );
                                $this->log(
                                    'Заказ №' . $this->currentOrderId . ' пользователь ' . $this->currentUserId,
                                    true
                                );
                                $this->log('---------------', true);
                                return false;
                            }
                        } else {
                            $this->log('Добавляем в Sailplay телефон пользователя из контактной информации заказа ' . $this->orderPhone);
                            $res = SailPlayApi::addUserPhone($this->orderEmail, $this->orderPhone);
                            if ($res) {
                                $this->log('Пользователю ' . $this->orderEmail . ' в Sailplay добавлен номер ' . $this->orderPhone);
                                $this->log('Дальнейшая связь с Sailplay по номеру - ' . $this->orderPhone);
                                $this->userIdentifier = $this->orderPhone;
                                $this->userIdentifierType = 'phone';
                                $this->userPhoneSailplay = $res->phone;
                                $this->userEmailSailplay = $res->email;
                                if (empty($userPhone)) {
                                    $this->log('Добавляем пользователю в профиль на сайте телефон из контактной информации заказа - ' . $this->formatPhone($this->orderPhone));
                                    $USER->Update($userId, ['PERSONAL_PHONE' => $this->formatPhone($this->orderPhone)]);
                                    EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                                }
                                return true;
                            } else {
                                $this->log('---------------', true);
                                $this->log(
                                    'Ошибка при добавлении пользователю' . $this->orderEmail . ' в Sailplay телефона из контактной информации заказа' . $this->orderPhone,
                                    true
                                );
                                $this->log(
                                    'Заказ №' . $this->currentOrderId . ' пользователь ' . $this->currentUserId,
                                    true
                                );
                                $this->log('---------------', true);
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return $this->registerSailplayUser($arUser);
    }


    private function registerSailplayUser($arUser)
    {
        $this->log('Регистрируем в Sailplay нового пользователя');

        if (isset($arUser['PERSONAL_PHONE'])) {
            $res = SailPlayApi::addUser($arUser['PERSONAL_PHONE'], $arUser);
            $this->userIdentifier = $arUser['PERSONAL_PHONE'];
            $this->userIdentifierType = 'phone';
        } else {
            $res = SailPlayApi::addUser($this->orderPhone, $arUser);
            $this->userIdentifier = $this->orderPhone;
            $this->userIdentifierType = 'phone';
        }
        if ($res) {
            $this->log('Пользователь в Sailplay создан: ' . $this->userIdentifier);
            $this->userPhoneSailplay = $res->phone;
            $this->userEmailSailplay = $res->email;
            $this->userIdSailplay = $res->id;
            //отписываем от подписок по умолчанию
            SailPlayApi::userUnsubscribe($this->userIdentifier, ['sms_all'], $this->userIdentifierType);
            SailPlayApi::userUnsubscribe($this->userIdentifier, ['email_all'], $this->userIdentifierType);
            $this->checkRegisterTagsInSailplay($arUser['ID']);
            return true;
        }
        $this->log('---------------', true);
        $this->log('Ошибка создания нового пользователя в Sailplay', true);
        $this->log('Заказ №' . $this->currentOrderId . ' пользователь ' . $arUser['ID'] . ' не обработан', true);
        $this->log('---------------', true);
        return false;
    }


    private function checkRegisterTagsInSailplay($userId)
    {
        $tags = [];
        $this->log('Проверяем теги регистрации на сайте у пользователя ' . $userId . ' (в Sailplay ' . $this->userIdentifier . ')');
        $res = SailPlayApi::userGetTags($this->userIdentifier, ['Регистрация на сайте'], $this->userIdentifierType);
        if (!empty($res)) {
            $this->log('Теги о регистрации на сайте найдены');
            return true;
        } else {
            $this->log('Тегов о регистрации на сайте нет');
            $tags[] = 'Регистрация на сайте';
            $tags[] = 'Регистрация при заказе';
            if ($this->orderType == 'ONE_CLICK') {
                $tags[] = 'Регистрация при заказе: 1 клик';
            } elseif ($this->orderType == 'RESERVATION') {
                $tags[] = 'Регистрация при заказе: резервирование в магазине';
            } elseif ($this->orderType == 'ORDER') {
                $tags[] = 'Регистрация при заказе: в корзине';
            }
            $geoTags = $this->arGeoTags[$this->orderLocation] ?? $this->loadGeoTags($this->orderLocation);
            $tags = array_merge($tags, $geoTags);
        }

        $this->log('Отправляем теги ');
        $this->log($tags);
        $res = SailplayApi::userAddTags($this->userIdentifier, $tags, $this->userIdentifierType);
        if ($res) {
            $this->log('Теги о регистрации на сайте отправлены');
            return true;
        }
        $this->log('---------------', true);
        $this->log('Ошибка отправки тегов о регистрации на сайте в Sailplay', true);
        $this->log('Заказ №' . $this->currentOrderId . ' пользователь ' . $userId, true);
        $this->log('---------------', true);
        return false;
    }

    private function getOrderInfo()
    {
        $orderPropertyCollection = $this->currentOrder->getPropertyCollection();
        foreach ($orderPropertyCollection as $orderPropertyItem) {
            if ($orderPropertyItem->getField('CODE') == "ORDER_TYPE") {
                $this->orderType = $orderPropertyItem->getField('VALUE');
            }
            if ($orderPropertyItem->getField('CODE') == "LOCATION") {
                $this->orderLocation = $orderPropertyItem->getField('VALUE');
            }
            if ($orderPropertyItem->getField('CODE') == "PHONE") {
                $this->orderPhone = $orderPropertyItem->getField('VALUE');
            }
            if ($orderPropertyItem->getField('CODE') == "EMAIL") {
                $this->orderEmail = $orderPropertyItem->getField('VALUE');
            }
        }
    }

    private function loadProductCodes()
    {
        if ($this->cache->InitCache(86400, 'sendOrderProductCodes', 'sailplay')) {
            $this->arCodeProducts = $this->cache->GetVars()['arCodeProducts'];
        } elseif ($this->cache->StartDataCache()) {
            $arSelect = array("ID", "IBLOCK_ID", "PROPERTY_KOD_1S", "PROPERTY_ARTICLE");
            $arFilter = array("IBLOCK_CODE" => 'CATALOG', "ACTIVE" => "Y");
            $res = CIBlockElement::GetList(
                array(),
                $arFilter,
                false,
                [],
                $arSelect
            );
            while ($arItem = $res->Fetch()) {
                $this->arCodeProducts[$arItem['PROPERTY_ARTICLE_VALUE']] = $arItem['PROPERTY_KOD_1S_VALUE'];
            }
            if (!$this->arCodeProducts) {
                $this->cache->AbortDataCache();
            } else {
                $this->cache->EndDataCache(['arCodeProducts' =>  $this->arCodeProducts]);
            }
        }
    }

    private function sendOrderInSailplay()
    {
        $this->log('Дата заказа: ' . $this->currentOrder->getDateInsert());
        $this->log('Формируем корзину для отправки заказа в Sailplay');
        $data = [];
        $data['order_num'] = $this->currentOrderId;
        $data['l_date'] = $this->currentOrder->getDateInsert()->getTimestamp();
        $basket = $this->currentOrder->getBasket();
        $i = 1;

        foreach ($basket as $basketItem) {
            $basketPropertyCollection = $basketItem->getPropertyCollection();
            foreach ($basketPropertyCollection as $basketPropertyItem) {
                if ($basketPropertyItem->getField('CODE') == "ARTICLE") {
                    $sku = $this->arCodeProducts[$basketPropertyItem->getField('VALUE')];
                }
            }

            $cart[$i] = [
                'sku' => $sku,
                'price' => $basketItem->getPrice(),
                'quantity' => $basketItem->getQuantity()
            ];
            $this->log('Добавляем товар в корзину');
            $this->log($cart[$i]);
            $i++;
        }
        $data['cart'] = json_encode($cart);
        $this->log('Отправляем заказ в Sailplay');
        $res = SailPlayApi::addPurchases($this->userIdentifier, $data['cart'], $data['order_num'], $data['l_date'], $this->userIdentifierType);
        if ($res) {
            $this->log('Заказ №' . $data['order_num'] . ' отправлен в Sailplay пользователю ' . $this->userIdentifier);
            return true;
        }
        $this->log('---------------', true);
        $this->log('Ошибка отправки заказа в Sailplay', true);
        $this->log('Заказ №' . $this->currentOrderId . ' пользователь ' . $this->currentUserId, true);
        $this->log('---------------', true);
        return false;
    }

    private function checkOrderInSailplay()
    {
        $this->log('Проверяем заказ №' . $this->currentOrderId . ' в Sailplay');
        $res = SailPlayApi::checkPurchases($this->currentOrderId);
        if (!$res) {
            $this->log('Заказ №' . $this->currentOrderId . ' в Sailplay не найден');
            return true;
        }

        if ($res->purchase->user_id == $this->userIdSailplay) {
            $this->log('Заказ №' . $this->currentOrderId . ' уже был отправлен в Sailplay');
        } else {
            $this->log('---------------', true);
            $this->log(
                'Заказ повторно найден у пользователя id ' . $res->purchase->user_id . ' (id пользователя в Sailplay)',
                true
            );
            $this->log(
                'Заказ №' . $this->currentOrderId . ' текущий пользователь id ' . $this->userIdSailplay . ' (id пользователя в Sailplay)',
                true
            );
            $this->log('---------------', true);
            $this->log('Заказ №' . $this->currentOrderId . ' найден в Sailplay не у создателя заказа, информация в ' . date('Y.m.d') . ' - ERRORS.log');
        }
        return false;
    }

    private function loadGeoTags($locCode)
    {
        $res = LocationTable::getList(array(
            'filter' => array(
                'CODE' => $locCode,
                '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ),
            'select' => array(
                'NAME_RU' => 'PARENTS.NAME.NAME',
                'TYPE_CODE' => 'PARENTS.TYPE.ID',
            ),
        ));
        while ($arItem = $res->fetch()) {
            if (intval($arItem["TYPE_CODE"]) == 5) {
                $geoTags['city'] = $arItem["NAME_RU"];
            }
            if (intval($arItem["TYPE_CODE"]) == 3) {
                $geoTags['region'] = $arItem["NAME_RU"];
            }
            if (intval($arItem["TYPE_CODE"]) == 1) {
                $geoTags['country'] = $arItem["NAME_RU"];
            }
        }
        $geoTags['region'] = $geoTags['region'] ?? $geoTags['city'];

        return $this->arGeoTags[$locCode] = [
            'Город на сайте: ' . $geoTags['city'],
            'Регион на сайте: ' . $geoTags['region'],
            'Страна на сайте: ' . $geoTags['country']
        ];
    }

    private function log($message, $error = false)
    {
        if (!$error) {
            qsoft_logger($message, date('Y.m.d') . '.log', '/local/logs/sailplay/ordersExport/');
        } else {
            qsoft_logger($message, date('Y.m.d') . ' - ERRORS.log', '/local/logs/sailplay/ordersExport/');
        }
    }
}
