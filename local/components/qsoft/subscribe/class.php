<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Qsoft\Sailplay\Tasks\SubscribeTask;
use Qsoft\Sailplay\Tasks\TaskManager;
use Qsoft\Sailplay\Tasks\TaskManagerException;
use Qsoft\Sailplay\Tasks\TaskRouter;

class QsoftSubscribeComponent extends \CBitrixComponent
{

    private const SUBSCRIBE_TABLE = 'qsoft_subscribe_token';
    private const EMAIL_UF = 'UF_SUBSCRIBE_EMAIL';

    public function executeComponent()
    {
        global $APPLICATION;
        if ($this->GetTemplateName() === 'check') {
            $token = $this->request->get('token');
            if (isset($token)) {
                $this->processToken($token);
            } else {
                return Functions::abort404();
            }
            $this->includeComponentTemplate();
        } else {
            $bAjax = $this->request->isAjaxRequest();
            if ($bAjax) {
                $APPLICATION->RestartBuffer();
            }

            if ($this->request->isPost() && check_bitrix_sessid() && $bAjax) {
                $this->processAjaxRequest();
            }
            $this->includeComponentTemplate();
            if ($bAjax) {
                $APPLICATION->FinalActions();
                exit;
            }
        }
    }

    private function processAjaxRequest()
    {
        $user = $this->getUser($this->request->get('EMAIL'));
        $this->arResult['MESSAGE'] = "Пожалуйста, подтвердите подписку, перейдя по ссылке из письма. Ссылка будет активна 24 часа.";
        if ($user === false) {
            $user = [
                'EMAIL' => $this->request->get('EMAIL'),
                'ID' => 0,
            ];
            $this->generateToken($user);
        } else {
            if ($user[self::EMAIL_UF] == 1) {
                // сценарии 1,2,3
                $taskManager = new TaskManager();
                $taskManager->setUser($user['ID']);
                $taskManager->addTask('subscribe', ['source' => 'footer', 'siteStatus' => 'Y']);
                $this->arResult['MESSAGE'] = "Вы уже подписаны!";
            } else {
                $this->generateToken($user);
            }
        }
    }

    private function processToken($token)
    {
        global $USER;

        $tokenResult = $this->getToken($token);
        if (!$tokenResult) {
            $this->arResult['MESSAGE'] = 'Ссылка устарела. Вы можете повторно получить ссылку заполнив форму подписки внизу сайта.';
            return false;
        }

        $user = $this->getUser($tokenResult['email']);
        $taskManager = new TaskManager();

        if ($tokenResult['user_id'] != 0 && $user) {
            $this->setSubscribeUser($tokenResult['user_id']);
            $taskManager->setUser($tokenResult['user_id']);
            $taskManager->addTask('subscribe', ['source' => 'footer', 'siteStatus' => 'W']);
            $USER->Authorize($tokenResult['user_id']);
            // сценарии 4,5,6
            $this->arResult['MESSAGE'] = 'Почта подтверждена. Вы подписаны!';
        } else {
            $pass = randString(10);
            $arFields = array(
                "LOGIN"             => $tokenResult['email'],
                "EMAIL"             => $tokenResult['email'],
                "ACTIVE"            => "Y",
                "GROUP_ID"          => array(5),
                "PASSWORD"          => $pass,
                "CONFIRM_PASSWORD"  => $pass,
                self::EMAIL_UF => "1",
            );

            $newUser = new CUser;
            $ID = $newUser->Add($arFields);
            if ($ID) {
                $taskManager->setUser($ID);
                $taskManager->addTask('subscribe', ['source' => 'footer', 'siteStatus' => 'N']);
                $USER->Authorize($ID);
                $this->deleteToken($tokenResult['email']);
                //сценарии 7,8,9
                $this->arResult['MESSAGE'] = 'Почта подтверждена. Вы подписаны. Для получения пароля к личному кабинету 
                        можете воспользоваться <a href="/auth/?forgot_password=yes">формой восстановления</a>.';
            }
        }
    }

    private function getUser($email)
    {
        // Если пользователя с таким Email нет, то user=false иначе массив со значениями
        $arParameters = [
            'SELECT' => [self::EMAIL_UF],
            'FIELDS' => ['ID', 'NAME', 'EMAIL', 'LAST_NAME', 'SECOND_NAME', 'PERSONAL_PHONE', 'PERSONAL_BIRTHDAY', 'PERSONAL_GENDER']
        ];
        $user = CUser::GetList(($by="ID"), ($order="ACS"), array("=EMAIL" => $email), $arParameters)->Fetch();
        return $user;
    }

    private function setSubscribeUser($id)
    {
        // Подписывает пользователя на рассылку
        $user = new CUser;
        $fields = array(self::EMAIL_UF => "1");
        $user->Update($id, $fields);
    }

    private function sendEmail($email, $token)
    {

        $fields = [
           'EMAIL' => $email,
           'CONFIRM_URL' => '/subscribe/?token=' . $token,
        ];
        
        CEvent::Send('SENDER_SUBSCRIBE_CONFIRM', SITE_ID, $fields);
    }

    private function generateToken($user)
    {
        // Генерирует токен из email и текущего времени, записывает значения в базу
        $token = md5($user['EMAIL'] . time());

        global $DB;
        $values = [
            'email' => "'" . $user['EMAIL'] . "'",
            'token' => "'" . $token . "'",
            'user_id' => $user['ID'],
            'date_create' => $DB->CurrentTimeFunction(),
        ];
        $this->deleteToken($user['EMAIL']);
        $sql = 'INSERT INTO ' . self::SUBSCRIBE_TABLE . ' (email, token, user_id, date_create) values(' . implode(",", $values) . ')';
        $DB->Query($sql);
        $this->sendEmail($user['EMAIL'], $token);

        return $token;
    }

    private function deleteToken($email)
    {
        global $DB;
        $sql = "DELETE FROM " . self::SUBSCRIBE_TABLE . " WHERE email = '" . $email . "';";
        $DB->Query($sql);
    }

    private function getToken($token)
    {
        // Возвращает массив с данными если токен есть и не прошло 24 часа, иначе false
        global $DB;

        $sql = "SELECT email, user_id, date_create FROM " . self::SUBSCRIBE_TABLE . " WHERE token='" . $token . "' AND DATE_ADD(date_create, INTERVAL 1 DAY) > NOW();";
        $db = $DB->Query($sql)->Fetch();
        if ($db != false) {
            return $db;
        } else {
            return false;
        }
    }
}
