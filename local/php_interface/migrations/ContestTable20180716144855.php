<?php

namespace Sprint\Migration;


class ContestTable20180716144855 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $db = \Bitrix\Main\Application::getConnection();
        $sql = '
        CREATE TABLE IF NOT EXISTS likee_contest (
            ID int(18) not null auto_increment,
            USER_ID int(18) not null,
            ART varchar(255) not null,
            STATUS char(1) not null default \'N\',
            CREATED int(11) not null default \'0\',
            PRIMARY KEY (ID),
            UNIQUE IX_UA (USER_ID, ART)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ';
        $sql2 = '
        CREATE TABLE IF NOT EXISTS likee_contest_list (
            ID int(18) not null auto_increment,
            ART varchar(255) not null,
            PRIMARY KEY (ID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ';
        $db->query($sql);
        $db->query($sql2);

        

        $iIBlockID = $helper->Iblock()->getIblockId('ACTIONS', 'CONTENT');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Включить конкурс',
            'CODE' => 'ENABLE_CONTEST',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'VALUES' => [
                [
                    "XML_ID" => "Y",
                    "VALUE" => "Да",
                    "DEF" => "N",
                    "SORT" => "100"
                ]
            ],
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Завершение конкурса',
            'CODE' => 'CONTEST_END',
            'PROPERTY_TYPE' => 'S:HTML',
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Правила конкурса',
            'CODE' => 'CONTEST_RULES',
            'PROPERTY_TYPE' => 'S:HTML',
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $db = \Bitrix\Main\Application::getConnection();
        $db->query('DROP TABLE IF EXISTS likee_contest');
        $db->query('DROP TABLE IF EXISTS likee_contest_list');

        $iIBlockID = $helper->Iblock()->getIblockId('ACTIONS', 'CONTENT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'ENABLE_CONTEST');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'CONTEST_END');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'CONTEST_RULES');

    }

}
