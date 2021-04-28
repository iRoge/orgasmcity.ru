<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

$APPLICATION->SetTitle('Обновление местоположений для служб доставки');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

try {
    $application = Application::getInstance();
    $request = $application->getContext()->getRequest();

    if ($request->getPost('apply') == Loc::getMessage('APPLY')) {
        $arFile = $request->getFileList()->toArray();

        if (!empty($arFile['file_locations']['tmp_name'])) {

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

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');