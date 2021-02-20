<?php


namespace Sprint\Migration;


class ChangePriceSegmentPropertyFeeds20200615161824 extends Version{

    protected $description = "Изменяет свойство фильтра по сегменту цены для фидов с одиночного на множественный";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Управление фидами',
            'CODE' => 'FEEDS_CONFIG_NEW',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1',
            'LIST_PAGE_URL' => '',
            'SORT' => '1100',
        ]);
        $helper->Iblock()->updatePropertyIfExists($iblockId1,'PRICESEGMENTID', [
            'NAME' => 'PriceSegmentID',
            'CODE' => 'PRICESEGMENTID',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_pricesegmentid'),
        ]);
    }

    public function down(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Управление фидами',
            'CODE' => 'FEEDS_CONFIG_NEW',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' => 's1',
            'LIST_PAGE_URL' => '',
            'SORT' => '1100',
        ]);
        $helper->Iblock()->updatePropertyIfExists($iblockId1,'PRICESEGMENTID', [
            'NAME' => 'PriceSegmentID',
            'CODE' => 'PRICESEGMENTID',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_pricesegmentid'),
        ]);
    }
}