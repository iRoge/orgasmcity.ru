<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION, $USER;

$APPLICATION->SetPageProperty('SUBTITLE', $USER->GetEmail());