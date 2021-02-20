<?php

namespace Sprint\Migration;

class franchiseWebForm20191227173729 extends Version
{
    protected $description = "Миграция вебформы для франчайзинга";

    public function up()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array(
        'NAME' => 'Форма франчайзинг',
        'SID' => 'FRANCHISE_FORM',
        'BUTTON' => 'Сохранить',
        'C_SORT' => '400',
        'FIRST_SITE_ID' => null,
        'IMAGE_ID' => null,
        'USE_CAPTCHA' => 'N',
        'DESCRIPTION' => '',
        'DESCRIPTION_TYPE' => 'text',
        'FORM_TEMPLATE' => '',
        'USE_DEFAULT_TEMPLATE' => 'Y',
        'SHOW_TEMPLATE' => null,
        'MAIL_EVENT_TYPE' => 'FORM_FILLING_FRANCHISE_FORM',
        'SHOW_RESULT_TEMPLATE' => null,
        'PRINT_RESULT_TEMPLATE' => null,
        'EDIT_RESULT_TEMPLATE' => null,
        'FILTER_RESULT_TEMPLATE' => '',
        'TABLE_RESULT_TEMPLATE' => '',
        'USE_RESTRICTIONS' => 'N',
        'RESTRICT_USER' => '0',
        'RESTRICT_TIME' => '0',
        'RESTRICT_STATUS' => null,
        'STAT_EVENT1' => 'form',
        'STAT_EVENT2' => 'franchise_form',
        'STAT_EVENT3' => '',
        'LID' => null,
        'C_FIELDS' => '0',
        'QUESTIONS' => '4',
        'STATUSES' => '1',
        'arSITE' =>
        array(
          0 => 's1',
        ),
        'arMENU' =>
        array(
          'en' => 'Franchise',
          'ru' => 'Заявки франчайзинг',
        ),
        'arGROUP' =>
        array(),
        'arMAIL_TEMPLATE' =>
        array(),
        ));
        $formHelper->saveStatuses($formId, array(
        0 =>
        array(
          'CSS' => '',
          'C_SORT' => '100',
          'ACTIVE' => 'Y',
          'TITLE' => 'Заполнена',
          'DESCRIPTION' => '',
          'DEFAULT_VALUE' => 'Y',
          'HANDLER_OUT' => '',
          'HANDLER_IN' => '',
        ),
        ));
        $formHelper->saveFields($formId, array(
        0 =>
        array(
          'ACTIVE' => 'Y',
          'TITLE' => 'ФИО',
          'TITLE_TYPE' => 'text',
          'SID' => 'NAME',
          'C_SORT' => '100',
          'ADDITIONAL' => 'N',
          'REQUIRED' => 'Y',
          'IN_FILTER' => 'N',
          'IN_RESULTS_TABLE' => 'Y',
          'IN_EXCEL_TABLE' => 'Y',
          'FIELD_TYPE' => '',
          'IMAGE_ID' => null,
          'COMMENTS' => '',
          'FILTER_TITLE' => '',
          'RESULTS_TABLE_TITLE' => '',
          'ANSWERS' =>
          array(
            0 =>
            array(
              'MESSAGE' => ' ',
              'VALUE' => '',
              'FIELD_TYPE' => 'text',
              'FIELD_WIDTH' => '0',
              'FIELD_HEIGHT' => '0',
              'FIELD_PARAM' => '',
              'C_SORT' => '100',
              'ACTIVE' => 'Y',
            ),
          ),
          'VALIDATORS' =>
          array(),
        ),
        1 =>
        array(
          'ACTIVE' => 'Y',
          'TITLE' => 'Телефон',
          'TITLE_TYPE' => 'text',
          'SID' => 'PHONE',
          'C_SORT' => '200',
          'ADDITIONAL' => 'N',
          'REQUIRED' => 'Y',
          'IN_FILTER' => 'N',
          'IN_RESULTS_TABLE' => 'Y',
          'IN_EXCEL_TABLE' => 'Y',
          'FIELD_TYPE' => '',
          'IMAGE_ID' => null,
          'COMMENTS' => '',
          'FILTER_TITLE' => '',
          'RESULTS_TABLE_TITLE' => '',
          'ANSWERS' =>
          array(
            0 =>
            array(
              'MESSAGE' => ' ',
              'VALUE' => '',
              'FIELD_TYPE' => 'text',
              'FIELD_WIDTH' => '0',
              'FIELD_HEIGHT' => '0',
              'FIELD_PARAM' => '',
              'C_SORT' => '100',
              'ACTIVE' => 'Y',
            ),
          ),
          'VALIDATORS' =>
          array(),
        ),
        2 =>
        array(
          'ACTIVE' => 'Y',
          'TITLE' => 'Email',
          'TITLE_TYPE' => 'text',
          'SID' => 'EMAIL',
          'C_SORT' => '300',
          'ADDITIONAL' => 'N',
          'REQUIRED' => 'Y',
          'IN_FILTER' => 'N',
          'IN_RESULTS_TABLE' => 'Y',
          'IN_EXCEL_TABLE' => 'Y',
          'FIELD_TYPE' => '',
          'IMAGE_ID' => null,
          'COMMENTS' => '',
          'FILTER_TITLE' => '',
          'RESULTS_TABLE_TITLE' => '',
          'ANSWERS' =>
          array(
            0 =>
            array(
              'MESSAGE' => ' ',
              'VALUE' => '',
              'FIELD_TYPE' => 'text',
              'FIELD_WIDTH' => '0',
              'FIELD_HEIGHT' => '0',
              'FIELD_PARAM' => '',
              'C_SORT' => '100',
              'ACTIVE' => 'Y',
            ),
          ),
          'VALIDATORS' =>
          array(),
        ),
        3 =>
        array(
          'ACTIVE' => 'Y',
          'TITLE' => 'Город',
          'TITLE_TYPE' => 'text',
          'SID' => 'CITY',
          'C_SORT' => '400',
          'ADDITIONAL' => 'N',
          'REQUIRED' => 'Y',
          'IN_FILTER' => 'N',
          'IN_RESULTS_TABLE' => 'Y',
          'IN_EXCEL_TABLE' => 'Y',
          'FIELD_TYPE' => '',
          'IMAGE_ID' => null,
          'COMMENTS' => '',
          'FILTER_TITLE' => '',
          'RESULTS_TABLE_TITLE' => '',
          'ANSWERS' =>
          array(
            0 =>
            array(
              'MESSAGE' => ' ',
              'VALUE' => '',
              'FIELD_TYPE' => 'text',
              'FIELD_WIDTH' => '0',
              'FIELD_HEIGHT' => '0',
              'FIELD_PARAM' => '',
              'C_SORT' => '100',
              'ACTIVE' => 'Y',
            ),
          ),
          'VALIDATORS' =>
          array(),
        ),
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formHelper->deleteFormIfExists('FRANCHISE_FORM');
    }
}
