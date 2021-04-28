<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Местоположения, которые не используют филиальную цену');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

use Bitrix\Main\Application;
use Bitrix\Sale\Location\LocationTable;

const CACHE_ID = 'branchless_locations';
const CACHE_TTL = 3000000;

$application = Application::getInstance();
$cache = new CPHPCache();

if ($cache->InitCache(CACHE_TTL, CACHE_ID, '/local/admin')) {
    $regions = $cache->GetVars()['result'];
} elseif ($cache->StartDataCache()) {
    $CACHE_MANAGER->StartTagCache('/local/admin');
    $CACHE_MANAGER->RegisterTag(CACHE_ID);

    $database = Application::getConnection();

    $branchList = $database->query('SELECT branch_id, location_code FROM b_qsoft_location2branch;');
    $branches = [];
    while ($branch = $branchList->fetch()) {
        $branches[$branch['location_code']] = $branch['branch_id'];
    }

    $locationList = LocationTable::getList([
        'select' => ['ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'NAME_RU' => 'NAME.NAME'],
    ]);
    $locations = [];
    while ($location = $locationList->fetch()) {
        $locations[$location['CODE']] = $location;
    }

    $locationCodes = array_column($locations, 'CODE', 'ID');
    foreach ($locations as &$location) {
        $location['PARENT_CODE'] = $locationCodes[$location['PARENT_ID']];
    }
    unset($location);

    $regions = [];
    foreach ($locations as $parent) {
        if ($parent['TYPE_ID'] != 3 || empty($parent['CODE'])) {
            continue;
        }
        
        $locationCode = $parent['CODE'];
        while (!empty($locationCode)) {
            if (!is_null($branches[$locationCode])) {
                continue 2;
            }

            $locationCode = $locations[$locationCode]['PARENT_CODE'];
        }

        $children = array_filter(
            $locations,
            function ($child) use ($parent, $branches) {
                return $child['TYPE_ID'] == 5 && $child['PARENT_CODE'] == $parent['CODE'] && is_null($branches[$child['CODE']]);
            }
        );

        if (!empty($children)) {
            $regions[$parent['CODE']] = [
                'NAME' => $locations[$parent['CODE']]['NAME_RU'],
                'CODE' => $parent['CODE'],
                'CITIES' => array_column($children, 'NAME_RU', 'CODE')
            ];
        }
    }

    uasort(
        $regions,
        function ($a, $b) {
            return strcmp($a['NAME'], $b['NAME']);
        }
    );

    foreach ($regions as &$region) {
        asort($region['CITIES']);
    }
    unset($region);

    $CACHE_MANAGER->EndTagCache();
    $cache->EndDataCache(['result' => $regions]);
}
?>

<div>
    <?php if (!empty($regions)) : ?>
        <table class="branchless-locations">
            <?php
            $maxCellWidth = 0;
            foreach ($regions as $region) {
                $citiesCount = count($region['CITIES']);
                $maxCellWidth = $maxCellWidth < $citiesCount ? $citiesCount : $maxCellWidth;
            } ?>
            <?php foreach ($regions as $region) : ?>
                <tr class="region">
                    <td colspan=<?= $maxCellWidth; ?>><?= $region['NAME']; ?></td>
                </tr>
                <tr class="city">
                    <?php
                    $citiesCount = count($region['CITIES']);
                    $cellWidth = (int) ($maxCellWidth / $citiesCount);
                    $excessWidth = $maxCellWidth % $citiesCount;
                    ?>
                    <?php foreach ($region['CITIES'] as $city) : ?>
                        <td colspan=<?= $cellWidth + (int) ($excessWidth-- > 0); ?>><?= $city; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <style>
            table.branchless-locations {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }
            table.branchless-locations td {
                padding: 5px;
                text-align: center;
                border: solid 1px;
                white-space: nowrap;
            }
            table.branchless-locations tr.region td {
                font-weight: bold;
                background-color: lightgrey;
            }
        </style>
    <?php else : ?>
        <p>Местоположения без привязки к филиалу не найдены.</p>
    <?php endif; ?>
<div>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_after.php');
