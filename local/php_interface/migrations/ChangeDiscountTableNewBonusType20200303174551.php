<?php

namespace Sprint\Migration;

class ChangeDiscountTableNewBonusType20200303174551 extends Version
{
    protected $description = "Изменяет свойство столба user_status в таблице qsoft_discounts_rules. Добавляется тип 4 к списку разрешенных значений.";

    protected $moduleVersion = "3.12.12";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "ALTER TABLE qsoft_discounts_rules MODIFY COLUMN user_status enum('0','1','2','3','4') NOT NULL DEFAULT '0'";
        $rs1 = $connection->query($sql);
        return $rs1;
    }

    public function down()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "ALTER TABLE qsoft_discounts_rules MODIFY COLUMN user_status enum('0','1','2','3') NOT NULL DEFAULT '0'";
        $rs1 = $connection->query($sql);
        return $rs1;
    }
}
