<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$strError = '';

if ($_SERVER['REQUEST_METHOD'] == "POST"
    && $_REQUEST['db_reset_delivery'] != ""
    && check_bitrix_sessid()
    && defined('IBLOCK_CATALOG')
    && CModule::IncludeModule("iblock")) {
    // свойство
    $noReservePropertyId = false;
    $filter = [
        'IBLOCK_ID' => IBLOCK_CATALOG,
        'CODE' => 'DISABLE_DELIVERY'
    ];
    $aItem = \CIBlockProperty::GetList(array('SORT' => 'ASC'), $filter)->Fetch();
    if ($aItem && isset($aItem['ID'])) {
        $noReservePropertyId = $aItem['ID'];
        $sql = "UPDATE b_iblock_element_prop_s16 SET PROPERTY_{$noReservePropertyId} = ''";
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->query($sql);
        $CACHE_MANAGER->ClearByTag("iblock_id_".IBLOCK_CATALOG);
        $CACHE_MANAGER->ClearByTag("catalogAll");
        LocalRedirect("/bitrix/admin/likee_delivery_reset.php?lang=".LANGUAGE_ID."&success=Y");
    } else {
        $strError .= 'В системе отсутствует свойство отключения доставки товара.<br>';
    }
}

$APPLICATION->SetTitle('Сброс запрета доставки товаров');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (!empty($_REQUEST['success'])) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => 'Сброс запрета доставки произведен успешно.',
        "TYPE" => 'OK',
        "HTML" => true
    ));
} elseif ($strError) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => $strError,
        "TYPE" => 'ERROR',
        "HTML" => true
    ));
}
?>
<form method="POST" action="likee_delivery_reset.php?lang=ru" enctype="multipart/form-data" name="editform">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <div class="adm-detail-content-item-block">
        <input type="submit" name="db_reset_delivery" value="Сбросить запрет доставки" class="adm-btn-save">
        <div class="adm-info-message-wrap">
            <div class="adm-info-message">
                <p>Всем артикулам, у которых установлено свойство запрета доставки, данное значение будет сброшено.</p>
                <p><span style="color:red">*</span> Внимание! Будет сброшен кэш <b>Каталога товаров</b>. Рекомендуемся производить во время меньшего трафика на сайте.</p>
            </div>
        </div>
    </div>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
