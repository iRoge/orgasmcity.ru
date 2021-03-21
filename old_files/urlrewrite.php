<?php
$arUrlRewrite=array (
  41 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ),
  20 => 
  array (
    'CONDITION' => '#^/catalog/sale/#',
    'RULE' => '',
    'ID' => 'fire:catalog',
    'PATH' => '/catalog/sale/index.php',
    'SORT' => 100,
  ),
  21 => 
  array (
    'CONDITION' => '#^/catalog/new/#',
    'RULE' => '',
    'ID' => 'fire:catalog',
    'PATH' => '/catalog/new/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/info/shops/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/info/shops/index.php',
    'SORT' => 100,
  ),
  22 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'fire:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  19 => 
  array (
    'CONDITION' => '#^/landing/#',
    'RULE' => '',
    'ID' => 'fire:landing',
    'PATH' => '/landing/index.php',
    'SORT' => 100,
  ),
  30 => 
  array (
    'CONDITION' => '#^/forum/#',
    'RULE' => '',
    'ID' => 'bitrix:forum',
    'PATH' => '/forum/index.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => 'bitrix:rest.provider',
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  40 => 
  array (
    'CONDITION' => '#^/blog/#',
    'RULE' => '',
    'ID' => 'fire:blog',
    'PATH' => '/blog/index.php',
    'SORT' => 100,
  ),
);
