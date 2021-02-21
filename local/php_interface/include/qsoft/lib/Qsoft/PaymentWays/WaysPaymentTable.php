<?php

namespace Qsoft\PaymentWays;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\SystemException;

class WaysPaymentTable extends Entity\DataManager
{
    private static $table_name = 'b_qsoft_ways_payment';
    private static $link_payment_field = 'WAY';

    public static function getTableName()
    {
        return self::$table_name;
    }

    public static function getMap()
    {
        try {
            return [
                (new Entity\IntegerField('ID'))
                    ->configurePrimary(true)
                    ->configureAutocomplete(true)
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_ID_FIELD')),

                //TODO Добавить валидатор
                (new Entity\StringField('NAME'))
                    ->configureRequired('true')
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_NAME_FIELD')),

                (new Entity\BooleanField('ACTIVE'))
                    ->configureRequired(true)
                    ->configureStorageValues('N', 'Y')
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_ACTIVE_FIELD')),

                (new Entity\BooleanField('LOCAL'))
                    ->configureRequired(true)
                    ->configureStorageValues('N', 'Y')
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_LOCAL_FIELD')),

                (new Entity\BooleanField('PREPAYMENT'))
                    ->configureRequired(true)
                    ->configureStorageValues('N', 'Y')
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_PREPAYMENT_FIELD')),

                (new Entity\StringField('DESCRIPTION'))
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_DESCRIPTION_FIELD')),

                (new Entity\IntegerField('SORT'))
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_SORT_FIELD')),

                (new OneToMany('PAYMENTS', WaysByPaymentServicesTable::class, self::$link_payment_field))
                    ->configureTitle(Loc::getMessage('PAYMENT_WAY_ENTITY_PAYMENTS_FIELD'))
            ];
        } catch (SystemException $e) {
            return [];
        }
    }
}
