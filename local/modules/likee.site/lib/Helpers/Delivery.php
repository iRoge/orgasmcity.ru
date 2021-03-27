<?php
/**
 * User: Azovcev Artem
 * Date: 24.04.17
 * Time: 16:16
 */

namespace Likee\Site\Helpers;


use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location\LocationTable;

/**
 * Класс для работы с доставкой. Содержит метод определяющий стоимость доставки.
 *
 * @package Likee\Site\Helpers
 */
class Delivery
{
    /**
     * Служба доставки
     */
    const DELIVERY_PICKUP_ID = 3;

    /**
     * Возвращает минимальную цену доставки до города
     *
     * @param string $sCityCode Код города
     * @param string $sCurrency Валюта
     * @return float|false Если есть возвращается цена, иначе false
     */
    public static function getMinDeliveryPriceForCity($sCityCode = '', $sCurrency = 'RUB')
    {
        global $USER;

        $iPrice = 0;

        if (!Loader::includeModule('sale'))
            return $iPrice;

        $obCache = Application::getCache();

        if ($obCache->initCache(3600 * 24, 'min_delivery_price_' . md5($sCityCode . '|' . $sCurrency . '|' . $USER->GetGroups()))) {
            $arVars = $obCache->getVars();
            $iPrice = $arVars['MIN_PRICE'];
        } else {
            $iPersonalType = \Likee\Site\User::getPersonalTypeId();
            $order = \Bitrix\Sale\Order::create(SITE_ID, 0, $sCurrency);
            $order->setPersonTypeId($iPersonalType);

            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $shipment->setField('CURRENCY', $sCurrency);

            if (strlen($sCityCode) > 0) {
                /** @var \Bitrix\Sale\PropertyValue $prop */
                foreach ($order->getPropertyCollection() as $prop) {
                    if ($prop->getPersonTypeId() != $iPersonalType)
                        continue;

                    $arProperty = $prop->getProperty();

                    if ($arProperty['IS_LOCATION'] == 'Y') {
                        $arLoc = LocationTable::getRow(['filter' => ['CODE' => $sCityCode]]);
                        if ($arLoc)
                            $prop->setValue($sCityCode);
                        break;
                    }
                }
            }

            $arServices = \Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);

            $arPrices = [];
            foreach ($arServices as $deliveryService) {
                if ($deliveryService->getId() == self::DELIVERY_PICKUP_ID)
                    continue;

                $calcResult = $deliveryService->calculate($shipment);
                if ($calcResult->isSuccess()) {
                    $arPrices[] = floatval($calcResult->getDeliveryPrice() + $calcResult->getExtraServicesPrice());
                }
            }

            $iPrice = count($arPrices) > 0 ? min($arPrices) : false;

            if ($obCache->startDataCache()) {
                $obCache->endDataCache(['PRICE' => $iPrice]);
            }
        }

        return $iPrice;
    }
}