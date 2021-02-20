<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use CSaleOrderProps;

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

        $arFields = [
            "PERSON_TYPE_ID" => 1,
            "NAME" => "ID 1C заказа",
            "TYPE" => "NUMBER",
            "CODE" => "ID_1C",
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
        if (Application::getConnection()->isTableExists(Base::getInstance($this->WaysEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->WaysEntity)->getDBTableName());
        }

        if (Application::getConnection()->isTableExists(Base::getInstance($this->LinkEntity)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->LinkEntity)->getDBTableName());
        }

        $res = CSaleOrderProps::GetList([], ['%CODE' => 'ID_1C'], false, false, ['ID']);

        while ($prop_id = $res->GetNext()) {
            if (CSaleOrderProps::Delete($prop_id['ID'])) {
                $this->outSuccess('Свойство %s удалено', $prop_id['ID']);
            }
        }
    }
}
