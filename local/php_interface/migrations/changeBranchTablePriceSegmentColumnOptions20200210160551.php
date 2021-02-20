<?php

namespace Sprint\Migration;

class changeBranchTablePriceSegmentColumnOptions20200210160551 extends Version
{
    protected $description = "Изменяет свойство столба price_segment_id в таблице b_respect_product_price. Добавляется сегмент Yellow к списку разрешенных значений. Так же добавляет новый сегмент 'Yellow' в HL блок сегментов";

    protected $moduleVersion = "3.12.12";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "ALTER TABLE b_respect_product_price MODIFY COLUMN price_segment_id enum('Red','White','Yellow') NOT NULL";
        $rs1 = $connection->query($sql);
        $sql = "INSERT INTO b_1c_dict_pricesegmentid (UF_XML_ID) VALUES ('Yellow');";
        $rs2 = $connection->query($sql);
        return $rs1 && $rs2;
    }

    public function down()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "ALTER TABLE b_respect_product_price MODIFY COLUMN price_segment_id enum('Red','White') NOT NULL";
        $rs1 = $connection->query($sql);
        $sql = "DELETE FROM b_1c_dict_pricesegmentid WHERE UF_XML_ID = 'Yellow'";
        $rs2 = $connection->query($sql);
        return $rs1;
    }
}
