<?php

namespace Sprint\Migration;


use CIBlockType;

class CatalogGroups20171227165643 extends Version {

    protected $description = "Инфоблок для группировки товаров";

    public function up(){
        $helper = new HelperManager();

        $iblockId = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Группировки',
            'CODE' => 'CATALOG_GROUPS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
            'LID' => 's1'
        ]);

        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Производитель',
            'ACTIVE' => 'Y',
            'SORT' => '1650',
            'CODE' => 'vendor',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '43',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'ElementXmlID',
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Коллекция',
            'ACTIVE' => 'Y',
            'SORT' => '1700',
            'CODE' => 'collection',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '60',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Хит',
            'ACTIVE' => 'Y',
            'SORT' => '1800',
            'CODE' => 'bestseller',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '61',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
            'VALUES' =>
                [
                    0 =>
                        [
                            'VALUE' => '0',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'b2b5d5fc355a39a0809c93e394af4d81',
                        ],
                    1 =>
                        [
                            'VALUE' => '1',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => '8bf8bbcdddddb02a644034ebe6475f8e',
                        ],
                    2 =>
                        [
                            'VALUE' => 'bestseller',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'baca29f737dfe4275467d38278347b6a',
                        ],
                ],
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Новинка',
            'ACTIVE' => 'Y',
            'SORT' => '1900',
            'CODE' => 'new',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '62',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
            'VALUES' =>
                [
                    0 =>
                        [
                            'VALUE' => '0',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => '945f7304f29774c3ac7880660f512ed3',
                        ],
                    1 =>
                        [
                            'VALUE' => '1',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'a94ae62b87eeeaa8b01ac2bcb7a15081',
                        ],
                    2 =>
                        [
                            'VALUE' => 'new',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => '647b4d554701de11112d69b35a51ae6e',
                        ],
                ],
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Год выпуска',
            'ACTIVE' => 'Y',
            'SORT' => '2400',
            'CODE' => 'year',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '67',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ]);
    }

    public function down() {
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('CATALOG_GROUPS');
    }
}
