<?

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Sale\BasketBase;
use Bitrix\Sale\Internals\CollectableEntity;
use Likee\Site\Helpers\HL;
use \Likee\Site\User;
use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Config\Option;
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
use Qsoft\Helpers\PriceUtils;
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
        "order",
        "1click",
        "coupon",
        "basketAdd",
        "basketDel",
    );
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
    /** @var BasketBase */
    // корзина
    private $basket;
    // валюта
    private $currency;
    // тип плательщика
    private $personType;
    // Данные по названию и классу пунктов выдачи из таблицы b_qsoft_pvz
    private $arPVZNames = [];

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
        // для всего, кроме корзины и товаров, ответ в JSON
        if (!$this->checkType(["cart", "offers"])) {
            $this->json = true;
        }
        if ($this->checkType(["order"])) {
            // устанавливаем корзину
            if (!$this->setBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // устанавливаем доставку
            $this->setDeliveryId();
            // устанавливаем оплату
            $this->setPaymentId();
            // проверяем данные
            if (!$this->checkData()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // проверяем корзину
            if (!$this->checkBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // проверяем юзера
            $this->checkUser();
            $this->updateUserProfile();
            if (!empty($this->arResult["ERRORS"])) {
                if (empty($this->arResult['ERRORS'])) {
                    // при ошибке возвращаем её на фронт в виде JSON
                    $this->returnError();
                }
            }
            // создаем заказ
            $this->createOrder();
        }
        if ($this->checkType(["cart"])) {
            // для аякс запроса карточки (когда есть ошибки) ставим флаг аякса
            if ($_POST["ajax"] == "Y") {
                $this->ajax = true;
            }
            // устанавливаем корзину
            if (!$this->setBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // применяем промокоды
            $this->getCoupon();
            // проверяем корзину
            if (!$this->checkBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            $this->getItems();
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
        if ($this->checkType(["coupon"])) {
            // проверяем купон
            $this->checkCoupon();
        }
        if ($this->checkType(["basketAdd", "basketDel"])) {
            // добавляем в корзину
            $offerId = intval($_POST["offerId"]);
            $quantity = intval($_POST["quantity"]);
            // загружаем корзину
            $this->setBasket();
            if ($this->checkType(["basketAdd"])) {
                // добавляем в корзину
                $this->addToBasket($offerId, $quantity);
            } else {
                // получаем список доставок
                $this->getDeliveries();
                $this->getDeliveryWays();
                // удаляем из корзины
                $this->deleteFromBasket($offerId, $quantity);
            }
        }
        if ($this->checkType(["1click"])) {
            // устанавливаем доставку
            $this->setDeliveryId();
            // устанавливаем оплату
            $this->setPaymentId();
            // проверяем данные
            if (!$this->checkData()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // устанавливаем корзину
            if (!$this->setBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // проверяем корзину
            if (!$this->checkBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // проверяем юзера
            $this->checkUser();
            $this->updateUserProfile();
            // создаем заказ
            $this->createOrder();
        }
        if ($this->checkType(["offers"])) {
            // устанавливаем корзину
            if (!$this->setBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // применяем промокоды
            $this->getCoupon();
            // проверяем корзину
            if (!$this->checkBasket()) {
                // при ошибке возвращаем её на фронт в виде JSON
                $this->returnError();
            }
            // получаем данные для вывода
            $this->getItems();
        }
        $this->includeComponentTemplate();
    }

    //обновление авторизованного профиля на сайте информацией из заказа
    private function updateUserProfile()
    {
        global $USER;
        //заменяем техническую почту или заполняем пустую
        if (!empty($this->postProps['EMAIL']) && $this->postProps['EMAIL'] != $this->user['EMAIL']) {
            if (empty($this->user['EMAIL']) || preg_match('`.*@orgasmcity.ru`i', $this->user['EMAIL'])) {
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

    // функции возврата данных
    private function returnError($error = false)
    {
        if ($error) {
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

    private function returnOk($text = null, $coupon = null, $info = null)
    {
        $arResult = [
            "status" => "ok",
        ];
        if ($info !== null) {
            $arResult["info"] = $info;
        }
        if ($text !== null) {
            $arResult["text"] = $text;
        }
        if ($coupon !== null) {
            $arResult["coupon"] = $coupon;
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
        if (!$this->arResult["COUPON"]) {
            $this->arResult["ERRORS"][] = "Промокод не указан";
            $this->returnError();
        }
        // очищаем имеющиеся купоны
        $this->delCoupon(true);
        // добавляем новый
        $res = CouponsManager::add($this->arResult["COUPON"]);
        if ($res) {
            $arCoupons = CouponsManager::get(true, array(), true, true);
            $arCoupon = array_shift($arCoupons);
            $this->checkCouponStatus($arCoupon);
        } else {
            $this->arResult["ERRORS"][] = "Промокод не существует";
            $this->returnError();
        }
    }

    private function delCoupon($check = false)
    {
        $arCoupons = CouponsManager::get(true, array(), true, true);
        if (!empty($arCoupons)) {
            foreach ($arCoupons as $arCoupon) {
                if ($check && $this->arResult["COUPON"] == $arCoupon["COUPON"]) {
                    $this->checkCouponStatus($arCoupon);
                }
                CouponsManager::delete($arCoupon["COUPON"]);
            }
        }
    }

    private function checkCouponStatus($arCoupon)
    {
        if ($arCoupon["STATUS"] == CouponsManager::STATUS_NOT_FOUND) {
            $this->arResult["ERRORS"][] = "Промокод не существует";
        } elseif ($arCoupon["STATUS"] == CouponsManager::STATUS_FREEZE) {
            $this->arResult["ERRORS"][] = "Промокод использован максимальное количество раз";
        } elseif ($arCoupon["STATUS"] == CouponsManager::STATUS_ENTERED
            || $arCoupon["STATUS"] == CouponsManager::STATUS_NOT_APPLYED
            || $arCoupon["STATUS"] == CouponsManager::STATUS_APPLYED
        ) {
            if ($this->loadBasket()) {
                $this->applyCoupon();
                $this->returnOk(floor($this->basket->getPrice()), $this->arResult["COUPON"]);
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
        if ($this->checkType(["order"]) && !$this->postProps['FIO']) {
            $this->arResult["ERRORS"]['FIO'] = "Не заполенено ФИО";
        }
        // только в корзине должны быть другие поля
        if ($this->checkType(array("order"))) {
            $props = array(
                "EMAIL" => "Не заполнен Email",
            );
            if (in_array($this->arResult["DELIVERY"]["ID"], $this->arPvzIds)) {
                $props["PICKPOINT_ID"] = "Не выбран ПВЗ";
            } elseif ($this->arResult["DELIVERY"]["ID"] !== MOSCOW_SELF_DELIVERY_ID) {
                if (!$this->postProps["HOUSE_USER"]) {
                    $props["STREET_USER"] = "Не заполнена улица";
                }
                $props["HOUSE_USER"] = "Не заполнен номер дома";
            }
            //проверяем телефон в профиле и заказе на дубликаты для Sailplay
            if ($USER->IsAuthorized()) {
                if (!$this->postProps['SKIP_CHECK_PHONE']) {
                    if (empty($this->user['PERSONAL_PHONE'])) {
                        $by = 'ID';
                        $order = 'ASC';
                        $rsUsers = CUser::GetList($by, $order, ["PERSONAL_PHONE" => $this->postProps['PHONE']]);
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
            return;
        }
        $this->setNewUserFields();
    }

    private function setNewUserFields()
    {
        $fio = preg_replace('/[\s]{2,}/', ' ', $this->postProps['FIO']);
        $name = explode(' ', $fio);
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

    // функции работы с корзиной
    private function setBasket()
    {
        if ($this->checkType(["cart", "offers", "order", "basketAdd", "basketDel"])) {
            // загружаем корзину
            if ($this->loadBasket()) {
                return true;
            }
            $this->arResult["ERRORS"] = "Не удалось загрузить корзину";
            return false;
        } else {
            // получаем ТП из POST и создаем корзину в случае 1click
            return $this->getPostItems();
        }
    }

    private function loadBasket(): bool
    {
        $this->basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);
        $arBasketItems = $this->basket->getBasketItems();
        // Актуализируем цены в корзине
        foreach ($arBasketItems as $basketItem) {
            $this->createBasketItem($basketItem->getProductId(), $basketItem->getQuantity());
            $basketItem->delete();
        }
        $this->basket->save();
        return (bool)$this->basket;
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

    private function addToBasket($offerId, $quantity)
    {
        // проверяем корзину на ограничения
        $this->checkBasketPositionLimit(1);
        if (count($this->arResult["ERRORS"]) > 0) {
            $this->returnError();
        }
        // проверяем есть ли ТП с таким ID
        // $this->basket->getExistsItem не работает, тк он сверяет все свойства, а у нас их ещё нет
        $arBasketItems = $this->basket->getBasketItems();
        // Создадим заказ и присвоим ему корзину для применения
        // промокодов для вычисления точной суммы корзины
        $order = $this->createNewOrder();
        $order->setBasket($this->basket);

        $basketItem = false;
        $basketSum = 0;
        foreach ($arBasketItems as $arItem) {
            $basketSum += $arItem->getPrice() * $arItem->getQuantity();
            if (!$basketItem && $arItem->getProductId() == $offerId) {
                $basketItem = $arItem;
            }
        }

        $rest = Functions::getRests($offerId)[$offerId];
        if ($basketItem && (($basketItem->getQuantity() + $quantity) > $rest)) {
            $this->arResult["ERRORS"][] = "Не достаточно остатка на складе по данному товару.
            У вас в корзине уже " . $basketItem->getQuantity() . " шт. Свободного остатка: " . $rest ?? 0;
            $this->returnError();
        } elseif ($quantity > $rest) {
            $this->arResult["ERRORS"][] = "Не достаточно остатка по данному товару.
             Уменьшите количество добавляемого товара в корзину. Свободного остатка: " . $rest ?? 0;
            $this->returnError();
        }

        if ($basketItem) {
            $basketItem->setField('QUANTITY', $basketItem->getQuantity() + $quantity);
        } else {
            $basketItem = $this->createBasketItem($offerId, $quantity);
        }

        $res = $this->basket->save();
        if ($res->isSuccess()) {
            $basketSum += $basketItem->getPrice() * $quantity;
            $this->returnOk($basketSum);
        }

        $this->arResult["ERRORS"][] = "Не удалось добавить или обновить товар в коризне";
        $this->returnError();
    }

    private function deleteFromBasket($offerId, $quantity)
    {
        // проверяем есть ли ТП с таким ID
        // $this->basket->getExistsItem не работает, тк он сверяет все свойства, а у нас их ещё нет
        $arBasketItems = $this->basket->getBasketItems();
        foreach ($arBasketItems as $arItem) {
            if ($arItem->getProductId() == $offerId) {
                $resultQty = $arItem->getQuantity() - $quantity;
                if ($resultQty < 1) {
                    $arItem->delete();
                } else {
                    $arItem->delete();
                    $arItem = $this->createBasketItem($offerId, $resultQty);
                }
                $res = $this->basket->save();
                if (empty($this->offers)) {
                    $this->checkBasketAvailability();
                }
                if ($res->isSuccess()) {
                    $price = $this->getSumPrice($this->offers);
                    $this->checkBasketSumLimit();
                    if (!empty($this->arResult['ERRORS'])) {
                        $this->returnError($this->arResult['ERRORS'][0]);
                    } else {
                        $this->returnOk($price, null, $resultQty);
                    }
                }
            }
        }
        $this->arResult["ERRORS"][] = "Не удалось удалить товар из корзины";
        $this->returnError();
    }

    private function createBasketItem($offerId, $quantity = 1)
    {
        $basketItem = $this->basket->createItem('catalog', $offerId);
        $arProps = $this->getOfferProps($offerId);
        $basketItem->setFields([
            'QUANTITY' => $quantity,
            'CURRENCY' => CurrencyManager::getBaseCurrency(),
            'LID' => SITE_ID,
            'PRODUCT_PRICE_ID' => $arProps["PRODUCT_ID"],
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ]);
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
            [],
            [
                "ID" => $offerId,
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "nTopCount" => 1,
            ],
            [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_SIZE",
                "PROPERTY_COLOR",
            ]
        )->Fetch();
        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return false;
        }
        // получаем свойства из товара
        $arProd = CIBlockElement::GetList(
            [],
            [
                "ID" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            ],
            false,
            [
                "nTopCount" => 1,
            ],
            [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_ARTICLE",
                "PROPERTY_COLOR",
                "PROPERTY_KOD_1S",
            ]
        )->Fetch();
        $arProps = [];
        if ($arProd["PROPERTY_ARTICLE_VALUE"]) {
            $arProps["PROPS"]["ARTICLE"] = [
                "CODE" => "ARTICLE",
                "NAME" => "Артикул",
                "VALUE" => $arProd["PROPERTY_ARTICLE_VALUE"],
                "SORT" => 1,
            ];
        }
        if ($arProd["PROPERTY_COLOR_VALUE"]) {
            CModule::IncludeModule('highloadblock');
            $hlblock = HLBT::getList(['filter' => ['=NAME' => 'Firecolorreference']])->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $color = $entity_data_class::getList([
                'filter' => ['UF_XML_ID' => $arProd["PROPERTY_COLOR_VALUE"]],
                'select' => ['UF_NAME'],
            ])->fetch()['UF_NAME'];
            $arProps["PROPS"]["COLOR"] = [
                "CODE" => "COLOR",
                "NAME" => "Цвет",
                "VALUE" => $color,
                "SORT" => 2,
            ];
        }
        // добавляем свойства из ТП, если оно есть
        if ($arOffer["PROPERTY_SIZE_VALUE"]) {
            $arProps["PROPS"]["SIZE"] = [
                "CODE" => "SIZE",
                "NAME" => "Размер",
                "VALUE" => $arOffer["PROPERTY_SIZE_VALUE"],
                "SORT" => count($arProps) + 1,
            ];
        }
        if ($arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            $arProps["PROPS"]["PROD_ID"] = [
                "CODE" => "PRODUCT_ID",
                "NAME" => "ID продукта",
                "VALUE" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "SORT" => count($arProps) + 1,
            ];
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
        if ($this->checkType(["1click"])) {
            return empty($this->arResult["ERRORS"]);
        }
        $this->checkBasketLimits();
        if ($this->checkType(["order"])) {
            return empty($this->arResult["ERRORS"]);
        }
    }

    private function checkBasketAvailability()
    {
        if ($this->checkType(["cart", "offers", "coupon", "basketAdd", "basketDel", "order"])) {
            $full = true;
        } else {
            $full = false;
        }
        // получаем предложения из корзины
        $this->offers = $this->getOffers($full);
        // если нет предложений
        if (empty($this->offers)) {
            if (!$full) {
                // при оформлении любым типом корзина не может быть пустой
                $this->arResult["ERRORS"][] = "Нет товаров в корзине";
            }
            return;
        }

        // формируем ID товаров
        $arProductIds = array_column($this->offers, 'PRODUCT_ID');

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

        $arCount = count($this->offers);
        if (($arCount + $add > $basketNum) && $arCount + $add > 0) {
            $this->arResult["ERRORS"][] = str_replace("%NUM%", $basketNum, Option::get("respect", "basket_max_art_num_text", "Максимальное количество позиций в корзине - %NUM% шт"));
        }
    }

    private function checkBasketSumLimit()
    {
        $basketSum = Option::get("respect", "basket_min_num", 1500);
        if ($this->getSumPrice($this->offers) < $basketSum) {
            $this->arResult["ERRORS"][] = str_replace("%NUM%", $basketSum, Option::get("respect", "basket_min_num_text", "Минимальная сумма заказа - %NUM% р"));
        }
    }

    // функции товаров
    private function getOffers($full = true)
    {
        $basketItems = $this->basket->getBasketItems();
        if (empty($basketItems)) {
            return null;
        }
        $arOffers = [];
        /** @var CollectableEntity $arItem */
        foreach ($basketItems as $arItem) {
            $offerId = $arItem->getProductId();
            if ($full) {
                $arOffers[$offerId] = [
                    "QUANTITY" => $arItem->getQuantity(),
                    "BASKET_PRICE" => $arItem->getPrice() * $arItem->getQuantity(),
                ];
            } else {
                $arOffers[$offerId] = $offerId;
            }
        }

        $arSelect = [
            "ID",
            "IBLOCK_ID",
            "PROPERTY_CML2_LINK",
            "XML_ID",
            "NAME"
        ];
        if ($full) {
            $arSelect[] = "PROPERTY_SIZE";
            $arSelect[] = "PROPERTY_COLOR";
            $arSelect[] = "PROPERTY_BASEWHOLEPRICE";
            $arSelect[] = "PROPERTY_BASEPRICE";
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
            $price = PriceUtils::getPrice($arItem["PROPERTY_BASEWHOLEPRICE_VALUE"], $arItem["PROPERTY_BASEPRICE_VALUE"]);
            if (!$price) {
                continue;
            }
            if ($full) {
                $arOffersNew[$arItem["ID"]] = [
                    "PRODUCT_ID" => $arItem["PROPERTY_CML2_LINK_VALUE"],
                    "XML_ID" => $arItem["XML_ID"],
                    "NAME" => $arItem["NAME"],
                    "SIZE" => $arItem["PROPERTY_SIZE_VALUE"],
                    "COLOR" => $arItem["PROPERTY_COLOR_VALUE"],
                    "QUANTITY" => $arOffers[$arItem["ID"]]["QUANTITY"],
                    "BASKET_PRICE" => $arOffers[$arItem["ID"]]["BASKET_PRICE"],
                    "BRANCH_PRICE" => $price["PRICE"] * $arOffers[$arItem["ID"]]["QUANTITY"],
                    "BRANCH_OLD_PRICE" => $price["OLD_PRICE"] * $arOffers[$arItem["ID"]]["QUANTITY"],
                ];
            } else {
                $arOffersNew[$arItem["ID"]] = [
                    "PRODUCT_ID" => $arItem["PROPERTY_CML2_LINK_VALUE"],
                    "XML_ID" => $arItem["XML_ID"],
                ];
            }
        }

        return $arOffersNew;
    }

    private function getItems()
    {
        $arProductIds = array_column($this->offers, 'PRODUCT_ID');
        // если нет ID продуктов, то ничего не делаем
        if (empty($arProductIds)) {
            return;
        }
        $arProducts = $this->getProducts($arProductIds);
        $this->setItems($this->offers, $arProducts);
    }

    private function getProducts($arProductIds)
    {
        $res = CIBlockElement::GetList(
            [],
            [
                "ID" => $arProductIds,
                "IBLOCK_ID" => IBLOCK_CATALOG,
            ],
            false,
            false,
            [
                "ID",
                "IBLOCK_ID",
                "CODE",
                "NAME",
                "DETAIL_PICTURE",
                "PREVIEW_PICTURE",
                "PROPERTY_ARTICLE",
            ]
        );
        $arProducts = [];

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
            );
        }

        return $arProducts;
    }

    private function setItems($arOffers, $arProducts)
    {
        $this->arResult["ITEMS"] = [];
        $this->arResult["DISCOUNT"] = 0;
        $arColors = $this->getColors();
        foreach ($arOffers as $id => $value) {
            $src = [];
            if ($arProducts[$value["PRODUCT_ID"]]["PREVIEW_PICTURE"]) {
                $src[] = $arProducts[$value["PRODUCT_ID"]]["PREVIEW_PICTURE"];
            }
            $value["PRICE"] = floor($value["BASKET_PRICE"]);
            $name = $arProducts[$value["PRODUCT_ID"]]["NAME"];
            $nameWithAdditions = $arProducts[$value["PRODUCT_ID"]]["NAME"];
            $nameAdditions = [];
            if ($value["SIZE"]) {
                $nameAdditions[] = 'Размер: <b>' . $value["SIZE"] . '</b>';
            }
            if ($value['COLOR']) {
                $value['COLOR'] = $arColors[$value['COLOR']];
                $nameAdditions[] = 'Цвет: <b>' . $value['COLOR'] . '</b>';
            }
            if (!empty($nameAdditions)) {
                $nameWithAdditions .= '<br>' . implode(', ', $nameAdditions);
            }
            $arItem = [
                "PRODUCT_ID" => $value["PRODUCT_ID"],
                "XML_ID" => $value["XML_ID"],
                "CODE" => $arProducts[$value["PRODUCT_ID"]]["CODE"],
                "SRC" => $src,
                "ARTICLE" => $arProducts[$value["PRODUCT_ID"]]["ARTICLE"],
                "NAME" => $name,
                "NAME_WITH_ADDITIONS" => $nameWithAdditions,
                "SIZE" => $value["SIZE"],
                "COLOR" => $value["COLOR"],
                "QUANTITY" => $value["QUANTITY"],
                "PRICE" => $value["BASKET_PRICE"],
                "OLD_CATALOG_PRICE" => $value["BRANCH_OLD_PRICE"],
            ];
            if ($this->arResult["COUPON"]) {
                $arItem["OLD_PRICE"] = $value["BRANCH_PRICE"];
                $this->arResult["DISCOUNT"] += ($value["BRANCH_PRICE"] - $value["BASKET_PRICE"]);
            }
            $this->arResult["ITEMS"][$id] = $arItem;

            if (!in_array($id, $this->arResult['OFFERS'])) {
                $this->arResult['OFFERS'][] = $id;
            }

        }
    }

    private function getColors()
    {
        $arColors = [];
        if ($this->initCache('color')) {
            $arColors = $this->getCachedVars('color');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag('catalogAll');

            $obEntity = HL::getEntityClassByHLName('Firecolorreference');
            if ($obEntity && is_object($obEntity)) {
                $sClass = $obEntity->getDataClass();
                $rsColors = $sClass::getList(['select' => ['UF_NAME', 'UF_XML_ID']]);

                while ($arColor = $rsColors->fetch()) {
                    $arColors[$arColor['UF_XML_ID']] = $arColor['UF_NAME'];
                }
            }
            if (!empty($arColors)) {
                $this->endTagCache();
                $this->saveToCache('color', $arColors);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        return $arColors;
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
        $order->setBasket($this->basket);
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($order->getBasket() as $item)
        {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }
        $shipment->setField("CURRENCY", $this->currency);
        // получаем доставки с ограничениями
        $res = DelManager::getRestrictedObjectsList($shipment);
        $arDelivery = [];
        Loader::includeModule('qsoft.pvzmap');
        foreach ($res as $deliveryId => $arItem) {
            $arDelivery[$deliveryId] = [
                "ID" => $deliveryId,
                "NAME" => $arItem->getName(),
                "PRICE" => $arItem->calculate()->getPrice(),
                "SORT" => $arItem->getSort(),
            ];
            if (strpos($arItem->getName(), "ПВЗ") !== false) {
                $this->arPvzIds[] = $deliveryId;
            }
        }

        // удаляем ПВЗ для всего, кроме карты
        if ($this->checkType(['1click'])) {
            foreach ($this->arPvzIds as $delId) {
                unset($arDelivery[$delId]);
            }
        }

        $arDelId = array_column($arDelivery, "ID");

        if (array_intersect($this->arPvzIds, $arDelId)) {
            $flag = false; //Флаг присутствия точек ПВЗ для выбранного города
            CBitrixComponent::includeComponentClass("qsoft:pvzmap"); // Подлючаем класс чтобы получить список ПВЗ в городе
            $PVZMap = new PVZMap;

            $arPVZ = $PVZMap->getPVZCollectionByCityAsArray();

            foreach ($arPVZ['PVZ'] as $pvz) {
                if (count($pvz) > 0) {
                    $flag = true;
                    break;
                }
            }
            $arDelivery = $this->filterEmptyPVZDeliveries($arDelivery, $arPVZ['PVZ']);

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
        $this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"] = array_unique($this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"]);
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
                if (isset($this->arResult["WAYS_DELIVERY"][$delivery['WAY_ID']])) {
                    if (!in_array($payment['ID'], $this->arResult["WAYS_DELIVERY"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'])) {
                        $this->arResult["WAYS_DELIVERY"][$delivery['WAY_ID']]['ALLOWED_PAYMENTS'][] = $payment['ID'];
                    }
                    if (in_array($delivery['ID'], $this->arResult["DELIVERY"]["PVZIDS"])) {
                        if (strpos($delivery['NAME'], "ПВЗ") !== false) {
                            foreach ($this->arPVZNames as $name) {
                                if (strpos($delivery['NAME'], $name['NAME']) !== false) {
                                    $this->arResult["WAYS_DELIVERY"][$delivery['WAY_ID']]['ALLOWED_PVZ_PAYMENTS'][$name['CLASS_NAME']][] = $payment['ID'];
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"] = array_unique($this->arResult["PAYMENT"]["ONLINE_PAYMENT_IDS"]);
        return $arPayments;
    }

    // функции заказа
    private function createOrder()
    {
        global $USER;
        global $LOCATION;
        // удаляем купоны у резерва и 1 клика
        $arCoupons = CouponsManager::get(true, array(), true, true);
        if (!empty($arCoupons)) {
            if ($this->checkType(["1click"])) {
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
        if (!array_sum($this->basket->getQuantityList())) {
            die;
        }
        $order->setBasket($this->basket);

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
        $shipmentCollection->calculateDelivery();
        $delPrice = $shipmentCollection->getBasePriceDelivery();
        $price = floor($order->getPrice());

        if ($this->checkType(["order"])) {
            $paymentWay = $this->getPaymentWays($this->arResult["PAYMENT"]["CURRENT"]["ID"]);
            $checkPrice = $price - $delPrice;

            if ($paymentWay['PREPAYMENT'] == 'Y') {
                $order->setField('STATUS_ID', 'N');
                if (($checkPrice >= Option::get("respect", "free_delivery_min_summ"))) {
                    $shipment->setBasePriceDelivery(0);
                    $price = $checkPrice;
                }
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
        $this->postProps["UTM_SOURCE"] = $_COOKIE['utm_source'] ? $_COOKIE['utm_source'] : '';
        $this->postProps["UTM_MEDIUM"] = $_COOKIE['utm_medium'] ? $_COOKIE['utm_medium'] : '';
        $this->postProps["UTM_CAMPAIGN"] = $_COOKIE['utm_campaign'] ? $_COOKIE['utm_campaign'] : '';
        $this->postProps["UTM_CONTENT"] = $_COOKIE['utm_content'] ? $_COOKIE['utm_content'] : '';
        $this->postProps["UTM_TERM"] = $_COOKIE['utm_term'] ? $_COOKIE['utm_term'] : '';
        //$this->postProps["CITY"] = $LOCATION->getName();
        //$this->postProps["AREA"] = $LOCATION->getArea();
        //$this->postProps['REGION'] = $LOCATION->getRegion();

        if ($USER->IsAuthorized()) {
            $this->postProps['EMAIL_PROFILE'] = $this->user['EMAIL'] ? $this->user['EMAIL'] : '';
            $this->postProps['PHONE_PROFILE'] = $this->user['PERSONAL_PHONE'] ? $this->user['PERSONAL_PHONE'] : '';
        }

        if (empty($this->postProps['STREET'])) {
            $this->postProps['STREET'] = $this->postProps['STREET_USER'];
        }
        // парсим номер дома на дом, корпус и строение
        $this->postProps['HOUSE'] = $this->postProps['HOUSE_USER'];

        if (in_array($this->arResult["DELIVERY"]["ID"], $this->arPvzIds)) {
            // очищаем ненужные свойства для ПВЗ
            $this->clearPropsPVZ();
        } else {
            unset($this->postProps['PICKPOINT_ID']);
        }

        // устанавливаем все имеющиеся свойства заказу
        foreach ($this->postProps as $key => $value) {
            if (!empty($value)) {
                if ($key === 'FIO') {
                    $fio = preg_replace('/[\s]{2,}/', ' ', $this->postProps['FIO']);
                    $arFIO = explode(' ', $fio);
                    $prop = $this->getPropByCode('NAME');
                    $prop->setValue($arFIO[0]);
                    $prop = $this->getPropByCode('LAST_NAME');
                    $prop->setValue($arFIO[1]);
                } else {
                    $prop = $this->getPropByCode($key);
                    if ($prop) {
                        $prop->setValue($value);
                    }
                }
            }
        }
        // сохраняем заказ
        $order->doFinalAction(true);
        $res = $order->save();
        $orderId = $order->getId();
        if ($res->isSuccess()) {
            // очищаем купоны из сессии
            $this->delCoupon();
            $_SESSION['NEW_ORDER_ID'] = $orderId;
            $_SESSION['CRITEO_NEW_ORDER_ID'] = $orderId;
            $this->returnOk($orderId, null, $this->offers);
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
        $order->setField('STATUS_ID', 'ZS'); // Устанавливаем статус "Подтвержден, отправить заказ поставщику"
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

    private function getUserDescriptrion()
    {
        switch ($this->type) {
            case "1click":
                return "Заказ создан через один клик";
            default:
                $addInfo = [];
                if ($_REQUEST['PROPS']['PORCH']) {
                    $addInfo[] = 'подъезд ' . $_REQUEST['PROPS']['PORCH'];
                }
                if ($_REQUEST['PROPS']['FLOOR']) {
                    $addInfo[] = 'этаж ' . $_REQUEST['PROPS']['FLOOR'];
                }
                if ($_REQUEST['PROPS']['INTERCOM']) {
                    $addInfo[] = 'код домофона ' . $_REQUEST['PROPS']['INTERCOM'];
                }
                $addInfo = implode(', ', $addInfo);
                return $_REQUEST['USER_DESCRIPTION'] ? $_REQUEST['USER_DESCRIPTION']
                    . ($addInfo ? '| Дополнительная информация: ' . $addInfo : '')
                    : ($addInfo ? '| Дополнительная информация: ' . $addInfo : '');
        }
    }

    private function getOrderType(): string
    {
        switch ($this->type) {
            case "1click":
                return 'ONE_CLICK';
            default:
                return 'ORDER';
        }
    }

    private function clearPropsPVZ()
    {
        $allowedProps = array(
            "FIO" => true,
            "EMAIL" => true,
            "PHONE" => true,
            "PICKPOINT_ID" => true,
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
        if (!empty($this->arResult['ITEMS'])) {
            $this->arResult['PRICE'] = array_sum(array_column($this->arResult['ITEMS'], 'PRICE'));
        }
    }

    // Вычисляет общую сумму офферов по ключу BASKET_PRICE
    private function getSumPrice($arOffers)
    {
        $price = 0;
        foreach ($arOffers as $offer) {
            $price += $offer['BASKET_PRICE'];
        }
        return $price;
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
            foreach ($arDeliveryWay['DELIVERY'] as $delivery) {
                if (strpos($this->arResult["DELIVERY"]["ARRAY"][$delivery]['NAME'], 'ПВЗ') !== false) {
                    if (PVZFactory::checkDeliverySrv($this->arResult["DELIVERY"]["ARRAY"][$delivery])) {
                        $arDeliveryWay['PVZ'] = true;
                        $flag = true;
                    };
                } else {
                    $flag = true;
                }
            }
            if ($flag) {
                $arDeliveryWays[$arDeliveryWay['ID']] = $arDeliveryWay;
            }
        }
        uasort($arDeliveryWays, function ($a, $b) {
            return $a['SORT'] <=> $b['SORT'];
        });

        $this->arResult['WAYS_DELIVERY'] = $arDeliveryWays;
        //Меняем id первой службы доставки (для выбора по умолчанию в шаблоне)
        $this->arResult["DELIVERY"]["ID"] = reset($arDeliveryWays)["DELIVERY"][0];
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
            if ($paymentId) {
                $arPaymentWays = $arPaymentWay;
            } else {
                $arPaymentWays[$arPaymentWay['ID']] = $arPaymentWay;
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
}
