<?php

namespace Sprint\Migration;


class CatalogGroups20171227165643 extends Version {

    protected $description = "Инфоблок для группировки товаров";

    public function up(){
        $helper = new HelperManager();

        $iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Группировки',
            'CODE' => 'CATALOG_GROUPS',
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LIST_PAGE_URL' => '',
        ]);

        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Показывать в разделах',
            'CODE' => 'MENU_SECTION',
            'PROPERTY_TYPE' => 'G',
            'MULTIPLE' => 'Y',
            'SORT' => '100',
            'LINK_IBLOCK_ID' => IBLOCK_CATALOG,
        ]);
        $helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Цвет пункта меню',
            'CODE' => 'MENU_COLOR',
            'PROPERTY_TYPE' => 'L',
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'SORT' => '120',
            'VALUES' => [
                [
                    "XML_ID" => "red",
                    "VALUE" => "Красный",
                    "DEF" => "Y",
                    "SORT" => "100"
                ],
                [
                    "XML_ID" => "black",
                    "VALUE" => "Черный",
                    "DEF" => "N",
                    "SORT" => "200"
                ],
                [
                    "XML_ID" => "green",
                    "VALUE" => "Зеленый",
                    "DEF" => "N",
                    "SORT" => "300"
                ],
            ],
        ]);
        
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
    }

    public function down(){
        $helper = new HelperManager();

        $ok = $helper->Iblock()->deleteIblockIfExists('CATALOG_GROUPS');

    }

}
