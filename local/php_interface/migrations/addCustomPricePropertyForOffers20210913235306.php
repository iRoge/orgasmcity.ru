<?php

namespace Sprint\Migration;


class addCustomPricePropertyForOffers20210913235306 extends Version
{
    protected $description = "Добавляет поля CUSTOM_PRICE, CUSTOM_OLD_PRICE, CUSTOM_DISCOUNT  для товарных предложений";

    protected $moduleVersion = "3.25.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->saveProperty(IBLOCK_OFFERS, array(
            'NAME' => 'Кастомная цена',
            'ACTIVE' => 'Y',
            'SORT' => '499',
            'CODE' => 'CUSTOM_PRICE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'N',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
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
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        $helper->Iblock()->saveProperty(IBLOCK_OFFERS, array(
            'NAME' => 'Кастомная старая цена',
            'ACTIVE' => 'Y',
            'SORT' => '499',
            'CODE' => 'CUSTOM_OLD_PRICE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'N',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
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
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        $helper->Iblock()->saveProperty(IBLOCK_OFFERS, array(
            'NAME' => 'Кастомная скидка',
            'ACTIVE' => 'Y',
            'SORT' => '499',
            'CODE' => 'CUSTOM_DISCOUNT',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'N',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
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
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        $helper->Iblock()->saveProperty(IBLOCK_OFFERS, array(
            'NAME' => 'Дата окончания скидки',
            'ACTIVE' => 'Y',
            'SORT' => '499',
            'CODE' => 'CUSTOM_DISCOUNT_DATE_TO',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
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
            'USER_TYPE' => 'DateTime',
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));
    }

    public function down()
    {
        //your code ...
    }
}
