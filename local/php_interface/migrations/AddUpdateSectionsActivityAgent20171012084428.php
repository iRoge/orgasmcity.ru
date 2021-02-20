<?php

namespace Sprint\Migration;


class AddUpdateSectionsActivityAgent20171012084428 extends Version {

    protected $description = "Добовляет агент для обновления активности секций";

    public function up(){
        $helper = new HelperManager();

        \Bitrix\Main\Loader::includeModule('likee.exchange');
        \Likee\Exchange\Task\Rests::updateSectionsActivity();

        $helper->Agent()->addAgentIfNotExists('likee.exchange','\Likee\Exchange\Agent::updateSectionsActivity();',7200,'');

    }

    public function down(){
        $helper = new HelperManager();

       $helper->Agent()->deleteAgentIfExists('likee.exchange','\Likee\Exchange\Agent::updateSectionsActivity();');

    }

}
