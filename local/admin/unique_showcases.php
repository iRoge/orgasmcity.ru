<?
use Bitrix\Catalog\StoreTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

global $USER;
global $APPLICATION;
global $CACHE_MANAGER;
global $DB;

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
if (!$USER->IsAdmin() && !$access) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Уникальные витрины с учетом работы складов');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$cache = new CPHPCache();

if ($cache->InitCache(604800, 'unique_showcases', '/local/admin')) {
    $arResult = $cache->GetVars()['result'];
    $arStores = $cache->GetVars()['stores'];
} elseif ($cache->StartDataCache()) {
    $CACHE_MANAGER->StartTagCache('/local/admin');
    $CACHE_MANAGER->RegisterTag('unique_showcases');

    $storeList = StoreTable::getList([
        'filter' => [
            'ACTIVE' => 'Y',
        ],
        'select' => [
            'ID',
            'TITLE',
        ]
    ]);

    $arStores = [];
    while ($store = $storeList->fetch()) {
        $arStores[$store['ID']] = $store['TITLE'];
    }

    $query = 'SELECT * FROM rdevs_uniqshowcase';
    $result = $DB->query($query);

    $arResult = [];
    while ($row = $result->Fetch()) {
        $arResult[$row['hash']] = [
            'UNIQ_ID' => $row['uniq_id'],
            'BRANCH' => unserialize($row['branch'])['NAME'],
            'LOCATIONS' => unserialize($row['regions']),
            'EXCEPTIONS' => unserialize($row['excepts']),
            'STORES' => unserialize($row['stores']),
        ];
    }

    $CACHE_MANAGER->EndTagCache();
    $cache->EndDataCache([
        'result' => $arResult,
        'stores' => $arStores,
    ]);
}

