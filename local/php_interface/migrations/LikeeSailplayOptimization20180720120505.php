<?php

namespace Sprint\Migration;


class LikeeSailplayOptimization20180720120505 extends Version {

    protected $description = "Отключение запросов к таблице likee_sailplay_exchange_list";

    public function up(){
        $helper = new HelperManager();

        UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'likee.sailplay', '\Likee\SailPlay\Handlers', 'onAfterIBlockElementUpdate');
        \CAgent::RemoveAgent('\Likee\SailPlay\Agent::updateElements();', 'likee.sailplay');
    }

    public function down(){
        $helper = new HelperManager();

        RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'likee.sailplay', '\Likee\SailPlay\Handlers', 'onAfterIBlockElementUpdate');
        \CAgent::AddAgent('\Likee\SailPlay\Agent::updateElements();', 'likee.sailplay', 'N', 600);
    }

}
