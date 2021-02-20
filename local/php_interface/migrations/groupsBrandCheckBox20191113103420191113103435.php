<?php

namespace Sprint\Migration;

use Bitrix\Iblock\PropertyTable;

class groupsBrandCheckBox20191113103420191113103435 extends Version
{
    protected $description = 'Добавляет в ИБ "Группировки" свойство "Брендовый".';

    private const IBLOCK_CODE = 'CATALOG_GROUPS';
    private const PROPERTY_CODE = 'IS_BRAND';

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE, 'SYSTEM');
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Брендовая группировка',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE,
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'C',
                'VALUES' => [
                    [
                        'VALUE' => 'Y',
                        'DEF' => 'N',
                        'SORT' => '500',
                        'XML_ID' => 'Y',
                    ],
                ],
            ]
        );
    }
    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE, 'SYSTEM');
        $propertyList = PropertyTable::getList([
            'filter' => ['=CODE' => self::PROPERTY_CODE, '=IBLOCK_ID' => $iblockID],
            'select' => ['ID'],
            'limit' => 1,
        ]);
        if ($property = $propertyList->fetch()) {
            PropertyTable::delete($property['ID']);
        }
    }
}
