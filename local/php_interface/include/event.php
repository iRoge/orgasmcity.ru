<?php

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\EventManager;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\GroupTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Sale;
use Bitrix\Main\Application;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['\Qsoft\Events\Order', 'OnOrderSaveHandler']);
$eventManager->addEventHandler('catalog', 'OnGetOptimalPriceResult', ['\Qsoft\Events\Order', 'OnGetOptimalPriceResultHandler']);
$eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\Qsoft\Events\MenuBuilder', 'handleEvent']);
$eventManager->addEventHandler('sale', 'OnCondSaleActionsControlBuildList', ['\Qsoft\Events\PriceSegmentDiscountCondition', 'GetControlDescr']);
$eventManager->addEventHandler('sale', 'OnSaleStatusOrder', ['\Qsoft\Events\Order', 'OnSaleStatusOrderHandler']);

$eventManager->addEventHandler('main', 'OnBeforeEventAdd', 'onBeforeEventAdd');
function onBeforeEventAdd($sEvent, $LID, &$arFields)
{
    switch ($sEvent) {
        case 'SALE_STATUS_CHANGED_N':
            $iOrder = $arFields['ORDER_ID'];

            if ($iOrder > 0) {
                $arOrder = CSaleOrder::GetByID($iOrder);

                $arPaySystem = \CSalePaysystem::GetByID($arOrder['PAY_SYSTEM_ID']);

                if (in_array($arPaySystem['CODE'], ['PAYANYWAY'])) {
                    $arFields['ORDER_PAY'] = 'Ссылка на оплату: ';
                    $arFields['ORDER_PAY'] .= '<a href="http://respect-shoes.ru/order/pay/?ORDER_ID=' . $iOrder . '">';
                    $arFields['ORDER_PAY'] .= 'http://respect-shoes.ru/order/pay/?ORDER_ID=' . $iOrder;
                    $arFields['ORDER_PAY'] .= '</a>';
                    $arFields['ORDER_PAY'] .= '<br/>';
                }
                break;
            }
    }
}

/**
 * События для метрики
 */
$eventManager->addEventHandler('main', 'OnAfterUserLogin', ['SiteAnalytics', 'userLoginEvent']);
$eventManager->addEventHandler('main', 'OnAfterUserRegister', ['SiteAnalytics', 'userRegisterEvent']);
$eventManager->addEventHandler("socialservices", "OnAfterSocServUserAdd", ['SiteAnalytics', 'userSocRegisterEvent']);
$eventManager->addEventHandler('main', 'OnEpilog', ['SiteAnalytics', 'appendEvents']);
$eventManager->addEventHandler('main', 'OnProlog', ['SiteAnalytics', 'onProlog']);

class SiteAnalytics
{
    public function onProlog()
    {
        $appendRequestQuery = false;

        if (! isset($_SESSION['RESPECT_REFERER'])) {
            $_SESSION['RESPECT_REFERER'] = empty($_SERVER['HTTP_REFERER']) ? '0' : $_SERVER['HTTP_REFERER'];
            $appendRequestQuery = true;
        } elseif (! empty($_SERVER['HTTP_REFERER']) && false === strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) {
            $_SESSION['RESPECT_REFERER'] = $_SERVER['HTTP_REFERER'];
            $appendRequestQuery = true;
        }

        if ($appendRequestQuery && (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '?'))) {
            $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            $_SESSION['RESPECT_REFERER'] .= (strpos($_SESSION['RESPECT_REFERER'], '?') ? '&' : '?').$query;
        }
    }
    public function userLoginEvent($arFields)
    {
        if (! empty($arFields['USER_ID'])) {
            $_SESSION['RESPECT_GOALS'][] = 'user_auth';
        }

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        $basket->refresh();
        $basket->save();
    }
    public function userRegisterEvent($arFields)
    {
        if (! empty($arFields['USER_ID'])) {
            $_SESSION['RESPECT_GOALS'][] = 'new_register';
        }
    }
    public function userSocRegisterEvent($arFields)
    {
        $_SESSION['RESPECT_GOALS'][] = 'new_soc_register';
    }
    public function appendEvents()
    {
        global $APPLICATION;
        if (! empty($_SESSION['RESPECT_GOALS']) && is_array($_SESSION['RESPECT_GOALS'])) {
            $sGoals = '';
            foreach ($_SESSION['RESPECT_GOALS'] as $goalName) {
                $sGoals .= "window.respectMetrkiaGoal.push('$goalName');";
            }

            if ($sGoals) {
                $APPLICATION->AddHeadString("<script>window.respectMetrkiaGoal = window.respectMetrkiaGoal || []; $sGoals</script>");
            }
            $_SESSION['RESPECT_GOALS'] = [];
        }
    }
}

