<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
ob_start();
use PHPMailer\PHPMailer\Exception;

// 1 - яндекс, 2 - майл, 3 - gmail, 4 - rambler, 5 - other, 6 - icloud
$limitsForDomainsTypesPerScript = [
    1 => 5,
    2 => 5,
    3 => 5,
    4 => 5,
    5 => 0,
    6 => 5,
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
            "SORT" => "ASC"
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
            'DATE_ACTION_END' => getActionDateEnd(),
        ];
        $message = Functions::insertFields($mailing['DETAIL_TEXT'], $fields);

        try {
            Functions::sendMarketingMail($subscriber['PROPERTY_EMAIL_VALUE'], $mailing['PREVIEW_TEXT'], $message, $subscriber['ID']);
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
        '@icloud.ru' => 6,
    ];

    foreach ($domainsTypes as $domain => $type) {
        if (strpos($email, $domain) !== false) {
            return $type;
        }
    }

    return 5;
}

function getActionDateEnd() {
    $dateTime = DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->modify('+5 days')->format('j F');
    $enMonths = [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    ];
    $ruMonths = [
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ];

    return str_replace($enMonths, $ruMonths, $dateTime);
}
