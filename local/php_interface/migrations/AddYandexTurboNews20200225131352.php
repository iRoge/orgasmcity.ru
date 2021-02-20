<?php

namespace Sprint\Migration;

class AddYandexTurboNews20200225131352 extends Version
{
    protected $description = "Добавляет поле \"Добавить в RSS канал\" в ИБ Пресс-центр, создает фид в \"Настройки создания фидов\"";

    protected $moduleVersion = "3.12.12";

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('NEWS', 'CONTENT');
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Добавить в RSS канал',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'ADD_RSS_CHANNEL',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
            'VALUES' =>
                array(
                    0 =>
                        array(
                            'VALUE' => 'Y',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'Y',
                        ),
                ),
        ));
        $iblockId = $helper->Iblock()->getIblockIdIfExists('FEEDS_CONFIG_NEW', 'SYSTEM');
        //Создание раздела
        $arFields = [
            "NAME" => 'yandex_turbo',
            "CODE" => 'news_yandex_turbo',
            "ACTIVE" => 'Y'
        ];
        $sectionId = $helper->Iblock()->addSectionIfNotExists($iblockId, $arFields);
//Создание элемента
        $arProps = [
            'FC_UPDATE_IMPORT' => 'N',
            'FC_UPDATE_TIME' => '',
            'PROPERTY_FC_UPDATE_PERIOD' => '',
            'IBLOCK_ELEMENT_PROP_VALUE' => '',
            'PROPERTY_SECTION' => '', //?
            'PROPERTY_PRICE_FROM' => '',
            'PROPERTY_PRICE_TO' => '',
            'PROPERTY_OFFERS_SIZE' => '',
            'PROPERTY_MLT' => '', //?
            'PROPERTY_MRT' => '', //?
            'PROPERTY_PRICESEGMENTID' => '', //?
            'PROPERTY_TYPEPRODUCT' => '', //?
            'PROPERTY_SUBTYPEPRODUCT' => '',
            'PROPERTY_HEELHEIGHT' => '',
            'PROPERTY_COLLECTION' => '',
            'PROPERTY_UPPERMATERIAL' => '',
            'PROPERTY_LININGMATERIAL' => '',
            'PROPERTY_SEGMENT_TO' => '',
            'PROPERTY_SEGMENT_FROM' => '',
            'PROPERTY_RHODEPRODUCT' => '',
            'PROPERTY_SEASON' => '',
            'PROPERTY_COUNTRY' => '',
            'PROPERTY_COLOR' => '',
            'PROPERTY_OFFERS_STORES' => '',
        ];

        $arFields = [
            "NAME" => "news_yandex_turbo",
            "CODE" => "news_yandex_turbo",
            "IBLOCK_SECTION_ID" => $sectionId,
            "ACTIVE" => "Y",
        ];
        $helper->Iblock()->addElementIfNotExists($iblockId, $arFields, $arProps);
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('NEWS', 'CONTENT');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'ADD_RSS_CHANNEL');
        $iblockId = $helper->Iblock()->getIblockIdIfExists('FEEDS_CONFIG_NEW', 'SYSTEM');
        $helper->Iblock()->deleteSectionIfExists($iblockId, 'news_yandex_turbo');
    }
}
