<?
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

ini_set('max_execution_time', 0);
ignore_user_abort(true);
set_time_limit(0);
Loader::includeModule('iblock');

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/constants.php');
CIBlock::disableTagCache(IBLOCK_CATALOG);
CIBlock::disableTagCache(IBLOCK_OFFERS);

CJSCore::Init(['fx']);
EventManager::getInstance()->addEventHandler('main', 'onBeforeUserLoginByHttpAuth', function (&$arAuth) {
    return false;
});
if (!$_COOKIE['device_type']) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/Mobile_Detect.php');

    $typeDevDetect = new Mobile_Detect();

    if ($typeDevDetect->isTablet()) {
        $GLOBALS['device_type'] = 'tablet';
    } elseif ($typeDevDetect->isMobile()) {
        $GLOBALS['device_type'] = 'mobile';
    } else {
        $GLOBALS['device_type'] = 'pc';
    }
} else {
    $GLOBALS['device_type'] = $_COOKIE['device_type'];
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/Functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/qsoft/superDebug.php');
// composer autoloader
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');

// создаем экземпляр класса, который будем везде использовать
$LOCATION = new Qsoft\Location;
global $LOCATION;

// env
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/env.php');
// UTM tags
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/init_utm.php');
// Возобновляем подписку людям перешедшим по email
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/init_subscribe.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// EVENTS
include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event.php');

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
    function pre($var)
    {
        if (!in_array($_SESSION["SESS_AUTH"]["USER_ID"], [1])) {
            return;
        }
        ob_start();
        var_dump($var);
        $dump = ob_get_clean();
        mb_internal_encoding('utf-8'); ?>
        <font style="text-align: left; font-size: 12px">
            <pre><?=$dump?></pre>
        </font><br>
        <?php
    }
}

function orgasm_logger($message, string $file = "log.txt", string $path = "/local/logs/", $where_flag = false)
{
    $message = (is_array($message) || is_object($message)) ? print_r($message, true) : $message;
    $log_path = $_SERVER['DOCUMENT_ROOT'] . $path;
    CheckDirPath($log_path, true);
    $log_file = $log_path . $file;
    $str = date('d.m.Y H:i:s') . " - " . $message . "\r\n";
    if ($where_flag) {
        $info = debug_backtrace();
        $info = $info[0];
        $info['file'] = substr($info['file'], strlen($_SERVER['DOCUMENT_ROOT']));
        $where = "{$info['file']}:{$info['line']}";
        $str = $where . "\r\n" . $str;
    }
    file_put_contents($log_file, $str, FILE_APPEND);
}

function myLog($data, $fileName = '1233321')
{
    global $APPLICATION, $LOCATION;
    // if (in_array($_SESSION["SESS_AUTH"]["USER_ID"], [1, 314458, 315911])){
    $trace = debug_backtrace();
    $caller = $trace[2];
    if ($APPLICATION->GetCurPage() != '/bitrix/components/bitrix/pull.request/ajax.php') {
        file_put_contents(
            $_SERVER["DOCUMENT_ROOT"] . '/debug/' . $fileName . ".dbg",
            print_r(
                [
                    "time" => date("H:i:s"),
                    "page" => $APPLICATION->GetCurPage(),
                    "location" => $LOCATION->getName(),
                    "function" =>
                    //                    [
                    //                    'file' => $caller['file'],
                    //                    'line' => $caller['line'],
                    //                    'args' => $caller['args'],
                    //                    'function' =>
                        $caller['function'],
                    //],
                    "data" => $data,
                ],
                1
            ),
            FILE_APPEND
        );
    }
    // }
}
