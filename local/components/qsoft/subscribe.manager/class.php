<? use Bitrix\Main\UserTable as UserTable;
use Qsoft\Helpers\SubscribeManager;
use Qsoft\Sailplay\SailPlayApi;
use Qsoft\Sailplay\Tasks\TaskManager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class QsoftSubscribeManagerComponent extends \CBitrixComponent
{
    private $mailing;

    public function executeComponent()
    {
        global $USER;
        if ($USER->IsAuthorized()) {
            $this->mailing = SubscribeManager::getSubscriberByEmail($USER->GetEmail());
            if ($this->request->isPost() && check_bitrix_sessid() && !$this->request->isAjaxRequest()) {
                $this->processPost();
            }
        }
        $this->arResult['SUBSCRIBED'] = $this->mailing['ACTIVE'] == 'Y';
        $this->includeComponentTemplate();
    }

    private function processPost()
    {
        global $USER;
        $subscribe = $this->request->get('subscribe') === 'Y';
        if ($this->mailing) {
            SubscribeManager::updateSubscriber(
                $this->mailing['ID'],
                false,
                $subscribe,
                $USER->GetByID($USER->GetID())->Fetch()["PERSONAL_PHONE"],
                $USER->GetFirstName(),
                $USER->GetLastName(),
                $USER->GetSecondName(),
            );
            $this->mailing['ACTIVE'] = $subscribe ? 'Y' : 'N';
        } else {
            SubscribeManager::addSubscriber(
                $USER->GetEmail(),
                true,
                $USER->GetByID($USER->GetID())->Fetch()["PERSONAL_PHONE"],
                $USER->GetFirstName(),
                $USER->GetLastName(),
                $USER->GetSecondName(),
            );
            $this->mailing['ACTIVE'] = 'Y';
        }
    }
}
