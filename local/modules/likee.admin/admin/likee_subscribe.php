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
    && $_REQUEST['download_tag_report'] != ""
    && check_bitrix_sessid()
    && defined('IBLOCK_CATALOG')
) {
    $dateFrom = empty($_POST['DATE_INSERT_FROM']) ? date('d.m.Y 00:00:00', (time()-(60*60*24*31))) : $_POST['DATE_INSERT_FROM'];
    $dateTo = empty($_POST['DATE_INSERT_TO']) ? date('d.m.Y H:i:s') : $_POST['DATE_INSERT_TO'];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=respect_subscribes.csv');

    $csv = fopen("php://output", 'w');

    \Bitrix\Main\Loader::includeModule('sender');

    $contactDb = \Bitrix\Sender\ContactTable::getList([
        'filter' => [
            '>=DATE_INSERT' => $dateFrom,
            '<=DATE_INSERT' => $dateTo,
        ],
        'order' => ['DATE_INSERT' => 'DESC']
    ]);
    while (($contact = $contactDb->fetch()) !== false) {
        $sSubscribeTag = '';

        $arUser = \CUser::GetList($by = 'ID', $order = 'ASC', [
            '=EMAIL' => $contact['EMAIL']
        ], [
            'SELECT' => ['UF_SUBSCRIBE_TAGS']
        ])->Fetch();

        if ($arUser && !empty($arUser['UF_SUBSCRIBE_TAGS'])) {
            $arSubscribeTags = explode(',', $arUser['UF_SUBSCRIBE_TAGS']);

            foreach ($arSubscribeTags as $tag) {
                if (false !== stripos($tag, 'одписка')) {
                    $sSubscribeTag = $tag;
                    break;
                }
            }
            unset($arSubscribeTags);
        }
        
        fputcsv($csv, [
            $contact['DATE_INSERT']->format('d.m.y'),
            $contact['EMAIL'],
            iconv('UTF-8', 'CP1251', $sSubscribeTag)
        ], ';');
    }

    fclose($csv);
    exit;
}

$APPLICATION->SetTitle('Отчет по подписчикам');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="POST" action="likee_subscribe.php?lang=ru" enctype="multipart/form-data" name="editform" target="_blank">

<div class="adm-detail-content-wrap">
    <div class="adm-detail-content">
        <div class="adm-detail-content-item-block">
            <table class="adm-detail-content-table edit-table" id="options_popup_fo_edit_table">
                <tbody>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">Интервал c:</td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <div class="adm-input-wrap adm-input-wrap-calendar">
                                <input class="adm-input adm-input-calendar" type="text" name="DATE_INSERT_FROM" size="22" value="<?= date('d.m.Y 00:00:00', (time()-(60*60*24*31))) ?>">
                                <span class="adm-calendar-icon" title="Нажмите для выбора даты" onclick="BX.calendar({node:this, field:'DATE_INSERT_FROM', form: '', bTime: true, bHideTime: false});"></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l">Интервал по:</td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <div class="adm-input-wrap adm-input-wrap-calendar">
                                <input class="adm-input adm-input-calendar" type="text" name="DATE_INSERT_TO" size="22" value="<?= date('d.m.Y H:i:s') ?>">
                                <span class="adm-calendar-icon" title="Нажмите для выбора даты" onclick="BX.calendar({node:this, field:'DATE_INSERT_TO', form: '', bTime: true, bHideTime: false});"></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>&nbsp;</div>
        <input type="submit" name="download_tag_report" value="Скачать отчет по активным подписчикам" class="adm-btn-save">
        <div class="adm-info-message-wrap">
            <div class="adm-info-message">
                <p>Будет сформирован отчет с указанием почтового адреса и тега подписки (источник).</p>
                <p>В отчете могут отсутствовать записи по тегам для подписчиков, добавленных до активации механизма сохранения тегов.</p>
            </div>
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