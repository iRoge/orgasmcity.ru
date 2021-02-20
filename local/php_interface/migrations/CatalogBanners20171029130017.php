<?php

namespace Sprint\Migration;


class CatalogBanners20171029130017 extends Version
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


        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Ссылка с кнопки',
            'CODE' => 'BUTTON_LINK',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Текст кнопки',
            'CODE' => 'BUTTON_NAME',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Раздел',
            'CODE' => 'SECTION',
            'PROPERTY_TYPE' => 'G',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Код спецраздела (для левого и правого меню)',
            'CODE' => 'SPECIAL',
        ]);


        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Расположить сверху (только для спец разделов)',
            'CODE' => 'SPECIAL_POSITION',
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

        $ok = $helper->Iblock()->deleteIblockIfExists('catalog_banners');

    }

}
