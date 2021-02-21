<?php

namespace Qsoft\Sailplay;

class SailPlayApi
{
    public static function addUser(string $identifier, array $userData, string $type = 'phone')
    {
        if ($type != 'phone') {
            $login = [
                'email'          => $identifier,
                'origin_user_id' => $userData['ID'],
            ];
        } else {
            $login = ['user_phone' => self::clearPhone($identifier)];
        }

        $params = array_merge(self::getAuthParams(), $login);
        $params = array_merge($params, self::parseUserData($userData));
        unset($params['new_phone']);
        unset($params['new_email']);
        if (!empty($userData['EMAIL'])) {
            $params['email'] = $userData['EMAIL'];
        }

        $response = SailPlayClient::makeRequest('users/add', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function addPurchases(string $identifier, $cart, $order, $date = '', string $type = 'phone')
    {
        $login = self::getLogin($identifier, $type);
        $params = array_merge(self::getAuthParams(), $login, [
            'order_num' => $order,
            'l_date' => $date,
            'cart' => $cart,
        ]);
        $response = SailPlayClient::makeRequest('purchases/new', $params);

        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function checkPurchases($orderId)
    {
        $params = array_merge(self::getAuthParams(), [
            'order_num' => $orderId,
        ]);
        $response = SailPlayClient::makeRequest('purchases/get', $params);

        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function updateUser(string $primary, string $value, array $userData)
    {
        $params = array_merge(self::getAuthParams(), self::parseUserData($userData));
        switch ($primary) {
            case 'phone':
                $params['user_phone'] = self::clearPhone($value);
                break;
            case 'mail':
                $params['email'] = $value;
                break;
            default:
                return false;
        }
        if ($params['new_email'] === $params['email']) {
            unset($params['new_email']);
        }
        if ($params['new_phone'] === $params['user_phone']) {
            unset($params['new_phone']);
        }
        if (!empty($params['new_email']) && !empty($params['new_phone'])) {
            $updateEmail = array_merge(self::getAuthParams(), [
                'new_email'  => $params['new_email'],
                'user_phone' => $value,
            ]);
            SailPlayClient::makeRequest('users/update', $updateEmail);
            unset($params['new_email']);
        }
        $response = SailPlayClient::makeRequest('users/update', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }

    public static function addUserPhone($email, $phone)
    {
        //проверить, что этого телефона в Sailplay вообще ни у кого нет
        $checkUser = self::getUserByPhone($phone);
        if ($checkUser) {
            $response['DUBLICATE'] = $checkUser;
            return $response;
        }
        //проверить, что у этого email в Sailplay нет телефона
        if (isset(self::getUserByMail($email)->phone)) {
            $response['ISSET'] = true;
            return $response;
        }
        //добавить телефон у пользователя по email
            $params = array_merge(self::getAuthParams(), [
                'email'  => $email,
                'new_phone' => self::clearPhone($phone),
            ]);
        $response['OK'] = SailPlayClient::makeRequest('users/update', $params);
        if ($response['OK']->status === 'error') {
            $response['ERROR'] = true;
            return $response;
        }
        return $response;
    }

    public static function addUserEmail($phone, $email)
    {
        //проверить, что этого email в Sailplay вообще ни у кого нет
        $checkUser = self::getUserByMail($email);
        if ($checkUser) {
            $response['DUBLICATE'] = $checkUser;
            return $response;
        }
        //проверить, что у этого phone в Sailplay нет email или он технический
        $emailInSailplay = self::getUserByPhone($phone)->email;
        if (!empty($emailInSailplay) && !preg_match('`.*@rshoes.ru`i', $emailInSailplay)) {
            $response['ISSET'] = true;
            return $response;
        }
        //добавить email у пользователя по phone
        $params = array_merge(self::getAuthParams(), [
            'phone'  => self::clearPhone($phone),
            'new_email' => $email,
        ]);
        $response['OK'] = SailPlayClient::makeRequest('users/update', $params);
        if ($response['OK']->status === 'error') {
            $response['ERROR'] = true;
            return $response;
        }
        return $response;
    }

    public static function addUserInfo($identifier, $identifierType, $newUserInfo)
    {
        $login = self::getLogin($identifier, $identifierType);
        $params = array_merge(self::getAuthParams(), $login, $newUserInfo);
        $response = SailPlayClient::makeRequest('users/update', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }

    public static function getUserByPhone($phone, $getHistory = false, $getSubscribe = false)
    {
        $params = array_merge(self::getAuthParams(), [
            'user_phone' => self::clearPhone($phone),
        ]);
        if ($getHistory) {
            $params['history'] = 1;
        }
        if ($getSubscribe) {
            $params['subscriptions'] = 1;
        }
        $response = SailPlayClient::makeRequest('users/info', $params);
        if ($response->status === 'error') {
            return false;
        }
        return self::checkEmail($response);
    }
    public static function getUserByMail($mail, $getHistory = false, $getSubscribe = false)
    {
        $params = array_merge(self::getAuthParams(), [
            'email' => $mail,
        ]);
        if ($getHistory) {
            $params['history'] = 1;
        }
        if ($getSubscribe) {
            $params['subscriptions'] = 1;
        }
        $response = SailPlayClient::makeRequest('users/info', $params);
        if ($response->status === 'error') {
            return false;
        }
        return self::checkEmail($response);
    }
    public static function getUserByUID($uid, bool $getHistory = false, bool $getSubscribe = false)
    {
        $params = array_merge(self::getAuthParams(), [
            'origin_user_id' => $uid,
        ]);
        if ($getHistory) {
            $params['history'] = 1;
        }
        if ($getSubscribe) {
            $params['subscriptions'] = 1;
        }
        $response = SailPlayClient::makeRequest('users/info', $params);
        if ($response->status === 'error') {
            return false;
        }
        return self::checkEmail($response);
    }
    public static function userAddTags(string $identifier, array $tags, string $type = 'phone')
    {
        $login = self::getLogin($identifier, $type);
        $params = array_merge(self::getAuthParams(), $login, [
            'tags' => implode(',', $tags),
        ]);
        $response = SailPlayClient::makeRequest('users/tags/add', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function userGetTags(string $identifier, array $tags = [], $type = 'phone')
    {
        $login = self::getLogin($identifier, $type);
        $params = array_merge(self::getAuthParams(), $login);
        if (!empty($tags)) {
            $params['tags'] = json_encode($tags);
        }
        $response = SailPlayClient::makeRequest('users/tags/list', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response->events;
    }
    public static function userRegisterConfirm(string $phone)
    {
        $params = array_merge(self::getAuthParams(), [
            'user_phone' => self::clearPhone($phone),
        ]);
        $response = SailPlayClient::makeRequest('users/email-registration/complete', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function sendSMS(string $phone, string $text)
    {
        $params = array_merge(self::getAuthParams(), [
            'user_phone' => self::clearPhone($phone),
            'text'       => $text,
        ]);
        $response = SailPlayClient::makeRequest('users/sms-code', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function userSubscribe(string $identifier, array $subscribeList, string $type = 'phone')
    {
        $login = self::getLogin($identifier, $type);
        $params = array_merge(self::getAuthParams(), $login, [
            'subscribe_list' => implode(",", $subscribeList),
        ]);
        $response = SailPlayClient::makeRequest('users/subscribe', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function userUnsubscribe(string $identifier, array $subscribeList, string $type = 'phone')
    {
        $login = self::getLogin($identifier, $type);
        $params = array_merge(self::getAuthParams(), $login, [
            'unsubscribe_list' => implode(",", $subscribeList),
        ]);
        $response = SailPlayClient::makeRequest('users/unsubscribe', $params);
        if ($response->status === 'error') {
            return false;
        }
        return $response;
    }
    public static function parseUserData(array $data)
    {
        $params = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'NAME':
                    $params['first_name'] = $value;
                    break;
                case 'LAST_NAME':
                    $params['last_name'] = $value;
                    break;
                case 'SECOND_NAME':
                    $params['middle_name'] = $value;
                    break;
                case 'PERSONAL_BIRTHDAY':
                    $params['birth_date'] = ConvertDateTime($value, 'YYYY-MM-DD') ?: null;
                    break;
                case 'PERSONAL_GENDER':
                    if (!empty($value)) {
                        $params['sex'] = self::parseSex($value) ?: null;
                    }
                    break;
                case 'EMAIL':
                    $params['new_email'] = $value;
                    break;
                case 'PERSONAL_PHONE':
                    if (!empty($value)) {
                        $params['new_phone'] = self::clearPhone($value);
                    }
                    break;
                case 'PERSONAL_MOBILE':
                    if (!empty($value) && empty($params['new_phone'])) {
                        $params['new_phone'] = self::clearPhone($value);
                    }
                    break;
                default:
                    break;
            }
        }
        return $params;
    }
    private static function getLogin(string $identifier, string $type = 'phone')
    {
        if ($type != 'phone') {
            return ['email' => $identifier];
        } else {
            return ['user_phone' => self::clearPhone($identifier)];
        }
    }
    private static function clearPhone(string $phone)
    {
        return str_replace(['+', '-', '(', ')', ' '], '', $phone);
    }
    private static function getAuthParams()
    {
        return SailPlayClient::AUTH_PARAMS;
    }
    private static function parseSex(string $sex)
    {
        return $sex === 'M' ? 1 : 2;
    }
    private static function checkEmail($response)
    {
        if (isset($response->email)) {
            $response->email = mb_strtolower($response->email);
        }
        return $response;
    }
}
