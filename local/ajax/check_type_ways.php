<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/lib/delivery/inputs.php");

use Bitrix\Sale\Delivery\Restrictions;
use Bitrix\Sale\Delivery\Services;
use Bitrix\Sale\Internals\Input;

$arDeliveriesID = $_REQUEST['deliveries_id'];
$type_ways = $_REQUEST['type_ways'];
$locationsResult = [];
$strError = '';
        
if (count($arDeliveriesID) > 1 && $type_ways == 'C') {
    $locations = [];
    $locationsID = [];
    foreach ($arDeliveriesID as $item) {
        $tableId = 'table_delivery_restrictions';
        $res = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList(array(
            'filter' => array(
                '=SERVICE_ID' => $item,
                '=SERVICE_TYPE' => Restrictions\Manager::SERVICE_TYPE_SHIPMENT
            ),
            'select' => array('ID', 'SERVICE_ID', 'CLASS_NAME', 'SORT', 'PARAMS'),
            'order' => array('SORT' => 'ASC', 'ID' => 'DESC')
        ));
        $data = $res->fetchAll();
        $dbRes = new \CDBResult;
        $dbRes->InitFromArray($data);
        $dbRecords = new \CAdminResult($dbRes, $tableId);
        $dbRecords->NavStart();
        while ($record = $dbRecords->Fetch()) {
            if (stripos($record['CLASS_NAME'], 'ByLocation')) {
                $paramsStructure = $record['CLASS_NAME']::getParamsStructure($record['SERVICE_ID']);
                $locations_noformat = explode('<br>', Input\Manager::getViewHtml($paramsStructure['LOCATION']));
                foreach ($locations_noformat as $item) {
                    $tmp = trim($item, PHP_EOL);
                    if (!empty($tmp)) {
                        $locations[] = $tmp;
                        $locationsID[] = $record['SERVICE_ID'];
                    }
                }
            }
        }
    }

    foreach (array_count_values($locations) as $key => $item) {
        if ($item > 1) {
            $locationPosition = array_keys($locations, $key);
            $locationPositionID = [];
            foreach ($locationPosition as $pos) {
                $locationPositionID[] = $locationsID[$pos];
            }
            $locationsResult[$key] = $locationPositionID;
            unset($locationPositionID);
        }
    }
}

if (!empty($locationsResult)) {
    $strError = implode(' ', array_map(function ($key, $value) {
        return $key . '<br>(в службах доставки с id: ' . $str2 = implode(', ', $value) . ')<br>';
    }, array_keys($locationsResult), $locationsResult));
}

if (empty($strError)) {
    echo 'OK';
} else {
    echo $strError;
}
