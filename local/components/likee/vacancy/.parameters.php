<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arCurrentValues */

if (!\Bitrix\Main\Loader::IncludeModule('iblock'))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array('-' => ' '));

$arIBlocks = array();
$db_iblock = CIBlock::GetList(array('SORT' => 'ASC'), array('SITE_ID' => $_REQUEST['site'], 'TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')));
while ($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes['ID']] = $arRes['NAME'];

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Тип инфоблока',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'CONTENT',
            'REFRESH' => 'Y',
        ),
        'IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Инфоблок',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,
            'DEFAULT' => '',
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
        ),
        'CACHE_TIME' => array('DEFAULT' => 36000000),
    ),
);
