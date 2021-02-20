<?php

namespace Sprint\Migration;

class AddManufacturerInProduct20200213095346 extends Version
{
    protected $description = "Добавляет поле производитель в ИБ Товары";

    protected $moduleVersion = "3.12.12";

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('CATALOG', 'test');
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Производитель',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'PROIZVODITEL',
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
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('CATALOG', 'test');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'PROIZVODITEL');
    }
}
