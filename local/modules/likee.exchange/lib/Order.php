<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;

use Bitrix\Catalog\StoreTable;
use Bitrix\Sale\Internals\StatusLangTable;
use Bitrix\Sale\Internals\OrderCouponsTable;

/**
 * Класс для работы с заказами. Содержит методы для получения информации по заказу.
 *
 * @package Likee\Exchange
 */
class Order
{
    /**
     * @var array Статусы заказа
     */
    public static $orderStatus = [
        10001 => 'F',
        10014 => 'P',
        10000 => 'N',
        10021 => 'R',
        10011 => 'K',
        10051 => 'A',
        10012 => 'O',
        10050 => 'C',
        10015 => 'D',
        10017 => 'E',
        10033 => 'H',
    ];
    /**
     * @var array Оплата заказа
     */
    public static $orderPayment = [
        12 => 4,
        13 => 5,
        14 => 14,
        1 => 1
    ];
    /**
     * @var array Доставка заказа
     */
    public static $orderDelivery = [
        10 => 2,
        30 => 16,
        8 => 3,
        11 => 17
    ];

    public static $arDadataProps = [
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

    /**
     * Возвращает данные заказа
     *
     * @param integer $iOrder Id заказа
     * @return array|bool Данные заказа или false, если заказ не найден
     */
    public static function getOrderData($iOrder)
    {
        $arOrder = \CSaleOrder::GetByID($iOrder);

        if (!$arOrder) {
            return false;
        }

        $arProps = self::getOrderProps($iOrder);
        $arProducts = self::getOrderProducts($iOrder);
        $arPickup = self::getPickupPoint($iOrder);
        $arUser = self::getUserData($arOrder['USER_ID']);
        $arStatuses = self::getOrderStatuses();
        $arPaySystem = self::getPaySystems();

        $arFio = explode(' ', $arProps['FIO']);
        $arData = [
            'id' => $iOrder,
            'datetime' => date('d.m.Y H:i:s'),
            'status' => [
                'id' => array_flip(self::$orderStatus)[$arOrder['STATUS_ID']],
                'name' => $arStatuses[$arOrder['STATUS_ID']]
            ],
            'user' => [
                'id' => $arUser['id'],
                'name' => $arFio[0] ? : $arUser['name'],
                'last_name' => $arFio[1] ? : $arUser['last_name'],
                'second_name' => $arFio[2] ? : $arUser['second_name'],
                'email' => $arProps['EMAIL'],
                'phone' => $arProps['PHONE'],
                'email_profile' => $arProps['EMAIL_PROFILE'],
                'phone_profile' => $arProps['PHONE_PROFILE'],
            ],
            'delivery' => [
                'id' => $arProps['ORDER_TYPE'] === 'RESERVATION'? array_flip(self::$orderDelivery)[$arOrder['DELIVERY_ID']]: $arProps['ID_1C_DELIVERY'],
                'date' => (empty($arProps['DELIVERY_TIME']) ? '' : $arProps['DELIVERY_TIME']),
                'price' => self::numberFormat($arOrder['PRICE_DELIVERY']),
                'comment' => $arOrder['USER_DESCRIPTION'],
                'address' => [
                    'region' => $arProps['REGION'],
                    'area' => $arProps['AREA'],
                    'city' => $arProps['CITY'],
                    'region_tovara' => $arProps['PRODUCT_REGION'],
                    'city_tovara' => $arProps['PRODUCT_CITY'],
                    'street' => $arProps['STREET'],
                    'homefull' => $arProps['HOUSE_USER'],
                    'home' => $arProps['HOUSE_NUM'],
                    'housing' => $arProps['HOUSING'],
                    'building' => $arProps['STRUCTURE'],
                    'porch' => $arProps['PORCH'],
                    'floor' => $arProps['FLOOR'],
                    'flat' => $arProps['FLAT'],
                    'intercom' => $arProps['INTERCOM'],
                    //'region_fias' => $arProps['REGIONFIAS'],
                    //'area_fias' => $arProps['AREAFIAS'],
                    //'city_fias' => $arProps['CITYFIAS'],
                    //'district_fias' => $arProps['DISTRICTFIAS'],
                    //'settlement_fias' => $arProps['SETTLEMENTFIAS'],
                    //'street_fias' => $arProps['STREETFIAS'],
                    //'unique_id' => $arProps['FIASCODE'],
                    'postcode' => $arProps['POSTALCODE'],
                ]
            ],
            'pay' => [
                'id' => $arProps['ID_1C_PAYMENT'],
                'name' => $arPaySystem[$arOrder['PAY_SYSTEM_ID']]
            ],
            'products' => $arProducts,
            'total_price' => self::numberFormat($arOrder['PRICE'])
        ];

        //добавляем массив свойств из дадаты
        foreach (self::$arDadataProps as $dadataProp) {
            $arData['delivery']['address'][$dadataProp] = $arProps[mb_strtoupper($dadataProp)];
        }

        if ($arPickup) {
            $arData['store']['id'] = $arPickup['XML_ID'];
            $arData['store']['name'] = $arPickup['TITLE'];
        }

        if (isset($arProps['PVZ_ID'])) {
            $arData['delivery']['pvz'] = $arProps['PVZ_ID'];
            $arData['vidzakaza'] = 'пункт выдачи заказа';
        }

        if (!empty($arProps['sotrudnikSozdalZakaz'])) {
            $arData['sotrudnik_sozdal_zakaz'] = $arProps['sotrudnikSozdalZakaz'];
        }

        /* информация по купонам */
        $couponList = OrderCouponsTable::getList([
            'select' => ['COUPON'],
            'filter' => ['=ORDER_ID' => $iOrder]
        ]);
        while ($coupon = $couponList->fetch()) {
            $arData['coupon'] = $coupon['COUPON'];
        }
        unset($coupon, $couponList);

        /* вид заказа */
        $arOrderTypeLabels = [
            'ONE_CLICK' => 'заказ в 1 клик',
            'RESERVATION' => 'резерв в магазине',
            'ORDER' => 'полная регистрация'
        ];
        if (! empty($arProps['ORDER_TYPE']) && isset($arOrderTypeLabels[$arProps['ORDER_TYPE']])) {
            $arData['vidzakaza'] = $arData['vidzakaza'] ?? $arOrderTypeLabels[$arProps['ORDER_TYPE']];
        }

        /* источник */
        if (isset($arProps['ORDER_REFERER'])) {
            parse_str($arProps['ORDER_REFERER'], $UMTData);
            $arData = array_merge($arData, $UMTData);
        }
        return $arData;
    }

    /**
     * Возвращает возможные статусы заказа
     *
     * @return array Массив статусов
     */
    public static function getOrderStatuses()
    {
        $arData = [];
        $rsLang = StatusLangTable::getList([
            'filter' => [
                'LID' => 'ru'
            ]
        ]);

        while ($arLang = $rsLang->fetch()) {
            $arData[$arLang['STATUS_ID']] = $arLang['NAME'];
        }

        return $arData;
    }

    /**
     * Возвращает платежные системы
     *
     * @return array массив платежных систем
     */
    public static function getPaySystems()
    {
        $arData = [];
        $rsPaySystem = \CSalePaySystem::GetList();

        while ($arPaySystem = $rsPaySystem->fetch()) {
            $arData[$arPaySystem['ID']] = $arPaySystem['NAME'];
        }

        return $arData;
    }

    /**
     * Возвращает свойства заказа
     *
     * @param integer $iOrder Id заказа
     * @return array Свойства заказа
     */
    public static function getOrderProps($iOrder)
    {

        $rsProps = \CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $iOrder]);

