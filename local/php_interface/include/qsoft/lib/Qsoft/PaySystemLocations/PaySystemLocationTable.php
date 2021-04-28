<?php

namespace Qsoft\PaySystemLocations;

use Bitrix\Sale\Location\Connector;

final class PaySystemLocationTable extends Connector
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_qsoft_sale_pay_system2location';
    }

    public function getLinkField()
    {
        return 'PAY_SYSTEM_ID';
    }

    public static function getLocationLinkField()
    {
        return 'LOCATION_CODE';
    }

    public function getTargetEntityName()
    {
        return 'Bitrix\Sale\Internals\PaySystemAction';
    }

    public static function getMap()
    {
        return array(
            
            'PAY_SYSTEM_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'primary' => true
            ),
            'LOCATION_CODE' => array(
                'data_type' => 'string',
                'required' => true,
                'primary' => true
            ),
            'LOCATION_TYPE' => array(
                'data_type' => 'string',
                'default_value' => self::DB_LOCATION_FLAG,
                'required' => true,
                'primary' => true
            ),

            // virtual
            'LOCATION' => array(
                'data_type' => '\Bitrix\Sale\Location\Location',
                'reference' => array(
                    '=this.LOCATION_CODE' => 'ref.CODE',
                    '=this.LOCATION_TYPE' => array('?', self::DB_LOCATION_FLAG)
                )
            ),
            'GROUP' => array(
                'data_type' => '\Bitrix\Sale\Location\Group',
                'reference' => array(
                    '=this.LOCATION_CODE' => 'ref.CODE',
                    '=this.LOCATION_TYPE' => array('?', self::DB_GROUP_FLAG)
                )
            ),

            'PAY_SYSTEM' => array(
                'data_type' => static::getTargetEntityName(),
                'reference' => array(
                    '=this.PAY_SYSTEM_ID' => 'ref.ID'
                )
            ),
        );
    }
}
