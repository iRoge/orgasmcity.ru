<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// 1 - яндекс, 2 - майл, 3 - gmail, 4 - rambler, 5 - other
$limitsForDomainsTypes = [
    1 => 1,
    2 => 1,
    3 => 1,
    4 => 1,
    5 => 1,
];

$mailing = CIBlockElement::GetList(
    [
        "ID" => "ASC"
    ],
    [
        'IBLOCK_ID' => IBLOCK_MAILINGS,
        'ACTIVE' => 'Y'
    ],
    false,
    false,
    ['ID', 'PROPERTY_RECEIVED_EMAILS', 'DETAIL_TEXT', 'PREVIEW_TEXT']
)->GetNext();

if ($mailing) {
    $receivedEmails = unserialize($mailing['PROPERTY_RECEIVED_EMAILS_VALUE']);
    $result = CIBlockElement::GetList(
        [
            "ID" => "ASC"
        ],
        [
            'IBLOCK_ID' => IBLOCK_SUBSCRIBERS,
            'ACTIVE' => 'Y',
        ],
        false,
        false,
        ['ID', 'PROPERTY_EMAIL', 'PROPERTY_NAME', 'PROPERTY_SECOND_NAME', 'PROPERTY_MIDDLE_NAME']
    );
    while ($subscriber = $result->GetNext()) {
        if (in_array($subscriber['PROPERTY_EMAIL_VALUE'], $receivedEmails)) {
            continue;
        }
        $domainType = getDomainType($subscriber['PROPERTY_EMAIL_VALUE']);
        if (!$limitsForDomainsTypes[$domainType]) {
            continue;
        }

        try {
            sendMail($subscriber['PROPERTY_EMAIL_VALUE'], $subscriber['ID'], $mailing['PREVIEW_TEXT'], $mailing['DETAIL_TEXT']);
            $limitsForDomainsTypes[$domainType]--;
            $receivedEmails[] = $subscriber['PROPERTY_EMAIL_VALUE'];
            $props = [];
            $props['RECEIVED_EMAILS'] = serialize($receivedEmails);
            CIBlockElement::SetPropertyValuesEx($mailing['ID'], IBLOCK_MAILINGS, $props);
            echo 'Message has been sent to ' . $subscriber['PROPERTY_EMAIL_VALUE'] . PHP_EOL;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$e->getMessage()}" . PHP_EOL;
        }
    }
}

function getDomainType($email)
{
    $domainsTypes = [
        '@yandex.ru' => 1,
        '@yandex.com' => 1,
        '@ya.ru' => 1,
        '@ya.com' => 1,
        '@mail.ru' => 2,
        '@list.ru' => 2,
        '@internet.ru' => 2,
        '@bk.ru' => 2,
        '@inbox.ru' => 2,
        '@gmail.com' => 3,
        '@rambler.ru' => 4,
    ];

    foreach ($domainsTypes as $domain => $type) {
        if (strpos($email, $domain) !== false) {
            return $type;
        }
    }

    return 5;
}

function sendMail($emailTo, $subscriberID, $subject, $body)
{
    $mail = new PHPMailer(true);
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = 'smtp.orgasmcity.ru';
    $mail->SMTPAuth = true;
    $mail->SMTPDebug = 0;
    $mail->Username = 'market@orgasmcity.ru';
    $mail->Password = 'org@smcity-market';
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    //Recipients
    $mail->setFrom('market@orgasmcity.ru', 'Ваш проводник в Городе Оргазма');
    $mail->addAddress($emailTo);

    //Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddCustomHeader(
        "List-Unsubscribe",
        'https://' . DOMAIN_NAME . '/unsubscribe/?email=' . $emailTo . '&id=' . $subscriberID
    );
    $mail->AddCustomHeader(
        "Precedence",
        'bulk'
    );
    $mail->send();
}
