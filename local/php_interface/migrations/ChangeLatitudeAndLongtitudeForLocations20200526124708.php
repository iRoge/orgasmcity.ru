<?php

#TODO На респекте и городе оргазма разные id. Нужно доработать

namespace Sprint\Migration;

class ChangeLatitudeAndLongtitudeForLocations20200526124708 extends Version
{
    protected $description = "Изменяет координаты центров одинаковоназванных городов";

    protected $moduleVersion = "3.14.6";

    public function up()
    {
        \Bitrix\Sale\Location\LocationTable::update(2627, array(
            'LATITUDE' => '55.445972',
            'LONGITUDE' => '78.311111',
        ));
        \Bitrix\Sale\Location\LocationTable::update(2202, array(
            'LATITUDE' => '56.910173',
            'LONGITUDE' => '60.798203',
        ));
        \Bitrix\Sale\Location\LocationTable::update(2036, array(
            'LATITUDE' => '55.049854',
            'LONGITUDE' => '55.955224',
        ));
        \Bitrix\Sale\Location\LocationTable::update(992, array(
            'LATITUDE' => '54.770557',
            'LONGITUDE' => '20.603903',
        ));
        \Bitrix\Sale\Location\LocationTable::update(298, array(
            'LATITUDE' => '52.337971',
            'LONGITUDE' => '35.351743',
        ));
        \Bitrix\Sale\Location\LocationTable::update(2206, array(
            'LATITUDE' => '56.814887',
            'LONGITUDE' => '61.320636',
        ));
        \Bitrix\Sale\Location\LocationTable::update(232, array(
            'LATITUDE' => '54.079614',
            'LONGITUDE' => '34.307990',
        ));
        \Bitrix\Sale\Location\LocationTable::update(962, array(
            'LATITUDE' => '67.612056',
            'LONGITUDE' => '33.668228',
        ));
        \Bitrix\Sale\Location\LocationTable::update(21, array(
            'LATITUDE' => '56.120959',
            'LONGITUDE' => '38.140940',
        ));
        \Bitrix\Sale\Location\LocationTable::update(2117, array(
            'LATITUDE' => '51.023605',
            'LONGITUDE' => '45.695044',
        ));
        \Bitrix\Sale\Location\LocationTable::update(998, array(
            'LATITUDE' => '54.942144',
            'LONGITUDE' => '22.490590',
        ));
        \Bitrix\Sale\Location\LocationTable::update(924, array(
            'LATITUDE' => '62.761551',
            'LONGITUDE' => '40.326613',
        ));
        \Bitrix\Sale\Location\LocationTable::update(2257, array(
            'LATITUDE' => '56.435805',
            'LONGITUDE' => '59.120491',
        ));
        \Bitrix\Sale\Location\LocationTable::update(905, array(
            'LATITUDE' => '59.530416',
            'LONGITUDE' => '45.458481',
        ));
        \Bitrix\Sale\Location\LocationTable::update(1002, array(
            'LATITUDE' => '54.408508',
            'LONGITUDE' => '22.013521',
        ));
        \Bitrix\Sale\Location\LocationTable::update(1012, array(
            'LATITUDE' => '54.729070',
            'LONGITUDE' => '20.004206',
        ));
        \Bitrix\Sale\Location\LocationTable::update(731, array(
            'LATITUDE' => '55.996052',
            'LONGITUDE' => '40.332281',
        ));
        \Bitrix\Sale\Location\LocationTable::update(1670, array(
            'LATITUDE' => '57.587599',
            'LONGITUDE' => '48.959521',
        ));
        \Bitrix\Sale\Location\LocationTable::update(601, array(
            'LATITUDE' => '53.932940',
            'LONGITUDE' => '37.626636',
        ));
        \Bitrix\Sale\Location\LocationTable::update(3045, array(
            'LATITUDE' => '42.970706',
            'LONGITUDE' => '132.411071',
        ));
    }

    public function down()
    {
        //your code ...
    }
}
