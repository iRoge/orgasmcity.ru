<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
set_time_limit(0);
ini_set('memory_limit', '2048M');

$nowDate = date('d.m.Y H:i:s');
$rsItems = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => IBLOCK_GROUPS,
        'PROPERTY_IS_ACTION' => true,
        [
            "LOGIC" => "OR",
            ["<=DATE_ACTIVE_FROM" => $nowDate, ">=DATE_ACTIVE_TO" => $nowDate, "ACTIVE" => 'N'],
            [">DATE_ACTIVE_FROM" => $nowDate, "ACTIVE" => 'Y'],
            ["<DATE_ACTIVE_TO" => $nowDate, "ACTIVE" => 'Y'],
        ],
    ],
    false,
    false,
    [
        'ID',
        'IBLOCK_ID',
        'CODE',
        'ACTIVE',
        'NAME'
    ]
);

while ($action = $rsItems->GetNext()) {
    $el = new CIBlockElement();
    $el->Update(
        $action['ID'],
        [
            'ACTIVE' => ($action['ACTIVE'] == 'N' ? 'Y' : 'N')
        ]
    );
}

\Qsoft\Helpers\PriceUtils::recalcPrices();
global $CACHE_MANAGER;
$CACHE_MANAGER->ClearByTag("pricesAll");
$CACHE_MANAGER->ClearByTag("groupsAll");
\Qsoft\Helpers\PriceUtils::getCachedPriceForUser(1);
