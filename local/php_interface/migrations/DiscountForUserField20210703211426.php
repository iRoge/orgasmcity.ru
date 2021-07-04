<?php

namespace Sprint\Migration;


class DiscountForUserField20210703211426 extends Version
{
    protected $description = "Добавляет пользовательское поле UF_DISCOUNT для клиентов для скидки";

    protected $moduleVersion = "3.25.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_DISCOUNT',
            'USER_TYPE_ID' => 'integer',
            'SORT' => '500',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => 'Discount',
                    'ru' => 'Скидка',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => 'Discount',
                    'ru' => 'Скидка',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => 'Discount',
                    'ru' => 'Скидка',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => 'An error in completing the user field «Discount»',
                    'ru' => 'Ошибка при заполнении пользовательского свойства «Скидка»',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => '',
                ),
        ));
    }

    public function down()
    {

    }
}
