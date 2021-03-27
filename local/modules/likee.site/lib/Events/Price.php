<?php
/**
 * User: Azovcev Artem
 * Date: 07.12.16
 * Time: 15:53
 */
namespace Likee\Site\Events;

use Bitrix\Main\Loader;

/**
 * Класс для обработки событий цен. Обрабатывает события изменения цены для обмена.
 *
 * @package Likee\Site\Events
 */
class Price
{
    /**
     * Обновление минимальной цены товара
     *
     * @param integer $ID Id Товара
     * @param array $arFields Товар
     */
    public static function updateMinPrice($ID, $arFields)
    {
        if ($arFields['PRODUCT_ID'] > 0) {
            Loader::includeModule('iblock');
            Loader::includeModule('catalog');

            $arModel = \CIBlockElement::GetList(
                [],
                [
                    '=ID' => \CIBlockElement::SubQuery('PROPERTY_CML2_LINK', ['ID' => $arFields['PRODUCT_ID']])
                ],
                false,
                false,
                ['ID', 'IBLOCK_ID']
            )->Fetch();

            if ($arModel) {
                $iPriceId = 7;

                $iSegmentPriceId = 8;
                $iSegmentPct = 0;

                $arOffer = \CIBlockElement::GetList(
                    ['CATALOG_PRICE_' . $iPriceId => 'ASC', 'CATALOG_PRICE_' . $iSegmentPriceId => 'ASC'],
                    [
                        'IBLOCK_ID' => IBLOCK_OFFERS,
                        '=PROPERTY_CML2_LINK' => $arModel['ID'],
                        '>CATALOG_PRICE_' . $iPriceId => 0
                    ],
                    false,
                    ['nTopCount' => 1],
                    ['ID', 'IBLOCK_ID', 'CATALOG_PRICE_' . $iPriceId, 'CATALOG_PRICE_' . $iSegmentPriceId]
                )->Fetch();

                if (! empty($arOffer['CATALOG_PRICE_' . $iSegmentPriceId]) && 0 < $arOffer['CATALOG_PRICE_' . $iSegmentPriceId]) {
                    $iPrice = (int) $arOffer['CATALOG_PRICE_' . $iPriceId];
                    $iSegmentPrice = (int) $arOffer['CATALOG_PRICE_' . $iSegmentPriceId];

                    if ($iPrice < $iSegmentPrice) {
                        $iSegmentPct = floor(($iSegmentPrice-$iPrice)*100/$iSegmentPrice);
                    }
                }

                \CIBlockElement::SetPropertyValuesEx($arModel['ID'], $arModel['IBLOCK_ID'], [
                    'MINIMUM_PRICE' => intval($arOffer['CATALOG_PRICE_' . $iPriceId]),
                    'SEGMENT_PCT' => $iSegmentPct
                ]);
            }
        }
    }

    /**
     * Обновление минимальной цены для обмена
     *
     * @param string $sTask Имя задачи
     * @param string $sXML Внешний код элемента
     * @param array $arFields Товар
     */
    public static function updateMinPriceForExchange($sTask, $sXML, $arFields)
    {
        self::updateMinPrice(0, ['PRODUCT_ID' => $arFields['PRODUCT_ID']]);
    }
}