<?php


namespace Qsoft\Pvzmap;

use Bitrix\Main\Entity;

class PVZTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_qsoft_pvz';
    }

    public static function getMap()
    {
        return [
            (new Entity\IntegerField('ID'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),

            (new Entity\StringField('NAME'))
                ->configureRequired(true),

            (new Entity\StringField('CLASS_NAME'))
                ->configureRequired(true),

            (new Entity\BooleanField('ACTIVE'))
                ->configureStorageValues('N', 'Y'),

            (new Entity\BooleanField('HIDE_POSTAMAT'))
                ->configureStorageValues('N', 'Y'),

            (new Entity\BooleanField('HIDE_ONLY_PREPAYMENT'))
                ->configureStorageValues('N', 'Y'),

            (new Entity\BooleanField('HIDE_POST'))
                ->configureStorageValues('N', 'Y'),
        ];
    }
}
