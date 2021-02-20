<?php

namespace Sprint\Migration;


class CatalogGroups_3194_20180814172207 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Акция',
            'CODE' => 'IS_ACTION',
            'PROPERTY_TYPE' => 'L',
            'VALUES' => [
                [
                    'XML_ID' => 'Y',
                    'VALUE' => 'Да',
                ]
            ],
            'LIST_TYPE' => 'C'
        ]);
    }

    public function down() {
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'IS_ACTION');

    }

}
