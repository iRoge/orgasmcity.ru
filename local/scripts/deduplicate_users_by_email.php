<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 2);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\DB\Exception as DatabaseException;
use Bitrix\Main\DB\Result as DatabaseResponse;
use Qsoft\Helpers\EventHelper;

const EOL = PHP_SAPI == 'cli' ? "\n" : '<br>';

const REASONS = [
    'SOC' => 'Авторизация через социальную сеть',
    'PHONE' => 'Отсутствует номер телефона',
    'AUTH' => 'Более ранняя авторизация',
    'ID' => 'Меньший ID',
];

EventHelper::killEvents(array("OnBeforeUserUpdate", "OnAfterUserUpdate"), "main");
EventHelper::killEvents(array("OnBeforeOrderUpdate", "OnOrderUpdate"), "sale");

$usersWithNonuniqueEmail = <<<SQL
    SELECT ID, EMAIL, LOGIN, PERSONAL_PHONE AS PHONE, LAST_LOGIN, EXTERNAL_AUTH_ID AS AUTH_TYPE FROM b_user WHERE EMAIL IN (
        SELECT EMAIL FROM b_user
        WHERE EMAIL <> '' AND EMAIL IS NOT NULL
        GROUP BY EMAIL HAVING COUNT(*) > 1
        ORDER BY EMAIL
    )
SQL;

try {
    if (!Loader::includeModule('main')) {
        throw new Exception('Не удалось подключить "Главный модуль".');
    }

    if (!Loader::includeModule('sale')) {
        throw new Exception('Не удалось подключить модуль "Интернет-магазин".');
    }

    $database = Application::getConnection();

    $userList = $database->query($usersWithNonuniqueEmail);

    $nonuniqueUsers = array();
    if ($userList instanceof DatabaseResponse) {
        $nonuniqueUsers = categorizeNonuniqueUsers($userList);
    }

    if (empty($nonuniqueUsers)) {
        throw new Exception('Не найдены пользователи с неуникальным адресом электронной почты.');
    }

    $log = [];
    $migrations = [];
    $anonymizations = [];
    foreach ($nonuniqueUsers as $user) {
        $master = $user['MASTER'];
        $clones = $user['CLONE'];

        foreach ($clones as $id => $clone) {
            $log[$id]['MASTER'] = $master['ID'];
            $log[$id]['REASON'] = REASONS[$clone['REASON_CODE']];

            if ($clone['REASON_CODE'] == 'SOC' || empty(trim($clone['PHONE'])) || empty(trim($master['PHONE']))) {
                $migrations[$clone['ID']] = $master['ID'];
                continue;
            }

            $anonymizations[] = $clone['ID'];
        }
    }

    if (empty($migrations) && empty($anonymizations)) {
        throw new Exception('Не найдены пользователи, которым необходим перенос данных или обезличение.');
    }

    if (!empty($migrations)) {
        moveOrders($migrations, $log);
        movePersonalData($migrations, $log);
        deleteUsers(array_keys($migrations), $log);
    }

    if (!empty($anonymizations)) {
        anonymizeUsers($anonymizations, $log);
    }

    printLog($log, EOL);
} catch (DatabaseException $exception) {
    exit('Произошла ошибка при обращении к БД:' . EOL . $exception->getMessage() . EOL);
} catch (Exception $exception) {
    exit('Скрипт преждевременно завершил работу:' . EOL . $exception->getMessage() . EOL);
}

