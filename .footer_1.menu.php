<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @global array $aMenuLinks */

$aMenuLinks = array(
    array(
        'Доставка',
        '/company_delivery/',
        array(),
        array(),
        ''
    ),
    array(
        'Оплата',
        '/company_payment/',
        array(),
        array(),
        ''
    ),
    array(
        'Возврат',
        '/company_repayment/',
        array(),
        array(),
        ''
    ),
    array(
        'Акции',
        '/events/actions/',
        array(),
        array("itemclass" => "forcart"),
        ''
    ),
    array(
        'Вопрос-ответ',
        '/faq/',
        array(),
        array("itemclass" => "hideincart"),
        ''
    ),
    array(
        'Бонусная программа',
        '/company_bonus/',
        array(),
        array("itemclass" => "forcart"),
        ''
    ),
);