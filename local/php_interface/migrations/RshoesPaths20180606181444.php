<?php

namespace Sprint\Migration;


class RshoesPaths20180606181444 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $db = \Bitrix\Main\Application::getConnection();
        $sql = '
        CREATE TABLE IF NOT EXISTS `likee_rshoes_redirects` (
            `id` int(11) NOT NULL,
            `path` varchar(255) NOT NULL,
            `url` varchar(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ';
        $db->query($sql);

    }

    public function down(){
        $helper = new HelperManager();

        $db = \Bitrix\Main\Application::getConnection();
        $sql = 'DROP TABLE IF EXISTS `likee_rshoes_redirects`';
        $db->query($sql);

    }

}