function categorizeNonuniqueUsers($userList)
{
    $result = [];

    while ($user = $userList->fetch()) {
        $email = $user['EMAIL'];
        @$master = $result[$email]['MASTER'];
        if (empty($master)) {
            $result[$email]['MASTER'] = $user;
            continue;
        }
        if ($user['AUTH_TYPE'] == 'socservices' && preg_match('/^(FB_|VKuser).+$/', trim($user['LOGIN'])) === 1) {
            $user['REASON_CODE'] = 'SOC';
            $result[$email]['CLONE'][$user['ID']] = $user;
            continue;
        }
        if ($master['AUTH_TYPE'] == 'socservices' && preg_match('/^(FB_|VKuser).+$/', trim($master['LOGIN'])) === 1) {
            $result[$email]['MASTER'] = $user;
            $master['REASON_CODE'] = 'SOC';
            $result[$email]['CLONE'][$master['ID']] = $master;
            continue;
        }
        if (empty(trim($user['PHONE']))) {
            $user['REASON_CODE'] = 'PHONE';
            $result[$email]['CLONE'][$user['ID']] = $user;
            continue;
        }
        if (empty(trim($master['PHONE']))) {
            $result[$email]['MASTER'] = $user;
            $master['REASON_CODE'] = 'PHONE';
            $result[$email]['CLONE'][$master['ID']] = $master;
            continue;
        }
        $masterLastLogin = $master['LAST_LOGIN'] instanceof DateTime ? $master['LAST_LOGIN']->getTimestamp() : 0;
        $userLastLogin = $user['LAST_LOGIN'] instanceof DateTime ? $user['LAST_LOGIN']->getTimestamp() : 0;
        if (!empty($masterLastLogin) || !empty($userLastLogin)) {
            if (!empty($masterLastLogin) && !empty($userLastLogin)) {
                if ($userLastLogin <= $masterLastLogin) {
                    $user['REASON_CODE'] = 'AUTH';
                    $result[$email]['CLONE'][$user['ID']] = $user;
                } else {
                    $result[$email]['MASTER'] = $user;
                    $master['REASON_CODE'] = 'AUTH';
                    $result[$email]['CLONE'][$master['ID']] = $master;
                }
            } elseif (!empty($masterLastLogin)) {
                $user['REASON_CODE'] = 'AUTH';
                $result[$email]['CLONE'][$user['ID']] = $user;
            } else {
                $result[$email]['MASTER'] = $user;
                $master['REASON_CODE'] = 'AUTH';
                $result[$email]['CLONE'][$master['ID']] = $master;
            }
            continue;
        }
        if ($user['ID'] <= $master['ID']) {
            $user['REASON_CODE'] = 'ID';
            $result[$email]['CLONE'][$user['ID']] = $user;
        } else {
            $result[$email]['MASTER'] = $user;
            $master['REASON_CODE'] = 'ID';
            $result[$email]['CLONE'][$master['ID']] = $master;
        }
    }

    return $result;
}

function moveOrders($map, &$log)
{
    $orderList = CSaleOrder::GetList(
        false,
        ['USER_ID' => array_keys($map)],
        false,
        false,
        ['ID', 'USER_ID']
    );

    while ($order = $orderList->Fetch()) {
        try {
            if (CSaleOrder::Update($order['ID'], ['USER_ID' => $map[$order['USER_ID']]]) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CSaleOrder::Update вернул false.');
            }
            $log[$order['USER_ID']]['ORDER']['SUCCESS'][] = $order['ID'];
        } catch (Exception $exception) {
            $log[$order['USER_ID']]['ORDER']['FAIL'][$order['ID']] = $exception->getMessage();
        }
    }
}

function movePersonalData($map, &$log)
{
    global $USER;

    $userList = UserTable::getList([
        'select' => [
            'ID',
            'PERSONAL_PHONE',
            'NAME',
            'LAST_NAME',
            'SECOND_NAME',
            'PERSONAL_PHONE',
            'PERSONAL_GENDER',
            'PERSONAL_BIRTHDAY',
            'PERSONAL_STREET',
            'UF_HOUSE',
            'UF_ST',
            'UF_HOUSING',
            'UF_ENTRANCE',
            'UF_FLOOR',
            'UF_APARTMENT',
            'UF_INTERCOM',
            'UF_TIME',
        ],
        'filter' => [
            '=ID' => array_unique(array_merge($map, array_keys($map)))
        ],
    ]);

    $personalData = [];
    while ($user = $userList->fetch()) {
        $personalData[$user['ID']] = $user;
    }

    $dataUpdate = [];
    foreach ($map as $donor => $acceptor) {
        foreach ($personalData[$acceptor] as $field => $value) {
            if (!empty($value) || !empty($dataUpdate[$acceptor][$field]) || empty($personalData[$donor][$field])) {
                continue;
            }
            $dataUpdate[$acceptor][$field] = $personalData[$donor][$field];
            $log[$donor]['PERSONAL_DATA']['SUCCESS'][$field] = $personalData[$donor][$field];
        }
    }

    foreach ($dataUpdate as $user => $fields) {
        try {
            if ($USER->Update($user, $fields) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CUser::Update вернул false.');
            }
        } catch (Exception $exception) {
            $clones = array_keys($map, $user);
            foreach ($clones as $clone) {
                $log[$clone]['PERSONAL_DATA']['FAIL'] = [
                    'FIELDS' => $log[$clone]['PERSONAL_DATA']['SUCCESS'],
                    'MESSAGE' => $exception->getMessage(),
                ];
                unset($log[$clone]['PERSONAL_DATA']['SUCCESS']);
            }
        }
    }
}

