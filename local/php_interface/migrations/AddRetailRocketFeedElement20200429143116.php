<?php

namespace Sprint\Migration;

class AddRetailRocketFeedElement20200429143116 extends Version
{
    protected $description = "Добавляет настройку фида для ритейл рокета";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        $helper = $this->getHelperManager();
        //Создание раздела
        $iblockId = $helper->Iblock()->getIblockIdIfExists('FEEDS_CONFIG_NEW', 'SYSTEM');
        $arFields = [
            "NAME" => 'retail_rocket',
            "CODE" => 'retail_rocket',
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
            "NAME" => "Retail Rocket",
            "CODE" => "retail_rocket",
            "IBLOCK_SECTION_ID" => $sectionId,
            "ACTIVE" => "Y",
        ];
        $helper->Iblock()->addElementIfNotExists($iblockId, $arFields, $arProps);
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('FEEDS_CONFIG_NEW', 'SYSTEM');
        $helper->Iblock()->deleteSectionIfExists($iblockId, 'retail_rocket');
    }
}
