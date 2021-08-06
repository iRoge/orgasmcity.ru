<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Отчет по прибыли');

$errors = [];
if ($_POST['download'] == 'Скачать') {
    if (!$_POST['date_from']) {
        $errors[] = 'Нужно ввести дату ОТ';
    }
    if (!$_POST['date_to']) {
        $errors[] = 'Нужно ввести дату ДО';
    }
}

if ($_POST['download'] == 'Скачать' && empty($errors)) {

    $spreadsheet = new Spreadsheet;
    $worksheet = $spreadsheet->getActiveSheet();

    $currentRow = 1;
    $worksheet->setCellValueExplicit('A' . $currentRow, 'ID', 's');
    $worksheet->setCellValueExplicit('B' . $currentRow, 'Выручка', 's');
    $worksheet->setCellValueExplicit('C' . $currentRow, 'Доставка', 's');
    $worksheet->setCellValueExplicit('D' . $currentRow, 'Агентские', 's');
    $worksheet->setCellValueExplicit('E' . $currentRow, 'Товар опт', 's');
    $worksheet->setCellValueExplicit('F' . $currentRow, 'utm_campaign', 's');
    $worksheet->setCellValueExplicit('G' . $currentRow, 'utm_source', 's');
    $worksheet->setCellValueExplicit('H' . $currentRow, 'Прибыль', 's');

    $arFilter = [
        "STATUS_ID" => ['DF', 'DN', 'F', 'IR', 'IC', 'PR', 'RS', 'SC', 'SP'],
        "CANCELED" => "N",
    ];
    if (isset($_POST['date_from'])) {
        $arFilter['>=DATE_INSERT'] = $_POST['date_from'];
    }
    if (isset($_POST['date_to'])) {
        $arFilter['<DATE_INSERT'] = $_POST['date_to'];
    }
    $rsOrders = CSaleOrder::GetList(
        [],
        $arFilter,
        false,
        [],
        ['ID', 'PRICE', 'PRICE_DELIVERY', 'UTM_SOURCE', 'UTM_CAMPAIGN', ''],
        []
    );
    $currentRow = 2;
    $revenueSum = 0;
    $profitSum = 0;
    $agentSum = 0;
    $deliverySum = 0;
    $optSum = 0;
    $arProfitByCampaigns = [];
    $arProfitBySources = [];
    while ($order = $rsOrders->GetNext()) {
        $dbOrderProps = CSaleOrderPropsValue::GetList(
            [],
            ["ORDER_ID" => $order['ID'], "CODE" => ["SUPPLIER_COST", "UTM_SOURCE", "UTM_CAMPAIGN"]]
        );
        $arProps = [];
        while ($arOrderProp = $dbOrderProps->GetNext()) {
            $arProps[$arOrderProp['CODE']] = $arOrderProp['VALUE'];
        }
        $agentsMoney = 150 + $order['PRICE'] * 0.02;
        $profit = $order['PRICE'] - $arProps['SUPPLIER_COST'] - $agentsMoney - $order['PRICE_DELIVERY'];
        $revenueSum += $order['PRICE'];
        $profitSum += $profit;
        $optSum += $arProps['SUPPLIER_COST'];
        $agentSum += $agentsMoney;
        $deliverySum += $order['PRICE_DELIVERY'];
        $worksheet->setCellValueExplicit('A' . $currentRow, $order['ID'], 's');
        $worksheet->setCellValueExplicit('B' . $currentRow, round($order['PRICE'], 2), 's');
        $worksheet->setCellValueExplicit('C' . $currentRow, round($order['PRICE_DELIVERY'], 2), 's');
        $worksheet->setCellValueExplicit('D' . $currentRow, $agentsMoney, 's');
        $worksheet->setCellValueExplicit('E' . $currentRow, round($arProps['SUPPLIER_COST'], 2), 's');
        $worksheet->setCellValueExplicit('F' . $currentRow, $arProps['UTM_CAMPAIGN'], 's');
        $worksheet->setCellValueExplicit('G' . $currentRow, $arProps['UTM_SOURCE'], 's');
        $worksheet->setCellValueExplicit('H' . $currentRow,  round($profit, 2), 's');
        if (!isset($arProfitBySources[$arProps['UTM_SOURCE']])) {
            $arProfitBySources[$arProps['UTM_SOURCE']] = [
                'COUNT' => 0,
                'PROFIT' => 0,
            ];
        }
        $arProfitBySources[$arProps['UTM_SOURCE']]['COUNT']++;
        $arProfitBySources[$arProps['UTM_SOURCE']]['PROFIT'] += $profit;
        if (!isset($arProfitByCampaigns[$arProps['UTM_CAMPAIGN']])) {
            $arProfitByCampaigns[$arProps['UTM_CAMPAIGN']] = [
                'COUNT' => 0,
                'PROFIT' => 0,
            ];
        }
        $arProfitByCampaigns[$arProps['UTM_CAMPAIGN']]['COUNT']++;
        $arProfitByCampaigns[$arProps['UTM_CAMPAIGN']]['PROFIT'] += $profit;
        $currentRow++;
    }

    $worksheet->setCellValueExplicit('B' . $currentRow, round($revenueSum, 2), 's');
    $worksheet->setCellValueExplicit('C' . $currentRow, round($deliverySum, 2), 's');
    $worksheet->setCellValueExplicit('D' . $currentRow, round($agentSum, 2), 's');
    $worksheet->setCellValueExplicit('E' . $currentRow, round($optSum, 2), 's');
    $worksheet->setCellValueExplicit('H' . $currentRow,  round($profitSum, 2), 's');

    // Прибыль по utm_campaign
    $worksheet->setCellValueExplicit('M1',  'Кампания', 's');
    $worksheet->setCellValueExplicit('N1',  'Кол-во заказов', 's');
    $worksheet->setCellValueExplicit('O1',  'Прибыль', 's');
    $currentRow = 2;
    foreach ($arProfitByCampaigns as $utmKey => $data) {
        $worksheet->setCellValueExplicit('M' . $currentRow, $utmKey, 's');
        $worksheet->setCellValueExplicit('N' . $currentRow, round($data['COUNT'], 2), 's');
        $worksheet->setCellValueExplicit('O' . $currentRow,  round($data['PROFIT'], 2), 's');
        $currentRow++;
    }

    // Прибыль по utm_source
    $worksheet->setCellValueExplicit('Q1',  'Источник', 's');
    $worksheet->setCellValueExplicit('R1',  'Кол-во заказов', 's');
    $worksheet->setCellValueExplicit('S1',  'Прибыль', 's');
    $currentRow = 2;
    foreach ($arProfitBySources as $utmKey => $data) {
        $worksheet->setCellValueExplicit('Q' . $currentRow, $utmKey, 's');
        $worksheet->setCellValueExplicit('R' . $currentRow, round($data['COUNT'], 2), 's');
        $worksheet->setCellValueExplicit('S' . $currentRow,  round($data['PROFIT'], 2), 's');
        $currentRow++;
    }

    ob_get_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="GeneratedFile.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    die;
} else {
    ?>
<div style="width: auto;max-width: 800px" class="adm-detail-content">
    <div style="width: auto; display: inline-block;" class="adm-detail-content-item-block">
        <?php if (!empty($errors)) { ?>
            <div class="adm-info-message-wrap adm-info-message-red">
                <div class="adm-info-message">
                    <div class="adm-info-message-title">
                        <?php foreach ($errors as $error) { ?>
                            <?=$error?> <br>
                        <?php } ?>
                    </div>
                    <div id="discount_reindex_error_cont"></div>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
        <?php } ?>
        <form method="POST" action="/bitrix/admin/profit_stat.php?lang=ru" enctype="multipart/form-data">
            <label>
                <input placeholder="Дата начала" name="date_from" type="text" value="" onclick="BX.calendar({node: this, field: this, bTime: true});">
            </label>
            <br><br>
            <label>
                <input placeholder="Дата конца" name="date_to" type="text" value="" onclick="BX.calendar({node: this, field: this, bTime: true});">
            </label>
            <br><br>
            <input name="download" type="submit" value="Скачать" title="Скачать отчет">
            <br><br>
        </form>
    </div>
</div>

<?php
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");