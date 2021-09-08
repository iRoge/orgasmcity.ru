<?php

namespace Sprint\Migration;


use CIBlockProperty;

class AddLastBuyTimePropertyForProduct20210908151821 extends Version
{
    protected $description = "Добавляет свойство \"Дата последней покупки\" для инфоблока товаров";

    protected $moduleVersion = "3.25.1";

    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->saveProperty(IBLOCK_CATALOG, array(
            'NAME' => 'Дата последней покупки товара',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'LAST_BUY_DATE',
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
            'VERSION' => '2',
            'USER_TYPE' => 'DateTime',
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));
    }

    public function down()
    {

    }
}
