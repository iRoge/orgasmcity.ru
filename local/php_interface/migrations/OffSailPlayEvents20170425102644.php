<?php

namespace Sprint\Migration;


class OffSailPlayEvents20170425102644 extends Version
{

    protected $description = "Отключение событий SailPlay";

    public function up()
    {
        $helper = new HelperManager();

        UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'sailplay.integration', 'SailplayEventHandlers', 'onAfterIBlockElementUpdate');
    }

    public function down()
    {
        $helper = new HelperManager();

        RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'sailplay.integration', 'SailplayEventHandlers', 'onAfterIBlockElementUpdate');
    }
}
