<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$instashoppingTemplatePath = '/local/components/likee/instashopping/templates/.default/';

\Bitrix\Main\Page\Asset::getInstance()->addCss($instashoppingTemplatePath . '/style.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs($instashoppingTemplatePath . '/script.js');