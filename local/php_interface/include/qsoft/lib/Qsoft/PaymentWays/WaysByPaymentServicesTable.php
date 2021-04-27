<?php

namespace Qsoft\PaymentWays;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Internals\PaySystemActionTable as PaymentTable;

class WaysByPaymentServicesTable extends Entity\DataManager
{
    private static $table_name = 'b_qsoft_ways_by_payment_services';

    public static function getTableName()
    {
        return self::$table_name;
    }

    public static function getMap()
    {
        try {
            return [
                (new Entity\IntegerField('WAY_ID'))
                    ->configurePrimary(true),

                (new Reference(
                    'WAY',
                    WaysPaymentTable::class,
                    Join::on('this.WAY_ID', 'ref.ID')
                ))
                    ->configureJoinType('inner'),

                (new Entity\IntegerField('PAYMENT_ID'))
                    ->configurePrimary(true),

                (new Reference(
                    'PAYMENT',
                    WaysPaymentTable::class,
                    Join::on('this.PAYMENT_ID', 'ref.ID')
                ))
                    ->configureJoinType('inner'),

                (new Entity\IntegerField('ID_1C'))
            ];
        } catch (ArgumentException $e) {
            return [];
        } catch (SystemException $e) {
            return [];
        }
    }

    public static function getPaymentObject($payment_id)
    {
        return PaymentTable::getByPrimary($payment_id, ['select' => ['*']])->fetchObject();
    }
    public static function getPaymentArray($payment_id)
    {
        return PaymentTable::getByPrimary($payment_id, ['select' => ['*']])->fetch();
    }
}
