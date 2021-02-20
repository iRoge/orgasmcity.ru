<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;

class FittinAuth20190815173609 extends Version
{
    protected $description = 'Создаёт таблицу для хранения аутентификационных данных приложения примерки "Fittin".';

    public function up()
    {
        $database = Application::getConnection();
        $database->query(
            'CREATE TABLE IF NOT EXISTS fittin_auth (
                USER_ID mediumint(8) UNSIGNED NOT NULL PRIMARY KEY, 
                TOKEN char(32) NOT NULL, 
                HASH char(32) NOT NULL, 
                AUTH_CODE char(32) NOT NULL, 
                AUTH_CODE_CREATION_TIME datetime NOT NULL
            )'
        );
    }

    public function down()
    {
        $database = Application::getConnection();
        $database->query('DROP TABLE IF EXISTS fittin_auth');
    }
}
