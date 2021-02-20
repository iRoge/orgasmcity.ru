<?php

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;

class ProductsSectionElementsUrlProperty20171111154319 extends Version
{
    protected $description = 'Меняет ссылки элементов и секций в инфоблоке товаров (меняет на старые ссылки со слэшем)';

    public function up()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => 'CATALOG'],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];

        IblockTable::update($iblockID, ['DETAIL_PAGE_URL' => '#SITE_DIR#/#ELEMENT_CODE#/']);
        IblockTable::update($iblockID, ['SECTION_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#/']);
    }

    public function down()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => 'CATALOG'],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];

        IblockTable::update($iblockID, ['DETAIL_PAGE_URL' => '#SITE_DIR#/#ELEMENT_CODE#']);
        IblockTable::update($iblockID, ['SECTION_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#']);
    }
}
