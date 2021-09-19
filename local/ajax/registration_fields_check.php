<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$id = (int)$_POST['id'];
$email = mb_strtolower($_POST['email']);
$phone = $_POST['personal_phone'];
$word = strtoupper($_POST['captcha_word']);
$sid = $_POST['captcha_sid'];
$arrayAnswer = [
    'email' => 1,
    'phone' => 1,
    'captcha' => 1
];

$user = $DB->Query("SELECT ID, NAME, EMAIL, PERSONAL_PHONE FROM b_user WHERE ID = '" . $DB->ForSQL($id) . "'")->Fetch();
$arrayAnswer['userName'] = $user["NAME"];
$arrayAnswer['userEmail'] = $user["EMAIL"];
$arrayAnswer['userPhone'] = $user["PERSONAL_PHONE"];

if (strlen($email) <= 0) {
    $arrayAnswer['email'] = "noRemoveClass";
} else {
    $resultEmail = $DB->Query("SELECT ID, EMAIL FROM b_user WHERE EMAIL = '" . $DB->ForSQL($email) . "'");

    if (!$array = $resultEmail->Fetch()) {
        $arrayAnswer['email'] = 0;
    } elseif (!empty($id) && $array['ID'] == $id){
        $arrayAnswer['email'] = 0;
    }
}

if (strlen($phone) <= 0) {
    $arrayAnswer['phone'] = "noRemoveClass";
} else {
    $resultPhone = $DB->Query("SELECT ID, PERSONAL_PHONE FROM b_user WHERE PERSONAL_PHONE = '" . $DB->ForSQL($phone) . "'");

    if (!$array = $resultPhone->Fetch()) {
        $arrayAnswer['phone'] = 0;
    } elseif (!empty($id) && $array['ID'] == $id) {
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