        $arData = [];
        while ($arProp = $rsProps->Fetch()) {
            if ('DELIVERY_TIME' == $arProp['CODE']) {
                $arPropVariant = \CSaleOrderPropsVariant::GetByValue($arProp['ORDER_PROPS_ID'], $arProp['VALUE']);
                if ($arPropVariant) {
                    $arProp['VALUE'] = $arPropVariant['NAME'];
                }
            }
            
            $arData[$arProp['CODE']] = $arProp['VALUE'];
        }
        return $arData;
    }

    /**
     * Возвращает информацию о пользователе
     *
     * @param integer $iUser Id пользователя
     * @return array Информация о пользователе
     */
    public static function getUserData($iUser)
    {
        $arUser = \CUser::GetByID($iUser)->Fetch();

        return [
            'id' => $iUser,
            'name' => $arUser['NAME'],
            'last_name' => $arUser['LAST_NAME'],
            'second_name' => $arUser['SECOND_NAME'],
            'email' => $arUser['EMAIL'],
            'phone' => $arUser['PERSONAL_PHONE'],
        ];
    }

    /**
     * Возвращает товары заказа
     *
     * @param integer $iOrder Id заказа
     * @return array Товары
     */
    public static function getOrderProducts($iOrder)
    {
        $rsBasket = \CSaleBasket::GetList([], ['ORDER_ID' => $iOrder]);

        $arData = [];

        $arDiscounts = self::getOrderDiscounts($iOrder);
        while ($arBasket = $rsBasket->Fetch()) {
            $arOfferProps = \CIBlockElement::GetByID($arBasket['PRODUCT_ID'])->GetNextElement()->GetProperties();
            $rsProduct = \CIBlockElement::GetByID($arOfferProps['CML2_LINK']['VALUE'])->GetNextElement();
            $arProductFields = $rsProduct->GetFields();
            $arProductProps = $rsProduct->GetProperties();
            $arData[] = [
                'id' => $arProductFields['XML_ID'],
                'artikul' => $arProductProps['ARTICLE']['VALUE'],
                'name' => $arBasket['NAME'],
                'size' => $arOfferProps['SIZE']['VALUE'],
                'price' => self::numberFormat($arBasket['PRICE']),
                'count' => $arBasket['QUANTITY'],
                'total_price' => self::numberFormat($arBasket['PRICE']*$arBasket['QUANTITY']),
                'discounts' => $arDiscounts[$arBasket['PRODUCT_ID']] ?: []
            ];
        }
        return $arData;
    }

    /**
     * Округляет число
     *
     * @param integer|float $number Число
     * @return float Результат округления
     */
    private static function numberFormat($number)
    {
        return round($number);
        return number_format($number, 0, '', ' ');
    }

    /**
     * Возвращает пункт выдачи
     *
     * @param integer $iOrder Id заказа
     * @return array|bool Пункт выдачи или false, если не найден
     */
    public static function getPickupPoint($iOrder)
    {

        $order = \Bitrix\Sale\Order::load($iOrder);
        $arStores = [];
        /** @var \Bitrix\Sale\Shipment $shipment */
        foreach ($order->getShipmentCollection() as $shipment) {
            if (!$shipment->isSystem()) {
                $arStores[] = $shipment->getStoreId();
            }
        }

        $iStore = reset($arStores);
        if ($iStore <= 0) {
            return false;
        }

        $arStore = StoreTable::getRowById($iStore);

        return $arStore;
    }

    /**
     * Возвращает скидки для заказа
     *
     * @param integer $iOrder Id заказа
     * @return array Скидки
     */
    public static function getOrderDiscounts($iOrder)
    {

        $order = \Bitrix\Sale\Order::load($iOrder);
        $discount = $order->getDiscount()->getApplyResult();

        $arDiscount = [];

        $arTitles = [];
        foreach ($discount['DISCOUNT_LIST'] as $ID => $disc) {
            $arTitles[$ID] = $disc['NAME'];
        }

        foreach ($discount['ORDER'] as $orderDiscount) {
            foreach ($orderDiscount['RESULT']['BASKET'] as $orderDiscountValue) {
                $arDiscount[$orderDiscountValue['PRODUCT_ID']][] = [
                    'id' => $orderDiscount['DISCOUNT_ID'],
                    'name' => $arTitles[$orderDiscount['DISCOUNT_ID']],
                    'price' => reset($orderDiscountValue['DESCR_DATA'])['RESULT_VALUE']
                ];
            }
        }

        foreach ($discount['BASKET'] as $basketDiscounts) {
            foreach ($basketDiscounts as $basketDiscount) {
                $arDiscount[$basketDiscount['PRODUCT_ID']][] = [
                    'id' => $basketDiscount['DISCOUNT_ID'],
                    'name' => $arTitles[$basketDiscount['DISCOUNT_ID']],
                    'price' => reset($orderDiscountValue['DESCR_DATA'])['RESULT_VALUE']
                ];
            }
        }

        return $arDiscount;
    }

    public static function getIstochnikData($url)
    {
        $arIstochnik = [
            'СМС' => '/utm_referrer=rshoes\.ru/i',
            'прямые заходы' => '/respect-shoes\.ru/i',
            'реклама' => '/utm_medium=cp[cm]/i',
            'поиск' => '/(:?www\.)?(?:yandex|google|rambler|mail|bing|yahoo|duckduckgo|1and1)\.[a-z]{2,4}/i',
            'соц сети' => '/(:?www\.)?(?:facebook|instagram|vk|ok|pinterest|diary|linkedin)\.[a-z]{2,4}/i',
            'ссылки с сайта' => '/(:?www\.)?.+\.[a-z]{2,4}/i',
        ];

        $arResult = [];
        $arResult['istochnik'] = 'прямые заходы';

        if (! empty($url) && ($arUrlParts = parse_url($url))) {
            $arResult['IstochnikDetal'] = $arUrlParts['host'];

            foreach ($arIstochnik as $sName => $sRegexp) {
                if (preg_match($sRegexp, $url)) {
                    $arResult['istochnik'] = $sName;

                    if ('СМС' == $sName) {
                        $arResult['IstochnikDetal'] = $sName;
                    }
                    break;
                }
            }
        } else {
            $arResult['IstochnikDetal'] = 'respect-shoes.ru';
        }

        return $arResult;
    }

    public static function getOrderPayment($orderId, $orderNumber)
    {
        $order = \Bitrix\Sale\Order::load($orderId);
        $payment = $order->getPaymentCollection()[0];
        $obPayment = $payment->getPaySystem();

        $arData = [
            'id' => $orderId,
            'pay' => [
                'id' => $obPayment->getField('ID'),
                'order_number' => $orderNumber,
            ],
        ];

        $result['arData'] = $arData;
        $result['paymentTag'] = $obPayment->getField('CODE') == 'SBERBANK' ? '<order_sberbank></order_sberbank>' : '';

        return $result;
    }
}
