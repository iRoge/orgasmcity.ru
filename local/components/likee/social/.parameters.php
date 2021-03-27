<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arCurrentValues */

if (!\Bitrix\Main\Loader::includeModule('iblock'))
    return;

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'FACEBOOK_LINK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Facebook',
        ),
        'VK_LINK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'VK',
        ),
        'INSTAGRAM_LINK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Instagram',
        ),
    ),
);