<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\UserTable;
use Qsoft\Helpers\EventHelper;

const EOL = PHP_SAPI === 'cli' ? "\n" : '<br>';
$arLog = [];
$arFail = [];

EventHelper::killEvents(array("OnBeforeUserUpdate", "OnAfterUserUpdate"), "main");
EventHelper::killEvents(array("OnBeforeOrderUpdate", "OnOrderUpdate"), "sale");

// Получение списка пользователей с неуникальным номером телефона
$usersWithNonUniquePhone = <<<SQL
    SELECT ID, EMAIL, PERSONAL_PHONE, LAST_LOGIN FROM b_user WHERE PERSONAL_PHONE IN (
        SELECT PERSONAL_PHONE FROM b_user
        WHERE PERSONAL_PHONE <> '' AND PERSONAL_PHONE IS NOT NULL
        GROUP BY PERSONAL_PHONE HAVING COUNT(*) > 1
    )
SQL;

$arNonuniqueUsers = [];
$database = Application::getConnection();

try {
    $dbNonuniqueUsers = $database->query($usersWithNonUniquePhone);
} catch (Exception $e) {
    echo 'Произошла ошибка во время запроса списка пользователей с неуникальным номером телефона:' . EOL;
    echo $e->getMessage() . EOL;
    exit();
}

while ($user = $dbNonuniqueUsers->fetch()) {
    $phone = $user['PERSONAL_PHONE'];
    @$master = $arNonuniqueUsers[$phone]['MASTER'];

    if (is_null($master)) {
        $arNonuniqueUsers[$phone]['MASTER'] = $user;
        continue;
    }

    if (preg_match('/\@rshoes.ru$/iu', trim($user['EMAIL'])) == 1) {
        $user['WHY_CLONE'] = 'технический email';
        $arNonuniqueUsers[$phone]['CLONE'][$user['ID']] = $user;
        continue;
    }

    if (preg_match('/\@rshoes.ru$/iu', trim($master['EMAIL'])) == 1) {
        $arNonuniqueUsers[$phone]['MASTER'] = $user;
        $master['WHY_CLONE'] = 'технический email';
        $arNonuniqueUsers[$phone]['CLONE'][$master['ID']] = $master;
        continue;
    }

    $masteLastLogin = is_object($master['LAST_LOGIN']) ? $master['LAST_LOGIN']->getTimestamp() : 0;
    $userLastLogin = is_object($user['LAST_LOGIN']) ? $user['LAST_LOGIN']->getTimestamp() : 0;
    
    if ($userLastLogin < $masteLastLogin) {
        $user['WHY_CLONE'] = 'более ранняя авторизация';
        $arNonuniqueUsers[$phone]['CLONE'][$user['ID']] = $user;
        continue;
    }

    if ($masteLastLogin < $userLastLogin) {
        $arNonuniqueUsers[$phone]['MASTER'] = $user;
        $master['WHY_CLONE'] = 'более ранняя авторизация';
        $arNonuniqueUsers[$phone]['CLONE'][$master['ID']] = $master;
        continue;
    }

    if ($user['ID'] < $master['ID']) {
        $user['WHY_CLONE'] = 'меньший ID';
        $arNonuniqueUsers[$phone]['CLONE'][$user['ID']] = $user;
        continue;
    }
    
    if ($master['ID'] < $user['ID']) {
        $arNonuniqueUsers[$phone]['MASTER'] = $user;
        $master['WHY_CLONE'] = 'меньший ID';
        $arNonuniqueUsers[$phone]['CLONE'][$master['ID']] = $master;
        continue;
    }

    $user['WHY_CLONE'] = 'равноправная копия';
    $arNonuniqueUsers[$phone]['CLONE'][$user['ID']] = $user;
}

if (empty($arNonuniqueUsers)) {
    exit('Пользователи с неуникальным номером телефона не обнаружены.' . EOL);
}

echo 'Число обнаруженных пользователей с неуникальным номером телефона: ' . $dbNonuniqueUsers->getSelectedRowsCount() . '.' . EOL;
echo 'Число номеров телефонов, ассоциированных с этими пользователями: ' . count($arNonuniqueUsers). '.' . EOL;

