<?php

namespace Sprint\Migration;


class CityDomains20180822145059 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'SEO данные городов',
            'CODE' => 'CATALOG_CITY_SEO',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('CATALOG_CITY_SEO');

    }

}
