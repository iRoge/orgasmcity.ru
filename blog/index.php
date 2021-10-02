<?php
@define(HIDE_TITLE, true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $APPLICATION;

$APPLICATION->IncludeComponent(
    "rdevs:events",
    "",
    [
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '86400',
        'CACHE_DIR' => '/blog',
        'IBLOCK_CODE' => 'blog',
        'IBLOCK_ID' => IBLOCK_BLOG,
        'SEF_FOLDER' => '/',
        'SEF_URL_TEMPLATES' => [
            'element' => '#SECTION_CODE#/#ELEMENT_CODE#/',
            'section' => '#SECTION_CODE#/',
        ],
        'SEF_DEFAULT_TEMPLATE' => 'section',
        'DEFAULT_SECTION' => [
            'TITLE' => 'Блог',
            'NAME' => 'Все посты',
            'EXTERNAL_ID' => 'blog',
            'LINK' => '/blog/',
        ],
    ],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
