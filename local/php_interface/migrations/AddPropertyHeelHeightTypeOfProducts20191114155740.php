<?php

namespace Sprint\Migration;

use Bitrix\Iblock\PropertyTable;

class AddPropertyHeelHeightTypeOfProducts20191114155740 extends Version
{
    protected $description = 'Добавляет в ИБ "Каталог товаров" и ИБ "Группировки" свойство "Степень высоты каблука".';

    private const IBLOCK_CODE = 'CATALOG';
    private const GROUP_IBLOCK_CODE = 'CATALOG_GROUPS';
    private const PROPERTY_CODE = 'HEELHEIGHT_TYPE';

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE);
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Cтепень высоты каблука (проставляется автоматически при импорте)',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE,
                'DEFAULT_VALUE' => 'Без каблука',
                'PROPERTY_TYPE' => 'S',
            ]
        );
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::GROUP_IBLOCK_CODE, 'SYSTEM');
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Степень высоты каблука',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE,
                'DEFAULT_VALUE' => null,
                'PROPERTY_TYPE' => 'L',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
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
                        'VALUE' => 'Высокий',
                        'DEF' => 'N',
                        'SORT' => '500',
                        'XML_ID' => 'high',
                    ],
                    [
                        'VALUE' => 'Средний',
                        'DEF' => 'N',
                        'SORT' => '500',
                        'XML_ID' => 'mid',
                    ],
                    [
                        'VALUE' => 'Низкий',
                        'DEF' => 'N',
                        'SORT' => '500',
                        'XML_ID' => 'low',
                    ],
                    [
                        'VALUE' => 'Без каблука',
                        'DEF' => 'N',
                        'SORT' => '500',
                        'XML_ID' => 'without',
                    ],
                ],
            ]
        );
    }
    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE);
        $propertyList = PropertyTable::getList([
            'filter' => ['=CODE' => self::PROPERTY_CODE, '=IBLOCK_ID' => $iblockID],
            'select' => ['ID'],
            'limit' => 1,
        ]);
        if ($property = $propertyList->fetch()) {
            PropertyTable::delete($property['ID']);
        }

        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::GROUP_IBLOCK_CODE, 'SYSTEM');
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
