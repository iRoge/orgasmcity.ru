<?php

namespace Sprint\Migration;

class AddStorePropertyToFeed20203101127778 extends Version
{

    protected $description = "Добавляет к инфоблоку настройки фидов новое свойство.";
    protected $moduleVersion = "3.12.12";
    private $IBlockCode = 'FEEDS_CONFIG_NEW';

    public function up()
    {
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId($this->IBlockCode, 'SYSTEM');
        $helper->Iblock()->saveProperty($iIBlockID, array (
            'NAME' => 'Склады',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'STORES',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'N',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'Y',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '2',
            'USER_TYPE' => 'store',
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iIBlockID, array (
            'NAME' => 'Резервирование',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'RESERVATION',
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
            'VERSION' => '2',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
            'VALUES' =>
                array (
                    0 =>
                        array (
                            'VALUE' => 'Y',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'Y',
                        ),
                ),
        ));
        $helper->Iblock()->saveProperty($iIBlockID, array (
            'NAME' => 'Доставка',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'DELIVERY',
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
            'VERSION' => '2',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
            'VALUES' =>
                array (
                    0 =>
                        array (
                            'VALUE' => 'Y',
                            'DEF' => 'N',
                            'SORT' => '500',
                            'XML_ID' => 'Y',
                        ),
                ),
        ));

        $helper->UserOptions()->saveElementForm($iIBlockID, array(
            'Элемент' =>
                array(
                    'ID' => 'ID',
                    'ACTIVE' => 'Активность',
                    'NAME' => 'Название',
                    'CODE' => 'Название файла выгрузки',

                    'PROPERTY_FC_UPDATE_IMPORT' => 'Обновления фида после ночной выгрузки',
                    'PROPERTY_FC_UPDATE_TIME' => 'Время обновления фида (ЧЧ:MM:СС)',
                    'PROPERTY_FC_UPDATE_PERIOD' => 'Периодичность обновления фида (ЧЧ:ММ:СС)',
                    'PROPERTY_LOCATION' => 'Местоположение',

                    'IBLOCK_ELEMENT_PROP_VALUE' => 'Значения свойств',

                    'PROPERTY_SECTION' => 'Товары из разделов',
                    'PROPERTY_PRICE_FROM' => 'Цена от',
                    'PROPERTY_PRICE_TO' => 'Цена до',
                    'PROPERTY_OFFERS_SIZE' => 'Размер',
                    'PROPERTY_PRICESEGMENTID' => 'PriceSegmentID',
                    'PROPERTY_SUBTYPEPRODUCT' => 'Вид изделия для Интернет-магазина',
                    'PROPERTY_COLLECTION' => 'Коллекция',
                    'PROPERTY_UPPERMATERIAL' => 'Материал верха',
                    'PROPERTY_LININGMATERIAL' => 'Материал подкладки',
                    'PROPERTY_RHODEPRODUCT' => 'Род изделия',
                    'PROPERTY_SEASON' => 'Сезон',
                    'PROPERTY_COUNTRY' => 'Страна происхождения',
                    'PROPERTY_STORES' => 'Склады',
                    'PROPERTY_RESERVATION' => 'Резервирование',
                    'PROPERTY_DELIVERY' => 'Доставка',
                ),
        ));
    }

    public function down()
    {
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId($this->IBlockCode, 'SYSTEM');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'STORES');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'RESERVATION');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'DELIVERY');
    }
}
