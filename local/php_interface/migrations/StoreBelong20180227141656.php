<?php

namespace Sprint\Migration;


class StoreBelong20180227141656 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $userFieldId = $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('CAT_STORE', 'UF_BELONG', [
            'USER_TYPE_ID' => 'enumeration',
            'SORT' => 100,
            'SETTINGS' => [
                'DISPLAY' => 'LIST',
                'LIST_HEIGHT' => '1',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Принадлежность',
                'en' => 'Belonging',
            ],
        ]);

        $enumField = new \CUserFieldEnum;
        $enumField->SetEnumValues($userFieldId, [
            "n0" => [
                'XML_ID' => 'retail',
                'VALUE' => 'Своя розница',
                'SORT' => '100'
            ],
            "n1" => [
                'XML_ID' => 'franchising',
                'VALUE' => 'Франчайзинг',
                'SORT' => '200'
            ],
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $helper->UserTypeEntity()->deleteUserTypeEntity('CAT_STORE', 'UF_BELONG');
    }

}
