<?php

namespace Sprint\Migration;


class CatalogBanners220171029130017 extends Version
{

    protected $description = "#10340 Баннеры в каталоге";

    public function up()
    {
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Баннеры в каталоге',
            'CODE' => 'catalog_banners',
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->deletePropertyIfExists($iblockId1, 'SPECIAL');

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Показывать в левом меню',
            'CODE' => 'SPECIAL_MLT',
            'PROPERTY_TYPE' => 'L',
            'VALUES' => [
                [
                    'XML_ID' => 'Y',
                    'VALUE' => 'Да',
                    'DEF' => 'Y'
                ]
            ],
            'LIST_TYPE' => 'C'
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Показывать в правом меню',
            'CODE' => 'SPECIAL_MRT',
            'PROPERTY_TYPE' => 'L',
            'VALUES' => [
                [
                    'XML_ID' => 'Y',
                    'VALUE' => 'Да',
                    'DEF' => 'Y'
                ]
            ],
            'LIST_TYPE' => 'C'
        ]);

    }

    public function down()
    {
        $helper = new HelperManager();

    }

}
