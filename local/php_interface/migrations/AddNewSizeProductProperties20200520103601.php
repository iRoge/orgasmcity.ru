<?php

namespace Sprint\Migration;

use Bitrix\Iblock\PropertyTable;

class AddNewSizeProductProperties20200520103601 extends Version
{
    protected $description = "Добавляет элементам инфоблока товаров новые свойства: Ширина, Высота, Длина.";

    protected $moduleVersion = "3.14.6";
    private const IBLOCK_CODE = 'CATALOG';
    private const PROPERTY_CODE1 = 'HEIGHT';
    private const PROPERTY_CODE2 = 'WIDTH';
    private const PROPERTY_CODE3 = 'LENGTH';

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE);
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Высота',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE1,
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'N',
            ]
        );
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Ширина',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE2,
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'N',
            ]
        );
        $helper->Iblock()->saveProperty(
            $iblockID,
            [
                'NAME' => 'Длина',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => self::PROPERTY_CODE3,
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'N',
            ]
        );
    }
    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockID = $helper->Iblock()->getIblockIdIfExists(self::IBLOCK_CODE);
        $propertyList = PropertyTable::getList([
            'filter' => ['=CODE' => array(self::PROPERTY_CODE1, self::PROPERTY_CODE2, self::PROPERTY_CODE3), '=IBLOCK_ID' => $iblockID],
            'select' => ['ID'],
            'limit' => 3,
        ]);
        while ($property = $propertyList->fetch()) {
            PropertyTable::delete($property['ID']);
        }
    }
}
