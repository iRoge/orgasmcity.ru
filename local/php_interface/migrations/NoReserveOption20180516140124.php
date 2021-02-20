<?php

namespace Sprint\Migration;


class NoReserveOption20180516140124 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Отключить возможность резервирования',
            'CODE' => 'NO_RESERVE',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'VALUES' => [
                [
                    "XML_ID" => "Y",
                    "VALUE" => "Да",
                    "DEF" => "Y",
                    "SORT" => "100"
                ]
            ],
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'NO_RESERVE');
    }

}
