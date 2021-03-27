<?php
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
global $APPLICATION;
global $CACHE_MANAGER;

if ($_SERVER['REQUEST_METHOD'] == "POST"
    && $_REQUEST['db_reset_500'] != ""
    && check_bitrix_sessid()
    && defined('IBLOCK_CATALOG')
) {
    $sql = 'UPDATE b_iblock_element SET SORT = 500 WHERE IBLOCK_ID = '. IBLOCK_CATALOG;
    $connection = \Bitrix\Main\Application::getConnection();
    $connection->query($sql);

    $CACHE_MANAGER->ClearByTag("iblock_id_".IBLOCK_CATALOG);

    LocalRedirect("/bitrix/admin/likee_sort_reset.php?lang=".LANGUAGE_ID."&success=Y");
}

$APPLICATION->SetTitle('Сброс сортировки товаров');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (! empty($_REQUEST['success'])) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => 'Сброс сортировки произведен успешно.',
        "TYPE" => 'OK',
        "HTML" => true
    ));
}

?>
<form method="POST" action="likee_sort_reset.php?lang=ru" enctype="multipart/form-data" name="editform">

<div class="adm-detail-content-item-block">
    <input type="submit" name="db_reset_500" value="Сбросить сортировку товаров" class="adm-btn-save">
    <div class="adm-info-message-wrap">
        <div class="adm-info-message">
            <p>Всем артикулам будет присвоено свойство <b>сортировка</b> «<b>500</b>»</p>
            <p><span style="color:red">*</span> Внимание! Будет сброшен кэш <b>Каталога товаров</b>. Рекомендуемся производить во время меньшего трафика на сайте.</p>
        </div>
    </div>
</div>

<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?echo LANG?>">
</form>
<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>