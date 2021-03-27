<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arCurrentValues */

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
        'CACHE_TIME' => array('DEFAULT' => 604800),
    ),
);