<?php

namespace Sprint\Migration;


class ActionsSubscribe20180605122703 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('ACTIONS', 'CONTENT');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Включить подписку в акции',
            'CODE' => 'ENABLE_SUBSCRIBE',
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
    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('ACTIONS', 'CONTENT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'ENABLE_SUBSCRIBE');
    }

}
