<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/shop.js');
//\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/pages/shops.js');
\Likee\Site\Helper::addBodyClass('page--shops');
$APPLICATION->SetPageProperty('SHOW_CITY_FILTER', 'Y');