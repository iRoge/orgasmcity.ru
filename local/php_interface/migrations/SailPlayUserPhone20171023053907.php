<?php

namespace Sprint\Migration;


use Bitrix\Main\Config\Option;

class SailPlayUserPhone20171023053907 extends Version
{
    protected $description = '#10304 настройка SailPlay для работы по телефону пользоватлея';

    public function up()
    {
        $helper = new HelperManager();
        
        Option::set('sailplay.integration', 'hashtype', '4');
    }

    public function down()
    {
        $helper = new HelperManager();

        Option::set('sailplay.integration', 'hashtype', '1');
    }
}