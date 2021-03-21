<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация на сайте");
?><?$APPLICATION->IncludeComponent(
	"fire:main.register", 
	"opt", 
	array(
		"AUTH" => "Y",
		"COMPONENT_TEMPLATE" => "opt",
		"SUCCESS_PAGE" => "/register/",
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y",
		"USER_CONSENT" => "Y",
		"USER_CONSENT_ID" => "1",
		"USER_CONSENT_IS_CHECKED" => "N",
		"USER_CONSENT_IS_LOADED" => "N"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>