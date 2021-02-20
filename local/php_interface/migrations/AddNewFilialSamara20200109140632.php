<?php

namespace Sprint\Migration;

class AddNewFilialSamara20200109140632 extends Version
{

    protected $description = "Добавляет в таблицу b_respect_branch Самарский филиал";

    public function up()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "INSERT INTO b_respect_branch (xml_id,name) VALUES ('000000001154','Самарский филиал')";
        $rs = $connection->query($sql);
        return $rs;
    }

    public function down()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "DELETE FROM b_respect_branch WHERE xml_id = '000000001154'";
        $rs = $connection->query($sql);
        return $rs;
    }
}
