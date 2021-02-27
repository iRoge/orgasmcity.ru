<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
$APPLICATION->SetPageProperty('HIDE_INSTAGRAM', 'Y');

global $USER;
if ($USER->IsAuthorized()) {
    LocalRedirect('/auth/');
}

\Likee\Site\Helper::addBodyClass('body--auth');

?>
<div class="column-8 column-center">
    <div class="auth-div-full2" style="display: block;">
<? $APPLICATION->IncludeComponent(
    "bitrix:main.register",
    "",
    array(
        'IN_HEADER' => 'Y',
        "USER_PROPERTY_NAME" => "",
        "SEF_MODE" => "Y",
        "SHOW_FIELDS" => array(
            1 => "NAME",
            2 => "SECOND_NAME",
            3 => "LAST_NAME",
            4 => "PERSONAL_BIRTHDAY",
            0 => "EMAIL",
            5 => "PERSONAL_PHONE",
            6 => 'PASSWORD',
            7 => 'CONFIRM_PASSWORD',
            8 => 'PERSONAL_GENDER'
        ),
        "REQUIRED_FIELDS" => array(
            0 => "EMAIL",
            1 => "NAME",
            2 => "SECOND_NAME",
            3 => "LAST_NAME",
            4 => "PERSONAL_BIRTHDAY",
            5 => "PERSONAL_PHONE",
            6 => 'PASSWORD',
            7 => 'CONFIRM_PASSWORD',
            8 => 'PERSONAL_GENDER'
        ),
        "AUTH" => "Y",
        "USE_BACKURL" => "Y",
        "SUCCESS_PAGE" => "/",
        "SET_TITLE" => "N",
        "USER_PROPERTY" => array(),
        "SEF_FOLDER" => "/registration/",
        "COMPONENT_TEMPLATE" => ".default",
        'POLITIC_FILE' => '/upload/files/offer.docx'
    )
); ?>
</div>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>