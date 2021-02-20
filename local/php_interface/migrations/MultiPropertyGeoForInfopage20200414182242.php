<?php

namespace Sprint\Migration;

class MultiPropertyGeoForInfopage20200414182242 extends Version
{
    protected $description = "Для информационных страниц делает свойство местоположение множественным";

    protected $moduleVersion = "3.12.12";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $infoIB = ['refundNew', 'delivery', 'reserv', 'contacts', 'payment'];
        foreach ($infoIB as $ibCode) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($ibCode, 'CONTENT');
            $helper->Iblock()->saveProperty($iblockId, array(
                'CODE' => 'LOCATION',
                'MULTIPLE' => 'Y',
                'MULTIPLE_CNT' => '1',
            ));
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $infoIB = ['refundNew', 'delivery', 'reserv', 'contacts', 'payment'];
        foreach ($infoIB as $ibCode) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($ibCode, 'CONTENT');
            $helper->Iblock()->saveProperty($iblockId, array(
                'CODE' => 'LOCATION',
                'MULTIPLE' => 'N',
            ));
        }
    }
}
