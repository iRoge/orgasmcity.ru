<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');


CModule::IncludeModule('highloadblock');

if (empty($_GET['share_id']) && !is_numeric($_GET['share_id'])) {
    echo '<p style="color: red; font-weight: bold">Ценовая акция не найдена</p>';
    die();
}

$shareId = $_GET['share_id'];
$hlblock = HLBT::getList(array('filter' => array('=NAME' => 'PriceShare')))->fetch();
if (!empty($hlblock) && is_array($hlblock)) {
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $rsData = $entity_data_class::getList(array(
        'filter' => array('ID' => $shareId),
        'select' => array('*'),
        'limit' => '1',
    ));
    while ($arData = $rsData->fetch()) {
        $arDataShare = $arData;
    }
}
if (empty($arDataShare)) {
    echo '<p style="color: red; font-weight: bold">Ценовая акция не найдена</p>';
    die();
}

// Пагинация
function paginateItems(&$articlesList, $paginationOptions, $currentPage)
{
    $i = 1;
    foreach ($articlesList as $key => $share) {
        if ($i > $currentPage * $paginationOptions) {
            $articlesList[$key]['hidden'] = true;
        } elseif ($i <= $currentPage * $paginationOptions - $paginationOptions) {
            $articlesList[$key]['hidden'] = true;
        }
        $i++;
    }
}
// Сортировка
function sortArticles(&$articlesList, $arSortOptions)
{
    usort($articlesList, function ($a, $b) use ($arSortOptions) {
        if ($arSortOptions[1] == 'ASC') {
            return $a[$arSortOptions[0]] <=> $b[$arSortOptions[0]];
        } else {
            return $b[$arSortOptions[0]] <=> $a[$arSortOptions[0]];
        }
    });
}

// Часть кода отвечающая за обработку настроек таблицы

$options['sort'] = COption::GetOptionString("qsoft", "shares_edit_sort", 'article,ASC');
$options['rows'] = COption::GetOptionString("qsoft", "shares_edit_rows", '');
$options['pagination'] = COption::GetOptionInt("qsoft", "shares_edit_pagination", 5);

// Массив для селектора количества элементов на странице
$arNumSharesForPage = ['5', '10', '20', '50', '100', '200', '500'];

