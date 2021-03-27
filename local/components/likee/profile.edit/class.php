<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Site
 * Date: 2017-02-26
 * Time: 15:56:00
 */
class LikeeProfileEditComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams['USER_ID'] = \Likee\Site\User::checkUserId($arParams['USER_ID']);

        if (is_array($arParams['GROUP_ID']))
            $arParams['GROUP_ID'] = array_map('intval', $arParams['GROUP_ID']);
        else
            $arParams['GROUP_ID'] = intval($arParams['GROUP_ID']);

        return $arParams;
    }

    public function executeComponent()
    {
        $this->arParams['PERSON_TYPE_ID'] = \Likee\Site\User::getPersonalTypeId($this->arParams['USER_ID']);

        if ($this->request->isPost() && check_bitrix_sessid() && $this->request->get('action') == 'update_profile') {
            $this->processRequest();
        }

        $this->arResult['PROPS'] = $this->loadProfileProps();
        $this->arResult['GROUPS'] = $this->loadProfileGroups();
        $this->arResult['PROFILE'] = $this->loadProfile();

        $this->includeComponentTemplate();
    }

    public function loadProfile()
    {
        $arProfile = \CSaleOrderUserProps::GetList(
            ['DATE_UPDATE' => 'DESC'],
            [
                'USER_ID' => $this->arParams['USER_ID'],
                'PERSON_TYPE_ID' => $this->arParams['PERSON_TYPE_ID']
            ]
        )->Fetch();

        if ($arProfile) {
            $rsFields = CSaleOrderUserPropsValue::GetList(
                [],
                ['USER_PROPS_ID' => $arProfile['ID']]
            );

            $arProfile['FIELDS'] = [];
            while ($arField = $rsFields->Fetch()) {
                $arProfile['FIELDS'][$arField['PROP_CODE']] = $arField;
            }
        }

        return $arProfile;
    }

    public function processRequest()
    {
        $arFields = $this->request->get('PROFILE');

        $iProfileId = intval($arFields['ID']);

        if ($iProfileId <= 0)
            $iProfileId = intval($this->addProfile());

        if ($iProfileId > 0)
            $this->updateProfileFields($arFields);


        $sNewPass = htmlentities(trim($this->request->get('NEW_PASSWORD')));
        $sNewPassConfirm = htmlentities(trim($this->request->get('NEW_PASSWORD_CONFIRM')));
        if (!empty($sNewPass) && !empty($sNewPassConfirm)) {
            $this->changePass($sNewPass, $sNewPassConfirm);
        }
    }

    public function updateProfileFields($arFields)
    {
        $arProfile = $this->loadProfile();
        $arProps = $this->loadProfileProps();

        if (!$arProfile)
            return false;

        if (empty($arFields['CITY']) && $arFields['REGION'] == 'Москва') {
            $arFields['CITY'] = $arFields['REGION'];
        }

        foreach ($arFields as $sFieldCode => $sFieldValue) {
            if (array_key_exists($sFieldCode, $arProfile['FIELDS'])) {
                $arProp = $arProfile['FIELDS'][$sFieldCode];

                \CSaleOrderUserPropsValue::Update($arProp['ID'], [
                    'NAME' => $arProp['NAME'],
                    'VALUE' => $sFieldValue
                ]);
            } elseif (array_key_exists($sFieldCode, $arProps)) {
                $arProp = $arProps[$sFieldCode];

                \CSaleOrderUserPropsValue::Add([
                    'USER_PROPS_ID' => $arProfile['ID'],
                    'ORDER_PROPS_ID' => $arProp['ID'],
                    'NAME' => $arProp['NAME'],
                    'VALUE' => $sFieldValue
                ]);
            }
        }

        return true;
    }

    public function addProfile()
    {
        if (\Likee\Site\User::isPartner()) {
            $sName = htmlentities(trim($_REQUEST['PROFILE']['COMPANY']));
        } else {
            $sName = htmlentities(trim($_REQUEST['LAST_NAME'] . ' ' . $_REQUEST['NAME'] . ' ' . $_REQUEST['SECOND_NAME']));
        }

        return \CSaleOrderUserProps::Add([
            'NAME' => $sName,
            'USER_ID' => $this->arParams['USER_ID'],
            'PERSON_TYPE_ID' => $this->arParams['PERSON_TYPE_ID']
        ]);
    }

    public function loadProfileProps()
    {
        static $arProps = null;

        if (is_null($arProps)) {
            $arFilter = [
                'PERSON_TYPE_ID' => $this->arParams['PERSON_TYPE_ID'],
                'USER_PROPS' => 'Y',
                'ACTIVE' => 'Y'
            ];

            if ($this->arParams['GROUP_ID'] > 0) {
                $arFilter['PROPS_GROUP_ID'] = $this->arParams['GROUP_ID'];
            }

            $rsProps = \CSaleOrderProps::GetList(
                ['SORT' => 'ASC'],
                $arFilter,
                false,
                false,
                array(
                    'ID', 'PERSON_TYPE_ID', 'NAME', 'TYPE', 'REQUIED', 'DEFAULT_VALUE', 'DEFAULT_VALUE_ORIG', 'SORT', 'USER_PROPS',
                    'IS_LOCATION', 'PROPS_GROUP_ID', 'SIZE1', 'SIZE2', 'DESCRIPTION', 'IS_PHONE', 'IS_EMAIL', 'IS_PROFILE_NAME',
                    'IS_PAYER', 'IS_LOCATION4TAX', 'IS_ZIP', 'CODE', 'IS_FILTERED', 'ACTIVE', 'UTIL',
                    'INPUT_FIELD_LOCATION', 'MULTIPLE', 'PAYSYSTEM_ID', 'DELIVERY_ID'
                )
            );

            $arProps = [];

            while ($arProp = $rsProps->Fetch()) {
                $arProp['REQUIRED'] = $arProp['REQUIED']; //it is bitrix!

                if ($arProp['TYPE'] == 'SELECT') {
                    $arProp['VALUES'] = [];

                    $rsEnums = CSaleOrderPropsVariant::GetList([], ['ORDER_PROPS_ID' => $arProp['ID']]);
                    while ($arEnum = $rsEnums->Fetch()) {
                        $arProp['VALUES'][] = $arEnum;
                    }
                }

                $arProps[$arProp['CODE']] = $arProp;
            }
        }

        if (\Likee\Site\User::isPartner()) {
            global $USER;

            $arUser = $USER::GetByID($USER->GetID())->Fetch();

            $arDefault = [
                'EMAIL' => $arUser['EMAIL'],
                'CONTACT_PHONE' => $arUser['PERSONAL_PHONE'],
                'CONTACT_NAME' => trim($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']),
                'COMPANY' => $arUser['WORK_COMPANY'],
                'REGION' => $arUser['WORK_STATE'],
                'CITY' => $arUser['WORK_CITY']
            ];

            foreach ($arProps as $sCode => &$arProp) {
                if (empty($arProp['VALUE']) && array_key_exists($sCode, $arDefault))
                    $arProp['VALUE'] = $arDefault[$sCode];
            }
            unset($arProp);
        }

        return $arProps;
    }

    public function loadProfileGroups()
    {
        static $arGroups = null;

        if (is_null($arGroups)) {
            $arFilter = [
                'PERSON_TYPE_ID' => $this->arParams['PERSON_TYPE_ID'],
                'ACTIVE' => 'Y'
            ];

            if ($this->arParams['GROUP_ID'] > 0)
                $arFilter['ID'] = $this->arParams['GROUP_ID'];

            $rsGroups = \CSaleOrderPropsGroup::GetList(
                ['SORT' => 'ASC'],
                $arFilter
            );

            $arGroups = [];
            while ($arGroup = $rsGroups->Fetch()) {
                $arGroups[$arGroup['ID']] = $arGroup;
            }
        }

        return $arGroups;
    }

    public function changePass($sNewPass, $sNewPassConfirm)
    {
        $obUser = new \CUser();

        $b = $obUser->Update($this->arParams['USER_ID'], [
            'PASSWORD' => $sNewPass,
            'CONFIRM_PASSWORD' => $sNewPassConfirm
        ]);

        if (!$b) {
            $this->arResult['ERRORS'][] = $obUser->LAST_ERROR;
        }

        return $b;
    }
}