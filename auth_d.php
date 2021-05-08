<?
define('NEED_AUTH', false);
define('NOT_CHECK_PERMISSIONS',true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$id = $_GET['id'] ? $_GET['id'] : 1;
$USER->Authorize($id);
LocalRedirect('/');
die();