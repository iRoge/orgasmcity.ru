<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

class AddTablePaySystem2Locations20191011093145 extends Version
{

    protected $description = "Таблица в которой хранятся привязки платежных систем к местоположениям.";
    private $entity_name = '\Qsoft\PaySystemLocations\PaySystemLocationTable';

    public function up()
    {
        if (!Application::getConnection()->isTableExists(Base::getInstance($this->entity_name)->getDBTableName())) {
            Base::getInstance($this->entity_name)->createDBTable();
        }
    }

    public function down()
    {
        if (Application::getConnection()->isTableExists(Base::getInstance($this->entity_name)->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance($this->entity_name)->getDBTableName());
        }
    }
}
