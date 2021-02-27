<?php
const ARRAY1 = array(
    0 =>
        array(
            'CONDITION' => '#^/articles/([a-zA-Z0-9_-]+)(?:/)*(?:[?]+.*)*$#',
            'RULE' => 'article_code=$1',
            'ID' => '',
            'PATH' => '/articles/detail.php',
            'SORT' => 100,
        ),
    1 =>
        array(
            'CONDITION' => '#^/tenders/([a-zA-Z0-9_-]+)(?:/)*(?:[?]+.*)*$#',
            'RULE' => 'tender_code=$1',
            'ID' => '',
            'PATH' => '/tenders/detail.php',
            'SORT' => 100,
        ),
    2 =>
        array(
            'CONDITION' => '#^/bitrix/services/ymarket/#',
            'RULE' => '',
            'ID' => '',
            'PATH' => '/bitrix/services/ymarket/index.php',
            'SORT' => 100,
        ),
    13 =>
        array(
            'CONDITION' => '#^/lookbook/page-([0-9]+)#',
            'RULE' => 'page=$1',
            'ID' => '',
            'PATH' => '/lookbook/index.php',
            'SORT' => 100,
        ),
    200 =>
        array(
            'CONDITION' => '#^/goroskop/page-([0-9]+)#',
            'RULE' => 'page=$1',
            'ID' => '',
            'PATH' => '/goroskop/index.php',
            'SORT' => 100,
        ),
    10 =>
        array(
            'CONDITION' => '#^/stssync/calendar/#',
            'RULE' => '',
            'ID' => 'bitrix:stssync.server',
            'PATH' => '/bitrix/services/stssync/calendar/index.php',
            'SORT' => 100,
        ),
    4 =>
        array(
            'CONDITION' => '#^/personal/orders/#',
            'RULE' => '',
            'ID' => 'bitrix:sale.personal.order',
            'PATH' => '/personal/orders/index.php',
            'SORT' => 100,
        ),
    5 =>
        array(
            'CONDITION' => '#^/news/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/events/index.php',
            'SORT' => 100,
        ),
    6 =>
        array(
            'CONDITION' => '#^/actions/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/events/index.php',
            'SORT' => 100,
        ),
    8 =>
        array(
            'CONDITION' => '#^/events/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/events/index.php',
            'SORT' => 100,
        ),
    7 =>
        array(
            'CONDITION' => '#^/shops/#',
            'RULE' => '',
            'ID' => 'bitrix:catalog.store',
            'PATH' => '/shops/index.php',
            'SORT' => 100,
        ),
    9 =>
        array(
            'CONDITION' => '#^/rest/#',
            'RULE' => '',
            'ID' => null,
            'PATH' => '/bitrix/services/rest/index.php',
            'SORT' => 100,
        ),
    3 =>
        array(
            'CONDITION' => '#^/api/#',
            'RULE' => '',
            'ID' => 'likee:1c.exchange',
            'PATH' => '/api/index.php',
            'SORT' => 100,
        ),
    11 =>
        array(
            'CONDITION' => '#^/(.+)#',
            'RULE' => '',
            'ID' => 'bitrix:catalog',
            'PATH' => '/catalog.php',
            'SORT' => 110,
        ),
    14 =>
        array(
            'CONDITION' => '#^/opt/#',
            'RULE' => '',
            'ID' => '',
            'PATH' => '/opt/index.php',
            'SORT' => 100,
        ),
    15 =>
        array(
            'CONDITION' => '#^/webmaster/#',
            'RULE' => '',
            'ID' => '',
            'PATH' => '/webmaster/index.php',
            'SORT' => 100,
        ),
);
$arUrlRewrite= ARRAY1;
