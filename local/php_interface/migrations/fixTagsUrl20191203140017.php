<?php

namespace Sprint\Migration;

use Bitrix\Iblock\IblockTable;

class fixTagsUrl20191203140017 extends Version
{
    protected $description = "Добавляет слэш в конец ссылок в ИБ тегов";

    /**
    * @throws Exceptions\HelperException
    * @return bool|void
    */
    public function up()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => 'CATALOG_TAGS'],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];

        IblockTable::update($iblockID, ['DETAIL_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#/tag_#ELEMENT_CODE#/']);
        IblockTable::update($iblockID, ['SECTION_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#/']);
    }

    public function down()
    {
        $iblockList = IblockTable::getList([
            'filter' => ['=CODE' => 'CATALOG_TAGS'],
            'select' => ['ID'],
            'limit' => 1,
        ]);

        $iblockID = $iblockList->fetch()['ID'];

        IblockTable::update($iblockID, ['DETAIL_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#/tag_#ELEMENT_CODE#']);
        IblockTable::update($iblockID, ['SECTION_PAGE_URL' => '#SITE_DIR#/#SECTION_CODE_PATH#']);
    }
}
