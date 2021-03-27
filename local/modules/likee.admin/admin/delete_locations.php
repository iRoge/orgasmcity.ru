<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
use Bitrix\Main\Application;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Main\Localization\Loc;
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/deleted_locations.txt';

$APPLICATION->SetTitle(Loc::getMessage('TITLE'));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
try {
    $application = Application::getInstance();
    $request = $application->getContext()->getRequest();

    if ($request->getPost('apply') == Loc::getMessage('APPLY')) {
        $arFile = $request->getFileList()->toArray();

        if (!empty($arFile['file_locations']['tmp_name'])) {
            $string = date('d.m.Y H:i:s') . ' - ' . $USER->GetId() . ' - ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
            $xls = IOFactory::load($arFile['file_locations']['tmp_name']);
            $xls->setActiveSheetIndex(0);
            $rowIterator = $xls->getActiveSheet()->getRowIterator();
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator('B', 'C');
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() == "B") {
                        $newStr = stristr($cell->getCalculatedValue(), ',', true);
                        if ($newStr != false) {
                            $namePlaceArr[$cell->getRow()]['city'] = $newStr;
                        } else {
                            $namePlaceArr[$cell->getRow()]['city'] = $cell->getCalculatedValue();
                        }
                    } elseif ($cell->getColumn() == "C") {
                        $newStr = stristr($cell->getCalculatedValue(), ',', true);
                        if ($newStr != false) {
                            $namePlaceArr[$cell->getRow()]['region'] = $newStr;
                        } else {
                            $namePlaceArr[$cell->getRow()]['region'] = $cell->getCalculatedValue();
                        }
                    }
                }
            }

            foreach ($namePlaceArr as $key => $item) {
                $arCityRegions[$item['city'] . "," . $item['region']] = $item;
            }

            $arLocationsCity = LocationTable::getList(array(
                'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_ID' => LOCATION_TYPE_CITY),
                'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
            ))->fetchAll();

            $arLocationsRegion = LocationTable::getList(array(
                'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_ID' => LOCATION_TYPE_REGION),
                'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
            ))->fetchAll();

            foreach ($arLocationsRegion as $item) {
                $arRegions[$item['ID']] = $item['NAME_RU'];
            }

            foreach ($arLocationsCity as $item) {
                $arCities[$item['NAME_RU'] . "," . $arRegions[$item['REGION_ID']]] = $item;
            }
            
            $count = 0;
            foreach ($arCities as $key => $value) {
                if (!empty($arCityRegions[$key])) {
                    $res = LocationTable::delete($value['ID']);
                    if ($res->isSuccess()) {
                        $string .= $value['ID'] .  ' - ' . $value['CODE'] . ' - ' . $value['NAME_RU'] . ' - ' . $arCityRegions[$key]['region'] . PHP_EOL;
                        $count++;
                    }
                }
            }

            file_put_contents($filePath, $string, FILE_APPEND);
            unlink($arFile['file_locations']['tmp_name']);
            LocalRedirect('/bitrix/admin/delete_locations.php?lang='.LANGUAGE_ID.'&count='.$count);
        } else {
            $error = Loc::getMessage('NO_FILE');
        }
    }
} catch (Exception $e) {
    $error = $e;
}
?>

<form name="import_settings" method="post" enctype="multipart/form-data">
<?
$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage('FILEMAN_UPL_TAB'),
        "ICON" => "fileman",
        "TITLE" => Loc::getMessage('FILEMAN_UPL_TAB_ALT')
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
    <? if (!empty($error)) :?>
        <? CAdminMessage::ShowMessage(array(
            "MESSAGE" => Loc::getMessage('ERROR', ['#ERROR#' => $error]),
            "TYPE" => "ERROR",
        )); ?>
    <?endif;?>
    <?if (isset($count) && !isset($error)) :?>
        <? CAdminMessage::ShowMessage(array(
            "MESSAGE" => Loc::getMessage('COUNT', ['#COUNT#' => $count]),
            "TYPE" => "OK",
        )); ?>
    <?endif;?>
    <? if (!isset($count) && !isset($error)) :?>
        <? CAdminMessage::ShowMessage(array(
            "MESSAGE" => Loc::getMessage('INFO'),
            "TYPE" => "OK",
        )); ?>
    <?endif;?>
    <tr>
        <td colspan="2" align="left">
            <input type="hidden" name="nums" value="5">
            <table id="bx-upload-tbl">
                <tr>
                    <td class="adm-detail-content-cell-r">
                        <input type="file" name="file_locations" size="30" maxlength="255" value="">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?$tabControl->EndTab();
$tabControl->Buttons(
    array(
        "btnApply" => true,
        "btnSave" => false,
        "back_url" => "/bitrix/admin/index.php"
    )
);
$tabControl->End();
?>
</form>