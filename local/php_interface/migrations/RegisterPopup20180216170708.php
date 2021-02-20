<?php

namespace Sprint\Migration;


class RegisterPopup20180216170708 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Окно после регистрации',
            'CODE' => 'REG_POPUP',
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Ссылка для изображения',
            'CODE' => 'URL',
            'PROPERTY_TYPE' => 'S',
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('REG_POPUP');
    }

}
