<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$arrayAnswer = [
    'loginError' => null,
];

if (empty($email)) {
    $userFields = $DB->Query("SELECT PERSONAL_PHONE, EMAIL FROM b_user WHERE PERSONAL_PHONE = '" . $DB->ForSQL($phone) . "'");
    if ($arUser = $userFields->Fetch()) {
        $email = $arUser['EMAIL'];
    }
    $errorText = 'Неверный телефон или пароль!';
    $event2 = "phone";
} else {
    $errorText = 'Неверный email или пароль!';
    $event2 = "email";
}
$isAuth = $USER->Login($email, $password);
if ($isAuth !== true) {
    $arrayAnswer['loginError'] = '<p>' . $errorText . '</p>';
}

echo json_encode($arrayAnswer);
