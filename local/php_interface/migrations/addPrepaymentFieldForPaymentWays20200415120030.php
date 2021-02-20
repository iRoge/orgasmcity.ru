<?php

namespace Sprint\Migration;

class addPrepaymentFieldForPaymentWays20200415120030 extends Version
{
    protected $description = "Добавляет поле \"Предоплата\" для блоков оплаты";

    public function up()
    {
        global $DB;

        $local = $DB->Query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'b_qsoft_ways_payment' AND column_name = 'PREPAYMENT'")->Fetch();
        if (!$local) {
            $DB->Query("ALTER TABLE b_qsoft_ways_payment ADD PREPAYMENT varchar(1);");
        }
    }

    public function down()
    {
        global $DB;

        $local = $DB->Query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'b_qsoft_ways_payment' AND column_name = 'PREPAYMENT'")->Fetch();
        if ($local) {
            $DB->Query("ALTER TABLE b_qsoft_ways_payment DROP COLUMN PREPAYMENT");
        }
    }
}
