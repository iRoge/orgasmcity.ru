<?php


namespace Sprint\Migration;

use Bitrix\Main\Db\SqlQueryException;

class Version20190830155000 extends Version
{
    protected $description = "Скидки по бонусной программе";

    public function up()
    {
        $helper = new HelperManager();
        try {
            $helper->Sql()->query("
                create table if not exists qsoft_discounts_rules
                (
                    id          int auto_increment primary key,
                    user_status enum ('0', '1', '2', '3') default '0'   not null,
                    brand       varchar(255)                     default 'All' not null,
                    branch      varchar(255)                      default 'All' not null,
                    typeproduct varchar(255)                      default 'All' not null,
                    vid         varchar(255)                      default 'All' not null,
                    discount    tinyint(3)                default 0     not null,
                    active      tinyint(3)                default 0     not null,
                    constraint qsoft_discounts_rules_id_uindex
                    unique (id)
                );
            ");
        } catch (SqlQueryException $e) {
            $this->outError($e->getDatabaseMessage());

            return false;
        }

        return true;
    }

    public function down()
    {
        $helper = new HelperManager();

        try {
            $helper->Sql()->query("drop table if exists qsoft_discounts_rules");
        } catch (SqlQueryException $e) {
            $this->outError($e->getDatabaseMessage());

            return false;
        }

        return true;
    }
}
