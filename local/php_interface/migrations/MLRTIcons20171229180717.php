<?php

namespace Sprint\Migration;


class MLRTIcons20171229180717 extends Version {

    protected $description = "Поле иконок для highload блоков спец разделов";

    public function up(){
        $helper = new HelperManager();

        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_208', 'UF_ICON', [
            'USER_TYPE_ID' => 'file',
            'SORT' => 1000,
            'SETTINGS' => [
                'EXTENSIONS' => 'jpg, gif, bmp, png, jpeg',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Иконка',
                'en' => 'Icon',
            ],
        ]);

        $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_209', 'UF_ICON', [
            'USER_TYPE_ID' => 'file',
            'SORT' => 1000,
            'SETTINGS' => [
                'EXTENSIONS' => 'jpg, gif, bmp, png, jpeg',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Иконка',
                'en' => 'Icon',
            ],
        ]);

    }

    public function down(){
        $helper = new HelperManager();

        $helper->UserTypeEntity()->deleteUserTypeEntity('HLBLOCK_208', 'UF_ICON');
        $helper->UserTypeEntity()->deleteUserTypeEntity('HLBLOCK_209', 'UF_ICON');

    }

}
