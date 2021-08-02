<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/articles/([a-zA-Z0-9_-]+)(?:/)*(?:[?]+.*)*$#',
    'RULE' => 'article_code=$1',
    'ID' => '',
    'PATH' => '/articles/detail.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/personal/orders/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/orders/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/press_center/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/press_center/index.php',
    'SORT' => 100,
  ),
  15 => 
  array (
    'CONDITION' => '#^/webmaster/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/webmaster/index.php',
    'SORT' => 100,
  ),
  8 => 
  array (
    'CONDITION' => '#^/actions/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/actions/index.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/events/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/events/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/shops/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/shops/index.php',
    'SORT' => 100,
  ),
  9 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/blog/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/blog/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/(.+)#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog.php',
    'SORT' => 110,
  ),
);
