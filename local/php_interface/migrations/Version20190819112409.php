<?php

namespace Sprint\Migration;

use Bitrix\Main\Db\SqlQueryException;

class Version20190819112409 extends Version
{

    protected $description = "Создание таблицы токенов подписки";

    public function up()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->Sql()->query("
                CREATE TABLE IF NOT EXISTS qsoft_subscribe_token (
                id int(11) unsigned NOT NULL AUTO_INCREMENT,
                email varchar(255) NOT NULL,
                token varchar(32) NOT NULL,
                date_create DATETIME NOT NULL,
                user_id mediumint unsigned NOT NULL,
                CONSTRAINT qsoft_subscribe_token_PK PRIMARY KEY (id)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci;");
        } catch (SqlQueryException $e) {
            $this->outError($e->getDatabaseMessage());

            return false;
        }

        return true;
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        try {
            $helper->Sql()->query("DROP TABLE IF EXISTS qsoft_subscribe_token");
        } catch (SqlQueryException $e) {
            $this->outError($e->getDatabaseMessage());

            return false;
        }

        return true;
    }
}