$eventManager->addEventHandler('form', 'onBeforeResultAdd', 'onBeforeResultAddHandler');
function onBeforeResultAddHandler($WEB_FORM_ID, &$arFields, &$arrVALUES)
{
    global $APPLICATION, $DB;
    if ($WEB_FORM_ID == 1) {
        $subject = $arrVALUES['form_text_3'];
        $arSubjectList = [];

        $rsData = $DB->Query('SELECT `UF_NAME` FROM `b_feedback_subjects`');
        while ($arItem = $rsData->Fetch()) {
            if (!empty($arItem['UF_NAME'])) {
                $arSubjectList[] = $arItem['UF_NAME'];
            }
        }

        if (!in_array($subject, $arSubjectList)) {
            $APPLICATION->ThrowException('Нет такой темы');
            return false;
        }
    }
}

$eventManager->addEventHandler('main', 'OnBeforeUserRegister', 'lowerUserEmail');
$eventManager->addEventHandler('main', 'OnBeforeUserAdd', 'lowerUserEmail');
$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', 'lowerUserEmail');

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

$eventManager->addEventHandler('sale', 'onSalePaySystemRestrictionsClassNamesBuildList', 'onSalePaySystemRestrictionsClassNamesBuildListHandler');
function onSalePaySystemRestrictionsClassNamesBuildListHandler()
{
    return new EventResult(
        EventResult::SUCCESS,
        array(
            'Qsoft\PaySystemLocations\RestrictionByLocation' => '/local/php_interface/include/qsoft/lib/Qsoft/PaySystemLocations/RestrictionByLocation.php',
        )
    );
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

$eventManager->addEventHandler('sale', 'OnCatalogStoreAdd', 'resetUniqueShowcases');
$eventManager->addEventHandler('sale', 'OnCatalogStoreDelete', 'resetUniqueShowcases');
$eventManager->addEventHandler('sale', 'OnCatalogStoreUpdate', 'resetUniqueShowcases');
function resetUniqueShowcases($id = null, $fields = null)
{
    Application::getInstance()->getTaggedCache()->clearByTag('unique_showcases');
    Application::getInstance()->getTaggedCache()->clearByTag('unique_showcasesWO');
}

$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', 'addLink');
function addLink($arFields)
{

    $rsIblock = CIBlock::GetList([], ['ID' => $arFields['IBLOCK_ID']]);
    $arIblock = $rsIblock->Fetch();

    if ($arIblock['CODE'] == IBLOCK_FEEDS) {
        CIBlockElement::SetPropertyValuesEx(
            $arFields['ID'],
            $arFields['IBLOCK_ID'],
            [
                'RUN_LINK' => 'https://' . $_SERVER['HTTP_HOST'] . '/feed.php?FEED_ID=' . $arFields['ID']
            ]
        );
    }
}

//Добавляем кнопку для ручного запуска фида
$eventManager->addEventHandler("main", "OnAdminContextMenuShow", "AddButtonByManualStartFeed");
function AddButtonByManualStartFeed(&$items)
{
    if ($GLOBALS["APPLICATION"]->GetCurPage(true) == "/bitrix/admin/iblock_element_edit.php") {
        $arIblock = IblockTable::getList([
            'filter' => ['ID' => intval($_REQUEST['IBLOCK_ID'])],
            'select' => ['CODE']
        ])->fetch();

        if ($arIblock['CODE'] = IBLOCK_FEEDS) {
            $arElement = CIBlockElement::GetList(
                [],
                ['ID' => intval($_REQUEST['ID'])],
                false,
                false,
                ['PROPERTY_RUN_LINK', 'ID', 'IBLOCK_ID']
            )->Fetch();

            if (!empty($arElement['PROPERTY_RUN_LINK_VALUE'])) {
                $items[] = array(
                    "TEXT"=>"Ручной запуск",
                    "ICON"=>"",
                    "TITLE"=>"Кнопка для ручного запуска",
                    "LINK"=>$arElement['PROPERTY_RUN_LINK_VALUE']
                );
            }
        }
    }
}

if (mb_strpos($_SERVER['REQUEST_URI'], '/bitrix/admin') !== false) {
    $eventManager->AddEventHandler("main", "OnBeforeProlog", "checkAccessRights", 50);
}

if (in_array($_SERVER['SCRIPT_NAME'], ['/bitrix/admin/iblock_element_edit.php', '/bitrix/admin/iblock_section_edit.php'])) {
    $eventManager->addEventHandler('main', 'OnAdminTabControlBegin', ['\Qsoft\Events\MenuTabBuilder', 'handleEvent']);
}
