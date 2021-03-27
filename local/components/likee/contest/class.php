<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class LikeeContest extends \CBitrixComponent
{
    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;

        if (! $USER->IsAuthorized()) {
            LocalRedirect("/");
        }

        $artList = $this->getContestArtList();
        $userResults = $this->getUserResults($USER->GetId());

        $arResponse = [];
        if (isset($_REQUEST['action'])) {
            if ('add_contest_response' == $_REQUEST['action'] && (! empty($_REQUEST['art']) && ! empty($_REQUEST['status'])) ) {
                try {
                    $arResponse['status'] = $this->addUserResponse($USER->GetId(), $_REQUEST['art'], $_REQUEST['status']);
                } catch (\Exception $e) {
                    $arResponse['status'] = false;
                }
            } elseif ('get_contest_result' == $_REQUEST['action']) {
                $arResponse['status'] = true;
                $arResponse['results'] = $userResults;
            }
        }

        if ($arResponse) {
            header('Content-type: application-json');
            $APPLICATION->RestartBuffer();
            echo json_encode($arResponse);
            $APPLICATION->FinalActions();
            exit;
        }
        
        $this->arResult['USER_ALLOWED'] = (count($artList) > count($userResults));
        $this->arResult['AUTOSTART'] = (!empty($_SESSION['RESPECT_GOALS']) && array_intersect(['user_auth', 'new_register', 'new_soc_register'], $_SESSION['RESPECT_GOALS']));
        $this->arResult['ITEMS'] = [];

        $userResultsArts = array_column($userResults, 'ART');

        foreach ($this->getItems($artList) as $art => $item) {
            if (in_array($art, $userResultsArts)) {
                continue;
            }

            $this->arResult['ITEMS'][] = $item;
        }

        $likesCount = 0;
        foreach ($userResults as $res) {
            $likesCount += ('Y' == $res['STATUS'] ? 1 : 0);
        }

        $this->arResult['STATS'] = [];
        $this->arResult['STATS']['TOTAL'] = count($userResults);
        $this->arResult['STATS']['LIKES'] = $likesCount;
        $this->arResult['STATS']['DISLIKES'] = (count($userResults) - $likesCount);
        $this->arResult['STATS']['REMAINS'] = count($this->arResult['ITEMS']);

        $this->includeComponentTemplate();

        return (bool) count($userResults);
    }

    public function getContestArtList()
    {
        static $artList = null;
        
        if (is_null($arList)) {
            $artList = [];

            $db = \Bitrix\Main\Application::getConnection();d;
            $sql = "SELECT * FROM `likee_contest_list` ORDER BY `ID` ASC";
            $res = $db->query($sql);

            while ($row = $res->fetch()) {
                $artList[] = $row['ART'];
            }
        }

        return $artList;
    }

    protected function getItems($artList)
    {
        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        
        $cacheTime = 604800;
        $sCacheKey = 'likee.contest';
        $sCacheDir = '/likee/site/';

        $arItems = [];

        if ($obCache->initCache($icacheTime, $sCacheKey, $sCacheDir)) {
            $arItems = $obCache->getVars();
        } elseif (\Bitrix\Main\Loader::includeModule('iblock') && $obCache->startDataCache()) {
            \Bitrix\Main\Application::getInstance()->getTaggedCache()->startTagCache($sCacheDir);
            \Bitrix\Main\Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.IBLOCK_CATALOG);
        
            $res = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => IBLOCK_CATALOG,
                    '!DETAIL_PICTURE' => false,
                    'PROPERTY_ARTICLE' => $artList
                ],
                false,
                false,
                ['IBLOCK_ID', 'ID', 'NAME', 'DETAIL_PICTURE', 'PROPERTY_ARTICLE']
            );
            while($arFields = $res->GetNext(false, false)) {
                if (! $arFields || empty($arFields['DETAIL_PICTURE'])) {
                    continue;
                }
                $arFields['SRC'] = CFile::GetPath($arFields['DETAIL_PICTURE']);
        
                $arItems[$arFields['PROPERTY_ARTICLE_VALUE']] = $arFields;
            }
            unset($res, $arFields);

            $arItems = array_merge(array_flip($artList), $arItems);
        
            \Bitrix\Main\Application::getInstance()->getTaggedCache()->endTagCache();

            $obCache->endDataCache($arItems);
        }

        return $arItems;
    }

    protected function getUserResults($uid)
    {
        $db = \Bitrix\Main\Application::getConnection();

        $uid = (int) $uid;
        $sql = "SELECT * FROM `likee_contest` WHERE `USER_ID` = {$uid} ORDER BY `CREATED` ASC";
        $res = $db->query($sql);

        return $res->fetchAll();
    }

    protected function addUserResponse($uid, $art, $status = 'N')
    {
        if (! $uid || ! $art || ! in_array($art, $this->getContestArtList())) {
            return false;
        }

        $db = \Bitrix\Main\Application::getConnection();

        $uid = (int) $uid;
        $status = in_array($status, ['Y', 'N']) ? $status : 'N';
        $created = time();

        $sql = "INSERT INTO `likee_contest` (`USER_ID`, `ART`, `STATUS`, `CREATED`) VALUES ('{$uid}', '{$art}', '{$status}', '{$created}');";
        $db->queryExecute($sql);

        return (bool) $db->getInsertedId();
    }
}