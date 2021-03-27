<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arCurrentValues */

global $LOCATION;

if (!\Bitrix\Main\Loader::includeModule('iblock'))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array('-' => ' '));

$arIBlocks = array();
$rsIBLocks = CIBlock::GetList(array('SORT' => 'ASC'), array('SITE_ID' => $_REQUEST['site'], 'TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')));
while ($arIBlock = $rsIBLocks->Fetch())
    $arIBlocks[$arIBlock['ID']] = $arIBlock['NAME'];

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Тип информационного блока',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'CONTENT',
            'REFRESH' => 'Y',
        ),
        'IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Код информационного блока',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,
            'DEFAULT' => '',
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
        ),
        'COUNT' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Кол-во элементов в списке',
            'TYPE' => 'STRING',
            'DEFAULT' => '20',
        ),
        'AUTOPLAY_CODE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'ID опции времени смены слайда',
            'TYPE' => 'STRING',
        ),
        'CACHE_TIME' => array('DEFAULT' => 604800),
        'LOCATION' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Местоположение',
            'TYPE' => 'STRING',
            'DEFAULT' => $LOCATION->getName(),
        ),
    ),
);
