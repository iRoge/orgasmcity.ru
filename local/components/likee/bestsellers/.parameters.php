<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arCurrentValues */

if (!\Bitrix\Main\Loader::includeModule('iblock'))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array('-' => ' '));

$arIBlocksProducts = array();
$rsIBLocks = CIBlock::GetList(array('SORT' => 'ASC'), array('SITE_ID' => $_REQUEST['site'], 'TYPE' => ($arCurrentValues['PRODUCTS_IBLOCK_TYPE'] != '-' ? $arCurrentValues['PRODUCTS_IBLOCK_TYPE'] : '')));
while ($arIBlock = $rsIBLocks->Fetch())
    $arIBlocksProducts[$arIBlock['ID']] = $arIBlock['NAME'];

$arIBlocksActions = array();
$rsIBLocks = CIBlock::GetList(array('SORT' => 'ASC'), array('SITE_ID' => $_REQUEST['site'], 'TYPE' => ($arCurrentValues['ACTIONS_IBLOCK_TYPE'] != '-' ? $arCurrentValues['ACTIONS_IBLOCK_TYPE'] : '')));
while ($arIBlock = $rsIBLocks->Fetch())
    $arIBlocksActions[$arIBlock['ID']] = $arIBlock['NAME'];


$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'PRODUCTS_IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Тип информационного блока товаров',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'CONTENT',
            'REFRESH' => 'Y',
        ),
        'PRODUCTS_IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Код информационного блока товаров',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksProducts,
            'DEFAULT' => '',
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
        ),

        'ACTIONS_IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Тип информационного блока акций',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'CONTENT',
            'REFRESH' => 'Y',
        ),
        'ACTIONS_IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Код информационного блока акций',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksActions,
            'DEFAULT' => '',
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
        ),

        'INSATGRAM_LINK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Ссылка на инстаграмм',
            'TYPE' => 'STRING'
        ),
        'CATALOG_LINK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Ссылка на каталог',
            'TYPE' => 'STRING'
        ),

        'CACHE_TIME' => array('DEFAULT' => 604800)
    )
);