$arMigration = [];
$arPhoneErasing = [];
foreach ($arNonuniqueUsers as $phone => $users) {
    foreach ($users['CLONE'] as $id => $clone) {
        $arLog[$id] = [
            'ID' => $id,
            'MASTER' => $users['MASTER']['ID'],
            'REASON' => $clone['WHY_CLONE'],
            'ORDERS_MOVED' => [],
            'FIELDS_MOVED' => [],
            'PHONE_ERASED' => false,
            'DELETED' => false,
        ];
        
        if ($users['MASTER']['EMAIL'] == $clone['EMAIL'] || preg_match('/\@rshoes.ru$/iu', trim($clone['EMAIL'])) == 1) {
            $arMigration[$id] = $users['MASTER']['ID'];
            continue;
        }

        if ($users['MASTER']['EMAIL'] != $clone['EMAIL']) {
            $arPhoneErasing[] = $id;
        }
    }
}

// Очистка поля 'PERSONAL_PHONE' у дублирующих пользователей
if (!empty($arPhoneErasing)) {
    $erasePersonalPhone = 'UPDATE b_user SET PERSONAL_PHONE = "" WHERE ID IN (' . implode(', ', $arPhoneErasing) . ')';
    
    try {
        $database->query($erasePersonalPhone);
        foreach ($arPhoneErasing as $id) {
            $arLog[$id]['PHONE_ERASED'] = true;
        }
    } catch (Exception $e) {
        $arFail['PHONE_ERASED'] = $e->getMessage();
    }
}

// Перенос заказов
$dbOrders = CSaleOrder::GetList(
    false,
    ['USER_ID' => array_keys($arMigration)],
    false,
    false,
    ['ID', 'USER_ID']
);

$arOrderUpdate = [];
while ($arOrder = $dbOrders->Fetch()) {
    $arOrderUpdate[$arOrder['ID']] = ['NEW' => $arMigration[$arOrder['USER_ID']], 'OLD' => $arOrder['USER_ID']];
}

if (!empty($arOrderUpdate)) {
    foreach ($arOrderUpdate as $orderID => $userID) {
        try {
            if (CSaleOrder::Update($orderID, ['USER_ID' => $userID['NEW']]) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CSaleOrder::Update вернул false');
            }
            $arLog[$userID['OLD']]['ORDERS_MOVED'][] = $orderID;
        } catch (Exception $e) {
            $arFail['ORDER_MOVED'][$orderID] = ['OLD_USER' => $userID['OLD'], 'NEW_USER' => $userID['NEW'], 'ERROR' => $e->getMessage()];
        }
    }
}

// Перенос личной информации
try {
    if (empty($arMigration)) {
        throw new Exception('Пользователи, которым необходим перенос личных данных, не обнаружены.');
    }

    $dbUserPersonalInfo = UserTable::getList([
        'select' => [
            'ID',
            'PERSONAL_PHONE',
            'LAST_NAME',
            'SECOND_NAME',
            'PERSONAL_GENDER',
            'PERSONAL_BIRTHDAY',
            'EMAIL',
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
        'filter' => ['=ID' => array_unique(array_merge($arMigration, array_keys($arMigration)))],
    ]);

    $arUserPersonalInfo = [];
    while ($arInformation = $dbUserPersonalInfo->fetch()) {
        $arUserPersonalInfo[$arInformation['ID']] = $arInformation;
    }

    if (empty($arUserPersonalInfo)) {
        throw new Exception('Личные данные пользователей не обнаружены.');
    }

    $arUserUpdate = [];
    foreach ($arMigration as $donorID => $acceptorID) {
        foreach ($arUserPersonalInfo[$acceptorID] as $field => $value) {
            if (empty($value) && empty($arUserUpdate[$acceptorID][$field]) && !empty($arUserPersonalInfo[$donorID][$field])) {
                $arUserUpdate[$acceptorID][$field] = $arUserPersonalInfo[$donorID][$field];
            }
        }
    }

    if (empty($arUserUpdate)) {
        throw new Exception('Нет новых данных для переноса.');
    }
    
    foreach ($arOrderUpdate as $orderID => $userID) {
        try {
            if (CSaleOrder::Update($orderID, ['USER_ID' => $userID['NEW']]) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CSaleOrder::Update вернул false');
            }
            $arLog[$userID['OLD']]['ORDERS_MOVED'][] = $orderID;
        } catch (Exception $e) {
            $arFail['ORDER_MOVED'][$orderID] = ['OLD_USER' => $userID['OLD'], 'NEW_USER' => $userID['NEW'], 'ERROR' => $e->getMessage()];
        }
    }

    foreach ($arUserUpdate as $userID => $arFields) {
        try {
            if ($USER->Update($userID, $arFields) === false) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CUser::Update вернул false');
            }

            $donorID = array_search($userID, $arMigration);
            $arLog[$donorID]['FIELDS_MOVED'] = array_merge($arLog[$donorID]['FIELDS_MOVED'], array_keys($arFields));
        } catch (Exception $e) {
            $donorID = array_search($userID, $arMigration);
            $arFail['FIELDS_MOVED'][$userID] = ['DONOR' => $donorID, 'FIELDS' => $arFields, 'ERROR' => $e->getMessage()];
        }
    }
} catch (Exception $e) {
    $arFail['FIELDS_MOVED'] = $e->getMessage();
}

