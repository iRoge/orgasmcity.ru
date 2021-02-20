<?php

namespace Sprint\Migration;

class franchiseAddTemplateMail20200214141737 extends Version
{
    protected $description = "Добавляет почтовый шаблон для формы франчайзинг";

    protected $moduleVersion = "3.12.12";

    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('FRANCHISE_FORM_SUBMIT', array(
            'LID' => 'ru',
            'NAME' => 'Отправка формы фрайчайзинг',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('FRANCHISE_FORM_SUBMIT', array(
            'LID' => 'en',
            'NAME' => 'Sending franchise results',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventMessage('FRANCHISE_FORM_SUBMIT', array(
            'LID' =>
                array(
                    0 => 's1',
                ),
            'ACTIVE' => 'Y',
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => '#FRANCHISE_RESULT_EMAIL#',
            'SUBJECT' => 'Заявка франчайзинг',
            'MESSAGE' => '  Фамилия Имя:  #NAME#
  Телефон:  #PHONE#
  E-mail:  #EMAIL#
  Город:  #CITY#',
            'BODY_TYPE' => 'text',
            'BCC' => '',
            'REPLY_TO' => '',
            'CC' => '',
            'IN_REPLY_TO' => '',
            'PRIORITY' => '',
            'FIELD1_NAME' => null,
            'FIELD1_VALUE' => null,
            'FIELD2_NAME' => null,
            'FIELD2_VALUE' => null,
            'SITE_TEMPLATE_ID' => '',
            'ADDITIONAL_FIELD' =>
                array(),
            'LANGUAGE_ID' => '',
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->deleteEventType([
            'EVENT_NAME' => 'FRANCHISE_FORM_SUBMIT',
            'LID' => 'ru'
        ]);
        $helper->Event()->deleteEventType([
            'EVENT_NAME' => 'FRANCHISE_FORM_SUBMIT',
            'LID' => 'en'
        ]);
        $helper->Event()->deleteEventMessage([
            'EVENT_NAME' => 'FRANCHISE_FORM_SUBMIT',
            'SUBJECT' => 'Заявка франчайзинг'
        ]);
    }
}
