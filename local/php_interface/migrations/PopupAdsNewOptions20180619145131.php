<?php

namespace Sprint\Migration;


class PopupAdsNewOptions20180619145131 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('POPUP_ADS', 'CONTENT');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'CODE' => 'USE_SUBSCRIBE',
            'NAME' => 'Разместить поле для подписки',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'C',
            'VALUES' => [
                [
                    'VALUE' => 'Да',
                    'XML_ID' => 'Y'
                ]
            ]
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'CODE' => 'SHOW_INTERVAL',
            'NAME' => 'Интервал между показом (секунд)',
            'PROPERTY_TYPE' => 'N',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Кому показывать',
            'CODE' => 'USERS',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'VALUES' => [
                [
                    "XML_ID" => "all",
                    "VALUE" => "Всем",
                    "SORT" => "100",
                    "DEF" => "Y"
                ],
                [
                    "XML_ID" => "auth",
                    "VALUE" => "Авторизованным",
                    "SORT" => "200"
                ],
                [
                    "XML_ID" => "anonim",
                    "VALUE" => "Неавторизованным",
                    "SORT" => "300"
                ]
            ],
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('POPUP_ADS', 'CONTENT');

        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'USE_SUBSCRIBE');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SHOW_INTERVAL');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'USERS');

    }

}
