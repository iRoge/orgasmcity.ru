<?php

namespace Qsoft\Helpers;

use CIBlockElement;
use Exception;

class SubscribeManager
{
    public static function updateSubscriber($mailingID, $email = false, $subscribed = null, $phone = false, $name = false, $secondName = false, $middleName = false)
    {
        $props = [];
        $fields = [];
        if ($email !== false) {
            $props['EMAIL'] = $email;
            $fields['NAME'] = $email;
        }
        if ($subscribed !== null) {
            $fields['ACTIVE'] = $subscribed ? 'Y' : 'N';
        }
        if ($phone !== false) {
            $props['PHONE'] = $phone;
        }
        if ($name !== false) {
            $props['NAME'] = $name;
        }
        if ($secondName !== false) {
            $props['SECOND_NAME'] = $secondName;
        }
        if ($middleName !== false) {
            $props['MIDDLE_NAME'] = $middleName;
        }
        if (!empty($fields)) {
            $el = new CIBlockElement;
            $el->Update($mailingID, $fields);
        }
        if (!empty($props)) {
            CIBlockElement::SetPropertyValuesEx($mailingID, IBLOCK_SUBSCRIBERS, $props);
        }
    }

    public static function addSubscriber($email, $subscribed = true, $phone = false, $name = false, $secondName = false, $middleName = false)
    {
        $el = new CIBlockElement;
        $mailingID = $el->Add([
            'IBLOCK_ID' => IBLOCK_SUBSCRIBERS,
            'NAME' => mb_strtolower(trim($email)),
            'ACTIVE' => $subscribed ? 'Y' : 'N',
        ]);
        $props = [];
        if ($email) {
            $props['EMAIL'] = $email;
        }
        if ($phone) {
            $props['PHONE'] = $phone;
        }
        if ($name) {
            $props['NAME'] = $name;
        }
        if ($secondName) {
            $props['SECOND_NAME'] = $secondName;
        }
        if ($middleName) {
            $props['MIDDLE_NAME'] = $middleName;
        }
        CIBlockElement::SetPropertyValuesEx($mailingID, IBLOCK_SUBSCRIBERS, $props);
    }

    public static function getSubscriber($id)
    {
        return CIBlockElement::GetList(
            [
                "SORT" => "ASC",
            ],
            [
                'IBLOCK_ID' => IBLOCK_SUBSCRIBERS,
                "ID" => $id,
            ],
            false,
            false,
            ['ID', 'ACTIVE', 'PROPERTY_EMAIL']
        )->Fetch();
    }

    public static function getSubscriberByEmail($email)
    {
        return CIBlockElement::GetList(
            [
                "SORT" => "ASC",
            ],
            [
                'IBLOCK_ID' => IBLOCK_SUBSCRIBERS,
                "=PROPERTY_EMAIL" => $email,
            ],
            false,
            false,
            ['ID', 'ACTIVE']
        )->Fetch();
    }
}