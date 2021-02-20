<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use CSaleOrderProps;

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

        $arFields = [
            "NAME" => "ID 1C Доставка",
            "CODE" => "ID_1C_DELIVERY",
        ];

        $res = CSaleOrderProps::GetList([], ['%CODE' => 'ID_1C'], false, false, ['ID']);
        while ($prop_id = $res->GetNext(true, false)) {
            if (CSaleOrderProps::update($prop_id, $arFields)) {
                $this->outSuccess('Свойство ID(' . $prop_id .') обновлено', $prop_id);
            }
        }

        $arFields = [
            "PERSON_TYPE_ID" => 1,
            "NAME" => "ID 1C Оплата",
            "TYPE" => "NUMBER",
            "CODE" => "ID_1C_PAYMENT",
            "PROPS_GROUP_ID" => 10
        ];

        $ID = CSaleOrderProps::Add($arFields);
        if ($ID > 0) {
            $this->outSuccess('Свойство %s добавлено (%s)', $arFields['NAME'], $ID);
        }

        $arFields['PERSON_TYPE_ID'] = 2;
        $arFields['PROPS_GROUP_ID'] = 11;

        $ID = CSaleOrderProps::Add($arFields);
        if ($ID > 0) {
            $this->outSuccess('Свойство %s добавлено (%s)', $arFields['NAME'], $ID);
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

        $res = CSaleOrderProps::GetList([], ['%CODE' => 'ID_1C_PAYMENT'], false, false, ['ID']);

        while ($prop_id = $res->GetNext()) {
            if (CSaleOrderProps::Delete($prop_id['ID'])) {
                $this->outSuccess('Свойство %s удалено', $prop_id['ID']);
            }
        }
    }
}
