<?php

namespace Sprint\Migration;

class EditPRICESEGMENTIDMultiplePropertyForGroupIblock20200313150047 extends Version
{
    protected $description = "Меняет тип свойства PRICESEGMENTID с одиночного на множественный";

    protected $moduleVersion = "3.13.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->saveProperty($iblockId, array (
            'NAME' => 'PriceSegmentID',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'PRICESEGMENTID',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'Y',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '2',
            'USER_TYPE' => 'directory',
            'USER_TYPE_SETTINGS' =>
                array (
                    'size' => 1,
                    'width' => 0,
                    'group' => 'N',
                    'multiple' => 'N',
                    'TABLE_NAME' => 'b_1c_dict_pricesegmentid',
                ),
            'HINT' => '',
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('CATALOG_GROUPS', 'SYSTEM');
        $helper->Iblock()->saveProperty($iblockId, array (
            'NAME' => 'PriceSegmentID',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'PRICESEGMENTID',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '2',
            'USER_TYPE' => 'directory',
            'USER_TYPE_SETTINGS' =>
                array (
                    'size' => 1,
                    'width' => 0,
                    'group' => 'N',
                    'multiple' => 'N',
                    'TABLE_NAME' => 'b_1c_dict_pricesegmentid',
                ),
            'HINT' => '',
        ));
    }
}
