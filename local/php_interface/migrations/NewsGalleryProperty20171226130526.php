<?php

namespace Sprint\Migration;


class NewsGalleryProperty20171226130526 extends Version {

    protected $description = "Дополнительное поле для новостного раздела";

    public function up(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('NEWS', 'CONTENT');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'CODE' => 'GALLERY',
            'NAME' => 'Доп. изображения',
            'PROPERTY_TYPE' => 'F',
            'MULTIPLE' => 'Y',
            'FILE_TYPE' => 'jpg, gif, bmp, png, jpeg'
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('NEWS', 'CONTENT');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'GALLERY');
    }

}
