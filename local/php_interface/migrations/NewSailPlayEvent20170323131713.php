<?php

namespace Sprint\Migration;


class NewSailPlayEvent20170323131713 extends Version
{

    protected $description = "";

    public function up()
    {
        $helper = new HelperManager();

        UnRegisterModuleDependences('sale', 'OnOrderAdd', 'sailplay.integration', 'SailplayEventHandlers', 'onOrderAdd');
        RegisterModuleDependences('sale', 'OnSaleOrderSaved', 'likee.site', '\Likee\Site\SailPlay', 'onOrderAdd');
    }

    public function down()
    {
        $helper = new HelperManager();

        RegisterModuleDependences('sale', 'OnOrderAdd', 'sailplay.integration', 'SailplayEventHandlers', 'onOrderAdd');
        UnRegisterModuleDependences('sale', 'OnSaleOrderSaved', 'likee.site', '\Likee\Site\SailPlay', 'onOrderAdd');
    }
}
