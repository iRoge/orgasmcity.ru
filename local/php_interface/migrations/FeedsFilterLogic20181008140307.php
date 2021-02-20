<?php

namespace Sprint\Migration;


class FeedsFilterLogic20181008140307 extends Version {

    protected $description = "Логика реализации доп настроек для генерации фидов";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Управление фидами',
            'CODE' => 'FEEDS_CONFIG',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
            'SORT' => '1100',
        ]);

        
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Обновления фида после ночной выгрузки',
            'CODE' => 'FC_UPDATE_IMPORT',
            'PROPERTY_TYPE' => 'L',
            'SORT' => '50',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'VALUES' => [
                [
                    "XML_ID" => "Y",
                    "VALUE" => "Да",
                    "DEF" => "N",
                    "SORT" => "100"
                ]
            ],
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Время обновления фида (ЧЧ)',
            'CODE' => 'FC_UPDATE_TIME',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '55',
            'HINT' => 'Используется для работы обновления в режиме Ежедневно',
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Периодичность обновления фида (часов)',
            'CODE' => 'FC_UPDATE_PERIOD',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '60',
            'HINT' => 'Используется для работы обновления в течении дня с периодичностью',
        ]);


        // поля из группировок
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Товары из разделов',
            'CODE' => 'SECTION',
            'PROPERTY_TYPE' => 'G',
            'MULTIPLE' => 'Y',
            'SORT' => '150',
            'LINK_IBLOCK_ID' => IBLOCK_CATALOG,
        ]);
        
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цена от',
            'CODE' => 'PRICE_FROM',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '200',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цена до',
            'CODE' => 'PRICE_TO',
            'PROPERTY_TYPE' => 'N',
            'SORT' => '250',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Размер',
            'CODE' => 'OFFERS_SIZE',
            'PROPERTY_TYPE' => 'S',
            'SORT' => '300',
            'HINT' => 'Список значений разделенных запятыми'
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'MLT',
            'CODE' => 'MLT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_mlt'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'MRT',
            'CODE' => 'MRT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_mrt'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Материал подкладки',
            'CODE' => 'LININGMATERIAL',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_lining_material'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Материал верха',
            'CODE' => 'UPPERMATERIAL',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_upper_material'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Вид изделия',
            'CODE' => 'TYPEPRODUCT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_type_product'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Род изделия',
            'CODE' => 'RHODEPRODUCT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_rhode_product'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Сезон',
            'CODE' => 'SEASON',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_season'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цвет',
            'CODE' => 'COLOR',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_color'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Вид изделия для Интернет-магазина',
            'CODE' => 'SUBTYPEPRODUCT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_subtype_product'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Коллекция',
            'CODE' => 'COLLECTION',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_collection'),
        ]);
        
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Страна происхождения',
            'CODE' => 'COUNTRY',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_country'),
        ]);
        
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Высота каблука',
            'CODE' => 'HEELHEIGHT',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_heel_height'),
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'PriceSegmentID',
            'CODE' => 'PRICESEGMENTID',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_1c_dict_pricesegmentid'),
        ]);


        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Процент уценки/скидки ОТ (ценовой сегмент)',
            'CODE' => 'SEGMENT_FROM',
            'PROPERTY_TYPE' => 'N',
            'HINT' => 'Необходимо использовать со связкой PriceSegmentID'
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Процент уценки/скидки ДО (ценовой сегмент)',
            'CODE' => 'SEGMENT_TO',
            'PROPERTY_TYPE' => 'N',
            'HINT' => 'Необходимо использовать со связкой PriceSegmentID'
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('FEEDS_CONFIG');
    }

}