// Удаление дублирующих пользователей
if (!empty($arMigration)) {
    $arDeletionID = array_keys($arMigration);
    $arDeletionResult = [];

    foreach ($arDeletionID as $id) {
        try {
            if (!$USER->Delete($id)) {
                throw new Exception($APPLICATION->GetException()->msg ?: 'CUser::Delete вернул false');
            }

            $arLog[$id]['DELETED'] = true;
        } catch (Exception $e) {
            $arFail['DELETED'][$id] = $e->getMessage();
        }
    }
}

if (!empty($arLog)) {
    uasort($arLog, function ($a, $b) {
        return $b['MASTER'] < $a['MASTER'];
    });
}
?>

<?php
if (!empty($arFail)) {
    if (!empty($arFail['PHONE_ERASED'])) {
        $arFail['PHONE_ERASED'] . EOL . EOL;
    }

    if (!empty($arFail['ORDER_MOVED'])) {
        echo 'Не удалось перенести следующие заказы:' . EOL;
        foreach ($arFail['ORDER_MOVED'] as $orderID => $data) {
            echo $orderID . ': ' . $data['OLD_USER'] . ' -/-> ' . $data['NEW_USER'] . ($data['ERROR'] ? ' (' . $data['ERROR'] . ');' : ';') . EOL;
        }
        echo EOL;
    }
    
    if (!empty($arFail['FIELDS_MOVED']) && is_string($arFail['FIELDS_MOVED'])) {
        echo 'Не удалось перенести личные данные пользователей:' . EOL;
        echo $arFail['FIELDS_MOVED'] . EOL . EOL;
    }

    if (!empty($arFail['FIELDS_MOVED']) && is_array($arFail['FIELDS_MOVED'])) {
        echo 'Не удалось обновить личные данные следующих пользователей:' . EOL;
        foreach ($arFail['FIELDS_MOVED'] as $userID => $data) {
            $fields = [];
            foreach ($data['FIELDS'] as $fieldCode => $fieldValue) {
                $fields[] = "$fieldCode = $fieldValue";
            }
            $fields = '[' . implode(', ', $fields) . ']';

            echo $userID . ': ' . $fields . ', донор: ' . $data['DONOR'] . ($data['ERROR'] ? ' (' . $data['ERROR'] . ');' : ';') . EOL;
        }
        echo EOL;
    }

    if (!empty($arFail['DELETED'])) {
        echo 'Не удалось удалить следующих пользователей:' . EOL;
        foreach ($arFail['DELETED'] as $userID => $error) {
            echo $userID . ($data['ERROR'] ? ': ' . $data['ERROR'] : '') . ';' . EOL;
        }
        echo EOL;
    }
}

if (!empty($arLog)) {
    echo 'ИЗМЕНЁННЫЕ ПОЛЬЗОВАТЕЛИ:' . EOL;

    foreach ($arLog as $arRecord) {
        echo 'Пользователь ' . $arRecord['ID'] . ', мастер ' . $arRecord['MASTER'] . ', ' , $arRecord['REASON'] . ', ';
        echo !empty($arRecord['ORDERS_MOVED']) ? 'заказы: ' . implode(', ', $arRecord['ORDERS_MOVED']) . ', ' : 'заказы не перенесены, ';
        echo !empty($arRecord['FIELDS_MOVED']) ? 'поля: ' . implode(', ', $arRecord['FIELDS_MOVED']) . ' ' : 'поля не перенесены ';
        echo '(' . ($arRecord['DELETED'] ? 'пользователь удалён' : 'стёрт номер телефона') . ')';
        echo ';' . EOL;
    }
}

