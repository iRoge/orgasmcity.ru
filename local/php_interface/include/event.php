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


if (mb_strpos($_SERVER['REQUEST_URI'], '/bitrix/admin') !== false) {
    $eventManager->AddEventHandler("main", "OnBeforeProlog", "checkAccessRights", 50);
}

if (in_array($_SERVER['SCRIPT_NAME'], ['/bitrix/admin/iblock_element_edit.php', '/bitrix/admin/iblock_section_edit.php'])) {
    $eventManager->addEventHandler('main', 'OnAdminTabControlBegin', ['\Qsoft\Events\MenuTabBuilder', 'handleEvent']);
}


$eventManager->addEventHandler("main", "OnEndBufferContent", "deleteKernelCss");
$eventManager->addEventHandler('main', 'OnEndBufferContent',  'deleteKernelScripts');

function deleteKernelScripts(&$content)
{
    global $USER;

    if (defined("ADMIN_SECTION")) {
        return;
    }

    if (is_object($USER) && $USER->IsAuthorized() && $USER->IsAdmin()) {
        $arPatternsToRemove = [
            '/<script[^>]+?>var _ba = _ba[^<]+<\/script>/',
        ];
    } else {
        $arPatternsToRemove = [
            '/<script.+?src=".+?js\/main\/core\/.+?(\.min|)\.js\?\d+"><\/script\>/',
            '/<script.+?src="\/bitrix\/js\/.+?(\.min|)\.js\?\d+"><\/script\>/',
            '/<link.+?href="\/bitrix\/js\/.+?(\.min|)\.css\?\d+".+?>/',
            '/<link.+?href="\/bitrix\/components\/.+?(\.min|)\.css\?\d+".+?>/',
            '/<script.+?src="\/bitrix\/.+?kernel_main.+?(\.min|)\.js\?\d+"><\/script\>/',
            '/<link.+?href=".+?kernel_main\/kernel_main(\.min|)\.css\?\d+"[^>]+>/',
            '/<link.+?href=".+?main\/popup(\.min|)\.css\?\d+"[^>]+>/',
            '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
            '/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/script>/',
            '/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/script>/',
            '/<script[^>]+?>var _ba = _ba[^<]+<\/script>/',
            '/<script[^>]+?>.+?bx-core.*?<\/script>/'
        ];
    }

    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n", $content);
}

function deleteKernelCss(&$content) {
    global $USER;
    if (defined("ADMIN_SECTION") || is_object($USER) && $USER->IsAdmin()) {
        return;
    }

    $arPatternsToRemove = Array(
        '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
    );

    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}
