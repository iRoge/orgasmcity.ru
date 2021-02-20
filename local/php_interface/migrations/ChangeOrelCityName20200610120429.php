<?php

namespace Sprint\Migration;

class ChangeOrelCityName20200610120429 extends Version
{
    protected $description = "Изменяет название города Орёл на Орел";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        \Bitrix\Sale\Location\LocationTable::update(360, array(
            'NAME' => array( // языковые названия
                'ru' => array(
                    'NAME' => 'Орел'
                ),
                'en' => array(
                    'NAME' => 'Orel'
                ),
            ),
            'LATITUDE' => '100.970143',
            'LONGITUDE' => '100.970143',
        ));
    }

    public function down()
    {
        \Bitrix\Sale\Location\LocationTable::update(360, array(
            'NAME' => array( // языковые названия
                'ru' => array(
                    'NAME' => 'Орёл'
                ),
                'en' => array(
                    'NAME' => 'Orel'
                ),
            ),
            'LATITUDE' => '52.970143',
            'LONGITUDE' => '36.063397',
        ));
    }
}