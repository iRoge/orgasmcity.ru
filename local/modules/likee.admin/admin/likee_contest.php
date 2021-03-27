<?php
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
global $APPLICATION;
global $CACHE_MANAGER;

$connection = \Bitrix\Main\Application::getConnection();
$artList = [];

$sql = "SELECT * FROM `likee_contest_list` ORDER BY `ID` ASC";
$res = $connection->query($sql);

while ($row = $res->fetch()) {
    $artList[] = $row['ART'];
}
unset($sql, $res);

if ($_SERVER['REQUEST_METHOD'] == "POST"
    && check_bitrix_sessid()
) {
    if ($_REQUEST['contest_export'] != "") {
        $userList = [];
        $artByUserList = [];

        $sql = "SELECT c.USER_ID, c.ART, c.STATUS, u.EMAIL, CONCAT_WS(' ', u.LAST_NAME, u.NAME) as FULL_NAME
                FROM likee_contest c 
                INNER JOIN b_user u ON c.USER_ID = u.ID
                ORDER BY c.USER_ID";
        $res = $connection->query($sql);
        
        while ($row = $res->fetch()) {
            $userList[$row['USER_ID']] = $row;
            $artByUserList[$row['USER_ID']][$row['ART']] = $row['STATUS'];
        }
        unset($sql, $res);

        $APPLICATION->RestartBuffer();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=contest_stat.csv');

        $out = fopen('php://output', 'w');

        $header = array_merge(['ID', 'FULL_NAME', 'EMAIL'], $artList);
        $header = array_map(function ($value) {
            return "{$value}\r";
        }, $header);
        fputcsv($out, $header, ';', '"');

        foreach ($artByUserList as $uid => $data) {
            $userData = [];
            $userData[] = $uid;
            $userData[] = iconv('utf-8', 'windows-1251', $userList[$uid]['FULL_NAME']);
            $userData[] = $userList[$uid]['EMAIL'];

            foreach ($artList as $art) {
                $userData[] = $artByUserList[$uid][$art];
            }

            fputcsv($out, $userData, ';', '"');
        }
        unset($userData, $data);

        fclose($out);

        $APPLICATION->FinalActions();
        exit;
    } elseif ($_REQUEST['contest_import'] != "") {
        $connection->query('TRUNCATE TABLE likee_contest_list');

        if (($handle = fopen($_FILES['import']['tmp_name'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $art = trim($data[0]);
                if ($art) {
                    $connection->query("INSERT INTO `likee_contest_list` (`ART`) VALUES ('{$art}');");
                }
            }
            fclose($handle);
        }

        $CACHE_MANAGER->ClearByTag("likee.contest");

        LocalRedirect("/bitrix/admin/likee_contest.php?lang=".LANGUAGE_ID."&success=Y");
    }
}

$APPLICATION->SetTitle('Управление конкурсом');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (! empty($_REQUEST['success'])) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => 'Импорт товаров произведен успешно.',
        "TYPE" => 'OK',
        "HTML" => true
    ));
}

?>
<form method="POST" action="likee_contest.php?lang=ru" enctype="multipart/form-data" name="editform">

<div class="adm-detail-content-item-block">
    <input type="submit" name="contest_export" value="Выгрузить отчет" class="adm-btn-save">
</div>

<div class="adm-detail-content-item-block">
    <input type="file" name="import" />
    <input type="submit" name="contest_import" value="Загрузить артикула" class="adm-btn-save">
</div>

<div class="adm-detail-content-item-block">
    <h3>Загруженные артикула</h3>
    <pre><?=implode("\n", $artList)?></pre>
</div>

<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?echo LANG?>">
</form>
<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>