function deleteUsers($users, &$log)
{
    global $USER;
    $users = array_unique($users);

    foreach ($users as $id) {
        try {
            if ($USER->Delete($id) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CUser::Delete вернул false.');
            }
            $log[$id]['DELETION']['RESULT'] = true;
        } catch (Exception $exception) {
            $log[$id]['DELETION'] = [
                'RESULT' => false,
                'MESSAGE' => $exception->getMessage(),
            ];
        }
    }
}

function anonymizeUsers($users, &$log)
{
    global $USER;
    $users = array_unique($users);

    foreach ($users as $id) {
        try {
            if ($USER->Update($id, ['EMAIL' => "$id@rshoes.ru"]) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CUser::Update вернул false.');
            }
            $log[$id]['ANONYMIZATION']['RESULT'] = true;
        } catch (Exception $exception) {
            $log[$id]['ANONYMIZATION'] = [
                'RESULT' => false,
                'MESSAGE' => $exception->getMessage(),
            ];
        }
    }
}

function printLog($log, $eol)
{
    if (empty($log)) {
        return;
    }

    uasort($log, function ($a, $b) {
        return $a['MASTER'] - $b['MASTER'];
    });

    foreach ($log as $id => $record) {
        if (isset($record['ANONYMIZATION'])) {
            $result = $record['ANONYMIZATION']['RESULT'] ? 'Email заменён на технический.' : ('Не удалось заменить email на технический (' . $record['ANONYMIZATION']['MESSAGE'] . ').');
        }

        if (isset($record['DELETION'])) {
            $result = $record['DELETION']['RESULT'] ? 'Пользователь удалён.' : ('Не удалось удалить пользователя (' . $record['DELETION']['MESSAGE'] . ').');
        }

        echo "КЛОН: $id, МАСТЕР: " . $record['MASTER'] . ' [' . $record['REASON'] . ']' . ($result ? " / $result" : '') . $eol;

        if (!empty($record['ORDER']['SUCCESS'])) {
            echo 'ПЕРЕНЕСЁННЫЕ ЗАКАЗЫ: ' . implode(', ', $record['ORDER']['SUCCESS']) . ';' . $eol;
        }

        if (!empty($record['ORDER']['FAIL'])) {
            $fails = [];
            foreach ($record['ORDER']['FAIL'] as $id => $message) {
                $fails[] = "$id ($message)";
            }
            echo 'НЕПЕРЕНЕСЁННЫЕ ЗАКАЗЫ: ' . implode(', ', $fails) . ';' . $eol;
        }

        if (!empty($record['PERSONAL_DATA']['SUCCESS'])) {
            $fields = [];
            foreach ($record['PERSONAL_DATA']['SUCCESS'] as $field => $value) {
                $fields[] = "$field = \"$value\"";
            }
            echo 'ПЕРЕНЕСЁННЫЕ ПОЛЯ ЛК: ' . implode(', ', $fields) . ';' . $eol;
        }

        if (!empty($record['PERSONAL_DATA']['FAIL'])) {
            echo 'НЕПЕРЕНЕСЁННЫЕ ПОЛЯ ЛК: ' . implode(', ', array_keys($record['PERSONAL_DATA']['FAIL']['FIELDS'])) . ' (' . $record['PERSONAL_DATA']['FAIL']['MESSAGE'] . ')' . ';' . $eol;
        }

        echo $eol;
    }
}
