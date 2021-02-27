<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$email = mb_strtolower($_POST['email']);
$phone = $_POST['personal_phone'];
$word = $_POST['captcha_word'];
$sid = $_POST['captcha_sid'];
$arrayAnswer = [
    'email' => 1,
    'phone' => 1,
    'captcha' => 1
];

if (strlen($email) <= 0) {
    $arrayAnswer['email'] = "noRemoveClass";
} else {
    $resultEmail = $DB->Query("SELECT EMAIL FROM b_user WHERE EMAIL = '" . $DB->ForSQL($email) . "'");

    if (!$array = $resultEmail->Fetch()) {
        $arrayAnswer['email'] = 0;
    }
}

if (strlen($phone) <= 0) {
    $arrayAnswer['phone'] = "noRemoveClass";
} else {
    $resultPhone = $DB->Query("SELECT PERSONAL_PHONE FROM b_user WHERE PERSONAL_PHONE = '" . $DB->ForSQL($phone) . "'");

    if (!$array = $resultPhone->Fetch()) {
        $arrayAnswer['phone'] = 0;
    }
}

if (strlen($word) <= 0 || strlen($sid) <= 0) {
    $arrayAnswer['captcha'] = "noRemoveClass";
} else {
    $resultCaptcha = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '" . $DB->ForSQL($sid, 32) . "'");

    if ($array = $resultCaptcha->Fetch()) {
        if ($array["CODE"] == $word) {
            $arrayAnswer['captcha'] = 0;
        }
    }
}

echo json_encode($arrayAnswer);
