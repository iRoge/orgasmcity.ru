<?php

namespace Sprint\Migration;


class CatalogBottomBanners20180817185601 extends Version {

    protected $description = "Произвольные блоки баннеры внизу страницы";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Настраиваемые блоки',
            'CODE' => 'BLOCK_BANNERS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Размер блока',
            'CODE' => 'BLOCK_SIZE',
            'PROPERTY_TYPE' => 'L',
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'SORT' => '120',
            'VALUES' => [
                [
                    "XML_ID" => "25",
                    "VALUE" => "Одинарный",
                    "DEF" => "Y",
                    "SORT" => "100"
                ],
                [
                    "XML_ID" => "5",
                    "VALUE" => "Двойной",
                    "DEF" => "N",
                    "SORT" => "200"
                ],
            ],
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Адрес перехода',
            'CODE' => 'LINK',
            'PROPERTY_TYPE' => 'S',
            'SORT' => '300',
            'HINT' => 'Ссылка для перехода при клике'
        ]);


        $iGroupsIBlockID = $helper->Iblock()->getIblockId('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Группировки',
            'CODE' => 'BLOCK_GROPUS',
            'PROPERTY_TYPE' => 'E',
            'SORT' => '400',
            'MULTIPLE' => 'Y',
            'LINK_IBLOCK_ID' => $iGroupsIBlockID,
            'HINT' => 'Укажите группировки на сттраницах которого будет выводится данный блок'
        ]);
    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('BLOCK_BANNERS');

    }

}
