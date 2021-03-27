<?php
@define(HIDE_TITLE, true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $APPLICATION;

// Редиректы
require($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/redirect.php');

$APPLICATION->IncludeComponent(
    "rdevs:events",
    "",
    [
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '86400',
        'CACHE_DIR' => '/events',
        'IBLOCK_CODE' => 'events',
        'IBLOCK_ID' => Functions::getEnvKey('IBLOCK_EVENTS', 70),
        'SEF_FOLDER' => '/events/',
        'SEF_URL_TEMPLATES' => [
            'section' => '#SECTION_CODE#/',
            'element' => '#SECTION_CODE#/#ELEMENT_CODE#/',
        ],
        'SEF_DEFAULT_TEMPLATE' => 'section',
        'DEFAULT_SECTION' => [
            'TITLE' => 'События',
            'NAME' => 'Все события',
            'EXTERNAL_ID' => 'events',
            'LINK' => '/events/',
        ],
    ],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
