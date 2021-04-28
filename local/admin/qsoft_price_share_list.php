<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use \Bitrix\Main\Config\Option;

CModule::IncludeModule('highloadblock');

$APPLICATION->SetTitle('Удаление/изменение ценовых акций');
$hlblock = HLBT::getList(array('filter' => array('=NAME' => 'PriceShare')))->fetch();
// Часть кода отвечающая за обработку настроек таблицы
$options['sort'] = COption::GetOptionString("qsoft", "shares_list_sort", 'last_change_date,ASC');
$options['rows'] = COption::GetOptionString("qsoft", "shares_list_rows", '');
$options['pagination'] = COption::GetOptionInt("qsoft", "shares_list_pagination", 5);

// Массив для селектора количества элементов на странице
$arNumSharesForPage = ['5', '10', '20', '50', '100', '200', '500'];

// Массив порядка элементов
if ($options['rows'] === '') {
    $sortCol = [
        0 => [
            'active' => 'Y',
            'name' => 'name',
            'lang' => 'Название акции',
        ],
        1 => [
            'active' => 'Y',
            'name' => 'creation_date',
            'lang' => 'Дата создания',
        ],
        2 => [
            'active' => 'Y',
            'name' => 'last_change_date',
            'lang' => 'Дата изменения',
        ],
        3 => [
            'active' => 'Y',
            'name' => 'active',
            'lang' => 'Активность',
        ],
        4 => [
            'active' => 'Y',
            'name' => 'active_from',
            'lang' => 'Начало акции',
        ],
        5 => [
            'active' => 'Y',
            'name' => 'active_to',
            'lang' => 'Конец акции',
        ],
        6 => [
            'active' => 'Y',
            'name' => 'share_type',
            'lang' => 'Вид акции',
        ],
        7 => [
            'active' => 'Y',
            'name' => 'price_segment',
            'lang' => 'Сегмент',
        ],
        8 => [
            'active' => 'Y',
            'name' => 'branches',
            'lang' => 'Cписок филиалов',
        ],
    ];
} else {
    $options['rows'] = explode(',', $options['rows']);
    $options['rows'] = array_chunk($options['rows'], 3);
    $validOptionAr = [];
    foreach ($options['rows'] as $row) {
        $validOptionAr[$row[1]]['name'] = $row[1];
        $validOptionAr[$row[1]]['lang'] = $row[2];
        $validOptionAr[$row[1]]['active'] = $row[0];
    }
    $sortCol = $validOptionAr;
}
// Обработка настроек таблицы поступивших из пост запроса
if (!empty($_POST['options'])) {
    if (!empty($_POST['options']['sort'])) {
        COption::SetOptionString("qsoft", "shares_list_sort", $_POST['options']['sort']);
        $options['sort'] = $_POST['options']['sort'];
    }
    if (!empty($_POST['options']['rows'])) {
        $sortCol = $_POST['options']['rows'];
        $optionStr = '';
        foreach ($sortCol as $col) {
            $optionStr = $optionStr . ',' . $col['active'] . ',' . $col['name'] . ',' . $col['lang'];
        }
        COption::SetOptionString("qsoft", "shares_list_rows", substr($optionStr, 1));
    }
    if (!empty($_POST['options']['pagination'])) {
        COption::SetOptionInt("qsoft", "shares_list_pagination", $_POST['options']['pagination']);
        $options['pagination'] = $_POST['options']['pagination'];
    }
}
// Изначальный номер страницы
$strPageN = 1;
// Часть кода отвечающая за обработку элементов таблицы
if (!empty($hlblock) && is_array($hlblock)) {
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    if (isset($_GET['delete_share_id'])) {
        $deleted_share = [];
        $rsData = $entity_data_class::getList(array(
            'filter' => array('ID' => $_GET['delete_share_id']),
            'select' => array('UF_BRANCHES', 'UF_NAME'),
        ));
        while ($arData = $rsData->fetch()) {
            $deleted_share['branches'] = explode(',', $arData['UF_BRANCHES']);
            $deleted_share['name'] = $arData['UF_NAME'];
        }
        $arSharesToChange = explode(',', $_GET['delete_share_id']);
        foreach ($arSharesToChange as $shareToChange) {
            $result = $entity_data_class::delete($shareToChange);
        }
        if ($result) {
            $success_delete = true;
        } else {
            $success_delete = false;
        }
    }
    if (isset($_GET['change_share_id'])) {
        if ($_GET['change_type'] == '1') {
            $arSharesToChange = explode(',', $_GET['change_share_id']);
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $changes['UF_ACTIVE'] = 'Y';
            foreach ($arSharesToChange as $shareToChange) {
                $result = $entity_data_class::update($shareToChange, $changes);
            }
        } elseif ($_GET['change_type'] == '0') {
            $arSharesToChange = explode(',', $_GET['change_share_id']);
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $changes['UF_ACTIVE'] = 'N';
            foreach ($arSharesToChange as $shareToChange) {
                $result = $entity_data_class::update($shareToChange, $changes);
            }
        } else {
            $errors[] = 'Не указан тип изменения активности акции';
        }
    }
    $sharesData = [];
    $rsData = $entity_data_class::getList(array(
        'filter' => array(),
        'select' => array('*'),
    ));
    while ($arData = $rsData->fetch()) {
        $sharesData[] = $arData;
    }
    $arShares = [];
    foreach ($sharesData as $arShare) {
        $field['articles'] = explode(',', $arShare['UF_ARTICLES']);
        $field['prices'] = explode(',', $arShare['UF_PRICES']);
        $field['prices1'] = explode(',', $arShare['UF_PRICES1']);
        $field['discounts'] = explode(',', $arShare['UF_DISCOUNTS']);
        $field['discounts_bp'] = explode(',', $arShare['UF_DISCOUNTS_BP']);
        $arShares[$arShare['ID']]['id'] = $arShare['ID'];
        $arShares[$arShare['ID']]['name'] = $arShare['UF_NAME'];
        $arShares[$arShare['ID']]['share_type'] = $arShare['UF_SHARE_TYPE'];
        $arShares[$arShare['ID']]['active'] = $arShare['UF_ACTIVE'];
        $arShares[$arShare['ID']]['active_from'] = $arShare['UF_ACTIVE_FROM'];
        $arShares[$arShare['ID']]['active_to'] = $arShare['UF_ACTIVE_TO'];
        $arShares[$arShare['ID']]['creation_date'] = $arShare['UF_CREATION_DATE'];
        $arShares[$arShare['ID']]['last_change_date'] = $arShare['UF_LAST_CHANGE_DATE'];
        $arShares[$arShare['ID']]['price_segment'] = $arShare['UF_PRICE_SEGMENT'];
        $arShares[$arShare['ID']]['branches'] = $arShare['UF_BRANCHES'];
        while ($article = array_shift($field['articles'])) {
            $arShares[$arShare['ID']]['products'][$article]['article'] = $article;
            $arShares[$arShare['ID']]['products'][$article]['price'] = array_shift($field['prices']);
            $arShares[$arShare['ID']]['products'][$article]['price1'] = array_shift($field['prices1']);
            $arShares[$arShare['ID']]['products'][$article]['discount'] = array_shift($field['discounts']);
            $arShares[$arShare['ID']]['products'][$article]['discount_bp'] = array_shift($field['discounts_bp']);
        }
    }
    // Сортировка
    $sort = explode(',', $options['sort']);
    usort($arShares, function ($a, $b) use ($sort) {
        if ($sort[1] == 'ASC') {
            return $a[$sort[0]] <=> $b[$sort[0]];
        } else {
            return $b[$sort[0]] <=> $a[$sort[0]];
        }
    });

    // Пагинация
    $pagesCount = ceil(count($arShares) / $options['pagination']);
    $i = 1;
    foreach ($arShares as $key => $share) {
        if ($i > $strPageN * $options['pagination']) {
            $arShares[$key]['hidden'] = true;
        } elseif ($i <= $strPageN * $options['pagination'] - $options['pagination']) {
            $arShares[$key]['hidden'] = true;
        }
        $i++;
    }
} else {
    echo '<p style="color: red; font-weight: bold">Не найден инфоблок ценовых акций</p>';
    die();
}
if (empty($arShares)) {
    $errors [] = 'Ценовые акции не найдены';
} else {
    // Достаем филиалы для селекта
    global $DB;
    $branches = [];
    $res = $DB->Query('SELECT id, name FROM b_respect_branch');
    foreach ($arShares as $share) {
        $arShareBranches[$share['ID']] = explode(',', $share['UF_BRANCHES']);
    }
    while ($branch = $res->fetch()) {
        $branches[$branch['id']] = $branch['name'];
    }
}
?>
<style>
    table.list-shares {
        border-spacing: 0;
        background-origin: padding-box;
    }
    table.list-shares td {
        text-align : center;
        width: auto;
        min-width: 125px;
        height: 80px;
        border-bottom: #0a3a68 solid 1px;
        padding-left: 10px;
        padding-right: 10px;
        font-size: 12px;
    }
    table.list-options td {
        text-align : center;
        width: auto;
        min-width: 0px;
        height: auto;
        border-bottom: none;
        padding-left: 10px;
        padding-right: 10px;
    }
    .min-cell {
        min-width: 0 !important;
    }
    thead.head-shares {
        border-bottom: 2px;
        width: 1326px;
    }
    thead.head-shares td {
        text-align: center;
    }
    select:disabled {
        opacity: 1 !important;
    }
    textarea:disabled {
        opacity: 1 !important;
    }
    .thead-icon {
        display:inline-block;
        position: relative;
        width:14px;
        height:12px;
        cursor:pointer;
        background: url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/grid-gear.svg) 50% 50% no-repeat;
        opacity:.4;
        -webkit-transition:opacity 200ms ease;
        -moz-transition:opacity 200ms ease;
        -o-transition:opacity 200ms ease;
        transition:opacity 200ms ease;
    }
    .thead-icon:hover {
        opacity: 1;
    }
    .tbody-icons {
        display:inline-block;
        position: relative;
        width:14px;
        height:12px;
        cursor:pointer;
        background:url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/sprite-interface.min.svg) 0px -201px no-repeat;
        opacity:.4;
        -webkit-transition:opacity 200ms ease;
        -moz-transition:opacity 200ms ease;
        -o-transition:opacity 200ms ease;
        transition:opacity 200ms ease;
    }
    .tbody-icons:hover {
        opacity: 1;
    }
    .sandwich-button {
        text-align : center;
        display: block;
        padding-top: 10px;
        padding-bottom: 10px;
        user-select: none;
        color: #5c6470;
        text-transform: uppercase;
        font-size: 11px;
    }
    .sandwich-button:hover {
        background: #eff0f1;
        cursor: pointer;
    }
    .options-div {
        width: 400px !important;
        user-select: none;
        top: -395px;
    }
    .sandwich-div {
        width: 150px;
        z-index: 1000;
        display: none;
        position: absolute;
        background: white;
        box-shadow: rgba(83, 92, 105, 0.12) 0px 7px 21px, rgba(83, 92, 105, 0.06) 0px -1px 6px 0px;
        padding-top: 5px;
        padding-bottom: 5px;
        animation: popupWindowShowAnimationOpacity 500ms;
        animation-duration: 500ms;
        animation-timing-function: ease;
        animation-delay: 0s;
        animation-iteration-count: 1;
        animation-direction: normal;
        animation-fill-mode: both;
        animation-play-state: running;
        animation-name: popupWindowShowAnimationOpacity;
        animation-fill-mode: both;
        user-select: none;
    }
    .sort-column:hover {
        color: orange;
        cursor: pointer;
    }
    .sort-arrow {
        background: url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/sprite-interface.min.svg) 3px -290px no-repeat;
        visibility: visible;
        display: inline-block;
        width: 14px;
        height: 12px;
        top: 0;
        left: -18px;
    }
    .sort-desc {
        background: url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/sprite-interface.min.svg) 3px -293px no-repeat !important;
        transform: rotate(180deg);
    }
    .list-options {
        padding: 10px;
        z-index: 10001;
    }
    .list-options input {
        user-select: none;
    }
    .list-options tr {
        width: auto !important;
        display: table-row !important;
        user-select: none;
    }
    .get-high {
        background: url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/sprite-interface.min.svg) 3px -293px no-repeat !important;
        transform: rotate(180deg);
        visibility: visible;
        display: inline-block;
        width: 14px;
        height: 12px;
        top: 0;
        left: -18px;
    }
    .get-down {
        background: url(/bitrix/components/bitrix/main.ui.grid/templates/.default/images/sprite-interface.min.svg) 3px -290px no-repeat;
        visibility: visible;
        display: inline-block;
        width: 14px;
        height: 12px;
        top: 0;
        left: -18px;
    }
    .arrow-button:hover {
        cursor: pointer;
    }
    .get-down .arrow-button:last-child {
        display:none;
    }
    .get-high .arrow-button:first-child {
        display:none;
    }
    .pagination-options-table{
        display: table;
        width: 100%;
        padding-top: 10px;
    }
    .pagination-options-table a:hover {
        cursor: pointer;
        color: orange;
        text-decoration: none;
    }
    .pagination-options-table tr {
        display: table;
        width: 100%;
        border-collapse: collapse;
        user-select: none;
    }
    .pagination-options-table td {
        display: table-cell;
        position: relative;
        height: 34px;
        text-transform: uppercase;
        padding-right: 40px;
        padding-left: 15px;
        color: #5c6470;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        text-align: left;
        box-sizing: border-box;
        min-width: 120px;
        width: 1%;
        font-size: 11px;
    }
    .table-options {
        color: #2675d7;
    }
    .table-options:hover {
        cursor: pointer;
        color: orange;
    }
