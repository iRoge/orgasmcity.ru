<?php

namespace Sprint\Migration;

class Version20190819125028 extends Version
{

    protected $description = "Удаление событий Sailplay";

    public function up()
    {
        UnRegisterModuleDependences('sale', 'OnSaleOrderSaved', 'likee.site', '\Likee\Site\SailPlay', 'onOrderAdd');
        UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'sailplay.integration', 'SailplayEventHandlers', 'onAfterIBlockElementUpdate');

        return true;
    }

    public function down()
    {
        $this->outError('Данную мигрцию нельзя откатить');

        return false;
    }
}
