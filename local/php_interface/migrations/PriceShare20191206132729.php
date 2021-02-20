<?php

namespace Sprint\Migration;

class PriceShare20191206132729 extends Version
{

    protected $description = "Добавляет HL блок для ценовых акций";

    public function up()
    {
        $helper = $this->getHelperManager();


        $helper->Hlblock()->saveHlblock(array (
                'NAME' => 'PriceShare',
                'TABLE_NAME' => 'b_qsoft_price_share',
                'LANG' =>
                    array (
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'HLBLOCK_PriceShare',
            'FIELD_NAME' => 'UF_NAME',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array (
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Название акции',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Название акции',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Название акции',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => 'Название акции',
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_SHARE_TYPE',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 5,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Тип акции',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Тип акции',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Тип акции',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Тип акции',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'HLBLOCK_PriceShare',
            'FIELD_NAME' => 'UF_ARTICLES',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array (
                    'ROWS' => 5,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Артикулы',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Артикулы',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Артикулы',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => 'Артикулы',
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_BRANCHES',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 5,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Филиалы',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Филиалы',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Филиалы',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Филиалы',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_ACTIVE',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Активность',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Активность',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Активность',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Активность',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'HLBLOCK_PriceShare',
            'FIELD_NAME' => 'UF_ACTIVE_FROM',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array (
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Активно от',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Активно от',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Активно от',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => 'Активно от',
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_ACTIVE_TO',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Активно до',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Активно до',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Активно до',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Активно до',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'HLBLOCK_PriceShare',
            'FIELD_NAME' => 'UF_CREATION_DATE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array (
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Дата создания',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Дата создания',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Дата создания',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => 'Дата создания',
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'HLBLOCK_PriceShare',
            'FIELD_NAME' => 'UF_LAST_CHANGE_DATE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => null,
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array (
                    'ROWS' => 1,
                    'REGEXP' => null,
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => null,
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'ru' => 'Дата последнего изменения',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'ru' => 'Дата последнего изменения',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'ru' => 'Дата последнего изменения',
                ),
            'ERROR_MESSAGE' =>
                array (
                    'ru' => 'Дата последнего изменения',
                ),
            'HELP_MESSAGE' =>
                array (
                    'ru' => null,
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_PRICE_SEGMENT',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Ценовой сегмент',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Ценовой сегмент',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Ценовой сегмент',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Ценовой сегмент',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_PRICES',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Цены price',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Цены price',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Цены price',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Цены price',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_PRICES1',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Цены price1',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Цены price1',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Цены price1',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Цены price1',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_DISCOUNTS',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Уценки от price',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Уценки от price',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Уценки от price',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Уценки от price',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                'ENTITY_ID' => 'HLBLOCK_PriceShare',
                'FIELD_NAME' => 'UF_DISCOUNTS_BP',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'ROWS' => 1,
                        'REGEXP' => null,
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'ru' => 'Скидки по БП',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'ru' => 'Скидки по БП',
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'ru' => 'Скидки по БП',
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'ru' => 'Скидки по БП',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'ru' => null,
                    ),
            ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $helper->Hlblock()->deleteHlblockIfExists('PriceShare');
    }
}
