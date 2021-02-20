<?php

namespace Sprint\Migration;

class AddFivePostInTablePVZ20200415125223 extends Version
{
    protected $description = "Добавляет 5POST в таблицу ПВЗ";

    protected $moduleVersion = "3.12.12";

    public function up()
    {
        global $DB;
        $DB->query("INSERT INTO b_qsoft_pvz(`NAME`, `CLASS_NAME`, `ACTIVE`) VALUES ('5Post (Пятерочка)', 'FivePost', 'Y')");
    }

    public function down()
    {
        global $DB;
        $DB->query("DELETE FROM b_qsoft_pvz WHERE `CLASS_NAME` = 'FivePost'");
    }
}
