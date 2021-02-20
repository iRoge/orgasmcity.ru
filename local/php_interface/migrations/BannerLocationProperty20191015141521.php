<?php

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;

class BannerLocationProperty20191015141521 extends Version
{
    protected $description = 'Меняет тип  свойства "Местоположение" для инфоблоков баннеров.';

    private const BANNER_CODES = [
        'HOME_SLIDER',
        'HOME_SLIDER2',
        'HOME_SLIDER_SELAE',
        'main_slider_mobile_2',
        'main_slider_mobile_3',
        'main_slider_mobile_4',
        'main_slider_mobile_5',
        'main_slider_mobile_6',
        'main_slider_mobile_7',
        'main_slider_mobile_8',
        'main_slider_mobile_9',
        'main_slider_mobile_10',
        'HOME_BANNERS_FOR_SLIDER',
    ];


    private const PROPERTY_CODE = 'LOCATION';
    public function up()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::BANNER_CODES],
            'select' => ['ID'],
        ]);
        
        while ($iblock = $iblockList->fetch()) {
            $prop = PropertyTable::getList([
                'select' => ['ID'],
                'filter' => [
                    '=IBLOCK_ID' => $iblock['ID'],
                    '=CODE' => self::PROPERTY_CODE
                ]
            ]);

            $prop = $prop->fetch();

            PropertyTable::update($prop['ID'], ['USER_TYPE' => 'LocationLink']);
        }
    }

    public function down()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::BANNER_CODES],
            'select' => ['ID'],
        ]);

        while ($iblock = $iblockList->fetch()) {
            $prop = PropertyTable::getList([
                'select' => ['ID'],
                'filter' => [
                    '=IBLOCK_ID' => $iblock['ID'],
                    '=CODE' => self::PROPERTY_CODE
                ]
            ]);

            $prop = $prop->fetch();

            PropertyTable::update($prop['ID'], ['USER_TYPE' => null]);
        }
    }
}
