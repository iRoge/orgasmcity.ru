<?

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Type\DateTime as DateTimeAlias;
use Likee\Site\Helper;
use Likee\Site\Helpers\HL;
use \Likee\Site\User;
use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Config\Option;
use \Bitrix\Catalog\StoreTable;
use \Bitrix\Currency\CurrencyManager;
use \Bitrix\Sale\Fuser;
use \Bitrix\Sale\Order;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\PaySystem\Manager as PayManager;
use \Bitrix\Sale\Delivery\Services\Manager as DelManager;
use \Bitrix\Sale\DiscountCouponsManager as CouponsManager;
use Qsoft\DeliveryWays\WaysByDeliveryServicesTable;
use Qsoft\DeliveryWays\WaysDeliveryTable;
use \Qsoft\Helpers\ComponentHelper;
use Qsoft\Helpers\EventHelper;
use \Qsoft\Helpers\IBlockHelper;
use Qsoft\PaymentWays\WaysByPaymentServicesTable;
use Qsoft\PaymentWays\WaysPaymentTable;
use Qsoft\Pvzmap\PVZFactory;
use Qsoft\Pvzmap\PVZTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class QsoftOrderComponent extends ComponentHelper
{
    // обрабатываемы типы действий
    private const TYPES = array(
        "cart",
        "offers",
        "offers2",
        "order",
        "1click",
        "reserv",
        "coupon",
        "basketAdd",
        "basketDel",
    );
    // самовывоз
    private const DELIVERY_PICKUP_ID = 3;
    // ПВЗ
    private $arPvzIds = array();
    // путь кэша
    protected string $relativePath = '/qsoft/order';
    // тип действия
    private $type;
    // тип ответа
    private $json = false;
    // флаг аякса
    public $ajax = false;
    // значения свойств заказа
    private $postProps;
    // пользователь
    private $user = array();
    // предложения
    private $offers = array();
    // свойства заказа
    private $orderProps;
    // корзина только местных или неместных товаров (для создания заказа)
    private $newBasket;
    // корзина
    private $basket;
    // валюта
    private $currency;
    // тип плательщика
    private $personType;
    // Данные по названию и классу пунктов выдачи из таблицы b_qsoft_pvz
    private $arPVZNames = [];
    // Массив свойств, получаемых от dadata
    private $arDadataProps = [
        'federal_district',
        'region_fias_id',
        'region_kladr_id',
        'region_with_type',
        'region_type',
        'region_type_full',
        'region',
        'area_fias_id',
        'area_kladr_id',
        'area_with_type',
        'area_type',
        'area_type_full',
        'area',
        'city_fias_id',
        'city_kladr_id',
        'city_with_type',
        'city_type',
        'city_type_full',
        'city',
        'city_area',
        'city_district_fias_id',
        'city_district_kladr_id',
        'city_district_with_type',
        'city_district_type',
        'city_district_type_full',
        'city_district',
        'settlement_fias_id',
        'settlement_kladr_id',
        'settlement_with_type',
        'settlement_type',
        'settlement_type_full',
        'settlement',
        'street_fias_id',
        'street_kladr_id',
        'street_with_type',
        'street_type',
        'street_type_full',
        'street',
        'house_fias_id',
        'house_kladr_id',
        'house_type',
        'house_type_full',
        'house',
        'block_type',
        'block_type_full',
        'block',
        'fias_id',
        'kladr_id',
        'beltway_hit',
        'beltway_distance',
    ];

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        Loader::includeModule("sale");
        Loader::includeModule("catalog");
        Loader::includeModule("iblock");
        return $arParams;
    }

    public function executeComponent()
    {
        global $LOCATION;
        // определяем, что делать
        if ($_REQUEST['action'] == 'subscribe') {
            return false;
        }
        $this->getType();
        // для аякс запроса карточки (когда есть ошибки) ставим флаг аякса
        if ($this->checkType(array("cart")) && $_POST["ajax"] == "Y") {
            $this->ajax = true;
        }
        // для всего, кроме корзины и товаров, ответ в JSON
        if (!$this->checkType(array("cart", "offers", "offers2"))) {
            $this->json = true;
        }
        if ($this->checkType(array("coupon"))) {
            // проверяем купон
            $this->checkCoupon();
        }
        if ($this->checkType(array("basketAdd", "basketDel"))) {
            // добавляем в корзину
            $offerId = intval($_POST["offerId"]);
            if ($this->checkType(array("basketAdd"))) {
                $quantity = intval($_POST["quantity"]);
                $isLocal = $_POST["isLocal"];
                // добавляем в корзину
                $this->addToBasket($offerId, $isLocal, $quantity);
            } else {
                $needLocalBasketPrice = $_POST["needLocalBasketPrice"];
                // получаем список доставок
                $this->getDeliveries();
                $this->getDeliveryWays();
                // удаляем из корзины
                $this->deleteFromBasket($offerId, $needLocalBasketPrice);
            }
        }
        if ($this->checkType(array("order", "1click", "reserv"))) {
            // устанавливаем доставку
            $this->setDeliveryId();
            // устанавливаем оплату
            $this->setPaymentId();
            // проверяем данные
            if (!$this->checkData()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
        }
        if ($this->checkType(array("cart", "offers", "offers2","order", "1click", "reserv"))) {
            // устанавливаем корзину
            if (!$this->setBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            if ($this->checkType(array("cart", "offers", "offers2"))) {
                // применяем промокоды
                $this->getCoupon();
            }
            // проверяем корзину
            if (!$this->checkBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            if ($this->checkType(array("cart", "offers", "offers2"))) {
                // получаем данные для вывода
                $this->getItems();
                $this->getAvailableOffersSizesForItems();
            }
        }
        if ($this->checkType(array("cart"))) {
            // получаем поля юзера
            $this->getUser();
            // устанавливаем стандартизированное название региона для dadata
            $this->arResult['DADATA_REGION_NAME'] = $LOCATION->getDadataStandartRegionNameFromLocation();
            // устанавливаем стандартизированное название города для dadata
            $this->arResult['DADATA_CITY_NAME'] = $LOCATION->getDadataStandartCityNameFromLocation();
            // проверяем работоспособность дадаты и остаток запросов на день
            $this->arResult['DADATA_STATUS'] = $LOCATION->getDadataStatus();
            // передаем данные юзера
            $this->arResult["USER"] = $this->user;
            //передаем имя хоста для установки кук
            $this->arResult['CURRENT_HOST'] = $LOCATION->getCurrentHost();
            //передаем куки с данными пользователя
            $this->getUserInfoCookie();
            // получаем список доставок
            $this->getDeliveries();
            if (!empty($this->arResult["DELIVERY"]["ARRAY"])) {
                $this->getDeliveryWays();
            }
            // получаем список оплат
            $this->getPayments();
            if (!empty($this->arResult["PAYMENT"]["ARRAY"])) {
                $this->getPaymentWays();
            }
            // получаем цену карточки с оплатой
            $this->getBasketsPrices();
        }
        if ($this->checkType(array("order", "1click", "reserv"))) {
            // проверяем юзера
            $this->checkUser();
            $this->updateUserProfile();
            // обновляем подписки юзера Sailplay
            $this->updateSubscribe();
            if (!empty($this->arResult["ERRORS"])) {
                if (empty($this->arResult['ERRORS']['LOCAL']) && empty($this->arResult['ERRORS']['NOT_LOCAL'])) {
                    // при ошибке возвращаем её на фронт в виде JSON
                    $this->returnError();
                }
            }
            // создаем заказ
            $this->createOrder();
        }
        $this->arResult['DADATA_PROPS'] = $this->arDadataProps;
        $this->includeComponentTemplate();
        $GLOBALS["BASKET_VIEW"] = $this->arResult["BASKET"];
    }

    //обновление авторизованного профиля на сайте информацией из заказа
    private function updateUserProfile()
    {
        global $USER;
        //заменяем техническую почту или заполняем пустую
        if (!empty($this->postProps['EMAIL']) && $this->postProps['EMAIL'] != $this->user['EMAIL']) {
            if (empty($this->user['EMAIL']) || preg_match('`.*@rshoes.ru`i', $this->user['EMAIL'])) {
                //ищем пользователя на сайте с такой почтой
                $rsUsers = CUser::GetList($by = 'ID', $order = 'ASC', ["EMAIL" => $this->postProps['EMAIL']]); // выбираем пользователей
                $rsUsersLogin = CUser::GetList($by = 'ID', $order = 'ASC', ["LOGIN" => $this->postProps['EMAIL']]); // выбираем пользователей
                if (!$rsUsers->Fetch() && !$rsUsersLogin->Fetch()) {
                    EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                    $USER->Update($this->user['ID'], ['EMAIL' => $this->postProps['EMAIL'], 'LOGIN' => $this->postProps['EMAIL']]);
                    $data['new']['EMAIL'] = $this->postProps['EMAIL'];
                }
            }
        }
        //заполняем пустой телефон
        if (empty($this->user['PERSONAL_PHONE'])) {
            //ищем пользователя на сайте с таким телефоном
            $rsUsers = CUser::GetList($by = 'ID', $order = 'ASC', ["PERSONAL_PHONE" => $this->postProps['PHONE']]); // выбираем пользователей
            if (!$rsUsers->Fetch()) {
                EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                $USER->Update($this->user['ID'], ['PERSONAL_PHONE' => $this->postProps['PHONE']]);
                $data['new']['PHONE'] = $this->postProps['PHONE'];
            }
        }
        if (!empty($data['new'])) {
            if ($USER->IsAuthorized()) {
                $taskManager = new TaskManager();
                $taskManager->setUser($this->user['ID']);
                $taskManager->addTask('addInfo', $data);
            }
        }
    }

    private function getUserInfoCookie()
    {
        $fio = explode('~', $_COOKIE['user_fio']);
        $email = explode('~', $_COOKIE['user_email']);
        $phone = explode('~', $_COOKIE['user_phone']);
        if ($fio[1] == 'undefined' || $email[1]== 'undefined' || $phone[1] == 'undefined') {
            $fio[1] = $fio[0];
            $email[1] = $email[0];
            $phone[1] = $phone[0];
        }
        $this->arResult['COOKIE_FIO'] = $fio;
        $this->arResult['COOKIE_EMAIL'] = $email;
        $this->arResult['COOKIE_PHONE'] = $phone;
    }

    private function updateSubscribe()
    {
        global $USER;
        $data = [];
        $data['subscribe']['email'] = isset($this->postProps['SUBSCRIBE_EMAIL']) && $this->postProps['SUBSCRIBE_EMAIL'] == 'on';
        $data['subscribe']['sms'] = isset($this->postProps['SUBSCRIBE_SMS']) && $this->postProps['SUBSCRIBE_SMS'] == 'on';
        if (!$data['subscribe']['email'] && !$data['subscribe']['sms']) {
            return;
        }
        $data['source'] = $this->type;
        $taskManager = new TaskManager();
        $taskManager->setUser($this->user['ID']);
        $taskManager->addTask('subscribe', $data);
        //обновление полей юзера
        $arFields = [];
        if ($data['subscribe']['sms']) {
            $arFields['UF_SUBSCRIBE_SMS'] = 1;
        }
        if ($data['subscribe']['email']) {
            $arFields['UF_SUBSCRIBE_EMAIL'] = 1;
        }
        if (!empty($arFields)) {
            EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
            $USER->Update($this->user['ID'], $arFields);
        }
    }

    // функции возврата данных
    private function returnError($error = false)
    {
        if ($this->checkType(array("order")) && (!empty($this->arResult['ERRORS']['LOCAL']) || !empty($this->arResult['ERRORS']['NOT_LOCAL']))) {
            $text = $_REQUEST['PROPS']['IS_LOCAL'] == 'Y' ? $this->arResult['ERRORS']['LOCAL'] :  $this->arResult['ERRORS']['NOT_LOCAL'];
        } elseif ($error) {
            $text = $error;
        } else {
            $text = $this->arResult['ERRORS'];
        }
        $arResult = array(
            "status" => "error",
            "text" => $text
        );
        $this->returnJSON($arResult);
    }

    private function returnOk($text = false, $coupon = false, $info = false, $gtmData = false)
    {
        $arResult = array(
            "status" => "ok",
        );
        if ($info) {
            $arResult["info"] = $info;
        }
        if ($text) {
            $arResult["text"] = $text;
        }
        if ($coupon) {
            $arResult["coupon"] = $coupon;
        }
        if ($gtmData) {
            $arResult["gtmData"] = $gtmData;
        }
        $this->returnJSON($arResult);
    }

    private function returnJSON($array)
    {
        if (!$this->json) {
            return;
        }
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-type: application/json');
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        //$APPLICATION->FinalActions();
        die();
    }

    // функции типа
    private function getType()
    {
        if (in_array($_REQUEST['action'], self::TYPES)) {
            $this->type = $_REQUEST['action'];
        } else {
            $this->type = "cart";
        }
    }

    public function checkType($types)
    {
        return in_array($this->type, $types);
    }

    // функции купонов
    private function checkCoupon()
    {
        $this->arResult["COUPON"] = $_REQUEST["coupon"];
        $needLocalCoupon = $_REQUEST["needLocalCoupon"] === 'Y' ? true : false;
        if (!$this->arResult["COUPON"]) {
            $this->arResult["ERRORS"][] = "Промокод не указан";
            $this->returnError();
        }
        // очищаем имеющиеся купоны
        $this->delCoupon(true, $needLocalCoupon);
        // добавляем новый
        $res = CouponsManager::add($this->arResult["COUPON"]);
        if ($res) {
            $arCoupons = CouponsManager::get(true, array(), true, true);
            $arCoupon = array_shift($arCoupons);
            $this->checkCouponStatus($arCoupon, $needLocalCoupon);
        } else {
            $this->arResult["ERRORS"][] = "Промокод не существует";
            $this->returnError();
        }
    }

    private function delCoupon($check = false, $needLocalCoupon = null)
    {
        $arCoupons = CouponsManager::get(true, array(), true, true);
        if (!empty($arCoupons)) {
            foreach ($arCoupons as $arCoupon) {
                if ($check && $this->arResult["COUPON"] == $arCoupon["COUPON"]) {
                    $this->checkCouponStatus($arCoupon, $needLocalCoupon);
                }
                CouponsManager::delete($arCoupon["COUPON"]);
            }
        }
    }

    private function checkCouponStatus($arCoupon, $needLocalCoupon = null)
    {
        if ($arCoupon["STATUS"] == CouponsManager::STATUS_NOT_FOUND) {
            $this->arResult["ERRORS"][] = "Промокод не существует";
        } elseif ($arCoupon["STATUS"] == CouponsManager::STATUS_FREEZE) {
            $this->arResult["ERRORS"][] = "Промокод использован максимальное количество раз";
        } elseif ($arCoupon["STATUS"] == CouponsManager::STATUS_ENTERED || $arCoupon["STATUS"] == CouponsManager::STATUS_NOT_APPLYED || $arCoupon["STATUS"] == CouponsManager::STATUS_APPLYED) {
            if ($this->loadBasket()) {
                $this->applyCoupon();
                if ($needLocalCoupon !== null) {
                    if (empty($this->offers)) {
                        $this->checkBasketAvailability();
                    }
                    $this->getItems();
                    $this->getBasketsPrices();
                    $this->returnOk($needLocalCoupon ? $this->arResult['PRICE']['LOCAL'] : $this->arResult['PRICE']['NOT_LOCAL'], $this->arResult["COUPON"]);
                } else {
                    $this->returnOk(floor($this->basket->getPrice()), $this->arResult["COUPON"]);
                }
            } else {
                $this->arResult["ERRORS"][] = "Не удалось применить промокод";
            }
        }
        if (count($this->arResult["ERRORS"]) == 0) {
            $this->arResult["ERRORS"][] = "Неизвестная ошибка, попробуйте ещё раз";
        }
        $this->returnError();
    }

    private function getCoupon()
    {
        $arCoupons = CouponsManager::get(true, array(), true, true);
        if (count($arCoupons) == 1) {
            $this->arResult["COUPON"] = reset($arCoupons)["COUPON"];
            $this->applyCoupon();
        }
    }

    private function applyCoupon()
    {
        // все, что нужно, чтобы применить скидки к корзине
        // создать пустой объект заказа
        $order = $this->createNewOrder();
        // присвоить ему корзину
        $order->setBasket($this->basket);
        // конец
    }

    // функции проверки данных
    private function checkData()
    {
        if (!$this->getPostProps()) {
            return false;
        }
        $this->checkOffersNum();
        $this->checkProps();
        return empty($this->arResult["ERRORS"]) ? true : false;
    }

    private function getPostProps()
    {
        if (!empty($this->postProps)) {
            return true;
        }
        $this->postProps = $_REQUEST["PROPS"];
        // удаляем свойства, которые мы заполняем сами, то есть они не должны приходить от юзера
        $this->delPostProps();
        if ($this->checkType(array("reserv"))) {
            $this->postProps["STORE"] = intval($_REQUEST["DELIVERY_STORE_ID"]);
        }
        if (empty($this->postProps)) {
            $this->arResult["ERRORS"][] = "Не получены свойства заказа";
            return false;
        }
        return true;
    }

    private function delPostProps()
    {
        // тут только те свойства, которые устанавливаются при каких-то условиях
        // свойства, которые устанавливаются при заказе всегда, итак будут перезаписаны
        $propsToDelete = array(
            "STORE",
            "FILIAL",
            "DISCOUNT_COUPON",
            "SALE_DISCOUNT",
        );
        foreach ($propsToDelete as $propName) {
            unset($this->postProps[$propName]);
        }
    }

    private function checkProps()
    {
        global $LOCATION;
        global $USER;
        // при оформлении заказа у нас ОБЯЗАТЕЛЬНО должен быть заполнен телефон в нужном формате
        if (!$this->postProps['PHONE']) {
            $this->arResult["ERRORS"]['PHONE'] = "Не заполенен телефон";
        }
        if ($this->postProps['PHONE'] &&
            (!preg_match('/^\+7 \([\d]{3}\) [\d]{3}-[\d]{2}-[\d]{2}$/', $this->postProps['PHONE']) ||
                preg_match('/^\+7 \(7[\d]{2}\) [\d]{3}-[\d]{2}-[\d]{2}$/', $this->postProps['PHONE']) ||
                preg_match('/^\+7 \(8[\d]{2}\) [\d]{3}-[\d]{2}-[\d]{2}$/', $this->postProps['PHONE']))) {
            $this->arResult["ERRORS"]['PHONE'] = "Неверный формат телефона";
        }
        // в корзине и резерве должно быть ФИО
        if ($this->checkType(array("order", "reserv")) && !$this->postProps['FIO']) {
            $this->arResult["ERRORS"]['FIO'] = "Не заполенено ФИО";
        }
        // только в резерве должен быть магазин
        if ($this->checkType(array("reserv"))) {
            if (!$this->postProps['STORE']) {
                $this->arResult["ERRORS"]['STORE'] = "Не указан магазин";
            } elseif ($LOCATION->checkStorages($this->postProps['STORE'], 2)) {
                $this->arResult["ERRORS"]['STORE'] = "Выбранный магазин не доступен для резерва в вашем регионе";
            }
        }
        // только в корзине должны быть другие поля
        if ($this->checkType(array("order"))) {
            $props = array(
                "EMAIL" => "Не заполнен Email",
            );
            if (in_array($this->arResult["DELIVERY"]["ID"], $this->arPvzIds)) {
                $props["PVZ_ID"] = "Не выбран ПВЗ";
            } else {
                if (!$this->postProps["HOUSE_USER"]) {
                    $props["STREET_USER"] = "Не заполнена улица";
                }
                $props["HOUSE_USER"] = "Не заполнен номер дома";
            }
            //проверяем телефон в профиле и заказе на дубликаты для Sailplay
            if ($USER->IsAuthorized()) {
                if (!$this->postProps['SKIP_CHECK_PHONE']) {
                    if (empty($this->user['PERSONAL_PHONE'])) {
                        $rsUsers = CUser::GetList($by = 'ID', $order = 'ASC', ["PERSONAL_PHONE" => $this->postProps['PHONE']]);
                        if ($rsUsers->Fetch()) {
                            $this->arResult["ERRORS"]['PHONE'] = 'Указанный номер телефона зарегистрирован на другого клиента';
                        }
                    }
                }
            }

            foreach ($props as $key => $value) {
                if (!$this->postProps[$key]) {
                    $this->arResult["ERRORS"][$key] = $value;
                }
            }
        }
    }

    private function checkOffersNum()
    {
        // проверяем наличие юзера
        $this->getUser(true);
        // если юзера нет (то есть нет его ID), то проверка пройдена
        if (!$this->user["ID"]) {
            return;
        }
        $ordesNum = Option::get("respect", "order_max_num", 5);
        $res = Order::getList(array(
            "select" => array(
                "USER_ID",
            ),
            "filter" => array(
                "USER_ID" => $this->user["ID"],
                ">=DATE_INSERT" => $this->getTime("start"),
                '<=DATE_INSERT' => $this->getTime("end"),
            ),
        ))->FetchAll();
        if (count($res) >= $ordesNum) {
            $this->arResult["ERRORS"][] = str_replace(
                "%NUM%",
                $ordesNum,
                Option::get("respect", "order_max_num_text", "Максимальное количество заказов в день - %NUM% шт")
            );
        }
    }

    // функции юзеров
    private function getUser($byPost = false)
    {
        global $USER;
        if (!empty($this->user)) {
            return;
        }
        if ($USER->IsAuthorized()) {
            $this->getUserFields(array(
                "ID" => $USER->GetID(),
            ));
        } elseif ($byPost) {
            $this->getUserByPost();
        }
    }

    private function getUserByPost()
    {
        global $DB;
        $arFilter = array();
        if ($this->postProps['PHONE']) {
            $arFilter["PERSONAL_PHONE"] = $this->postProps['PHONE'];
        }
        if ($this->postProps['EMAIL']) {
            $arFilter["EMAIL"] = $this->postProps['EMAIL'];
        }
        if (!empty($arFilter)) {
            $arFilter["LOGIC"] = "OR";
            if ($this->getUserFields($arFilter)) {
                return;
            }
        }
    }

    private function getUserFields($arFilter)
    {
        $arSelect = [
            "ID",
            "EMAIL",
            "NAME",
            "LAST_NAME",
            "SECOND_NAME",
            "PERSONAL_PHONE",
            "PERSONAL_STREET",
            "UF_HOUSE",
            "UF_ST",
            "UF_HOUSING",
            "UF_ENTRANCE",
            "UF_FLOOR",
            "UF_APARTMENT",
            "UF_INTERCOM",
            "UF_TIME",
            "UF_POSTALCODE",
            "UF_FIASCODE"
        ];

        $res = UserTable::GetList([
            "select" => $arSelect,
            "filter" => $arFilter,
        ]);
        if ($res->getSelectedRowsCount() > 1) {
            $arUser = [];
            while ($arItem = $res->Fetch()) {
                if ($arItem["PERSONAL_PHONE"] == $arFilter["PERSONAL_PHONE"]) {
                    $arUser = $arItem;
                    break;
                }
                if ($arItem["EMAIL"] == $arFilter["EMAIL"]) {
                    $arUser = $arItem;
                }
            }
            $this->user = $arUser;
        } elseif ($res->getSelectedRowsCount() == 1) {
            $this->user = $res->Fetch();
        } else {
            return false;
        }
        return true;
    }

    private function checkUser()
    {
        if ($this->user["ID"]) {
            $this->checkUserSailplay();
            return;
        }
        $this->setNewUserFields();
        $this->checkUserSailplay(true);
    }

    private function setNewUserFields()
    {
        $name = explode(' ', $this->postProps['FIO']);
        $pass = randString(8);
        $this->user = array(
            "GROUP_ID" => [2, 3, 5],
            "LOGIN" => $this->postProps['EMAIL'] ?? $pass . "@rshoes.ru",
            "EMAIL" => $this->postProps['EMAIL'] ?? $pass . "@rshoes.ru",
            "PASSWORD" => $pass,
            "CONFIRM_PASSWORD" => $pass,
            "NAME" => $name[0], // имя
            "LAST_NAME" => $name[1], // фамилия
            "SECOND_NAME" => $name[2], // отчество
            "PERSONAL_PHONE" => $this->postProps['PHONE'], // телефон
            "PERSONAL_STREET" => $this->postProps['STREET'], // улица
            "UF_HOUSE" => $this->postProps['HOUSE'], // дом
            "UF_ST" => $this->postProps['STRUCTURE'], // строение
            //TODO в отдельном тикете: все поля из профиля в заказе
            "UF_HOUSING" => "", // корпус
            "UF_ENTRANCE" => "", // подъезд
            "UF_FLOOR" => "", // этаж
            "UF_APARTMENT" => $this->postProps['FLAT'], // квартирка или офис
            "UF_INTERCOM" => "", // домофон
            "UF_TIME" => $this->postProps['DELIVERY_TIME'], // желаемое время доставки
            "UF_POSTALCODE" => $this->postProps['POSTALCODE'],
            "UF_FIASCODE" => $this->postProps['FIASCODE'],
        );
        // создаем пользователя
        $this->createUser();
    }

    private function createUser()
    {
        global $USER;
        if ($this->user["ID"]) {
            return;
        }
        $userId = $USER->Add($this->user);
        EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
        if ($userId) {
            if (!$this->postProps['EMAIL']) {
                $res = $USER->Update($userId, array(
                    "LOGIN" => $userId . "@rshoes.ru",
                    "EMAIL" => $userId . "@rshoes.ru",
                ));
                EventHelper::killEvents(['OnBeforeUserUpdate', 'OnAfterUserUpdate'], 'main');
                if ($res) {
                    $this->user["LOGIN"] = $userId . "@rshoes.ru";
                    $this->user["EMAIL"] = $userId . "@rshoes.ru";
                }
            }
            $this->user["ID"] = $userId;
            $USER->Authorize($userId);
        } else {
            $this->arResult["ERRORS"][] = "Не удалось создать пользователя";
            $this->returnError();
        }
    }

    private function checkUserSailplay($newBySite = false)
    {
        $data = [];
        $data['type_order'] = $this->type;
        $data['order_phone'] = $this->postProps['PHONE'];
        $data['order_email'] = $this->postProps['EMAIL'];
        if ($newBySite) {
            $data['send_register_tag'] = true;
        }
        $_SESSION['SUC_REG'] = 'Y';

        $taskManager = new TaskManager();
        $taskManager->setUser($this->user['ID']);
        try {
            $taskManager->addTask('register', $data);
        } catch (TaskManagerException $e) {
            qsoft_logger($e->getMessage(), 'eventsExceptions.log', true);
        }
    }

    // функции работы с корзиной
    private function setBasket()
    {
        if ($this->checkType(array("cart", "offers", "offers2", "order"))) {
            // загружаем корзину
            if ($this->loadBasket()) {
                return true;
            }
            $this->arResult["ERRORS"] = "Не удалось загрузить корзину";
            return false;
        } else {
            // получаем ТП из POST и создаем корзину
            return $this->getPostItems();
        }
    }

    private function loadBasket()
    {
        $this->basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
        return $this->basket ? true : false;
    }

    private function getPostItems()
    {
        $offerIds = array();
        if (is_array($_REQUEST["PRODUCTS"])) {
            foreach ($_REQUEST["PRODUCTS"] as $offerId) {
                $offerIds[] = intval($offerId);
            }
        } elseif (intval($_REQUEST["PRODUCTS"])) {
            $offerIds[] = intval($_REQUEST["PRODUCTS"]);
        }
        if (empty($offerIds)) {
            $this->arResult["ERRORS"][] = "Товары не получены";
            return false;
        }
        // создаем корзину
        return $this->createBasket($offerIds);
    }

    private function createBasket($offerIds)
    {
        // создаем корзину
        $this->basket = Basket::create(SITE_ID);
        // устанавливаем юзера
        $this->basket->setFUserId(Fuser::getId());
        // заполняем корзину
        foreach ($offerIds as $offerId) {
            if (!$this->createBasketItem($offerId)) {
                $this->arResult["ERRORS"][] = "Не удалось добавить товар к заказу";
                return false;
            }
        }
        return true;
    }

    private function addToBasket($offerId, $isLocal, $quantity)
    {
        // загружаем корзину
        $this->basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
        // проверяем корзину на ограничения
        $this->checkBasketPositionLimit(1);
        if (count($this->arResult["ERRORS"]) > 0) {
            $this->returnError();
        }
        // проверяем есть ли ТП с таким ID
        // $this->basket->getExistsItem не работает, тк он сверяет все свойства, а у нас их ещё нет
        $arBasketItems = $this->basket->getBasketItems();
        $isExist = false;
        foreach ($arBasketItems as $arItem) {
            if ($arItem->getProductId() == $offerId) {
                $isExist = $arItem;
                break;
            }
        }
        if ($isExist) {
            // На будущее, когда можно будет заказывать несколько
            /*
            $item->setField('QUANTITY', $quantity);
            $res = $this->basket->save();
            if ($res->isSuccess()) {
                $this->returnOk("Товар в корзине успешно обновлён");
            }
            //*/
            $this->returnOk(0);
        } else {
            if ($basketItem = $this->createBasketItem($offerId, $isLocal, $quantity)) {
                $res = $this->basket->save();
                if ($res->isSuccess()) {
                    $prop = $basketItem->getPropertyCollection();
                    $props = $prop->getPropertyValues();
                    $this->returnOk(1, false, false, ['offerId' => $offerId,
                            'prodId' => $props['PRODUCT_ID']['VALUE'],
                            'price1' => $basketItem->getPrice(),      // Цена за единицу
                            'price2' => $basketItem->getFinalPrice(),
                            'size' => $props['SIZE']['VALUE'],
                        ]);
                }
            }
        }
        $this->arResult["ERRORS"][] = "Не удалось добавить или обновить товар в коризне";
        $this->returnError();
    }

    private function deleteFromBasket($offerId, $needLocalBasketPrice)
    {
        // загружаем корзину
        $this->basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
        // проверяем есть ли ТП с таким ID
        // $this->basket->getExistsItem не работает, тк он сверяет все свойства, а у нас их ещё нет
        $arBasketItems = $this->basket->getBasketItems();
        foreach ($arBasketItems as $arItem) {
            if ($arItem->getProductId() == $offerId) {
                $arItem->delete();
                $res = $this->basket->save();
                if (empty($this->offers)) {
                    $this->checkBasketAvailability();
                }
                if ($res->isSuccess()) {
                    $price = $this->getTypePrice($this->offers, $needLocalBasketPrice);
                    $this->checkBasketSumLimit();
                    if ($needLocalBasketPrice === 'Y' && !empty($this->arResult['ERRORS']['LOCAL'])) {
                        $this->returnError($this->arResult['ERRORS']['LOCAL'][0]);
                    } elseif ($needLocalBasketPrice === 'N' && !empty($this->arResult['ERRORS']['NOT_LOCAL'])) {
                        $this->returnError($this->arResult['ERRORS']['NOT_LOCAL'][0]);
                    } else {
                        $this->returnOk($price);
                    }
                }
            }
        }
        $this->arResult["ERRORS"][] = "Не удалось удалить товар из корзины";
        $this->returnError();
    }

    private function createBasketItem($offerId, $isLocal = 'Y', $quantity = 1)
    {
        $GLOBALS['localBasketFlag'] = $isLocal;
        $basketItem = $this->basket->createItem('catalog', $offerId);
        $arProps = $this->getOfferProps($offerId);
        $arProps["PROPS"]["LOCAL"] = array(
            "NAME" => "Местный",
            "CODE" => "IS_LOCAL",
            "VALUE" => $isLocal,
            "SORT" => count($arProps) + 1,
        );
        $basketItem->setFields(array(
            'QUANTITY' => $quantity,
            'CURRENCY' => CurrencyManager::getBaseCurrency(),
            'LID' => SITE_ID,
            'PRODUCT_PRICE_ID' => $arProps["PRODUCT_ID"],
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ));
        if (!empty($arProps['PROPS'])) {
            $prop = $basketItem->getPropertyCollection();
            $prop->setProperty($arProps['PROPS']);
            return $basketItem;
        }
        return false;
    }

    private function getOfferProps($offerId)
    {
        // получаем свойства из ТП
        $arOffer = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $offerId,
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ),
            false,
            array(
                "nTopCount" => 1,
            ),
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_SIZE",
            )
        )->Fetch();
        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return false;
        }
        // получаем свойства из товара
        $arProd = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            ),
            false,
            array(
                "nTopCount" => 1,
            ),
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_ARTICLE",
                "PROPERTY_COLOR",
                "PROPERTY_KOD_1S",
            )
        )->Fetch();
        $arProps = [];
        if ($arProd["PROPERTY_ARTICLE_VALUE"]) {
            $arProps["PROPS"]["ARTICLE"] = array(
                "CODE" => "ARTICLE",
                "NAME" => "Артикул",
                "VALUE" => $arProd["PROPERTY_ARTICLE_VALUE"],
                "SORT" => 1,
            );
        }
        if ($arProd["PROPERTY_COLOR_VALUE"]) {
            CModule::IncludeModule('highloadblock');
            $hlblock = HLBT::getList(array('filter' => array('=NAME' => 'Color')))->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $color = $entity_data_class::getList(array(
                'filter' => array('UF_XML_ID' => $arProd["PROPERTY_COLOR_VALUE"]),
                'select' => array('UF_NAME'),
            ))->fetch()['UF_NAME'];
            $arProps["PROPS"]["COLOR"] = array(
                "CODE" => "COLOR",
                "NAME" => "Цвет",
                "VALUE" => $color,
                "SORT" => 2,
            );
        }
        if ($arProd["PROPERTY_KOD_1S_VALUE"]) {
            $arProps["PROPS"]["KOD_1S"] = array(
                "CODE" => "KOD_1S",
                "NAME" => "Код 1С",
                "VALUE" => $arProd["PROPERTY_KOD_1S_VALUE"],
                "SORT" => 3,
            );
        }
        // добавляем свойства из ТП, если оно есть
        if ($arOffer["PROPERTY_SIZE_VALUE"]) {
            $arProps["PROPS"]["SIZE"] = array(
                "CODE" => "SIZE",
                "NAME" => "Размер",
                "VALUE" => $arOffer["PROPERTY_SIZE_VALUE"],
                "SORT" => count($arProps) + 1,
            );
        }
        if ($arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            $arProps["PROPS"]["PROD_ID"] = array(
                "CODE" => "PRODUCT_ID",
                "NAME" => "ID продукта",
                "VALUE" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "SORT" => count($arProps) + 1,
            );
        }
        $arProps["PRODUCT_ID"] = $arOffer["PROPERTY_CML2_LINK_VALUE"];

        return $arProps;
    }

    // функции товаров корзины
    private function checkBasket()
    {
        // проверяем возможность покупки
        if (empty($this->offers)) {
            $this->checkBasketAvailability();
        }
        // заканчиваем проверки для резерва и 1 клика
        if ($this->checkType(array("1click", "reserv"))) {
            return empty($this->arResult["ERRORS"]) ? true : false;
        }
        $this->checkBasketLimits();
        if ($this->checkType(array("order"))) {
            return $_REQUEST['PROPS']['IS_LOCAL'] == 'Y' ? empty($this->arResult['ERRORS']['LOCAL']) ? true : false : empty($this->arResult['ERRORS']['NOT_LOCAL']) ? true : false;
        } else {
            return empty($this->arResult["ERRORS"]) ? true : false;
        }
    }

    private function checkBasketAvailability()
    {
        if ($this->checkType(array("cart", "offers", "offers2", "coupon", "basketDel", "order"))) {
            $full = true;
        } else {
            $full = false;
        }
        // получаем ID предложений
        $this->offers = $this->getOfferIds($full);
        // если нет ID предложений
        if (empty($this->offers)) {
            if (!$full) {
                // при оформлении любым типом корзина не может быть пустой
                $this->arResult["ERRORS"][] = "Нет товаров в корзине";
            }
            return;
        }
        // получаем данные по ТП из БД
        $this->offers = $this->getOffers($this->offers, $full);
        // формируем ID товаров
        $arProductIds = $this->getProductIds($this->offers);
        // если нет ID продуктов
        if (empty($arProductIds)) {
            if (!$full) {
                // при оформлении любым типом корзина не может быть пустой
                $this->arResult["ERRORS"][] = "Нет товаров в корзине";
            }
            return;
        }

        $arRests = Functions::getRests(array_keys($this->offers));

        // проверяем, что у ТП есть остатки и цена
        foreach ($this->offers as $offerId => $arItem) {
            if ($full) {
                $this->offers[$offerId]["PRICE"] = $this->offers[$offerId]["PRICE"];
                $this->offers[$offerId]["OLD_PRICE"] = $this->offers[$offerId]["OLD_PRICE"];
            }
            if (!$arRests[$offerId]) {
                $this->arResult["PROBLEM_OFFERS"][$offerId] = $offerId;
            }
        }
        // если есть проблемные ТП
        if (!empty($this->arResult["PROBLEM_OFFERS"])) {
            $this->arResult["ERRORS"][] = "Некоторые товары не доступны для доставки в вашем регионе";
        }
    }

    private function checkBasketLimits()
    {
        $this->checkBasketPositionLimit();
        $this->checkBasketSumLimit();
    }

    private function checkBasketPositionLimit($add = 0)
    {
        $basketNum = Option::get("respect", "basket_max_art_num", 6);

        $arCountY = array_count_values(array_column($this->offers, 'IS_LOCAL'))['Y'];
        if (($arCountY + $add > $basketNum) && $arCountY + $add > 0) {
            $this->arResult["ERRORS"]['LOCAL'][] = str_replace("%NUM%", $basketNum, Option::get("respect", "basket_max_art_num_text", "Максимальное количество позиций в корзине - %NUM% шт"));
        }
        $arCountN = array_count_values(array_column($this->offers, 'IS_LOCAL'))['N'];
        if (($arCountN + $add > $basketNum) && $arCountN + $add > 0) {
            $this->arResult["ERRORS"]['NOT_LOCAL'][] = str_replace("%NUM%", $basketNum, Option::get("respect", "basket_max_art_num_text", "Максимальное количество позиций в корзине - %NUM% шт"));
        }
    }

    private function checkBasketSumLimit()
    {
        $basketSum = Option::get("respect", "basket_min_num", 2500);
        if (in_array('Y', array_column($this->offers, 'IS_LOCAL')) && $this->getTypePrice($this->offers, 'Y') < $basketSum) {
            $this->arResult["ERRORS"]['LOCAL'][] = str_replace("%NUM%", $basketSum, Option::get("respect", "basket_min_num_text", "Минимальная сумма заказа - %NUM% р"));
        }
        if (in_array('N', array_column($this->offers, 'IS_LOCAL')) && $this->getTypePrice($this->offers, 'N') < $basketSum) {
            $this->arResult["ERRORS"]['NOT_LOCAL'][] = str_replace("%NUM%", $basketSum, Option::get("respect", "basket_min_num_text", "Минимальная сумма заказа - %NUM% р"));
        }
    }

    // функции товаров
    private function getOfferIds($full = true)
    {
        $basketItems = $this->basket->getBasketItems();
        $arOffers = array();
        foreach ($basketItems as $arItem) {
            $offerId = $arItem->getProductId();
            $basketPropertyCollection = $arItem->getPropertyCollection();
            foreach ($basketPropertyCollection as $basketPropertyItem) {
                if ($basketPropertyItem->getField('CODE') == "IS_LOCAL") {
                    $isLocal = $basketPropertyItem->getField('VALUE');
                }
            }
            if ($full) {
                $arOffers[$offerId] = array(
                    "QUANTITY" => $arItem->getQuantity(),
                    "BASKET_PRICE" => $arItem->getPrice(),
                    "IS_LOCAL" => $isLocal,
                );
            } else {
                $arOffers[$offerId] = $offerId;
            }
        }
        return $arOffers;
    }

    private function getOffers($arOffers, $full = true)
    {
        $arSelect = array(
            "ID",
            "IBLOCK_ID",
            "PROPERTY_CML2_LINK",
        );
        if ($full) {
            $arSelect[] = "PROPERTY_SIZE";
            $arSelect[] = "PROPERTY_COLOR";
        }
        $res = CIBlockElement::GetList(
            [],
            [
                "ID" => array_keys($arOffers),
                "IBLOCK_ID" => IBLOCK_OFFERS,
            ],
            false,
            false,
            $arSelect
        );
        $arOffersNew = [];
        while ($arItem = $res->Fetch()) {
            if ($full) {
                $arOffersNew[$arItem["ID"]] = array(
                    "PRODUCT_ID" => $arItem["PROPERTY_CML2_LINK_VALUE"],
                    "SIZE" => $arItem["PROPERTY_SIZE_VALUE"],
                    "COLOR" => $arItem["PROPERTY_COLOR_VALUE"],
                    "QUANTITY" => $arOffers[$arItem["ID"]]["QUANTITY"],
                    "BASKET_PRICE" => $arOffers[$arItem["ID"]]["BASKET_PRICE"],
                    "IS_LOCAL" => $arOffers[$arItem["ID"]]["IS_LOCAL"],
                );
            } else {
                $arOffersNew[$arItem["ID"]] = array(
                    "PRODUCT_ID" => $arItem["PROPERTY_CML2_LINK_VALUE"],
                );
            }
        }
        return $arOffersNew;
    }

    private function getItems()
    {
        $arProductIds = $this->getProductIds($this->offers);
        // если нет ID продуктов, то ничего не делаем
        if (empty($arProductIds)) {
            return;
        }
        $arProducts = $this->getProducts($arProductIds);
        $this->setItems($this->offers, $arProducts);
    }

    private function getProductIds($arOffers)
    {
        $arProductIds = array();
        foreach ($arOffers as $arItem) {
            $arProductIds[$arItem["PRODUCT_ID"]] = $arItem["PRODUCT_ID"];
        }
        return $arProductIds;
    }

    private function getProducts($arProductIds)
    {
        $res = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arProductIds,
                "IBLOCK_ID" => IBLOCK_CATALOG,
            ),
            false,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "CODE",
                "NAME",
                "DETAIL_PICTURE",
                "PREVIEW_PICTURE",
                "PROPERTY_ARTICLE",
                'PROPERTY_BRAND',
                'PROPERTY_RHODEPRODUCT',
                'PROPERTY_VID',
                'PROPERTY_TYPEPRODUCT',
                'PROPERTY_SUBTYPEPRODUCT',
                'PROPERTY_COLLECTION',
                'PROPERTY_COLORSFILTER',
                'PROPERTY_SEASON',
                'PROPERTY_LININGMATERIAL',
                'PROPERTY_UPPERMATERIAL',
            )
        );
        $arProducts = array();

        while ($arItem = $res->Fetch()) {
            if (is_numeric($arItem["DETAIL_PICTURE"])) {
                $arItem["DETAIL_PICTURE"] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
            } else {
                $arItem["DETAIL_PICTURE"] = "";
            }
            if (is_numeric($arItem["PREVIEW_PICTURE"])) {
                $arItem["PREVIEW_PICTURE"] = CFile::GetPath($arItem["PREVIEW_PICTURE"]);
            } else {
                $arItem["PREVIEW_PICTURE"] = "";
            }
            $arProducts[$arItem["ID"]] = array(
                "NAME" => $arItem["NAME"],
                "CODE" => $arItem["CODE"],
                "DETAIL_PICTURE" => $arItem["DETAIL_PICTURE"],
                "PREVIEW_PICTURE" => $arItem["PREVIEW_PICTURE"],
                "ARTICLE" => $arItem["PROPERTY_ARTICLE_VALUE"],
                'BRAND' => $arItem["PROPERTY_BRAND_VALUE"],
                'UPPERMATERIAL' => $arItem["PROPERTY_UPPERMATERIAL_VALUE"],
                'LININGMATERIAL' => $arItem["PROPERTY_LININGMATERIAL_VALUE"],
                'SEASON' => $arItem["PROPERTY_SEASON_VALUE"],
                'COLORSFILTER' => $arItem["PROPERTY_COLORSFILTER_VALUE"][0],
                'COLLECTION' => $arItem["PROPERTY_COLLECTION_VALUE"],
                'SUBTYPEPRODUCT' => $arItem["PROPERTY_SUBTYPEPRODUCT_VALUE"],
                'TYPEPRODUCT' => $arItem["PROPERTY_TYPEPRODUCT_VALUE"],
                'VID' => $arItem["PROPERTY_VID_VALUE"],
                'RHODEPRODUCT' => $arItem["PROPERTY_RHODEPRODUCT_VALUE"],
            );

            $props['BRAND'][$arItem["PROPERTY_BRAND_VALUE"]] = $arItem["PROPERTY_BRAND_VALUE"];
            $props['UPPERMATERIAL'][$arItem["PROPERTY_UPPERMATERIAL_VALUE"]] = $arItem["PROPERTY_UPPERMATERIAL_VALUE"];
            $props['LININGMATERIAL'][$arItem["PROPERTY_LININGMATERIAL_VALUE"]] = $arItem["PROPERTY_LININGMATERIAL_VALUE"];
            $props['SEASON'][$arItem["PROPERTY_SEASON_VALUE"]] = $arItem["PROPERTY_SEASON_VALUE"];
            $props['COLORSFILTER'][$arItem["PROPERTY_COLORSFILTER_VALUE"][0]] = $arItem["PROPERTY_COLORSFILTER_VALUE"][0];
            $props['COLLECTIONHB'][$arItem["PROPERTY_COLLECTION_VALUE"]] = $arItem["PROPERTY_COLLECTION_VALUE"];
            $props['SUBTYPEPRODUCT'][$arItem["PROPERTY_SUBTYPEPRODUCT_VALUE"]] = $arItem["PROPERTY_SUBTYPEPRODUCT_VALUE"];
            $props['TYPEPRODUCT'][$arItem["PROPERTY_TYPEPRODUCT_VALUE"]] = $arItem["PROPERTY_TYPEPRODUCT_VALUE"];
            $props['VID'][$arItem["PROPERTY_VID_VALUE"]] = $arItem["PROPERTY_VID_VALUE"];
            $props['RHODEPRODUCT'][$arItem["PROPERTY_RHODEPRODUCT_VALUE"]] = $arItem["PROPERTY_RHODEPRODUCT_VALUE"];
        }

        $arProps = $this->getPropsForGTM($props);

        foreach ($arProducts as &$arItem) {
             $arItem['BRAND'] = $arProps['BRAND'][$arItem['BRAND']];
             $arItem['UPPERMATERIAL'] = $arProps['UPPERMATERIAL'][$arItem['UPPERMATERIAL']];
             $arItem['LININGMATERIAL'] = $arProps['LININGMATERIAL'][$arItem['LININGMATERIAL']];
             $arItem['SEASON'] = $arProps['SEASON'][$arItem['SEASON']];
             $arItem['COLORSFILTER'] = $arProps['COLORSFILTER'][$arItem['COLORSFILTER']];
             $arItem['COLLECTION'] = $arProps['COLLECTIONHB'][$arItem['COLLECTION']];
             $arItem['SUBTYPEPRODUCT'] = $arProps['SUBTYPEPRODUCT'][$arItem['SUBTYPEPRODUCT']];
             $arItem['TYPEPRODUCT'] = $arProps['TYPEPRODUCT'][$arItem['TYPEPRODUCT']];
             $arItem['VID'] = $arProps['VID'][$arItem['VID']];
             $arItem['RHODEPRODUCT'] = $arProps['RHODEPRODUCT'][$arItem['RHODEPRODUCT']];
        }

        return $arProducts;
    }

    private function setItems($arOffers, $arProducts)
    {
        $this->arResult["ITEMS"] = array();
        $this->arResult["DISCOUNT"]['NOT_LOCAL'] = 0;
        $this->arResult["DISCOUNT"]['LOCAL'] = 0;
        foreach ($arOffers as $id => $value) {
            $src = array();
            if ($arProducts[$value["PRODUCT_ID"]]["DETAIL_PICTURE"]) {
                $src[] = $arProducts[$value["PRODUCT_ID"]]["DETAIL_PICTURE"];
            }
            if ($arProducts[$value["PRODUCT_ID"]]["PREVIEW_PICTURE"]) {
                $src[] = $arProducts[$value["PRODUCT_ID"]]["PREVIEW_PICTURE"];
            }
            $value["BASKET_PRICE"] = floor($value["BASKET_PRICE"]);
            $arItem = array(
                "PRODUCT_ID" => $value["PRODUCT_ID"],
                "CODE" => $arProducts[$value["PRODUCT_ID"]]["CODE"],
                "SRC" => $src,
                "ARTICLE" => $arProducts[$value["PRODUCT_ID"]]["ARTICLE"],
                "NAME" => $arProducts[$value["PRODUCT_ID"]]["NAME"],
                "SIZE" => $value["SIZE"],
                "QUANTITY" => $value["QUANTITY"],
                "PRICE" => $value["BASKET_PRICE"],
                "OLD_CATALOG_PRICE" => $value["BRANCH_OLD_PRICE"],
                "BRANCH" => $value["BRANCH"],
                "IS_LOCAL" => $value["IS_LOCAL"],
                "BRAND" => $arProducts[$value["PRODUCT_ID"]]["BRAND"],
                "UPPERMATERIAL" => $arProducts[$value["PRODUCT_ID"]]["UPPERMATERIAL"],
                "LININGMATERIAL" => $arProducts[$value["PRODUCT_ID"]]["LININGMATERIAL"],
                "SEASON" => $arProducts[$value["PRODUCT_ID"]]["SEASON"],
                "COLORSFILTER" => $arProducts[$value["PRODUCT_ID"]]["COLORSFILTER"],
                "COLLECTION" => $arProducts[$value["PRODUCT_ID"]]["COLLECTION"],
                "SUBTYPEPRODUCT" => $arProducts[$value["PRODUCT_ID"]]["SUBTYPEPRODUCT"],
                "TYPEPRODUCT" => $arProducts[$value["PRODUCT_ID"]]["TYPEPRODUCT"],
                "VID" => $arProducts[$value["PRODUCT_ID"]]["VID"],
                "RHODEPRODUCT" => $arProducts[$value["PRODUCT_ID"]]["RHODEPRODUCT"],
            );
            if ($this->arResult["COUPON"]) {
                $arItem["OLD_PRICE"] = $value["BRANCH_PRICE"];
                if ($arItem['IS_LOCAL'] == 'Y') {
                    $this->arResult["DISCOUNT"]['LOCAL'] += ($value["BRANCH_PRICE"] - $value["BASKET_PRICE"]);
                } else {
                    $this->arResult["DISCOUNT"]['NOT_LOCAL'] += ($value["BRANCH_PRICE"] - $value["BASKET_PRICE"]);
                }
            }
            if ($arItem['IS_LOCAL'] == 'Y') {
                $this->arResult["ITEMS"]['LOCAL'][$id] = $arItem;
            } else {
                $this->arResult["ITEMS"]['NOT_LOCAL'][$id] = $arItem;
            }
            if (!in_array($id, $this->arResult['OFFERS'])) {
                $this->arResult['OFFERS'][] = $id;
            }
        }
    }

    // функции доставки
    private function getDeliveries()
    {
        if ($this->arResult["DELIVERY"]) {
            return;
        }
        $this->arResult["DELIVERY"]["ARRAY"] = $this->getDeliveriesArray();
        $this->arResult["DELIVERY"]["ID"] = reset($this->arResult["DELIVERY"]["ARRAY"])["ID"];
    }

    private function setDeliveryId()
    {
        if ($this->arResult["DELIVERY"]["ID"]) {
            return;
        }
        $arDeliveries = $this->getDeliveriesArray();
        switch ($this->type) {
            case 'order':
                $this->arResult["DELIVERY"]["ID"] = intval($_REQUEST["DELIVERY"]);
                break;
            case 'reserv':
                $this->arResult["DELIVERY"]["ID"] = self::DELIVERY_PICKUP_ID;
                break;
            default:
                $this->arResult["DELIVERY"]["ID"] = reset($arDeliveries)["ID"];
        }
        if (!$this->arResult["DELIVERY"]["ID"]) {
            $this->arResult["ERRORS"][] = "Не выбрана служба доставки";
            return;
        }
        $this->arResult["DELIVERY"]["CURRENT"] = $arDeliveries[$this->arResult["DELIVERY"]["ID"]];
    }

    private function getDeliveriesArray()
    {
        global $LOCATION;
        // создаем пустой заказ
        $order = $this->createNewOrder();
        // ограничение по местоположению
        $this->orderProps = $order->getPropertyCollection();
        $prop = $this->getPropByCode("LOCATION");
        $prop->setValue($LOCATION->code);
        // создаем объект доставки
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipment->setField("CURRENCY", $this->currency);
        // получаем доставки с ограничениями
        $res = DelManager::getRestrictedObjectsList($shipment);
        $arDelivery = array();
        Loader::includeModule('qsoft.pvzmap');
        foreach ($res as $deliveryId => $arItem) {
            $arDelivery[$deliveryId] = array(
                "ID" => $deliveryId,
                "NAME" => $arItem->getName(),
                "PRICE" => $arItem->calculate()->getPrice(),
                "SORT" => $arItem->getSort(),
            );

            if (strpos($arItem->getname(), "ПВЗ") !== false) {
                $this->arPvzIds[] = $deliveryId;
            }
        }

        // удаляем самовывоз для всего, кроме резервирования
        if ($this->checkType(array('cart', 'order', '1click'))) {
            unset($arDelivery[self::DELIVERY_PICKUP_ID]);
        }
        // удаляем ПВЗ для всего, кроме карты
        if ($this->checkType(array('reserv', '1click'))) {
            foreach ($this->arPvzIds as $delId) {
                unset($arDelivery[$delId]);
            }
        }

        $arDelId = array_column($arDelivery, "ID");

        if (array_intersect($this->arPvzIds, $arDelId)) {
            $flag = false; //Флаг присутствия точек ПВЗ для выбранного города

            if (Loader::includeModule('qsoft.pvzmap')) {
                CBitrixComponent::includeComponentClass("qsoft:pvzmap"); // Подлючаем класс чтобы получить список ПВЗ в городе
                $PVZMap = new PVZMap();

                $arPVZ = $PVZMap->getPVZCollectionByCityAsArray();

                foreach ($arPVZ['PVZ'] as $pvz) {
                    if (count($pvz) > 0) {
                        $flag = true;
                        break;
                    }
                }
                $arDelivery = $this->filterEmptyPVZDeliveries($arDelivery, $arPVZ['PVZ']);
            }

            if (!$flag) {
                foreach ($this->arPvzIds as $delId) {
                    unset($arDelivery[$delId]);
                }
            } else {
                $this->arResult["DELIVERY"]["PVZ"] = true;
            }
        }
        $this->arResult["DELIVERY"]["PVZIDS"] = $this->arPvzIds;
        uasort($arDelivery, function ($a, $b) {
            return $a['PRICE'] <=> $b['PRICE'];
        });

        return $arDelivery;
    }

    // фильтрует ПВЗ службы доставки по которым отсутствуют ПВЗ в текущем городе
    private function filterEmptyPVZDeliveries($arDeliveries, $arPVZ)
    {
        $this->arPVZNames = PVZFactory::loadPVZ();
        reset($arDeliveries);//Сбрасываем указатель чтобы цикл каждый раз отрабатывал
        foreach ($arDeliveries as $id => $delivery) {
            if (in_array($id, $this->arPvzIds)) {
                foreach ($this->arPVZNames as $name) {
                    if (strpos($delivery['NAME'], $name['NAME']) !== false) {
                        if (empty($arPVZ[$name['CLASS_NAME']])) {
                            unset($arDeliveries[$id]);
                            break;
                        }
                    }
                }
            }
        }
        return $arDeliveries;
    }

    // функции оплаты
    private function getPayments()
    {
        if (!empty($this->arResult["PAYMENT"])) {
            return;
        }
        $this->arResult["PAYMENT"]["ARRAY"] = $this->getPaymentsArray();
        $this->arResult["PAYMENT"]["ID"] = reset($this->arResult["PAYMENT"]["ARRAY"])["ID"];
    }

    private function setPaymentId()
    {
        if ($this->arResult["PAYMENT"]["ID"]) {
            return;
        }
        $arPayments = $this->getPaymentsArray();
        switch ($this->type) {
            case 'order':
                $this->arResult["PAYMENT"]["ID"] = intval($_REQUEST["PAYMENT"]);
                break;
            default:
                $this->arResult["PAYMENT"]["ID"] = reset($arPayments)["ID"];
        }

        if (!$this->arResult["PAYMENT"]["ID"]) {
            $this->arResult["ERRORS"][] = "Не выбран метод оплаты";
            return;
        }
        $this->arResult["PAYMENT"]["CURRENT"] = $arPayments[$this->arResult["PAYMENT"]["ID"]];
    }

    private function getPaymentsArray()
    {
        global $LOCATION;
        // создаем пустой заказ
        $order = $this->createNewOrder();
        // ограничение по местоположению
        $this->orderProps = $order->getPropertyCollection();
        $prop = $this->getPropByCode("LOCATION");
        $prop->setValue($LOCATION->code);
        if ($this->checkType(array('cart'))) {
            return $this->setDeliveryPaymentLinks();
        }
        // ограничение по доставке
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipment->setFields(array(
            "CURRENCY" => $this->currency,
            "DELIVERY_ID" => $this->arResult["DELIVERY"]["ARRAY"]
        ));
        // создаем объект оплаты
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();
        // получаем оплаты с ограничениями
        $res = PayManager::getListWithRestrictions($payment);
        $arPayment = array();
        foreach ($res as $paymentId => $arItem) {
            $arPayment[$paymentId] = array(
                "ID" => $paymentId,
                "NAME" => $arItem["NAME"],
                "DESCRIPTION" => $arItem["DESCRIPTION"],
            );
            if (in_array($arItem['CODE'], ONLINE_PAYMENT_CODES)) {
                $this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"][] = $paymentId;
            }
        }
        return $arPayment;
    }

    // Устанавливаем ограничения по оплатам для доставок для js (data-allowed-payments атрибут)
    private function setDeliveryPaymentLinks()
    {
        global $LOCATION;
        $arPayments = [];
        foreach ($this->arResult["DELIVERY"]["ARRAY"] as $delivery) {
            // создаем пустой заказ
            $order = $this->createNewOrder();
            // ограничение по местоположению
            $this->orderProps = $order->getPropertyCollection();
            $prop = $this->getPropByCode("LOCATION");
            $prop->setValue($LOCATION->code);
            // ограничение по доставке
            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $shipment->setFields(array(
                "CURRENCY" => $this->currency,
                "DELIVERY_ID" => $delivery['ID']
            ));
            // создаем объект оплаты
            $paymentCollection = $order->getPaymentCollection();
            $payment = $paymentCollection->createItem();
            // получаем оплаты с ограничениями
            $res = PayManager::getListWithRestrictions($payment);
            foreach ($res as $id => $payment) {
                $arPayments[$id] = array(
                    "ID" => $id,
                    "NAME" => $payment["NAME"],
                    "DESCRIPTION" => $payment["DESCRIPTION"],
                );
                if (in_array($payment['CODE'], ONLINE_PAYMENT_CODES)) {
                    $this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"][] = $id;
                }
                if (isset($this->arResult["WAYS_DELIVERY"]["NOT_LOCAL"][$delivery['WAY_ID']])) {
                    if (!in_array($payment['ID'], $this->arResult["WAYS_DELIVERY"]["NOT_LOCAL"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'])) {
                        $this->arResult["WAYS_DELIVERY"]["NOT_LOCAL"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'][] = $payment['ID'];
                    }
                    if (in_array($delivery['ID'], $this->arResult["DELIVERY"]["PVZIDS"])) {
                        if (strpos($delivery['NAME'], "ПВЗ") !== false) {
                            foreach ($this->arPVZNames as $name) {
                                if (strpos($delivery['NAME'], $name['NAME']) !== false) {
                                    $this->arResult["WAYS_DELIVERY"]["NOT_LOCAL"][$delivery['WAY_ID']]['ALLOWED_PVZ_PAYMENTS'][$name['CLASS_NAME']][] = $payment['ID'];
                                }
                            }
                        }
                    }
                } elseif (isset($this->arResult["WAYS_DELIVERY"]["LOCAL"][$delivery['WAY_ID']])) {
                    if (!in_array($payment['ID'], $this->arResult["WAYS_DELIVERY"]["LOCAL"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'])) {
                        $this->arResult["WAYS_DELIVERY"]["LOCAL"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'][] = $payment['ID'];
                    }
                    if (in_array($delivery['ID'], $this->arResult["DELIVERY"]["PVZIDS"])) {
                        if (strpos($delivery['NAME'], "ПВЗ") !== false) {
                            foreach ($this->arPVZNames as $name) {
                                if (strpos($delivery['NAME'], $name['NAME']) !== false) {
                                    $this->arResult["WAYS_DELIVERY"]["LOCAL"][$delivery['WAY_ID']]['ALLOWED_PVZ_PAYMENTS'][$name['CLASS_NAME']][] = $payment['ID'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // получение внешних данных для местоположений с указанием кода для проверки возможности курьерской доставки
        $res = \Bitrix\Sale\Location\ExternalTable::getList(array(
            'filter' => array(
                '=SERVICE.CODE' => 'is_courier',
            )
        ));
        while ($item = $res->fetch()) {
            $serviceId = $item['SERVICE_ID'];
        }

        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array(
                'CODE' => array($LOCATION->code),
            ),
            'select' => array(
                'EXTERNAL.*',
            )
        ));
        while ($item = $res->fetch()) {
            if ($item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_ID'] == $serviceId) {
                $isCourier = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
            }
        }
        // удаляем курьера, т.к. регион не городской
        foreach ($this->arResult["WAYS_DELIVERY"] as $local => $arItem) {
            foreach ($arItem as $num => $arVal) {
                if (empty($arVal['DELIVERY'])) {
                    unset($this->arResult["WAYS_DELIVERY"][$local][$num]);
                    continue;
                }
                if (empty($isCourier) && $arVal['TYPEWAYS'] == 'C') {
                    unset($this->arResult["WAYS_DELIVERY"][$local][$num]);
                    continue;
                }
            }
        }

        return $arPayments;
    }

    // функции заказа
    private function createOrder()
    {
        global $LOCATION;
        global $USER;
        // удаляем купоны у резерва и 1 клика
        $arCoupons = CouponsManager::get(true, array(), true, true);
        if (!empty($arCoupons)) {
            if ($this->checkType(array("1click", "reserv"))) {
                foreach ($arCoupons as $arCoupon) {
                    CouponsManager::delete($arCoupon["COUPON"]);
                }
            } else {
                if (count($arCoupons) == 1) {
                    $arCoupon = reset($arCoupons);
                    if ($arCoupon['STATUS'] != 16 && $arCoupon['STATUS'] != 1) {
                        $this->postProps["DISCOUNT_COUPON"] = $arCoupon["COUPON"];
                        $this->postProps["SALE_DISCOUNT"] = $arCoupon["DISCOUNT_NAME"];
                    }
                }
            }
        }
        $order = $this->createNewOrder($this->user['ID']);
        if ($this->checkType(array("1click", "reserv"))) {
            if (!array_sum($this->basket->getQuantityList())) {
                die;
            }
            $order->setBasket($this->basket);
        } else {
            $this->divideBasket();
            if (!array_sum($this->newBasket->getQuantityList())) {
                die;
            }
            $order->setBasket($this->newBasket);
        }
        // доставка
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipment->setField("CURRENCY", $order->getCurrency());
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($order->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }
        $shipment->setFields(array(
            "DELIVERY_ID" => $this->arResult["DELIVERY"]["CURRENT"]["ID"],
            "DELIVERY_NAME" => $this->arResult["DELIVERY"]["CURRENT"]["NAME"],
        ));
        if ($this->checkType(array("reserv"))) {
            $shipment->setStoreId($this->postProps["STORE"]);
        }
        $shipmentCollection->calculateDelivery();
        $delPrice = $shipmentCollection->getBasePriceDelivery();
        $price = floor($order->getPrice());

        if ($this->checkType(array("order"))) {
            $paymentWay = $this->getPaymentWays($this->arResult["PAYMENT"]["CURRENT"]["ID"]);
            $checkPrice = $price - $delPrice;

            if (($checkPrice >= Option::get("respect", "prepayment_min_summ")) && $paymentWay['PREPAYMENT'] == 'Y') {
                $shipment->setBasePriceDelivery(0);
                $price = $checkPrice;
            }
        }

        // оплата
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();

        $payment->setFields(array(
            "PAY_SYSTEM_ID" => $this->arResult["PAYMENT"]["CURRENT"]["ID"],
            "PAY_SYSTEM_NAME" => $this->arResult["PAYMENT"]["CURRENT"]["NAME"],
            "SUM" => $price,
        ));
        // поля
        $order->setField("USER_DESCRIPTION", $this->getUserDescriptrion());
        // свойства
        $this->orderProps = $order->getPropertyCollection();
        // добавляем некоторые свойства к тем, что уже имеем
        $this->postProps["ORDER_REVENUE"] = $price;
        $this->postProps["ORDER_TYPE"] = $this->getOrderType();
        $this->postProps["LOCATION"] = $LOCATION->code;
        //$this->postProps["CITY"] = $LOCATION->getName();
        //$this->postProps["AREA"] = $LOCATION->getArea();
        //$this->postProps['REGION'] = $LOCATION->getRegion();
        $this->postProps['ORDER_REFERER'] = $this->getUTMProps();

        foreach ($LOCATION->getDadataInfo() as $dadataKey => $dadataVal) {
            if (!empty($dadataVal) && empty($this->postProps[mb_strtoupper($dadataKey)])) {
                $this->postProps[mb_strtoupper($dadataKey)] = $dadataVal;
            }
        }
        $arCityVsRegionArray = [
            'city' => 'region',
            'city_fias_id' => 'region_fias_id',
            'city_kladr_id' => 'region_kladr_id',
            'city_with_type' => 'region_with_type',
            'city_type' => 'region_type',
            'city_type_full' => 'region_type_full',
            ];
        foreach ($arCityVsRegionArray as $cityKey => $regionKey) {
            if (empty($this->postProps[mb_strtoupper($cityKey)])) {
                $this->postProps[mb_strtoupper($cityKey)] = $this->postProps[mb_strtoupper($regionKey)];
            }
        }
        if ($USER->IsAuthorized()) {
            $this->postProps['EMAIL_PROFILE'] = $this->user['EMAIL'] ? $this->user['EMAIL'] : '';
            $this->postProps['PHONE_PROFILE'] = $this->user['PERSONAL_PHONE'] ? $this->user['PERSONAL_PHONE'] : '';
        }
        if ((($this->postProps['IS_LOCAL'] == 'Y' || $this->checkType(array("1click"))) && !$LOCATION->exepRegionFlag) || $this->checkType(array("reserv"))) {
            $this->postProps['PRODUCT_REGION'] = $this->postProps['REGION'];
            $this->postProps['PRODUCT_CITY'] = $this->postProps['CITY'];
        } else {
            $this->postProps['PRODUCT_REGION'] = 'Москва';
            $this->postProps['PRODUCT_CITY'] = 'Москва';
        }
        if (empty($this->postProps['STREET'])) {
            $this->postProps['STREET'] = $this->postProps['STREET_USER'];
        }
        // парсим номер дома на дом, корпус и строение
        if (getDadataStatus()) {
            $this->postProps['HOUSE_NUM'] = preg_match(
                '/д\s*(\S+)/im',
                $this->postProps['HOUSE_USER'],
                $matches
            ) ? $matches[1] : ""; // Дом
        } else {
            $this->postProps['HOUSE_NUM'] = $this->postProps['HOUSE_USER'];
            $this->postProps['HOUSE'] = $this->postProps['HOUSE_USER'];
        }
        $this->postProps['HOUSING'] = preg_match(
            '/к\s*(\S+)/im',
            $this->postProps['HOUSE_USER'],
            $matches
        ) ? $matches[1] : ""; // Корпус
        $this->postProps['STRUCTURE'] = preg_match(
            '/стр\s*(\S+)/im',
            $this->postProps['HOUSE_USER'],
            $matches
        ) ? $matches[1] : ""; // Строение

        if ($this->checkType(array("reserv"))) {
            $this->initStoreProps();
        }
        if (in_array($this->arResult["DELIVERY"]["ID"], $this->arPvzIds)) {
            // очищаем ненужные свойства для ПВЗ
            $this->clearPropsPVZ();
        } else {
            unset($this->postProps['PVZ_ID']);
        }

        if (!empty($_COOKIE['_ga'])) {
            if (preg_match('/(\d*\.\d*)$/', $_COOKIE['_ga'], $matches)) {
                $this->postProps['GA_COOKIE'] = $matches;
            } else {
                $this->postProps['GA_COOKIE'] = 'кука не соответствует условиям';
            }
        }

        // устанавливаем все имеющиеся свойства заказу
        foreach ($this->postProps as $key => $value) {
            if (!empty($value)) {
                $prop = $this->getPropByCode($key);
                if ($prop) {
                    $prop->setValue($value);
                }
            }
        }
        // сохраняем заказ
        $order->doFinalAction(true);
        $res = $order->save();
        $orderId = $order->getId();
        $gtmData['id'] = $orderId;
        $gtmData['tax'] = number_format($order->getTaxPrice(), 0, '', '');
        $gtmData['price'] = number_format($order->getPrice(), 0, '', '');
        $gtmData['delivery'] = number_format($order->getDeliveryPrice(), 0, '', '');
        $gtmData['coupon'] = $this->postProps["DISCOUNT_COUPON"];
        if ($res->isSuccess()) {
            // очищаем купоны из сессии
            $this->delCoupon();
            $_SESSION['NEW_ORDER_ID'] = $orderId;
            $_SESSION['CRITEO_NEW_ORDER_ID'] = $orderId;
            $this->returnOk($orderId, false, $this->offers, $gtmData);
        } else {
            $this->arResult["ERRORS"][] = $res->getErrorMessages();
            $this->returnError();
        }
    }

    private function createNewOrder($userId = 0)
    {
        $this->setPersonType();
        $this->setCurrency();
        $order = Order::create(SITE_ID, $userId, $this->currency);
        $order->setPersonTypeId($this->personType);
        return $order;
    }

    private function setPersonType()
    {
        if ($this->personType) {
            return;
        }
        $this->personType = User::getPersonalTypeId();
    }

    private function setCurrency()
    {
        if ($this->currency) {
            return;
        }
        $this->currency = CurrencyManager::getBaseCurrency();
    }

    private function getUTMProps()
    {
        $UTM = [
            'istochnik' => $_COOKIE['istochnik'] ? $_COOKIE['istochnik'] : '',
            'utm_source' => $_COOKIE['utm_source'] ? $_COOKIE['utm_source'] : '',
            'utm_medium' => $_COOKIE['utm_medium'] ? $_COOKIE['utm_medium'] : '',
            'utm_campaign' => $_COOKIE['utm_campaign'] ? $_COOKIE['utm_campaign'] : '',
            'utm_content' => $_COOKIE['utm_content'] ? $_COOKIE['utm_content'] : '',
            'utm_term' => $_COOKIE['utm_term'] ? $_COOKIE['utm_term'] : '',
        ];

        $queryString = http_build_query($UTM);

        return $queryString;
    }

    private function getUserDescriptrion()
    {
        switch ($this->type) {
            case "1click":
                return "Заказ создан через один клик";
            default:
                return $_REQUEST['USER_DESCRIPTION'] ?: '';
        }
    }

    private function getOrderType()
    {
        switch ($this->type) {
            case "reserv":
                return 'RESERVATION';
            case "1click":
                return 'ONE_CLICK';
            default:
                return 'ORDER';
        }
    }

    private function initStoreProps()
    {
        if (!$this->postProps["STORE"]) {
            return;
        }
        $arStore = StoreTable::getList(array(
            "filter" => array(
                "ID" => $this->postProps["STORE"],
            ),
            "select" => array(
                "TITLE",
                "UF_FILIAL",
            ),
            "limit" => 1,
        ))->Fetch();
        if (!empty($arStore)) {
            $this->postProps["STORE"] = $arStore["TITLE"];
            $this->postProps["FILIAL"] = $arStore["UF_FILIAL"];
        }
    }

    private function clearPropsPVZ()
    {
        $allowedProps = array(
            "FIO" => true,
            "EMAIL" => true,
            "PHONE" => true,
            "PVZ_ID" => true,
            "DISCOUNT_COUPON" => true,
            "SALE_DISCOUNT" => true,
            "ORDER_REVENUE" => true,
            "ORDER_TYPE" => true,
            "LOCATION" => true,
            "CITY" => true,
            "REGION" => true,
            "PRODUCT_REGION" => true,
            "PRODUCT_CITY" => true,
            "EMAIL_PROFILE" => true,
            "PHONE_PROFILE" => true,
        );
        foreach ($this->postProps as $key => $value) {
            if (!$allowedProps[$key]) {
                unset($this->postProps[$key]);
            }
        }
    }

    private function getPropByCode($code)
    {
        foreach ($this->orderProps as $property) {
            if ($property->getField("CODE") == $code) {
                return $property;
            }
        }
    }

    private function getBasketsPrices()
    {
        if (!empty($this->arResult['ITEMS']['LOCAL'])) {
            $this->arResult['PRICE']['LOCAL'] = array_sum(array_column($this->arResult['ITEMS']['LOCAL'], 'PRICE'));
        }
        if (!empty($this->arResult['ITEMS']['NOT_LOCAL'])) {
            $this->arResult['PRICE']['NOT_LOCAL'] = array_sum(array_column($this->arResult['ITEMS']['NOT_LOCAL'], 'PRICE'));
        }
    }

    private function getTypePrice($arOffers, $needLocalBasketPrice = 'Y')
    {
        $price = 0;
        foreach ($arOffers as $offer) {
            if ($needLocalBasketPrice === $offer['IS_LOCAL'] || $offer['IS_LOCAL'] === 'N' && !$offer['IS_LOCAL']) {
                $price += $offer['BASKET_PRICE'];
            }
        }
        return $price;
    }

    private function divideBasket()
    {
        $this->newBasket = $this->basket->copy();
        $basketItems = $this->basket->getBasketItems();

        foreach ($basketItems as $arItem) {
            $basketPropertyCollection = $arItem->getPropertyCollection();
            foreach ($basketPropertyCollection as $basketPropertyItem) {
                if ($basketPropertyItem->getField('CODE') == "IS_LOCAL") {
                    $isLocal = $basketPropertyItem->getField('VALUE');
                }
            }
            if ($this->postProps["IS_LOCAL"] == $isLocal) {
                continue;
            }
            $this->newBasket->getItemById($arItem->getField('ID'))->delete();
        }
    }

    private function getDeliveryWays(): void
    {
        $rsDeliveryWaysIds = WaysByDeliveryServicesTable::getList([
            'filter' => ['@DELIVERY_ID' => array_keys($this->arResult["DELIVERY"]["ARRAY"])],
            'group' => ['WAY_ID'],
            'select' => ['*']
        ]);

        while ($arDeliveryWaysId = $rsDeliveryWaysIds->fetch()) {
            $arDeliveryWaysIds[$arDeliveryWaysId['WAY_ID']]['WAY_ID'] = $arDeliveryWaysId['WAY_ID'];
            $this->arResult["DELIVERY"]["ARRAY"][$arDeliveryWaysId["DELIVERY_ID"]]['WAY_ID'] = $arDeliveryWaysId["WAY_ID"];
        }
        foreach ($this->arResult["DELIVERY"]["ARRAY"] as $key => $delivery) {
                $arDeliveryWaysIds[$delivery['WAY_ID']]['DELIVERY_ID'][] = $key;
                $arDeliveryWaysIds[$delivery['WAY_ID']]['PRICES'][] = $delivery['PRICE'];
        }

        $rsDeliveryWays = WaysDeliveryTable::getList([
            'select' => ['*'],
            'filter' => ['ACTIVE' => 'Y', '@ID' => array_keys($arDeliveryWaysIds)]
        ]);

        while ($arDeliveryWay = $rsDeliveryWays->fetch()) {
            $arDeliveryWay['DELIVERY'] = $arDeliveryWaysIds[$arDeliveryWay['ID']]['DELIVERY_ID'];
            $arDeliveryWay['PRICES'] = $arDeliveryWaysIds[$arDeliveryWay['ID']]['PRICES'];
            $flag = false;
            if ($arDeliveryWay['LOCAL'] == 'Y') {
                foreach ($arDeliveryWay['DELIVERY'] as $delivery) {
                    if (strpos($this->arResult["DELIVERY"]["ARRAY"][$delivery]['NAME'], 'ПВЗ') !== false) {
                        if (PVZFactory::checkDeliverySrv($this->arResult["DELIVERY"]["ARRAY"][$delivery], true)) {
                            $arDeliveryWay['PVZ'] = true;
                            $flag = true;
                        };
                    } else {
                        $flag = true;
                    }
                }
                if ($flag) {
                    $arDeliveryWays['LOCAL'][$arDeliveryWay['ID']] = $arDeliveryWay;
                }
            } else {
                foreach ($arDeliveryWay['DELIVERY'] as $delivery) {
                    if (strpos($this->arResult["DELIVERY"]["ARRAY"][$delivery]['NAME'], 'ПВЗ') !== false) {
                        if (PVZFactory::checkDeliverySrv($this->arResult["DELIVERY"]["ARRAY"][$delivery], false)) {
                            $arDeliveryWay['PVZ'] = true;
                            $flag = true;
                        };
                    } else {
                        $flag = true;
                    }
                }
                if ($flag) {
                    $arDeliveryWays['NOT_LOCAL'][$arDeliveryWay['ID']] = $arDeliveryWay;
                }
            }
        }
        uasort($arDeliveryWays['LOCAL'], function ($a, $b) {
            return $a['SORT'] <=> $b['SORT'];
        });
        uasort($arDeliveryWays['NOT_LOCAL'], function ($a, $b) {
            return $a['SORT'] <=> $b['SORT'];
        });

        $this->arResult['WAYS_DELIVERY'] = $arDeliveryWays;
        //Меняем id первой службы доставки (для выбора по умолчанию в шаблоне)
        $this->arResult["DELIVERY"]["LOCAL"]["ID"] = reset($arDeliveryWays["LOCAL"])["DELIVERY"][0];
        $this->arResult["DELIVERY"]["NOT_LOCAL"]["ID"] = reset($arDeliveryWays["NOT_LOCAL"])["DELIVERY"][0];
        foreach ($this->arPVZNames as $PVZInfo) {
            $this->arResult["DELIVERY"]['PVZ_HIDE_POSTAMAT'][$PVZInfo['CLASS_NAME']] = $PVZInfo['HIDE_POSTAMAT'];
            $this->arResult["DELIVERY"]['PVZ_HIDE_ONLY_PREPAYMENT'][$PVZInfo['CLASS_NAME']] = $PVZInfo['HIDE_ONLY_PREPAYMENT'];
        }
    }

    private function getPaymentWays($paymentId = false)
    {
        $paymentIds = $paymentId ?: array_keys($this->arResult["PAYMENT"]["ARRAY"]);

        $rsPaymentWaysIds = WaysByPaymentServicesTable::getList([
            'filter' => ['@PAYMENT_ID' => $paymentIds],
            'group' => ['WAY_ID'],
            'select' => ['WAY_ID', 'PAYMENT_ID']
        ]);

        while ($arPaymentWaysId = $rsPaymentWaysIds->fetch()) {
            $arPaymentWaysIds[$arPaymentWaysId['WAY_ID']] = $arPaymentWaysId;
        }

        $rsPaymentWays = WaysPaymentTable::getList([
            'select' => ['*'],
            'filter' => ['ACTIVE' => 'Y', '@ID' => array_column($arPaymentWaysIds, 'WAY_ID')]
        ]);

        while ($arPaymentWay = $rsPaymentWays->fetch()) {
            $arPaymentWay['PAYMENT'] = $arPaymentWaysIds[$arPaymentWay['ID']]['PAYMENT_ID'];
            if ($paymentId && $arPaymentWay['LOCAL'] == $this->postProps["IS_LOCAL"]) {
                $arPaymentWays = $arPaymentWay;
            } else {
                if ($arPaymentWay['LOCAL'] == 'Y') {
                    $arPaymentWays['LOCAL'][$arPaymentWay['ID']] = $arPaymentWay;
                } else {
                    $arPaymentWays['NOT_LOCAL'][$arPaymentWay['ID']] = $arPaymentWay;
                }
            }
        }

        $this->arResult['WAYS_PAYMENT'] = $arPaymentWays;
        if ($paymentId) {
            return $arPaymentWays;
        } else {
            //Меняем id первой службы доставки (для выбора по умолчанию в шаблоне)
            $this->arResult["PAYMENT"]["ID"] = reset($arPaymentWays)['PAYMENT'];
        }
    }

    private function getAvailableOffersSizesForItems()
    {
        global $LOCATION;

        $arCarts = [
            'LOCAL' => 'NOT_LOCAL',
            'NOT_LOCAL' => 'LOCAL',
        ];

        foreach ($arCarts as $primaryCart => $secondaryCart) {
            if (isset($this->arResult['ITEMS'][$primaryCart])) {
                foreach ($this->arResult['ITEMS'][$primaryCart] as $arItem) {
                    if (!empty($arItem['SIZE'])) {
                        $arProductsInCart[$arItem['PRODUCT_ID']] = $arItem['PRODUCT_ID'];
                    }
                }
            }
        }

        if (!empty($arProductsInCart)) {
            $sizeProducts = $LOCATION->getAmountOffersByProductId($arProductsInCart);
        }

        foreach ($arCarts as $primaryCart => $secondaryCart) {
            if (isset($this->arResult['ITEMS'][$primaryCart])) {
                foreach ($this->arResult['ITEMS'][$primaryCart] as $offerId => &$arItem) {
                    if (!empty($arItem['SIZE'])) {
                        foreach ($sizeProducts[$arItem['PRODUCT_ID']] as $availableOffer => $availableSize) {
                            if (!isset($this->arResult['ITEMS'][$primaryCart][$availableOffer]) && !isset($this->arResult['ITEMS'][$secondaryCart][$availableOffer])) {
                                $arItem['AVAILABLE_SIZES'][$availableSize] = $availableOffer;
                            }
                        }
                    }
                }
                unset($arItem);
            }
        }
    }

    private function getPropsForGTM(array $props) : array
    {
        foreach ($props as $key => $value) {
            $obEntity = HL::getEntityClassByHLName($key);

            if ($obEntity && is_object($obEntity)) {
                $sClass = $obEntity->getDataClass();

                $rsData = $sClass::getList(['select' => ['UF_XML_ID', 'UF_NAME'], 'filter' => ['UF_XML_ID' => $value]]);

                while ($prop = $rsData->fetch()) {
                    $props[$key][$prop['UF_XML_ID']] = $prop['UF_NAME'];
                }
            }
        }

        return $props;
    }

    private function getStoreSeller($seller_id, $storeSeller_id)
    {
        $storeUser = CUser::GetList(
            ($by = 'id'),
            ($order = 'asc'),
            ['WORK_PAGER' => $seller_id],
            [
                'FIELDS' => [
                    'WORK_COMPANY',
                    'WORK_DEPARTMENT',
                ],
                'SELECT' => [
                    'UF_STORE',
                ]
            ]
        )->Fetch();

        Loader::includeModule('highloadblock');

        $obSellers = HL::getEntityClassByHLName('Sellers');
        $arSeller = [];

        if ($obSellers && is_object($obSellers)) {
            $sellerClass = $obSellers->getDataClass();

            $arSeller = $sellerClass::getList(
                [
                    'filter' =>
                        [
                            'ID' => $storeSeller_id,
                            'UF_STORE_ID' => $storeUser['UF_STORE'],
                            //'UF_FIRED' => 0,
                        ],
                    'select' =>
                        ['ID', 'UF_SURNAME', 'UF_NAME', 'UF_PATRONYMIC', 'UF_FULL_NAME', 'UF_SELLER_SELF_CODE'],
                ]
            )->fetch();
        }

        return $arSeller;
    }
}
