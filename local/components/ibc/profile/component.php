<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;

if ($USER->IsAdmin()){ 
	$arResult['BTN'] = '<button class="refresh">Обновить описание</button>';
}else{
	$arResult['BTN'] = "Ты не авторизован!";
}

$this->IncludeComponentTemplate();
?>