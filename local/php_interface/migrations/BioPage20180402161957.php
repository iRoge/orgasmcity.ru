<?php

namespace Sprint\Migration;


class BioPage20180402161957 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Баннеры BIO',
            'CODE' => 'BIO_BANNERS',
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Ссылка',
            'CODE' => 'LINK',
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('BIO_BANNERS');
    }

}
