<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}

if ($_POST["clear"] == "Y") {
    if ($_POST["agree"] == "Y") {
        $CACHE_MANAGER->ClearByTag("catalogAll");
        $ok = true;
    } else {
        $error = "Вы не согласились со сбросом кеша!";
    }
}

$APPLICATION->SetTitle('Сброс кеша каталога');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

?>
    <h3>Кеш витрин каталога</h3>
<?
if ($ok) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Кеш каталога успешно очищен!",
        "TYPE" => "OK",
    ));
} elseif ($error) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => $error,
        "TYPE" => "ERROR",
    ));
} else {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Внимание!!! Будет сброшен кеш всех витрин (секций, группировок, спец разделов) каталога во всех городах. Не рекомендуется делать при нагрузке на сайт!",
        "TYPE" => "OK",
    ));
} ?>
    <form action="<?= $APPLICATION->GetCurPage() ?>" method="POST">
        <input type="hidden" name="clear" value="Y">
        <label><input type="checkbox" name="agree" value="Y">Я понимаю, что будет сброшен кэш всех витрин в
            каталоге</label>
        <br><br>
        <input class="adm-btn-save" type="submit" name="submit" value="Сбросить кеш">
    </form>
    <hr>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
