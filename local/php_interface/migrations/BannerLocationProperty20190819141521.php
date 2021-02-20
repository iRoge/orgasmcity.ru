<?php

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;

class BannerLocationProperty20190819141521 extends Version
{
    protected $description = 'Создаёт свойство "Местоположение" для инфоблоков баннеров.';

    private const BANNER_CODES = [
        'HOME_SLIDER',
        'HOME_SLIDER2',
        'HOME_SLIDER_SELAE',
        'main_slider_mobile_2',
        'main_slider_mobile_3',
        'HOME_BANNERS_FOR_SLIDER',
    ];

    private const PROPERTY = [
        'NAME' => 'Местоположение',
        'CODE' => 'LOCATION',
        'ACTIVE' => 'Y',
        'PROPERTY_TYPE' => 'S',
        'MULTIPLE' => 'Y',
    ];

    public function up()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::BANNER_CODES],
            'select' => ['ID'],
        ]);
        
        while ($iblock = $iblockList->fetch()) {
            PropertyTable::add(array_merge(['IBLOCK_ID' => $iblock['ID']], self::PROPERTY));
        }
    }

    public function down()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::BANNER_CODES],
            'select' => ['ID'],
        ]);

        $iblockID = [];
        while ($iblock = $iblockList->fetch()) {
            $iblockID[] = $iblock['ID'];
        }
        
        $propertyList = PropertyTable::getList([
            'filter' => ['CODE' => self::PROPERTY['CODE'], 'IBLOCK_ID=' => $iblockID],
            'select' => ['ID'],
        ]);
        
        while ($property = $propertyList->fetch()) {
            PropertyTable::delete($property['ID']);
        }
    }
}
