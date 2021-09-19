<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\UserGroupTable;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['\Qsoft\Events\Order', 'OnOrderSaveHandler']);
$eventManager->addEventHandler('catalog', 'OnGetOptimalPriceResult', ['\Qsoft\Events\Order', 'OnGetOptimalPriceResultHandler']);
$eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\Qsoft\Events\MenuBuilder', 'handleEvent']);
$eventManager->addEventHandler('sale', 'OnCondSaleActionsControlBuildList', ['\Qsoft\Events\PriceSegmentDiscountCondition', 'GetControlDescr']);
$eventManager->addEventHandler('sale', 'OnSaleStatusOrder', ['\Qsoft\Events\Order', 'OnSaleStatusOrderHandler']);

$eventManager->addEventHandler('main', 'OnBeforeUserRegister', 'lowerUserEmail');
$eventManager->addEventHandler('main', 'OnBeforeUserAdd', 'lowerUserEmail');
$eventManager->addEventHandler('main', 'OnBeforeUserRegister', 'subscribeUser');
$eventManager->addEventHandler('main', 'OnBeforeUserAdd', 'subscribeUser');
$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', 'lowerUserEmail');
$eventManager->addEventHandler('main', 'OnAfterUserAdd', 'sendRegistrationMessage');

function sendRegistrationMessage(&$arFields)
{
    CEvent::Send("USER_REGISTRATION", SITE_ID,
        [
            "EMAIL_TO" => $arFields['EMAIL'],
            "LOGIN" => $arFields['EMAIL'],
            "PASSWORD" => $arFields['CONFIRM_PASSWORD'],
            "SERVER_NAME" => DOMAIN_NAME,
        ]
    );
}

function lowerUserEmail(&$arFields)
{
    if (isset($arFields['EMAIL'])) {
        $arFields['EMAIL'] = mb_strtolower($arFields['EMAIL']);
    }

    if (isset($arFields['LOGIN'])) {
        $arFields['LOGIN'] = $arFields['EMAIL'];
        $arFields['LOGIN'] = mb_strtolower($arFields['LOGIN']);
    }
}

function subscribeUser(&$arFields)
{
    $subscriber = \Qsoft\Helpers\SubscribeManager::getSubscriberByEmail($arFields['EMAIL']);
    if (!$subscriber) {
        \Qsoft\Helpers\SubscribeManager::addSubscriber($arFields['EMAIL']);
    } elseif ($subscriber['ACTIVE'] == 'N') {
        \Qsoft\Helpers\SubscribeManager::updateSubscriber($subscriber['ID'], false, true);
    }
}

$eventManager->addEventHandler('main', 'OnAfterUserAuthorize', 'setUserGroup');
function setUserGroup(&$arFields)
{
    global $USER;
    $registeredUsersGroupID = 5;

    if (!($USER instanceof CUser)) {
        return;
    }

    if (!in_array($registeredUsersGroupID, $USER->GetUserGroupArray())) {
        UserGroupTable::add([
            'USER_ID' => $USER->GetID(),
            'GROUP_ID' => $registeredUsersGroupID
        ]);
    }
}

$eventManager->addEventHandler('sale', 'registerInputTypes', 'registerInputTypePaySystemLocations');
function registerInputTypePaySystemLocations(\Bitrix\Main\Event $event)
{
    \Bitrix\Sale\Internals\Input\Manager::register(
        "PAY_SYSTEM_LOCATIONS",
        array(
            'CLASS' => '\Qsoft\PaySystemLocations\PaySystemLocationsInput',
            'NAME' => 'Местоположения платежной системы',
        )
    );
}


if (mb_strpos($_SERVER['REQUEST_URI'], '/bitrix/admin') !== false) {
    $eventManager->AddEventHandler("main", "OnBeforeProlog", "checkAccessRights", 50);
}

if (in_array($_SERVER['SCRIPT_NAME'], ['/bitrix/admin/iblock_element_edit.php', '/bitrix/admin/iblock_section_edit.php'])) {
    $eventManager->addEventHandler('main', 'OnAdminTabControlBegin', ['\Qsoft\Events\MenuTabBuilder', 'handleEvent']);
}
