<?
global $APPLICATION;
global $USER;
$access = false;
$dbGroup = \Bitrix\Main\GroupTable::getList(array(
    'filter' => array("STRING_ID" => ['seo', 'modules_sa'])
));
while ($arGroup = $dbGroup->Fetch()) {
    if (in_array($arGroup['ID'], $USER->GetUserGroupArray())) {
        $access = true;
        break;
    }
}

if ($APPLICATION->GetGroupRight("likee.admin") != "D") {
    $aMenu = [
        "parent_menu" => "global_menu_services",
        "section" => "Orgasmcity",
        "sort" => 50,
        "text" => "Orgasmcity",
        "icon" => "sys_menu_icon",
        "page_icon" => "sys_page_icon",
        "items_id" => "likee_respect",
        "items" => [
            [
                "text" => "Настройки",
                "url" => "likee_options.php?lang=" . LANGUAGE_ID,
            ],
            [
                "text" => "Кеш каталога",
                "url" => "catalog_cache.php?lang=" . LANGUAGE_ID,
            ],
            [
                "text" => "Способы доставки",
                "url" => "delivery_ways.php?lang=" . LANGUAGE_ID,
            ],
            [
                "text" => "Способы оплаты",
                "url" => "payment_ways.php?lang=" . LANGUAGE_ID,
            ],
            [
                "text" => "Отчет по заказам",
                "url" => "profit_stat.php?lang=" . LANGUAGE_ID,
            ],
        ]
    ];

    return $aMenu;
}
return false;
