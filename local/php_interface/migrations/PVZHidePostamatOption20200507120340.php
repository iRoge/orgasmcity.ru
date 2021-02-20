<?php

namespace Sprint\Migration;

class PVZHidePostamatOption20200507120340 extends Version
{
    protected $description = "Добавляет столбец HIDE_POSTAMAT в таблицу b_qsoft_pvz";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        global $DB;
        $DB->query("ALTER TABLE b_qsoft_pvz ADD HIDE_POSTAMAT VARCHAR (1) NOT NULL DEFAULT 'N'");
    }

    public function down()
    {
        global $DB;
        $DB->query("ALTER TABLE b_qsoft_pvz DROP COLUMN HIDE_POSTAMAT");
    }
}
