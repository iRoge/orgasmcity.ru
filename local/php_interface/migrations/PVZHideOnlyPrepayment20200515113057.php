<?php

namespace Sprint\Migration;

class PVZHideOnlyPrepayment20200515113057 extends Version
{
    protected $description = "Добавляет столбец HIDE_ONLY_PREPAYMENT в таблицу b_qsoft_pvz";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        global $DB;
        $DB->query("ALTER TABLE b_qsoft_pvz ADD HIDE_ONLY_PREPAYMENT VARCHAR (1) NOT NULL DEFAULT 'N'");
    }

    public function down()
    {
        global $DB;
        $DB->query("ALTER TABLE b_qsoft_pvz DROP COLUMN HIDE_ONLY_PREPAYMENT");
    }
}