</style>
<div style="width: auto; display: inline-block;" class="adm-detail-content">
    <div style="width: auto; display: inline-block;" class="adm-detail-content-item-block">
        <?php if (isset($success_delete)) : ?>
            <?php if (!$success_delete) : ?>
                <div class="adm-info-message-wrap adm-info-message-red">
                    <div class="adm-info-message">
                        <div class="adm-info-message-title">
                            Произошла ошибка при удалении</div>
                        <div id="discount_reindex_error_cont"></div>
                        <div class="adm-info-message-icon"></div>
                    </div>
                </div>
            <?php elseif ($_GET['delete_share_id']) : ?>
                <div class="adm-info-message-wrap adm-info-message-green reservation_success">
                    <div class="adm-info-message">
                        <div class="adm-info-message-title">Акц(ии/ия) были успешно удалены</div>
                        <div class="adm-info-message-icon"></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['success'])) : ?>
                <div style="max-width: 800px" class="adm-info-message-wrap adm-info-message-green reservation_success">
                    <div class="adm-info-message">
                        <div class="adm-info-message-title">
                            <?=$_GET['success'] === 'added' ? 'Ценовая акция успешно добавлена' : 'Ценовая акция успешно изменена'?>
                        </div>
                        <br>
                        <?php if (!empty($_GET['bad_articles'])) : ?>
                        <div class="adm-info-message-title">
                            Несуществующие артикулы в акции:
                            <span style="color: red"><?=$_GET['bad_articles']?></span>
                        </div>
                        <?php endif; ?>
                        <div class="adm-info-message-icon"></div>
                    </div>
                </div>
        <?php endif; ?>
        <?php if (!empty($errors)) : ?>
            <div class="adm-info-message-wrap adm-info-message-red">
                <div class="adm-info-message">
                    <div class="adm-info-message-title">
                        <?if (is_array($errors)) :?>
                            <? foreach ($errors as $value) : ?>
                                <?=$value?> <br>
                            <? endforeach; ?>
                        <? else : ?>
                            <?=$errors?> <br>
                        <? endif; ?>
                    </div>
                    <div id="discount_reindex_error_cont"></div>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
        <? endif; ?>
        <div>
            <input style="width: 200px" type="button" value="Создать акцию" onclick='top.window.location="/bitrix/admin/qsoft_price_share_add.php?lang=ru"' title="Создать акцию">
        </div>
        <div style="height: 83px;">
                <table class="list-shares">
                    <thead class="head-shares">
                        <tr>
                            <td class="min-cell">
                                <input id="select-all" style="width: 15px; height:15px;" type="checkbox" onclick="switchCheck(this)">
                            </td>
                            <td class="with-popup min-cell">
                                <span class="tbody-icons" onclick="sandwichDivPopup(this)"></span>
                                <div class="sandwich-div">
                                    <div class="sandwich-button" data-action="delete" onclick="changeShares(this)">
                                        <span>Удалить акции</span>
                                    </div>
                                    <div class="sandwich-button" data-action="deactivate" onclick="changeShares(this)">
                                        <span>Деактивировать акции</span>
                                    </div>
                                    <div class="sandwich-button" data-action="activate" onclick="changeShares(this)">
                                        <span>Активировать акции</span>
                                    </div>
                                </div>
                            </td>
                            <?foreach ($sortCol as $col) :
                                if (!$col['active']) {
                                    continue;
                                }
                                ?>
                                <td <?=($col['name'] == 'share_type' || $col['name'] == 'price_segment' || $col['name'] == 'active') ? 'style="min-width: 85px;"' : ''?> <?=$col['name'] == 'branches' ? 'style="min-width: 170px"' : ''?>>
                                    <div <?=$col['name'] != 'branches' ? 'class="sort-column"' : ''?> data-name="<?=$col['name']?>" onclick="createSubmitFormSort(this)"><h4><?=$col['lang']?><span class="<?=$sort[0] == $col['name'] ? $sort[1] == 'DESC' ? 'sort-arrow sort-desc' : 'sort-arrow' : ''?>"></span></h4></div>
                                </td>
                            <?endforeach;?>
                        </tr>
                    </thead>
                </table>
        </div>
        <div class="scrollable-div-articles" style="background: white ;height: fit-content;max-height: 672px;overflow: scroll; width: fit-content">
            <div style="height: auto">
                <table class="list-shares">
                    <tbody>
                    <?php $i = 0; foreach ($arShares as $share) : ?>
                        <tr class="tr-share" <?=$share['hidden'] ? 'hidden' : ''?> <?=$i%2 == 1 ? "style='background: #F5F9F9'" : ''?>>
                            <td class="min-cell">
                                <input class="js-check checkbox-for-share-changes" data-id="<?=$share['id']?>" style="width: 15px; height:15px;" type="checkbox">
                            </td>
                            <td class="with-popup min-cell">
                                <span class="tbody-icons" onclick="sandwichDivPopup(this)"></span>
                                <div class="sandwich-div">
                                    <div class="sandwich-button" onclick="confirmDelete(<?=$share['id']?>)">
                                        <span>Удалить акцию</span>
                                    </div>
                                    <div class="sandwich-button" onclick="confirmSwitchActivation(<?=$share['id']?>, <?=$share['active'] == 'Y' ? 0 : 1?>)">
                                        <span><?=$share['active'] == 'Y' ? 'Деактивировать' : 'Активировать'?> акцию</span>
                                    </div>
                                    <div class="sandwich-button">
                                        <form hidden method="POST" action="/bitrix/admin/qsoft_price_share_add.php?lang=ru" enctype="multipart/form-data" name="copy_price_share" id="copy_price_share">
                                            <input name="name" type="hidden" value="<?=$share['name']?>">
                                            <input name="share_type" type="hidden" value="<?=$share['share_type']?>" >
                                            <input name="active_from" type="hidden" value="<?=$share['active_from']?>">
                                            <input name="active_to" type="hidden" value="<?=$share['active_to']?>">
                                            <?php foreach (array_flip(explode(',', $share['branches'])) as $key => $branch) : ?>
                                                <input class="js-check" style="width: 15px; height:15px;" name="branches[<?=$key?>]" type="hidden" value="<?=$branch?>" checked>
                                            <?php endforeach; ?>
                                            <input name="segment" type="hidden" value="<?=$share['price_segment']?>">
                                            <? foreach ($share['products'] as $key => $product) : ?>
                                            <input name="articles[<?=$key?>]" type="text" value="<?=$product['article']?>">
                                            <input name="prices[<?=$key?>]" type="text" value="<?=$product['price']?>">
                                            <input name="prices1[<?=$key?>]" type="text" value="<?=$product['price1']?>">
                                            <input name="discounts[<?=$key?>]" type="text" value="<?=$product['discount']?>">
                                            <input name="discounts_bp[<?=$key?>]" type="text" value="<?=$product['discount_bp']?>">
                                            <input name="action[change_list]" type="text" value="Копировать">
                                            <? endforeach; ?>
                                        </form>
                                        <span onclick="$(this).siblings('form').submit(); return false;">Копировать акцию</span>
                                    </div>
                                    <div class="sandwich-button"  onclick='top.window.location="/bitrix/admin/qsoft_price_share_edit.php?lang=ru&share_id=<?=$share['id']?>"'>
                                        <span>Посмотреть акцию</span>
                                    </div>
                                </div>
                            </td>
                            <? foreach ($sortCol as $col) :
                                ?>
                                <? if ($col['name'] == 'name') : ?>
                            <td <?=!$col['active'] ? 'hidden' : ''?>>
                                <p><a style="text-decoration: none" href="/bitrix/admin/qsoft_price_share_edit.php?lang=ru&share_id=<?=$share['id']?>"><?=$share['name']?></a></p>
                            </td>
                                <? elseif ($col['name'] == 'active') :?>
                            <td style="min-width: 85px;text-align: center" <?=!$col['active'] ? 'hidden' : ''?>>
                                <p><?=$share['active'] == 'Y' ? '<span style="color: green">Y</span>': '<span style="color: red">N</span>'?></p>
                            </td>
                                <? elseif ($col['name'] == 'share_type' || $col['name'] == 'price_segment') :?>
                            <td style="min-width: 85px;" <?=!$col['active'] ? 'hidden' : ''?>>
                                <p><?=$share[$col['name']]?></p>
                            </td>
                                <? elseif ($col['name'] == 'branches') :?>
                            <td <?=!$col['active'] ? 'hidden' : ''?>>
                                <select disabled multiple style="width: 170px;height: 80px; font-size: 12px;" >
                                    <?php foreach ($branches as $key => $branch) : ?>
                                        <?=isset(array_flip(explode(',', $share[$col['name']]))[$key]) ? '<option>' . $branch . '</option>' : ''?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                                <? else :?>
                            <td <?=!$col['active'] ? 'hidden' : ''?>>
                                <p><?=$share[$col['name']]?></p>
                            </td>
                                <? endif;?>
                            <? endforeach;?>
                        </tr>
                        <?php $i++;
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <form method="POST" action="/bitrix/admin/qsoft_price_share_list.php?lang=ru" enctype="multipart/form-data" name="edit_pagination_number" id="edit_pagination_number">
            <table class="pagination-options-table">
                <tr>
                    <td class="with-popup" style="width: 20%">
                        <span class="table-options" onclick="sandwichDivPopup(this)">Настройки таблицы</span>
                        <div class="sandwich-div options-div">
                            <form method="POST" action="/bitrix/admin/qsoft_price_share_list.php?lang=ru" enctype="multipart/form-data" name="edit-column-options">
                                <table class="list-options">
                                    <thead>
                                    <tr>
                                        <td><p>Название колонки</p></td>
                                        <td><p>Видимость</p></td>
                                        <td><p>Порядок</p></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? foreach ($sortCol as $col) : ?>
                                        <tr>
                                            <td>
                                                <input readonly name="options[rows][<?=$col['name']?>][lang]" type="text" value="<?=$col['lang']?>">
                                                <input type="hidden" name="options[rows][<?=$col['name']?>][name]" type="text" value="<?=$col['name']?>">
                                            </td>
                                            <td><input style="width: 16px; height:16px;" name="options[rows][<?=$col['name']?>][active]" type="checkbox" value="Y" <?=$col['active'] == 'Y' ? 'checked' : ''?>></td>
                                            <td><span class="arrow-button get-high" onclick="getHighColumn(this)"></span><span class="arrow-button get-down" onclick="getDownColumn(this)"></span></td>
                                        </tr>
                                    <? endforeach; ?>
                                    </tbody>
                                </table>
                                <input style="margin-top: 10px;margin-bottom: 10px;left: 285px;width: 100px" type="submit" value="Сохранить" title="Сохранить настройки колонок">
                                <input style="margin-top: 10px;margin-bottom: 10px;left: 75px;width: 100px" type="button" value="Отменить" onclick="$(this).parent().hide()" title="Закрыть окно настроек">
                            </form>
                        </div>
                    </td>
                    <td style="width: 60%">
                        <div style="width: 300px; margin-left: auto; margin-right: auto">
                            <a style="display: <?=($strPageN != 1) ? 'block' : 'none'?>;position: absolute; top: 10px; left: 130px" id="prev-page" value="<?=$strPageN-1?>" onclick="goToPageN(this)"> < Предыдущая</a>
                            <span style="display: block;position: absolute; top: 10px; left: 250px">Страница</span>
                            <select style="display: block;position: absolute; top: 3px; left: 315px" id="page-selector" onchange="goToPageN(this)" name="options[pagination]">
                                <? for ($i = 1; $i <= $pagesCount; $i++) : ?>
                                    <option value="<?=$i?>" <?=$i == $strPageN ? 'selected' : ''?>><?=$i?></option>
                                <? endfor; ?>
                            </select>
                            <a id="next-page" style="display: <?=($strPageN != $pagesCount) ? 'block' : 'none'?>;position: absolute; top: 10px; left: 380px" value="<?=$strPageN+1?>" onclick="goToPageN(this)">Следующая > </a>
                        </div>
                    </td>
                    <td style="width: 20%; line-height: 12px">
                        <div style="width: 200px; margin-left: 30%">
                            <span>Акций на странице:</span>
                            <select onchange="submitPaginationForm()" name="options[pagination]">
                                <? foreach ($arNumSharesForPage as $num) :?>
                                <option value="<?=$num?>" <?=$num == $options['pagination'] ? 'selected' : ''?>><?=$num?></option>
                                <? endforeach;?>
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script>
    $(document).mouseup(function (e) {
        let div = $(".with-popup");
        if (!div.is(e.target) && div.has(e.target).length === 0) {
            $('.sandwich-div').css('display', 'none');
        }
    });
    function confirmDelete (share_id) {
        if (confirm("Вы точно хотите удалить акцию?")) {
            top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&delete_share_id=" + share_id;
        } else {
            return false;
        }
    }
    function confirmSwitchActivation (share_id, type) {
        if (type == 1) {
            if (confirm("Вы точно хотите активировать акцию?")) {
                top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&change_share_id=" + share_id + "&change_type=1";
            } else {
                return false;
            }
        } else if (type == 0) {
            if (confirm("Вы точно хотите деактивировать акцию?")) {
                top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&change_share_id=" + share_id + "&change_type=0";
            } else {
                return false;
            }
        }
    }
    function switchCheck (elem) {
        if (elem.checked) {
            $('.js-check').prop('checked', true);
        } else {
            $('.js-check').prop('checked', false);
        }
    }
    function sandwichDivPopup (elem) {
        let check = $(elem).siblings(".sandwich-div").is(':hidden');
        $('.sandwich-div').hide();
        if (check) {
            $(elem).siblings(".sandwich-div").show();
        } else {
            $(elem).siblings(".sandwich-div").hide();
        }
    }
    function createSubmitFormSort (elem) {
        let form = document.createElement('form');
        form.action = '/bitrix/admin/qsoft_price_share_list.php?lang=ru';
        form.method = 'POST';
        let h4 = $(elem).children('h4');
        let changeSortType = h4.children('.sort-desc').length > 0 ? 'ASC' : 'DESC';
        form.innerHTML = '<input name="options[sort]" value="' + $(elem).attr('data-name') + ',' + changeSortType + '">';
        document.body.append(form);
        form.submit();
    }
    function getHighColumn (elem) {
        let currentTr = $(elem).parent().parent();
        let prev = currentTr.prev();
        if (prev.length != 1) {
            return false;
        }
        let prev2 = prev.clone();
        let elem2 = currentTr.clone();
        elem2.insertAfter(prev);
        prev2.insertAfter(currentTr);
        prev.remove();
        currentTr.remove();
    }
    function getDownColumn (elem) {
        let currentTr = $(elem).parent().parent();
        let next = currentTr.next();
        if (next.length != 1) {
            return false;
        }
        let next2 = next.clone();
        let elem2 = currentTr.clone();
        elem2.insertAfter(next);
        next2.insertAfter(currentTr);
        next.remove();
        currentTr.remove();
    }
    function submitPaginationForm () {
        $('.list-options').remove();
        $('#edit_pagination_number').submit();
    }
    function changeShares (elem) {
        let action = $(elem).attr('data-action');
        let idsToChange = $('.checkbox-for-share-changes').map(function() {
            if ($(this).prop('checked')) {
                return this.dataset.id;
            }
            return false;
        }).get().filter(function (el) {
            return el != false;
        });;
        if (action === 'delete') {
            top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&delete_share_id=" + idsToChange.join(',');
        } else if (action === 'deactivate') {
            top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&change_share_id=" + idsToChange.join(',') + '&change_type=0';
        } else if (action === 'activate') {
            top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&change_share_id=" + idsToChange.join(',') + '&change_type=1';
        }
    }
    function goToPageN (elem) {
        let pageN = Number($(elem).attr('value'));
        let trArticles = $('.tr-share');
        trArticles.each((function (pageN) {
            return function (index) {
                if (index + 1 > pageN * <?=$options['pagination']?>) {
                    $(this).hide();
                } else if (index + 1 <= pageN * <?=$options['pagination']?> - <?=$options['pagination']?>) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            }
        })(pageN));
        if (pageN == 1) {
            $('#prev-page').hide();
        } else {
            $('#prev-page').show();
        }
        if (pageN == <?=$pagesCount?>) {
            $('#next-page').hide();
        } else {
            $('#next-page').show();
        }
        $('#prev-page').attr('value', pageN - 1);
        $('#next-page').attr('value', pageN + 1);
        $('#page-selector').children('option').each((function (pageN) {
            return function(index) {
                if ($(this).val() == pageN) {
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                }
            }
        })(pageN));
    }
</script>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