// Массив порядка элементов
if ($options['rows'] === '') {
    $sortCol = [
        0 => [
            'active' => 'Y',
            'name' => 'article',
            'lang' => 'Артикул',
        ],
        1 => [
            'active' => 'Y',
            'name' => 'price',
            'lang' => 'Price',
        ],
        2 => [
            'active' => 'Y',
            'name' => 'price1',
            'lang' => 'Price1',
        ],
        3 => [
            'active' => 'Y',
            'name' => 'discount',
            'lang' => 'Уценка от price',
        ],
        4 => [
            'active' => 'Y',
            'name' => 'discount_bp',
            'lang' => 'Скидка по БП',
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
// Параметры сортировки
$sort = explode(',', $options['sort']);
// Изначальный номер страницы
$pageN = 1;
// Массив параметров акции
$arShare = [];
// Обработка данных из запроса
if (!empty($_POST)) {
    // Обработка настроек таблицы поступивших из пост запроса
    if (!empty($_POST['options'])) {
        if (!empty($_POST['options']['sort'])) {
            COption::SetOptionString("qsoft", "shares_edit_sort", $_POST['options']['sort']);
            $sort = explode(',', $_POST['options']['sort']);
        }
        if (!empty($_POST['options']['rows']) && ($_POST['action']['change_list'] == 'Сохранить')) {
            $sortCol = $_POST['options']['rows'];
            $optionStr = '';
            foreach ($sortCol as $col) {
                $optionStr = $optionStr . ',' . $col['active'] . ',' . $col['name'] . ',' . $col['lang'];
            }
            COption::SetOptionString("qsoft", "shares_edit_rows", substr($optionStr, 1));
        }
        if (!empty($_POST['options']['pagination'])) {
            COption::SetOptionInt("qsoft", "shares_edit_pagination", $_POST['options']['pagination']);
            $options['pagination'] = $_POST['options']['pagination'];
        }
    }
    // Обработка новых артикулов из csv файла
    if (!empty($_POST['action']['change_list'])) {
        if ($_POST['action']['change_list'] == 'Добавить' || $_POST['action']['change_list'] == 'Поменять') {
            $sharesData = [];
            if ($_FILES['import']['tmp_name'] && (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == 'xlsx')) {
                // Создаём ридер
                $reader = new Xlsx();
                // Читаем файл и записываем информацию в переменную
                $spreadsheet = $reader->load($_FILES['import']['tmp_name']);

                // Так можно достать объект Cells, имеющий доступ к содержимому ячеек
                $cells = $spreadsheet->getActiveSheet()->getCellCollection();

                // Далее перебираем все заполненные строки (столбцы A - E)
                for ($row = 1; $row <= $cells->getHighestRow(); $row++) {
                    if ($artCell = $cells->get('A'.$row)) {
                        $article = $artCell->getValue();
                        $sharesData[$article ? $article : $row]['article'] = $article;
                    } else {
                        $article = '';
                        $sharesData[$article ? $article : $row]['article'] = '';
                    }
                    if ($priceCell = $cells->get('B'.$row)) {
                        $sharesData[$article ? $article : $row]['price'] = $priceCell->getValue();
                    } else {
                        $sharesData[$article ? $article : $row]['price'] = '';
                    }
                    if ($price1Cell = $cells->get('C'.$row)) {
                        $sharesData[$article ? $article : $row]['price1'] = $price1Cell->getValue();
                    } else {
                        $sharesData[$article ? $article : $row]['price1'] = '';
                    }
                    if ($discountCell = $cells->get('D'.$row)) {
                        $sharesData[$article ? $article : $row]['discount'] = $discountCell->getValue();
                    } else {
                        $sharesData[$article ? $article : $row]['discount'] = '';
                    }
                    if ($discount_bpCell = $cells->get('E'.$row)) {
                        $sharesData[$article ? $article : $row]['discount_bp'] = $discount_bpCell->getValue();
                    } else {
                        $sharesData[$article ? $article : $row]['discount_bp'] = '';
                    }
                }
            } else {
                if (!$_FILES['import']['tmp_name']) {
                    $errors['main'][] = 'Не загружен файл';
                } else {
                    $errors['main'][] = 'Не верное расширение файла, должно быть xlsx';
                }
            }
            $arGetListArticles = [];
            $dataArticles = CIBlockElement::getList(
                array(),
                array('ACTIVE' => 'Y', 'IBLOCK_ID' => IBLOCK_CATALOG),
                false,
                array(),
                array('ID', 'PROPERTY_ARTICLE')
            );
            while ($article = $dataArticles->getNext()) {
                $arGetListArticles[$article['PROPERTY_ARTICLE_VALUE']] = $article['ID'];
            }
            $badArticles = [];
            foreach ($sharesData as $key => $row) {
                if (!empty($row['article'])) {
                    if (!key_exists($row['article'], $arGetListArticles)) {
                        $badArticles[] = $row['article'];
                    }
                }
                $share_type = $_POST['share_type'];
            }
        }
        // Собираем свойства акции
        $arShare['name'] = $_POST['name'];
        $arShare['active'] = $_POST['active'] ? 'Y' : 'N';
        $arShare['share_type'] = $_POST['share_type'];
        $arShare['active_from'] = $_POST['active_from'];
        $arShare['active_to'] = $_POST['active_to'];
        $arShare['price_segment'] = $_POST['segment'];
        $arShare['branches'] = implode(',', array_keys($_POST['branches']));
        $arShareBranches = explode(',', $arShare['branches']);
        // Собираем данные по артикулам из запроса
        $column['articles'] = $_POST['articles'];
        $column['prices'] = $_POST['prices'];
        $column['prices1'] = $_POST['prices1'];
        $column['discounts'] = $_POST['discounts'];
        $column['discounts_bp'] = $_POST['discounts_bp'];
        $i = $i ? $i : 1;
        while (($article = array_shift($column['articles'])) || $article === '') {
            $arShare['products'][$article ? $article : $i]['article'] = $article ? $article : '';
            $arShare['products'][$article ? $article : $i]['price'] = array_shift($column['prices']);
            $arShare['products'][$article ? $article : $i]['price1'] = array_shift($column['prices1']);
            $arShare['products'][$article ? $article : $i]['discount'] = array_shift($column['discounts']);
            $arShare['products'][$article ? $article : $i]['discount_bp'] = array_shift($column['discounts_bp']);
            $i++;
        }
        if (($_POST['action']['change_list'] == 'Добавить') && (empty($errors['main']))) {
            if (!empty($arShare['products'])) {
                $arShare['products'] = $sharesData + $arShare['products'];
            } else {
                $arShare['products'] = $sharesData;
            }
        } elseif (($_POST['action']['change_list'] == 'Поменять') && (empty($errors['main']))) {
            $arShare['products'] = $sharesData;
        }
        sortArticles($arShare['products'], $sort);
        paginateItems($arShare['products'], $options['pagination'], $pageN);
    } elseif ($_POST['action']['change_share']) {
        // Сохранение / применение новых св-в акции
        $success = false;
        $errors = [];
        $changes = [];
        // Валидируем общие поля акции
        if (!$_POST['name']) {
            $errors['main'][] = 'Название должно состоять хотя бы из 1 символа';
        }
        if (empty($_POST['active_from'])) {
            $errors['main'][] = 'Заполните дату начала акции';
        }
        if (!empty($_POST['active_to'])) {
            if (!(strtotime(date("d.m.Y H:i:s")) < strtotime($_POST['active_to']))) {
                $errors['main'][] = 'Дата окончания не может быть в прошлом';
            }
        } else {
            $errors['main'][] = 'Заполните дату окончания акции';
        }
        if (empty($_POST['branches'])) {
            $errors['main'][] = 'Нужно выбрать филиалы';
        }
        $arGetListArticles = [];
        $dataArticles = CIBlockElement::getList(
            array(),
            array('ACTIVE' => 'Y', 'IBLOCK_ID' => IBLOCK_CATALOG),
            false,
            array(),
            array('ID', 'PROPERTY_ARTICLE')
        );
        while ($article = $dataArticles->getNext()) {
            $arGetListArticles[$article['PROPERTY_ARTICLE_VALUE']] = $article['ID'];
        }
        // Собираем свойства акции
        $arShare['name'] = $_POST['name'];
        $arShare['active'] = $_POST['active'] ? 'Y' : 'N';
        $arShare['share_type'] = $_POST['share_type'];
        $arShare['active_from'] = $_POST['active_from'];
        $arShare['active_to'] = $_POST['active_to'];
        $arShare['price_segment'] = $_POST['segment'];
        $arShare['branches'] = implode(',', array_keys($_POST['branches']));
        $arShareBranches = explode(',', $arShare['branches']);
        // Собираем данные по артикулам из запроса и валидируем
        $column['articles'] = $_POST['articles'];
        $column['prices'] = $_POST['prices'];
        $column['prices1'] = $_POST['prices1'];
        $column['discounts'] = $_POST['discounts'];
        $column['discounts_bp'] = $_POST['discounts_bp'];
        $i = 1;
        while (($article = array_shift($column['articles'])) || $article === '') {
            $arShare['products'][$article ? $article : $i]['article'] = $article ? $article : '';
            $arShare['products'][$article ? $article : $i]['price'] = array_shift($column['prices']);
            $arShare['products'][$article ? $article : $i]['price1'] = array_shift($column['prices1']);
            $arShare['products'][$article ? $article : $i]['discount'] = array_shift($column['discounts']);
            $arShare['products'][$article ? $article : $i]['discount_bp'] = array_shift($column['discounts_bp']);
            $i++;
        }
        sortArticles($arShare['products'], $sort);
        paginateItems($arShare['products'], $options['pagination'], $pageN);
        $badArticles = [];
        $i = 1;
        foreach ($arShare['products'] as $key => $row) {
            if (!empty($row['article']) && $row['article']) {
                if (!key_exists($row['article'], $arGetListArticles)) {
                    $badArticles[] = $row['article'];
                }
            }
            $share_type = $_POST['share_type'];
            if ($share_type == 1) {
                if (!trim($row['price'])) {
                    $errors['csv'][1]['text'] = 'Не указано поле price (1-ый тип акции) в:';
                    $errors['csv'][1]['row'] .= 'строка №' . $i . ', ';
                }
            } elseif ($share_type == 2) {
                if (!trim($row['discount']) || $row['discount'] == '0') {
                    $errors['csv'][2]['text'] = 'Не указано поле discount (2-ой тип акции) в строках:';
                    $errors['csv'][2]['row'] .= 'строка №' . $i . ', ';
                }
            } else {
                if (!trim($row['discount_bp']) || $row['discount_bp'] == '0') {
                    $errors['csv'][3]['text'] = 'Не указано поле discount_bp (3-ий тип акции) в строках:';
                    $errors['csv'][3]['row'] .= 'строка №' . $i . ', ';
                }
            }
            if (!is_numeric($row['price']) && $row['price'] != '') {
                $errors['csv'][4]['text'] = 'В поле price присутствует что-то кроме цифр в строках:';
                $errors['csv'][4]['row'] .= 'строка №' . $i . ', ';
            }
            if (!is_numeric($row['price']) && $row['price1'] != '') {
                $errors['csv'][5]['text'] = 'В поле price присутствует что-то кроме цифр в строках:';
                $errors['csv'][5]['row'] .= 'строка №' . $i . ', ';
            }
            if (!is_numeric($row['discount']) && $row['discount'] != '') {
                $errors['csv'][6]['text'] = 'В поле discount присутствует что-то кроме цифр в строках:';
                $errors['csv'][6]['row'] .= 'строка №' . $i . ', ';
            }
            if (!is_numeric($row['discount_bp']) && $row['discount_bp'] != '') {
                $errors['csv'][7]['text'] = 'В поле discount_bp присутствует что-то кроме цифр в строках:';
                $errors['csv'][7]['row'] .= 'строка №' . $i . ', ';
            }
            if (($row['article'] == '')) {
                $errors['csv'][0]['text'] = 'Не заполнен артикул в строках: ';
                $errors['csv'][0]['row'] .= 'строка №' . $i . ', ';
            }
            $i++;
        }
        // Добавляем\удаляем из бд все, что насобирали, что более не требует валидации
        if (empty($errors)) {
            $changes['UF_ACTIVE_FROM'] = $_POST['active_from'];
            $changes['UF_ACTIVE_TO'] = $_POST['active_to'];
            $changes['UF_NAME'] = $_POST['name'];
            $changes['UF_ACTIVE'] = $_POST['active'] ? 'Y' : 'N';
            $changes['UF_PRICE_SEGMENT'] = $_POST['segment'];
            $changes['UF_LAST_CHANGE_DATE'] = date("d.m.Y H:i:s");
            $changes['UF_PRICES'] = implode(',', $_POST['prices']);
            $changes['UF_BRANCHES'] = implode(',', array_keys($_POST['branches']));
            $changes['UF_ARTICLES'] = implode(',', $_POST['articles']);
            $changes['UF_SHARE_TYPE'] = $_POST['share_type'];
            if ($changes['UF_SHARE_TYPE'] == '1') {
                $changes['UF_PRICES1'] =implode(',', $_POST['prices1']);
            }
            if ($changes['UF_SHARE_TYPE'] == '2') {
                $changes['UF_DISCOUNTS'] = implode(',', $_POST['discounts']);
            }
            if ($changes['UF_SHARE_TYPE'] == '3') {
                $changes['UF_DISCOUNTS_BP'] = implode(',', $_POST['discounts_bp']);
            }
            if ($_POST['action']['change_share'] == 'Применить') {
                $result = $entity_data_class::update($shareId, $changes);
                if ($result) {
                    $success = true;
                }
            } else {
                $result = $entity_data_class::update($shareId, $changes);
                if ($result) {
                    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/bitrix/admin/qsoft_price_share_list.php?lang=ru&success=changed&bad_articles=" . implode(',', $badArticles));
                }
            }
        }
    }
} else {
    // Данные для страницы без POST запросов

    // Собираем свойства акции
    $arShare['id'] = $arDataShare['ID'];
    $arShare['name'] = $arDataShare['UF_NAME'];
    $arShare['active'] = $arDataShare['UF_ACTIVE'];
    $arShare['share_type'] = $arDataShare['UF_SHARE_TYPE'];
    $arShare['active_from'] = $arDataShare['UF_ACTIVE_FROM'];
    $arShare['active_to'] = $arDataShare['UF_ACTIVE_TO'];
    $arShare['price_segment'] = $arDataShare['UF_PRICE_SEGMENT'];
    $arShare['branches'] = $arDataShare['UF_BRANCHES'];
    $arShareBranches = explode(',', $arShare['branches']);
    // Достаем поартикулярный список
    $field['articles'] = explode(',', $arDataShare['UF_ARTICLES']);
    $field['prices'] = explode(',', $arDataShare['UF_PRICES']);
    $field['prices1'] = explode(',', $arDataShare['UF_PRICES1']);
    $field['discounts'] = explode(',', $arDataShare['UF_DISCOUNTS']);
    $field['discounts_bp'] = explode(',', $arDataShare['UF_DISCOUNTS_BP']);
    while ($article = array_shift($field['articles'])) {
        $arShare['products'][$article ? $article : $i]['article'] = $article;
        $arShare['products'][$article ? $article : $i]['price'] = array_shift($field['prices']);
        $arShare['products'][$article ? $article : $i]['price1'] = array_shift($field['prices1']);
        $arShare['products'][$article ? $article : $i]['discount'] = array_shift($field['discounts']);
        $arShare['products'][$article ? $article : $i]['discount_bp'] = array_shift($field['discounts_bp']);
    }
    sortArticles($arShare['products'], $sort);
    paginateItems($arShare['products'], $options['pagination'], $pageN);
}
// Количество страниц
$pagesCount = ceil(count($arShare['products']) / $options['pagination']);

// Достаем филиалы для селекта
global $DB;
$branches = [];
$res = $DB->Query('SELECT id, name FROM b_respect_branch');
while ($branch = $res->fetch()) {
    $branches[$branch['id']] = $branch['name'];
}
$APPLICATION->SetTitle('Ценовая акция #' . $shareId);
?>
<style>
    a:hover {
        cursor: pointer;
    }
    .share-buttons {
        position: relative;
        display: inline-block;
        margin: 0 auto;
    }
    td {
        width: 250px;
    }
    table {
        margin: 0 auto;
    }
    table.list-articles {
        border-spacing: 0;
        background-origin: padding-box;
    }
    table.list-articles td {
        text-align : left;
        width: auto;
        min-width: 125px;
        height: 40px;
        border-bottom: #0a3a68 solid 1px;
        padding-left: 10px;
        padding-right: 10px;
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
    thead.head-articles {
        border-bottom: 2px;
    }
    thead.head-articles td {
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
        top: -275px;
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
    .main-share-options td{
        height:30px;
    }
</style>
<div style="display: inline-block;float: left">
    <a href="/bitrix/admin/qsoft_price_share_list.php?lang=<?=LANGUAGE_ID?>" class="adm-detail-toolbar-btn"><span class="adm-detail-toolbar-btn-l"></span><span class="adm-detail-toolbar-btn-text">Вернутся в список акций</span><span class="adm-detail-toolbar-btn-r"></span></a>
</div>
    <br>
    <br>
<div style="width: auto;max-width: 800px" class="adm-detail-content">
    <div style="width: auto; display: inline-block;" class="adm-detail-content-item-block">
        <?php if (!empty($errors)) : ?>
            <div class="adm-info-message-wrap adm-info-message-red">
                <div class="adm-info-message">
                    <div class="adm-info-message-title">
                        <?php if (!empty($errors['main'])) : ?>
                            <b><h3>Ошибки формы:</h3></b>
                            <?php foreach ($errors['main'] as $error) : ?>
                                <?=$error?> <br>
                            <?php endforeach;
                            unset($errors['main']);?>
                        <?php endif; ?>
                        <?php if (!empty($errors['csv'])) : ?>
                            <b><h3>Ошибки данных списка:</h3></b>
                            <?php foreach ($errors['csv'] as $key => $error) : ?>
                               <p><?=$error['text']?></p><?=$error['row']?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div id="discount_reindex_error_cont"></div>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($sharesData) || $success) : ?>
        <div class="adm-info-message-wrap adm-info-message-green reservation_success">
            <div class="adm-info-message">
                <div class="adm-info-message-title">
                <?php if ($success) : ?>
                    <p>Изменения успешно сохранены</p>
                <?php endif; ?>
                <?php if ($_POST['action']['change_list'] == 'Добавить') : ?>
                    <p>Артикулы добавлены в список, нажмите "Применить" или "Сохранить", чтобы сохранить результат</p>
                <?php endif;?>
                <?php if ($_POST['action']['change_list'] == 'Поменять') : ?>
                    <p>Список изменен, нажмите "Применить" или "Сохранить", чтобы сохранить результат</p>
                <?php endif;?>
                <?php if (!empty($badArticles)) : ?>
                    Несуществующие артикулы в списке:
                    <span style="color: red"><?=implode(', ', $badArticles)?></span>
                <?php endif;?>
            </div>
            <div class="adm-info-message-icon"></div>
        </div>
        </div>
        <?php endif; ?>
    <form method="POST" action="/bitrix/admin/qsoft_price_share_edit.php?lang=ru&share_id=<?=$shareId?>" enctype="multipart/form-data" name="edit_price_share" id="edit_price_share">
        <table class="main-share-options">
            <thead>
                <tr>
                    <td>
                        <h2><b>Свойство</b></h2>
                    </td>
                    <td>
                        <h2><b>Значение</b></h2>
                    </td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <h3><b>Название акции</b></h3>
                </td>
                <td>
                    <input name="name" type="text" value="<?=$_POST['name'] ? $_POST['name'] : $arShare['name']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Тип акции</b></h3>
                </td>
                <td>
                    <select id="share-type-selector" name="share_type" onchange="changePriceSegment(this)">
                        <option value="1" <?=$_POST ? $_POST['share_type'] == 1 ? 'selected' : '' : $arShare['share_type'] == 1 ? 'selected' : '' ?>>1</option>
                        <option value="2" <?=$_POST ? $_POST['share_type'] == 2 ? 'selected' : '' : $arShare['share_type'] == 2 ? 'selected' : '' ?>>2</option>
                        <option value="3" <?=$_POST ? $_POST['share_type'] == 3 ? 'selected' : '' : $arShare['share_type'] == 3 ? 'selected' : '' ?>>3</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Активность</b></h3>
                </td>
                <td>
                    <input style="width: 15px; height:15px;" name="active" type="checkbox" value="Y" <?=!empty($_POST) ? ($_POST['active'] == 'Y' ? 'checked' : '') : ($arShare['active'] == 'Y' ? 'checked' : '')?>>
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Активно c</b></h3>
                </td>
                <td>
                    <input placeholder="Нажмите для раскрытия" name="active_from" type="text" value="<?=$_POST['active_from'] ? $_POST['active_from'] : $arShare['active_from']?>" onclick="BX.calendar({node: this, field: this, bTime: true});">
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Активно до</b></h3>
                </td>
                <td>
                    <input placeholder="Нажмите для раскрытия" name="active_to" type="text" value="<?=$_POST['active_to'] ? $_POST['active_to'] : $arShare['active_to']?>" onclick="BX.calendar({node: this, field: this, bTime: true});">
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Филиалы</b></h3>
                </td>
                <td style="display: block; width: 250px; height: 130px; overflow: auto">
                    <b>
                        <?php foreach ($branches as $key => $branch) : ?>
                            <input class="js-check-branches" style="width: 15px; height:15px;" name="branches[<?=$key?>]" type="checkbox" value="<?=$branch?>" <?=!empty($_POST) ? key_exists($key, $_POST['branches']) ? 'checked' : '' : isset(array_flip($arShareBranches)[$key]) ? 'checked' : ''?>>
                            <span><?=$branch?></span>
                            <br>
                        <?php endforeach; ?>
                </td>
                <td>
                    <input type="button" value="Выбрать все" onclick='doCheck()' title="Выбрать все">
                    <input type="button" value="Убрать все" onclick='removeCheck()' title="Убрать все">
                </td>
            </tr>
            <tr>
                <td>
                    <h3><b>Сегмент</b></h3>
                </td>
                <td>
                    <input name="segment" type="text" value="<?=!empty($_POST) ? $_POST['share_type'] == 1 || $_POST['share_type'] == 2 ? 'Red' : 'White' : $arShare['share_type'] == 1 || $arShare['share_type'] == 2 ? 'Red' : 'White'?>" readonly>
                </td>
            </tr>
            </tbody>
        </table>
        <h2 id="articles-h2">Артикулы</h2>
        <div>
            <div style="overflow: auto">
                <table class="list-articles">
                    <thead class="head-articles">
                        <tr>
                            <td class="min-cell">
                                <span>#</span>
                            </td>
                            <td class="min-cell">
                                <input id="select-all" style="width: 15px; height:15px;" type="checkbox" onclick="switchCheck(this)">
                            </td>
                            <td class="with-popup min-cell">
                                <span class="tbody-icons" onclick="sandwichDivPopup(this)"></span>
                                <div class="sandwich-div">
                                    <div class="sandwich-button" data-action="delete" onclick="changeArticles(this)">
                                        <span>Удалить строки</span>
                                    </div>
                                </div>
                            </td>
                            <?foreach ($sortCol as $col) :
                                if (!$col['active']) {
                                    continue;
                                }
                                ?>
                                <td <?=($col['name'] == 'share_type' || $col['name'] == 'price_segment' || $col['name'] == 'active') ? 'style="min-width: 85px;"' : ''?>>
                                    <div <?=$col['name'] != 'branches' ? 'class="sort-column"' : ''?> data-name="<?=$col['name']?>" onclick="createSubmitFormSort(this)"><h4><?=$col['lang']?><span class="<?=$sort[0] == $col['name'] ? $sort[1] == 'DESC' ? 'sort-arrow sort-desc' : 'sort-arrow' : ''?>"></span></h4></div>
                                </td>
                            <?endforeach;?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="scrollable-div-articles" style="background: white ;height: fit-content;max-height: 672px;overflow: scroll; width: fit-content">
            <div style="height: auto">
                <table class="list-articles">
                    <tbody id="articles_list">
                        <? $i = 1; foreach ($arShare['products'] as $key => $product) : ?>
                            <tr class="tr-article" <?=$product['hidden'] ? 'hidden' : ''?> id="<?=$key?>" style='background-color: #E1EAEB'>
                                <td style="text-align: center" class="min-cell row-number">
                                    <span><?=$i;?></span>
                                </td>
                                <td class="min-cell">
                                    <input class="js-check checkbox-for-share-changes" style="width: 15px; height:15px;" type="checkbox">
                                </td>
                                <td class="with-popup min-cell">
                                    <span class="tbody-icons" onclick="sandwichDivPopup(this)"></span>
                                    <div class="sandwich-div">
                                        <div class="sandwich-button" onclick="deleteArticle(this)">
                                            <span>Удалить строку</span>
                                        </div>
                                        <div class="sandwich-button" onclick="copyArticle(this)">
                                            <span>Копировать строку</span>
                                        </div>
                                    </div>
                                </td>
                            <? foreach ($sortCol as $col) :
                                ?>
                                <? if ($col['name'] == 'article') : ?>
                                <td <?=!$col['active'] ? 'hidden' : ''?>>
                                    <input id="<?=$product['article']?>" style="display: block;margin: 0 auto;width: 100px" name="articles[<?=$key?>]" type="text" value="<?=$product['article']?>">
                                </td>
                                <? elseif ($col['name'] == 'price') :?>
                                <td <?=!$col['active'] ? 'hidden' : ''?>>
                                    <input class="price-input" placeholder="Пусто - из базы" style="display: block;margin: 0 auto;width: 100px" name="prices[<?=$key?>]" type="text" value="<?=$product['price']?>">
                                </td>
                                <? elseif ($col['name'] == 'price1') :?>
                                <td <?=!$col['active'] ? 'hidden' : ''?>>
                                    <input class="price1-input" placeholder="Пусто - из базы" style="display: block;margin: 0 auto;width: 100px" name="prices1[<?=$key?>]" type="text" value="<?=$product['price1']?>" <?=$arShare['share_type'] != 1 ? 'readonly' : ''?>
                                </td>
                                <? elseif ($col['name'] == 'discount') :?>
                                <td <?=!$col['active'] ? 'hidden' : ''?>>
                                    <input class="discount-input" style="display: block;margin: 0 auto;width: 100px" name="discounts[<?=$key?>]" type="text" value="<?=$arShare['share_type'] == 2 ? $product['discount'] : ''?>" <?=$arShare['share_type'] != 2 ? 'readonly' : ''?>
                                </td>
                                <? else :?>
                                <td <?=!$col['active'] ? 'hidden' : ''?>>
                                    <input class="discount-bp-input" style="display: block;margin: 0 auto;width: 100px" name="discounts_bp[<?=$key?>]" type="text" value="<?=$arShare['share_type'] == 3 ? $product['discount_bp'] : ''?>" <?=$arShare['share_type'] != 3 ? 'readonly' : ''?>
                                </td>
                                <? endif;?>
                            <? endforeach;?>
                        </tr>
                            <? $i++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <table class="pagination-options-table">
            <tr>
                <td class="with-popup" style="width: 20%">
                    <span class="table-options" onclick="sandwichDivPopup(this, 'Y')">Настройки таблицы</span>
                    <div class="sandwich-div options-div">
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
                                    <td>
                                        <input style="width: 16px; height:16px;" name="options[rows][<?=$col['name']?>][active]" type="checkbox" value="Y" <?=$col['active'] == 'Y' ? 'checked' : ''?>>
                                    </td>
                                    <td>
                                        <span class="arrow-button get-high" onclick="getHighColumn(this)"></span><span class="arrow-button get-down" onclick="getDownColumn(this)"></span>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                            </tbody>
                        </table>
                        <input style="margin-top: 10px;margin-bottom: 10px;left: 285px;width: 100px" type="submit" name="action[change_list]" value="Сохранить" title="Сохранить настройки колонок">
                        <input style="margin-top: 10px;margin-bottom: 10px;left: 75px;width: 100px" type="button" name="action[change_list]" value="Отменить" onclick="$(this).parent().hide()" title="Закрыть окно настроек">
                    </div>
                </td>
                <td style="width: 40%">
                    <div style="width: 300px; margin-left: auto; margin-right: auto">
                            <a style="position: absolute; top: 12px" <?=($pageN != 1) ? '' : 'hidden'?> id="prev-page" style="padding-right: 10px" value="<?=$pageN-1?>" onclick="goToPageN(this)"> < Предыдущая</a>
                        <span style="position: absolute; top: 12px; left: 125px">Страница</span>
                        <select id="page-selector" style="position: absolute; left: 195px; top: 5px;" onchange="goToPageN(this)" name="options[pagination]">
                            <? for ($i = 1; $i <= $pagesCount; $i++) : ?>
                                <option value="<?=$i?>" <?=$i == $pageN ? 'selected' : ''?>><?=$i?></option>
                            <? endfor; ?>
                        </select>
                            <a style="position: absolute; left: 250px; top: 12px" <?=($pageN != $pagesCount) ? '' : 'hidden'?> id="next-page" style="padding-left: 10px" value="<?=$pageN+1?>" onclick="goToPageN(this)">Следующая > </a>
                    </div>
                </td>
                <td style="width: 40%; line-height: 12px">
                    <div style="width: 235px;">
                        <span style="position: absolute;top: 12px;">Артикулов на странице:</span>
                        <select style="position: absolute;top: 5px; left: 170px" onchange="submitPaginationForm()" name="options[pagination]">
                            <? foreach ($arNumSharesForPage as $num) :?>
                                <option value="<?=$num?>" <?=$num == $options['pagination'] ? 'selected' : ''?>><?=$num?></option>
                            <? endforeach;?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
        <h3 style="padding-top: 25px">Добавить вручную</h3>
    </form>
        <table class="js-error" style="width: 750px">
            <tr style="height: 10px">
                <td>
                    <span>Артикул</span><input style="width: 100px;" name="sArt" type="text" value="">
                </td>
                <td>
                    <span>Price</span><input style="width: 100px" class="price-input" name="sPrice" type="text" value="">
                </td>
                <td>
                    <span>Price1</span><input style="width: 100px" class="price1-input" name="sPrice1" type="text" value="" <?=$arShare['share_type'] != 1 ? 'readonly' : ''?>>
                </td>
                <td>
                    <span>Уценка от Price</span><input style="width: 100px" class="discount-input" name="sDiscount" type="text" value="" <?=$arShare['share_type'] != 2 ? 'readonly' : ''?>>
                </td>
                <td>
                    <span>Скидка по БП</span><input style="width: 100px" class="discount-bp-input" name="sDiscountBP" type="text" value="" <?=$arShare['share_type'] != 3 ? 'readonly' : ''?>>
                </td>
                <td>
                    <input style="margin-top: 13px;width: 100px" onclick='addRow()' class="adm-btn-save" type="button" value="Добавить" title="Добавить строчку">
                </td>
            </tr>
        </table>
        <h3 style="padding-top: 25px">Установить свойства для всех артикулов</h3>
        <table class="" style="width: 650px">
            <tr style="height: 10px">
                <td>
                    <span>Price</span><input style="width: 100px" name="cPrice" type="text" value="">
                </td>
                <td>
                    <span>Price1</span><input style="width: 100px" class="price1-input" name="cPrice1" type="text" value="" <?=$arShare['share_type'] != 1 ? 'readonly' : ''?>>
                </td>
                <td>
                    <span>Уценка от Price</span><input style="width: 100px" class="discount-input" name="cDiscount" type="text" value="" <?=$arShare['share_type'] != 2 ? 'readonly' : ''?>>
                </td>
                <td>
                    <span>Скидка по БП</span><input style="width: 100px" class="discount-bp-input" name="cDiscountBP" type="text" value="" <?=$arShare['share_type'] != 3 ? 'readonly' : ''?>>
                </td>
                <td style="padding-left: 29px">
                    <input style="margin-top: 13px;width: 100px" onclick='allArticlesChange()' class="adm-btn-save" type="button" value="Установить" title="Установить значение для всех артикулов в списке">
                </td>
            </tr>
        </table>
        <table style="width: 450px">
            <tr>
                <td>
                    <h3 style="display: inline-block;">Из файла</h3>
                    <img class="js-img-hover" src="/bitrix/js/main/core/images/hint.gif" style="display: inline-block; margin-left: 5px;">
                    <div style="border: black solid 2px;display: none; padding: 10px; position: absolute; background: white; z-index: 10000;" class="js-file-info-popup">
                        <p>Файл акции должен иметь формат xlsx!<br>
                            Столбцы должны быть строго в таком порядке: <br>
                            столбец А - артикул<br>
                            столбец B - цена price (обязательна для 1-го типа акции)<br>
                            столбец C - цена price1<br>
                            столбец D - скидка от price для 2-го типа акций (обязательна для 2-го типа акции)<br>
                            столбец E - скидка по бонусному предложению для 3-го типа акций (обязательна для 3-го типа акции)<br><br>
                            Все не общие и не пренадлежащие типу акции (не подразумевающие изменения этим типом акции) поля будут просто игнорированы функционалом.
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <input style="position: absolute;" type="file" name="import" form="edit_price_share"/>
                </td>
                <td>
                    <input  style="margin-top: 20px" class="adm-btn-save" name="action[change_list]" type="submit" value="Добавить" title="Добавить артикулы из файла (одинаковые артикулы заменятся на новые)" form="edit_price_share">
                    <input  style="margin-top: 20px" class="adm-btn-save" name="action[change_list]" type="submit" value="Поменять" title="Полностью поменять на артикулы из файла" form="edit_price_share">
                </td>
            </tr>
        </table>
        <div style="padding-top: 75px" class="share-buttons">
            <input class="adm-btn-save" name="action[change_share]" type="submit" title="Сохранить акцию" value="Сохранить" form="edit_price_share">
            <input class="adm-btn-save" name="action[change_share]" type="submit" value="Применить" title="Применить изменения" form="edit_price_share">
            <input type="button" value="Удалить" name="delete" onclick='confirmDelete()' title="Удалить акцию и вернуться">
            <input type="button" value="Отменить" name="cancel" onclick='top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru"' title="Не изменять и вернуться">
        </div>
    </div>
</div>
<script>
    $('.adm-detail-content').ready(function () {
        if ($('.adm-info-message-wrap').length) {
            $('html, body').animate({scrollTop: $('.adm-info-message-wrap').offset().top}, 0);
        } else if ('<?=$_POST['PAGE_N']?>') {
            $('html, body').animate({scrollTop: $('.pagination-options-table').offset().top - $(window).height() + 75}, 0);
        } else if ('<?=$_POST['action']['change_list']?>') {
            $('html, body').animate({scrollTop: $('#articles-h2').offset().top}, 0);
        }
        return false;
    });
    $(document).mouseup(function (e) {
        let div = $(".with-popup");
        if (!div.is(e.target) && div.has(e.target).length === 0) {
            $('.sandwich-div').css('display', 'none');
        }
    });
    $('.js-img-hover').hover(function () {
        $('.js-file-info-popup').show()
    }, function () {
        $('.js-file-info-popup').hide()
    })
    function copyArticle(elem) {
        $(elem).parent().hide();
        let newElem = $(elem).parent().parent().parent().clone();
        newElem.children('.row-number').empty();
        newElem.css('background', '#D0F0C0');
        $('#articles_list').append(newElem);
        let div = $(".scrollable-div-articles");
        div.scrollTop(div.prop('scrollHeight'));
    }
    function doCheck() {
        $('.js-check-branches').prop('checked', true)
    }
    function removeCheck() {
        $('.js-check-branches').prop('checked', false)
    }
    function confirmDelete() {
        if (confirm("Вы точно хотите удалить акцию?")) {
            top.window.location="/bitrix/admin/qsoft_price_share_list.php?lang=ru&delete_share_id=<?=$shareId?>"
        } else {
            return false;
        }
    }
    function deleteArticle(elem) {
        $(elem).parent().parent().parent().remove();
        $('#articles_list tr:hidden:first').show();
    }
    function addRow() {
        let article = $('input[name=sArt]');
        let price = $('input[name=sPrice]');
        let price1 = $('input[name=sPrice1]');
        let discount = $('input[name=sDiscount]');
        let discount_bp = $('input[name=sDiscountBP]');
        if ($('input[name=sArt]').val() != '') {
            let checkArtIfExists = document.getElementById($('input[name=sArt]').val());
            if (!checkArtIfExists) {
                $('#articles_list').append('        <tr class="tr-article" id="' + article.val() + '" style="background: #D0F0C0"><td style="width: 30px; text-align: center" class="min-cell row-number">\n'+
                    '                                    <span></span>\n'+
                    '                                </td><td style="text-align: center;" class="min-cell">\n' +
                    '                        <input class="js-check checkbox-for-share-changes" style="width: 15px; height:15px;" type="checkbox">\n' +
                    '                    </td>\n' +
                    '                    <td class="with-popup min-cell">\n' +
                    '                        <span class="tbody-icons" onclick="sandwichDivPopup(this)"></span>\n' +
                    '                        <div class="sandwich-div">\n' +
                    '                            <div class="sandwich-button" onclick="deleteArticle(this)">\n' +
                    '                                <span>Удалить строку</span>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                    </td><td>' +
                    '                <input style="display: block;margin: 0 auto;width: 100px" name="articles[' + article.val() + ']" type="text" value="' + article.val() + '">\n</td>\n<td>' +
                    '<input class="price-input" placeholder="Пусто - из базы" style="display: block;margin: 0 auto;width: 100px" name="prices[' + article.val() + ']" type="text" value="' + price.val() + '">' +
                    '            </td>\n' +
                    '            <td>\n' +
                    '                <input class="price1-input" placeholder="Пусто - из базы" style="display: block;margin: 0 auto;width: 100px" name="prices1[' + article.val() + ']" type="text" value="' + price1.val() + '"' + ($('#share-type-selector').val() != 1 ? 'readonly' : '') + '>' +
                    '            </td>\n' +
                    '            <td>\n' +
                    '                <input class="discount-input" style="display: block;margin: 0 auto;width: 100px" name="discounts[' + article.val() + ']" type="text" value="' + discount.val() + '"' + ($('#share-type-selector').val() != 2 ? 'readonly' : '') + '>' +
                    '            </td>\n' +
                    '            <td>\n' +
                    '                <input class="discount-bp-input" style="display: block;margin: 0 auto;width: 100px" name="discounts_bp[' + article.val() + ']" type="text" value="' + discount_bp.val() + '"' + ($('#share-type-selector').val() != 3 ? 'readonly' : '') + '>' +
                    '            </td>\n' +
                    '        </tr>');
                $('.p-error').remove();
                $('.p-success').remove();
                article.val('');
                price.val('');
                price1.val('');
                discount.val('');
                discount_bp.val('');
                $('.js-error').after('<p class="p-success" style="color: green">Артикул добавлен</p>');
            } else if (!$('#already_exists').length) {
                $('.p-error').remove();
                $('.p-success').remove();
                $('.js-error').after('<p class="p-error" style="color: red">Данный артикул уже существует</p>');
            }
        } else {
            $('.p-error').remove();
            $('.p-success').remove();
            $('.js-error').after('<p class="p-error" style="color: red">Введите артикул</p>');
        }
        let div = $(".scrollable-div-articles");
        div.scrollTop(div.prop('scrollHeight'));
    }
    function changePriceSegment(select) {
        let PSElement = $('input[name="segment"]');
        let val = select.value;
        let price1Inputs = $('.price1-input');
        let discountInputs = $('.discount-input');
        let discountBpInputs = $('.discount-bp-input');
        if (val == 1) {
            price1Inputs.attr('readonly', false);
            discountInputs.attr('readonly', true);
            discountBpInputs.attr('readonly', true);
            PSElement.val('Red');
        } else if (val == 2) {
            price1Inputs.attr('readonly', true);
            discountInputs.attr('readonly', false);
            discountBpInputs.attr('readonly', true);
            PSElement.val('Red');
        } else if (val == 3) {
            price1Inputs.attr('readonly', true);
            discountInputs.attr('readonly', true);
            discountBpInputs.attr('readonly', false);
            PSElement.val('White');
        }
    }
    function switchCheck (elem) {
        if (elem.checked) {
            $('.js-check').each(function (index) {
                if (!$(this).is(':hidden')) {
                    $(this).prop('checked', true);
                }
            })
        } else {
            $('.js-check').each(function (index) {
                if (!$(this).is(':hidden')) {
                    $(this).prop('checked', false);
                }
            })
        }
    }
    function sandwichDivPopup (elem, WOMove = 'N') {
        let sandwich = $(elem).siblings(".sandwich-div");
        if (WOMove == 'N') {
            sandwich.css('top', ($(elem).offset().top + 20) + 'px');
        }
        let check = sandwich.is(':hidden');
        $('.sandwich-div').hide();
        if (check) {
            sandwich.show();
        } else {
            sandwich.hide();
        }
    }
    function createSubmitFormSort (elem) {
        let form = $('#edit_price_share');
        let h4 = $(elem).children('h4');
        let changeSortType = h4.children('.sort-desc').length > 0 ? 'ASC' : 'DESC';
        form.append('<input type="hidden" name="options[sort]" value="' + $(elem).attr('data-name') + ',' + changeSortType + '">');
        form.append('<input type="hidden" name="action[change_list]" value="Y">');
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
    function goToPageN (elem) {
        let pageN = Number($(elem).attr('value'));
        let trArticles = $('.tr-article');
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
    function submitPaginationForm () {
        let form = $('#edit_price_share');
        form.append('<input type="hidden" name="action[change_list]" value="Y">');
        form.submit();
    }
    function changeArticles (elem) {
        let action = $(elem).attr('data-action');
        let articlesToChange = $('.checkbox-for-share-changes').map(function() {
            if ($(this).prop('checked')) {
                return $(this).parent().parent();
            }
            return false;
        }).get().filter(function (el) {
            if (el == false) {
                return false;
            } else if (el.is(':hidden') == false) {
                return true;
            }
            return false;
        });
        let numArticlesToChange = articlesToChange.length;
        if (action === 'delete') {
            for (let i = 0; i < numArticlesToChange; i++) {
                articlesToChange[i].remove();
            }
            let i = 1;
            $('#articles_list tr:hidden').each((function(i,numArticlesToChange) {
                return function() {
                    $(this).show();
                    if (i == numArticlesToChange) {
                        return false;
                    }
                    i++;
                }
            })(i,numArticlesToChange))
        }
        $('.sandwich-div').hide();
    }
    function allArticlesChange () {
        let priceValue = $('input[name=cPrice]').val();
        let price1Value = $('input[name=cPrice1]').val();
        let discountValue = $('input[name=cDiscount]').val();
        let discount_bpValue = $('input[name=cDiscountBP]').val();
        $('#articles_list input.price-input').each(function (i) {
            if (priceValue != '') {
                $(this).val(priceValue);
            }
        });
        $('#articles_list input.price1-input').each(function (i) {
            if (price1Value != '') {
                $(this).val(price1Value);
            }
        });
        $('#articles_list input.discount-input').each(function (i) {
            if (discountValue != '') {
                $(this).val(discountValue);
            }
        });
        $('#articles_list input.discount-bp-input').each(function (i) {
            if (discount_bpValue != '') {
                $(this).val(discount_bpValue);
            }
        });
        $('input[name=cPrice]').val('');
        $('input[name=cPrice1]').val('');
        $('input[name=cDiscount]').val('');
        $('input[name=cDiscountBP]').val('');
    }
</script>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
