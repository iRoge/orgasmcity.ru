<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

class AddTableWaysByPayment20200227127873 extends Version
{

    protected $description = "Создал таблицу в которой хранятся способы оплаты. А также таблицу в которой храниться привязка способов оплаты к службам оплаты.";
    private $WaysEntity = '\Qsoft\PaymentWays\WaysPaymentTable';
    private $LinkEntity = '\Qsoft\PaymentWays\WaysByPaymentServicesTable';

    public function up()
    {
        global $DB;

        if (!Application::getConnection()->isTableExists(Base::getInstance($this->WaysEntity)->getDBTableName())) {
            Base::getInstance($this->WaysEntity)->createDBTable();
        }

        if (!Application::getConnection()->isTableExists(Base::getInstance($this->LinkEntity)->getDBTableName())) {
            Base::getInstance($this->LinkEntity)->createDBTable();
        }

        $local = $DB->Query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'b_qsoft_ways_delivery' AND column_name = 'LOCAL'")->Fetch();
        if (!$local) {
            $DB->Query("ALTER TABLE b_qsoft_ways_delivery ADD LOCAL varchar(1);");
        }
    }

    public function down()
    {
        global $DB;

        if (Application::getConnection()->isTableExists(Base::getInstance($this->WaysEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->WaysEntity)->getDBTableName());
        }

        if (Application::getConnection()->isTableExists(Base::getInstance($this->LinkEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->LinkEntity)->getDBTableName());
        }

        $local = $DB->Query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'b_qsoft_ways_delivery' AND column_name = 'LOCAL'")->Fetch();
        if ($local) {
            $DB->Query("ALTER TABLE b_qsoft_ways_delivery DROP COLUMN LOCAL");
        }
    }
}
