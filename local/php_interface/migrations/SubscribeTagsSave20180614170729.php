<?php

namespace Sprint\Migration;


class SubscribeTagsSave20180614170729 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('USER', 'UF_SUBSCRIBE_TAGS', [
            'USER_TYPE_ID' => 'string',
            'SETTINGS' => [
                'ROWS' => '5',
            ],
            'EDIT_FORM_LABEL'   => [
                'ru'    => 'Теги подписки',
                'en'    => 'Subscribe tags',
            ],
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_SUBSCRIBE_TAGS');
    }

}
