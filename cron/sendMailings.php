<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
ob_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// 1 - яндекс, 2 - майл, 3 - gmail, 4 - rambler, 5 - other
$limitsForDomainsTypesPerScript = [
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
    $receivedEmails = json_decode($mailing['~PROPERTY_RECEIVED_EMAILS_VALUE'], true);
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
        if (!$limitsForDomainsTypesPerScript[$domainType]) {
            continue;
        }

        $fields = [
            'EMAIL' => $subscriber['PROPERTY_EMAIL_VALUE'],
            'NAME' => $subscriber['PROPERTY_NAME_VALUE'],
            'SECOND_NAME' => $subscriber['PROPERTY_SECOND_NAME_VALUE'],
            'MIDDLE_NAME' => $subscriber['PROPERTY_MIDDLE_NAME_VALUE'],
            'SUBSCRIBER_ID' => $subscriber['ID'],
            'TITLE' => $mailing['PREVIEW_TEXT'],
        ];
        $message = insertFields($mailing['DETAIL_TEXT'], $fields);

        try {
            sendMail($subscriber['PROPERTY_EMAIL_VALUE'], $subscriber['ID'], $message, $mailing['DETAIL_TEXT']);
            echo 'Message has been sent to ' . $subscriber['PROPERTY_EMAIL_VALUE'] . PHP_EOL;
            $limitsForDomainsTypesPerScript[$domainType]--;
            $receivedEmails[] = $subscriber['PROPERTY_EMAIL_VALUE'];
            $props = [];
            $props['RECEIVED_EMAILS'] = json_encode($receivedEmails, JSON_FORCE_OBJECT);
            CIBlockElement::SetPropertyValuesEx($mailing['ID'], IBLOCK_MAILINGS, $props);
            $el = new CIBlockElement();
            $el->Update($subscriber['ID'], ['ACTIVE' => 'N']);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$e->getMessage()}" . PHP_EOL;
        }
    }
}
$result = ob_get_clean();
orgasm_logger($result, 'mailingsLog.log', '/cron/logs/');

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

function insertFields($message, $fields)
{
    foreach ($fields as $field => $value) {
        $message = str_replace('##' . $field . '##', $value, $message);
    }

    return $message;
}

/**
 * @throws Exception
 */
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
        '<https://' . DOMAIN_NAME . '/unsubscribe/?email=' . $emailTo . '&id=' . $subscriberID . '&check=1>'
    );
    $mail->AddCustomHeader(
        "List-Unsubscribe-Post",
        'List-Unsubscribe=One-Click'
    );
    $mail->AddCustomHeader(
        "Precedence",
        'bulk'
    );
    $mail->send();
}
