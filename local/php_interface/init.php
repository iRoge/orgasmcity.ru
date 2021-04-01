<?
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

Loader::includeModule('iblock');

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/constants.php');
CIBlock::disableTagCache(IBLOCK_CATALOG);
CIBlock::disableTagCache(IBLOCK_OFFERS);

CJSCore::Init(['fx']);
EventManager::getInstance()->addEventHandler('main', 'onBeforeUserLoginByHttpAuth', function (&$arAuth) {
    return false;
});

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/Functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/qsoft/superDebug.php');
// composer autoloader
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');

// создаем экземпляр класса, который будем везде использовать
$LOCATION = new \Qsoft\Location\Location;
global $LOCATION;

// env
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/env.php');
// UTM tags
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/init_utm.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// EVENTS
include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/search.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/form.php');


AddEventHandler("main", "OnEpilog", "process404");
function process404()
{
    global $APPLICATION;
    if (defined('ERROR_404') && ERROR_404 == 'Y') {
        $APPLICATION->RestartWorkarea();
        include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
        include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/footer.php");
    }
}

if (!function_exists("pre")) {
    function pre($var, $die = false, $all = false)
    {
        global $USER;
        if ($USER->IsAdmin() || $all == true) {
            ob_start();
            var_dump($var);
            $dump = ob_get_clean();
            mb_internal_encoding('utf-8'); ?>
            <font style="text-align: left; font-size: 12px">
                <pre><?=$dump?></pre>
            </font><br>
            <?
        }
        if ($die) {
            die;
        }
    }
}