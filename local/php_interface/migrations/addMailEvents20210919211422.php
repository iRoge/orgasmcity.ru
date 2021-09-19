<?php

namespace Sprint\Migration;

use CEventMessage;

class addMailEvents20210919211422 extends Version
{
    protected $description = "Создает почтовые события";

    protected $moduleVersion = "3.25.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $em = new CEventMessage;

        $helper->Event()->saveEventType('ORDER_CREATE', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо после создания заказа',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта
                #PRICE# - сумма заказа',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_CREATE', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Ваш_заказ_доставлен.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_CREATE',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ оформлен',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('ORDER_CREATE_PREPAYMENT', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо после создания заказа по предоплате',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта
                #PRICE# - сумма заказа',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_CREATE_PREPAYMENT', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Заказ_оформлен_но_не_оплачен.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_CREATE_PREPAYMENT',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ оформлен',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('USER_REGISTRATION', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо после регистрации пользователя',
            'DESCRIPTION' => '#PASSWORD# - пароль
                #SERVER_NAME# - домен сайта
                #LOGIN# - логин',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('USER_REGISTRATION', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Спасибо_за_регистрацию.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'USER_REGISTRATION',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Спасибо за регистрацию',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('USER_SUBSCRIBE', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо после подписки пользователя',
            'DESCRIPTION' => '#SERVER_NAME# - домен сайта',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('USER_SUBSCRIBE', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Спасибо_за_подписку.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'USER_SUBSCRIBE',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Спасибо за подписку!',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('ORDER_PAID', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо после оплаты заказа',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта
                #PRICE# - сумма заказа',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_PAID', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/заказ_оплачен.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_PAID',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ успешно оплачен',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('ORDER_ASSEMBLY', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо о переходе заказа в статус комплектации',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_ASSEMBLY', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Заказ_в_статусе_комплектации.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_ASSEMBLY',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ начали комплектовать',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('ORDER_SEND', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо о переходе заказа в статус высланного',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_SEND', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Заказ_отправлен_курьерам.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_SEND',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ отправлен службе доставки',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);

        $helper->Event()->saveEventType('ORDER_DELIVERED', array(
            'LID' => 'ru',
            'EVENT_TYPE' => 'email',
            'NAME' => 'Письмо о переходе заказа в статус доставленного',
            'DESCRIPTION' => '#ORDER_ID# - ID заказа
                #SERVER_NAME# - домен сайта',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('ORDER_DELIVERED', array(
            'LID' => 'en',
            'EVENT_TYPE' => 'email',
            'NAME' => '',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/Ваш_заказ_доставлен.html');
        $arTemplate = Array(
            'ACTIVE'=> 'Y',
            'EVENT_NAME' => 'ORDER_DELIVERED',
            'LID' => Array('s1'),
            'EMAIL_FROM' => '#EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => 'Ваш заказ был доставлен',
            'BODY_TYPE' => 'html',
            'MESSAGE' => $body,
        );
        $em->Add($arTemplate);
    }

    public function down()
    {
        //your code ...
    }
}
