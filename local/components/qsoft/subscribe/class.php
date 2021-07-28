<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Qsoft\Helpers\SubscribeManager;

class QsoftSubscribeComponent extends \CBitrixComponent
{

    public function executeComponent()
    {
        global $APPLICATION;

        $bAjax = $this->request->isAjaxRequest();
        if ($bAjax) {
            $APPLICATION->RestartBuffer();
        }

        if ($this->request->isPost() && check_bitrix_sessid() && $bAjax) {
            try {
                if ($this->request->get('action') == 'subscribeSurprise') {
                    $this->processAjaxRequestSurprise();
                } else {
                    $this->processAjaxRequest();
                }
            } catch (Exception $e) {
                $this->arResult['MESSAGE'] = "Упс, кажется произошла ошибка. Обратитесь в суппорт";
                orgasm_logger($e->getMessage(), 'error.log', '/local/logs/', true);
            }
        }
        $this->includeComponentTemplate();
        if ($bAjax) {
            $APPLICATION->FinalActions();
            exit;
        }
    }

    private function processAjaxRequest()
    {
        $mailing = SubscribeManager::getSubscriberByEmail($this->request->get('EMAIL'));
        $this->arResult['MESSAGE'] = "Спасибо за подписку!";
        if (!$mailing) {
            SubscribeManager::addSubscriber($this->request->get('EMAIL'));
        } else {
            if ($mailing['ACTIVE'] === 'Y') {
                $this->arResult['MESSAGE'] = "Вы уже подписаны!";
            } else {
                SubscribeManager::updateSubscriber($mailing['ID'], false, true);
            }
        }
    }

    private function processAjaxRequestSurprise()
    {
        $mailing = SubscribeManager::getSubscriberByEmail($this->request->get('EMAIL'));
        if (!$mailing) {
            $mailingID = SubscribeManager::addSubscriber($this->request->get('EMAIL'));
        } else {
            SubscribeManager::updateSubscriber($mailing['ID'], false, true);
            $mailingID = $mailing['ID'];
        }
        $this->arResult['MESSAGE'] = "Письмо с сюрпризом выслано вам на почту!";
        $this->sendPromocode($this->request->get('EMAIL'), $mailingID);
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendPromocode($email, $subscriberID)
    {
        $dateEnd = (new \Bitrix\Main\Type\DateTime())->add('+7 days');

        do {
            $coupon = $this->generateCoupon(7);
        } while (\Bitrix\Catalog\DiscountCouponTable::isExist($coupon));

        $couponsResult = \Bitrix\Sale\Internals\DiscountCouponTable::add(
            [
                'ACTIVE_TO' => $dateEnd,
                'DISCOUNT_ID' => 4,
                'COUPON' => $coupon,
                'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                'MAX_USE' => 1,
                'USER_ID' => 0,
            ]
        );
        $fields = [
            'PROMOCODE' => $coupon,
            'SUBSCRIBER_ID' => $subscriberID,
            'EMAIL' => $email,
        ];
        $html = file_get_contents(__DIR__ . '/surprise.html');
        $subject = 'Как и обещали. Ваш сюрприз!';
        $body = Functions::insertFields($html, $fields);
        Functions::sendMail($email, $subject, $body, $subscriberID);
    }

    private function generateCoupon($strength)
    {
        $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $inputLength = strlen($permittedChars);
        $randomString = '';
        for($i = 0; $i < $strength; $i++) {
            $randomCharacter = $permittedChars[mt_rand(0, $inputLength - 1)];
            $randomString .= $randomCharacter;
        }
        return mb_strtoupper($randomString);
    }
}
