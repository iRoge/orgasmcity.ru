<?php

namespace Sprint\Migration;


class LikeePromoSectionsSeo20180202161148 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'SEO для спецразделов',
            'CODE' => 'SEO_PROMO',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
            'SORT' => 1000
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'NAME' => 'Адрес страницы',
            'CODE' => 'URL',
            'PROPERTY_TYPE' => 'S',
            'SORT' => '100',
            'HINT' => 'Часть адреса спецраздела ( пример /catalog/НУЖНАЯ-ЧАСТЬ )',
            'COL_COUNT' => '50'
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('SEO_PROMO');

    }

}
