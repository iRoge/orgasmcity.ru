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
        $this->arResult['STATUS'] = 1;
        if ($this->request->isPost() && check_bitrix_sessid() && $bAjax) {
            try {
                if ($this->request->get('action') == 'subscribeSurprise') {
                    $this->IncludeComponentTemplate('popupBanner');
                    $this->processAjaxRequestSurprise();
                } else {
                    $this->processAjaxRequest();
                }
            } catch (Exception $e) {
                $this->arResult['MESSAGE'] = "Упс, кажется произошла ошибка. Обратитесь в чат поддержки";
                $this->arResult['STATUS'] = 0;
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
}
