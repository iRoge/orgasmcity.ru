<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
/** @global CMain $APPLICATION */
/** @global array $aMenuLinks */

$aMenuLinks = array(
    array(
        'История заказов',
        '/personal/orders/',
        array(),
        array(),
        '$USER->IsAuthorized()'
    ),

    array(
        'Бонусы',
        '/personal/bonuses/',
        array(),
        array(),
        '$USER->IsAuthorized()'
    ),
    array(
        'Личные данные',
        '/personal/',
        array(),
        array(),
        '$USER->IsAuthorized()'
    ),
    array(
        'Управление рассылкой',
        '/personal/subscribe/',
        array(),
        array(),
        '$USER->IsAuthorized()'
    ),
    array(
        'Выйти',
        $APPLICATION->GetCurPageParam('logout=yes', 'logout'),
        array(),
        array(),
        '$USER->IsAuthorized()'
    )
);