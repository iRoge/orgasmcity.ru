<?php

#TODO: Изменяет название для инфоблока, который возможно понадобится для компонента на главной

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;

class BannerIBlockRenaming20190819162604 extends Version
{
    protected $description = 'Изменяет название инфоблока "Баннеры на главной (слайдер)" на "Мини-баннеры на главной".';

    private const IBLOCK_CODE = 'HOME_BANNERS_FOR_SLIDER';

    public function up()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::IBLOCK_CODE],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];
        IblockTable::update($iblockID, ['NAME' => 'Мини-баннеры на главной']);
    }

    public function down()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => self::IBLOCK_CODE],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];
        IblockTable::update($iblockID, ['NAME' => 'Баннеры на главной (слайдер)']);
    }
}
