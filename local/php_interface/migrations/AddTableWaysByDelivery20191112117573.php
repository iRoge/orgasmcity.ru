<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

class AddTableWaysByDelivery20191112117573 extends Version
{

    protected $description = "Создал таблицу в которой хранятся способы доставки. А также таблицу в которой храниться привязка способов доставки к службам доставки.";
    private $WaysEntity = '\Qsoft\DeliveryWays\WaysDeliveryTable';
    private $LinkEntity = '\Qsoft\DeliveryWays\WaysByDeliveryServicesTable';

    public function up()
    {
        if (!Application::getConnection()->isTableExists(Base::getInstance($this->WaysEntity)->getDBTableName())) {
            Base::getInstance($this->WaysEntity)->createDBTable();
        }

        if (!Application::getConnection()->isTableExists(Base::getInstance($this->LinkEntity)->getDBTableName())) {
            Base::getInstance($this->LinkEntity)->createDBTable();
        }
    }

    public function down()
    {
        if (Application::getConnection()->isTableExists(Base::getInstance($this->WaysEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->WaysEntity)->getDBTableName());
        }

        if (Application::getConnection()->isTableExists(Base::getInstance($this->LinkEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->LinkEntity)->getDBTableName());
        }
    }
}