if (isset($_POST["is_ajax_download"])) {
    $APPLICATION->RestartBuffer();

    $arHeadTable = [
        "A" => "НОМЕР" . PHP_EOL . "П/П",
        "B" => "УНИКАЛЬНЫЙ ID ВИТРИНЫ",
        "C" => "УНИКАЛЬНЫЙ HASH ВИТРИНЫ",
        "D" => "ЦЕНОВОЙ ФИЛИАЛ",
        "E" => "РЕГИОН (ГОРОД)",
        "F" => "КРОМЕ",
        "G" => "СКЛАДЫ",
    ];

    $arUnderHeadTable = [
        "G" => "Название",
        "H" => "Доставка",
        "I" => "Резерв",
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $lastKey = 0;

    foreach ($arUnderHeadTable as $key => $value) {
        $sheet->getColumnDimension($key)->setAutoSize(true);
        $sheet->setCellValue($key . '2', $value);
        $lastKey = $key;
    }

    foreach ($arHeadTable as $key => $value) {
        if ($value === "СКЛАДЫ") {
            $sheet->mergeCells($key . '1:' . $lastKey . '1');
        } else {
            $sheet->mergeCells($key . '1:' . $key . '2');
        }
        $sheet->getColumnDimension($key)->setAutoSize(true);
        $sheet->setCellValue($key . '1', $value);
    }

    $sheet->getStyle("A1:" . $lastKey . "2")->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    $count = 1;
    $xlsRow = 3;
    foreach ($arResult as $showcaseId => $arItem) {
        $xlsRowMerge = $xlsRow;
        $sheet->setCellValue("A" . ($xlsRow), $count);
        $sheet->setCellValue("B" . ($xlsRow), $arItem['UNIQ_ID']);
        $sheet->setCellValue("C" . ($xlsRow), $showcaseId);
        $sheet->setCellValue("D" . ($xlsRow), $arItem['BRANCH']);
        $sheet->setCellValue("E" . ($xlsRow), strip_tags(implode(PHP_EOL, $arItem['LOCATIONS'])));
        $sheet->setCellValue("F" . ($xlsRow), strip_tags(implode(PHP_EOL, $arItem['EXCEPTIONS'] == ["0000073738" => "Москва"]?"":$arItem['EXCEPTIONS'])));
        foreach ($arItem['STORES'] as $storeId => $arStore) {
            $sheet->setCellValue("G" . ($xlsRow), "[" . sprintf('%03d', $storeId) . "] " . $arStores[$storeId]);
            $sheet->setCellValue("H" . ($xlsRow), $arStore[0] == 1 ? "Да" : "Нет");
            $sheet->setCellValue("I" . ($xlsRow), $arStore[1] == 1 ? "Да" : "Нет");
            $xlsRow++;
        }
        $sheet->mergeCells("A" . $xlsRowMerge . ':A' . ($xlsRow - 1));
        $sheet->mergeCells("B" . $xlsRowMerge . ':B' . ($xlsRow - 1));
        $sheet->mergeCells("C" . $xlsRowMerge . ':C' . ($xlsRow - 1));
        $sheet->mergeCells("D" . $xlsRowMerge . ':D' . ($xlsRow - 1));
        $sheet->mergeCells("E" . $xlsRowMerge . ':E' . ($xlsRow - 1));
        $sheet->mergeCells("F" . $xlsRowMerge . ':F' . ($xlsRow - 1));
        $count++;
    }

    $sheet->getStyle("A3:A" . $xlsRow)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle("B3:F" . $xlsRow)->getAlignment()
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle("H3:I" . $xlsRow)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $writer = new Xlsx($spreadsheet);
    $uploaddir = COption::GetOptionString("main", "upload_dir", "upload");

    $filename = "/" . $uploaddir . "/tmp/unique_showcases.xlsx";
    $writer->save($_SERVER["DOCUMENT_ROOT"] . $filename);

    exit($filename);
}
?>
    <button id="download-xlsx">Скачать в формате xlsx</button>
    <button id="refresh_uniqshowcase-wo">Пересчитать уникальные витрины с учетом работы складов</button>
    <br>
    <br>
    <table class="unique-showcases">
        <thead>
        <tr>
            <th rowspan="2">НОМЕР<br>П/П</th>
            <th rowspan="2">УНИКАЛЬНЫЙ ID ВИТРИНЫ</th>
            <th rowspan="2">УНИКАЛЬНЫЙ HASH ВИТРИНЫ</th>
            <th rowspan="2">ЦЕНОВОЙ ФИЛИАЛ</th>
            <th rowspan="2">РЕГИОН (ГОРОД)</th>
            <th rowspan="2">КРОМЕ</th>
            <th colspan="3">СКЛАДЫ</th>
        </tr>
        <tr>
            <th>Название</th>
            <th>Доставка</th>
            <th>Резерв</th>
        </tr>
        </thead>
        <tbody>
        <? $count = 1;
        foreach ($arResult as $showcaseId => $arItem) : ?>
            <tr>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= $count ?></td>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= $arItem['UNIQ_ID'] ?></td>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= $showcaseId ?></td>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= $arItem['BRANCH'] ?></td>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= implode("<br>", $arItem['LOCATIONS']) ?></td>
            <td rowspan="<?= count($arItem['STORES']) ?>"><?= implode("<br>", $arItem['EXCEPTIONS'] == ["0000073738" => "Москва"]?"":$arItem['EXCEPTIONS']) ?></td>
            <? foreach ($arItem['STORES'] as $storeId => $arStore) : ?>
                <td>[<?= sprintf('%03d', $storeId) ?>] <?= $arStores[$storeId] ?></td>
                <td class="td-center"><?= $arStore[0] == 1 ? "Да" : "Нет" ?></td>
                <td class="td-center"><?= $arStore[1] == 1 ? "Да" : "Нет" ?></td>
                </tr><tr>
            <? endforeach ?>
            </tr>
            <?
            $count++;
        endforeach ?>
        </tbody>
    </table>
    <style>
        table.unique-showcases {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table.unique-showcases td, table.unique-showcases th {
            padding: 5px;
            border: solid 1px;
        }

        table.unique-showcases td.td-center {
            text-align: center;
        }
    </style>
    <script>
        $("#download-xlsx").on("click", function () {
            $.ajax({
                async: true,
                type: "POST",
                url: window.location.href,
                data: [{name: "is_ajax_download", value: "true"}],
                success: function (data) {
                    console.log(data);
                    location.href = data;
                }
            });
        });
        $("#refresh_uniqshowcase-wo").on("click", function () {
            $.ajax({
                async: true,
                type: "POST",
                url: "/local/scripts/uniq_sc.php",
                data: {'with_store_work_type': 'yes', 'value': "true"},
                success: function (data) {
                    $("#refresh_result").html(data);
                    if (data === 'OK') {
                        location.reload();
                    }
                }
            })
        });
    </script>
<? require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
