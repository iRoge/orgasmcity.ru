<?php

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\PropertyEnumerationTable;

class CatalogGroupOnlineTryOnProperty20190826125959 extends Version
{
    protected $description = 'Добавляет в ИБ "Группировки" свойство "Онлайн примерочная".';

    private const IBLOCK_CODE = 'CATALOG_GROUPS';
    private const PROPERTY_CODE = 'ONLINE_TRY_ON';

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE, 'SYSTEM');
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Онлайн примерочная',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE,
                'DEFAULT_VALUE' => null,
                'PROPERTY_TYPE' => 'L',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'C',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
                'FILE_TYPE' => null,
                'MULTIPLE_CNT' => '5',
                'LINK_IBLOCK_ID' => '0',
                'WITH_DESCRIPTION' => 'N',
                'SEARCHABLE' => 'N',
                'FILTRABLE' => 'Y',
                'IS_REQUIRED' => 'N',
                'VERSION' => '1',
                'USER_TYPE' => null,
                'USER_TYPE_SETTINGS' => null,
                'HINT' => null,
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
