<?php

namespace Sprint\Migration;


class RegionsHL20180911121152 extends Version {

    protected $description = "Настройка возможности отображения контента в рамках разных поддоменов";

    public function up(){
        $helper = new HelperManager();

        $hlblockId = $helper->Hlblock()->addHlblockIfNotExists([
            'NAME' => 'RespectDomains',
            'TABLE_NAME' => 'b_respect_domains',
        ]);

        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_XML_ID', [
            'USER_TYPE_ID' => 'string',
            'SETTINGS' => [
                'SIZE' => '50',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Внешний код',
                'en' => 'Внешний код',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Внешний код',
                'en' => 'Внешний код',
            ],
        ]);

        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_NAME', [
            'USER_TYPE_ID' => 'string',
            'SETTINGS' => [
                'SIZE' => '50',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Название',
                'en' => 'Название',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Название',
                'en' => 'Название',
            ],
        ]);

        $iSliderIBlockID = $helper->Iblock()->getIblockId('HOME_SLIDER', 'CONTENT');

        $helper->Iblock()->addPropertyIfNotExists($iSliderIBlockID, [
            'NAME' => 'Домены',
            'CODE' => 'RESPECTDOMAINS',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'directory',
            'MULTIPLE' => 'Y',
            'USER_TYPE_SETTINGS' => array('size'=>'1', 'width'=>'0', 'group'=>'N', 'multiple'=>'N', 'TABLE_NAME'=>'b_respect_domains'),
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iSliderIBlockID, [
            'NAME' => 'Скрыть на выбранных',
            'CODE' => 'RESPECTDOMAINS_HIDDEN',
            'PROPERTY_TYPE' => 'L',
            'VALUES' => [
                [
                    'XML_ID' => 'Y',
                    'VALUE' => 'Да',
                ]
            ],
            'LIST_TYPE' => 'C'
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $iSliderIBlockID = $helper->Iblock()->getIblockId('HOME_SLIDER', 'SYSTEM');
        
        $helper->Iblock()->deletePropertyIfExists($iSliderIBlockID, 'RESPECTDOMAINS');
        $helper->Iblock()->deletePropertyIfExists($iSliderIBlockID, 'RESPECTDOMAINS_HIDDEN');

        $helper->Hlblock()->deleteHlblockIfExists('Subdomains');

    }

}
