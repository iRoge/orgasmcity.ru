<?php

namespace Sprint\Migration;


class OrderPayEvent20171102212825 extends Version {

    protected $description = "#10335";

    public function up(){
        $helper = new HelperManager();

        $helper->Event()->addEventTypeIfNotExists('SALE_ORDER_PAY_MESSAGE', array(
            'NAME' => 'Письмо на оплату заказа',
            'LID' => 'ru',
            'DESCRIPTION' => ''
        ));

        $helper->Event()->addEventMessageIfNotExists('SALE_ORDER_PAY_MESSAGE', [
            'SUBJECT' => 'Письмо на оплату заказа',
            'MESSAGE' => '<a href="http://#SERVER_NAME#/order/pay/?ORDER_ID=#ORDER_ID#">Оплатить заказ #ORDER_ID#</a>',
            'LID' => 's1'
        ]);
    }

    public function down(){
        $helper = new HelperManager();

    }

}
