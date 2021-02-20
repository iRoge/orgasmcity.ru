<?php

namespace Sprint\Migration;

class addNewSailplayEmail20200410152216 extends Version
{
    protected $description = "Добавляет новый почтовый шаблон для Sailplay дублирование данных";

    protected $moduleVersion = "3.12.12";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('SAILPLAY_USER_INFO_IS_USED', array(
            'LID' => 'ru',
            'NAME' => 'Дублирование новых данных пользователя в Sailplay',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('SAILPLAY_USER_INFO_IS_USED', array(
            'LID' => 'en',
            'NAME' => 'Дублирование новых данных пользователя в Sailplay',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventMessage('SAILPLAY_USER_INFO_IS_USED', array(
            'LID' =>
                array(
                    0 => 's1',
                ),
            'ACTIVE' => 'Y',
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => 'orlova_m@respect-mail.ru,klimovich_v@respect-mail.ru,alexgtd2011@yandex.ru',
            'SUBJECT' => 'Дублирование данных пользователя в Sailplay',
            'MESSAGE' => 'Пользователю на сайте был добавлен #NEW_INFO_TEXT# из данных заказа. На сайте пользователей с таким #NEW_INFO_TEXT# нет, а в Sailplay обнаружен другой пользователь:

                Данные текущего пользователя в Sailplay:
                id - #SP_ID_1#
                phone - #SP_PHONE_1#
                email - #SP_EMAIL_1#
                фамилия - #SP_LAST_NAME_1#
                имя - #SP_NAME_1#
                отчество - #SP_SECOND_NAME_1#
                
                Данные обнаруженного пользователя в Sailplay:
                id - #SP_ID_2#
                phone - #SP_PHONE_2#
                email - #SP_EMAIL_2#
                фамилия - #SP_LAST_NAME_2#
                имя - #SP_NAME_2#
                отчество - #SP_SECOND_NAME_2#
                
                Данные текущего пользователя на сайте:
                id - #ID#
                phone - #PHONE#
                email - #EMAIL#
                фамилия - #LAST_NAME#
                имя - #NAME#
                отчество - #SECOND_NAME#',
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
            'EVENT_NAME' => 'SAILPLAY_USER_INFO_IS_USED',
            'LID' => 'ru'
        ]);
        $helper->Event()->deleteEventType([
            'EVENT_NAME' => 'SAILPLAY_USER_INFO_IS_USED',
            'LID' => 'en'
        ]);
        $helper->Event()->deleteEventMessage([
            'EVENT_NAME' => 'SAILPLAY_USER_INFO_IS_USED',
            'SUBJECT' => 'Дублирование данных пользователя в Sailplay'
        ]);
    }
}
