<?php

use Likee\Site\Helpers\HL;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
\Bitrix\Main\Loader::includeModule('likee.site');
\Bitrix\Main\Loader::includeModule('highloadblock');

use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Highloadblock\HighloadBlockLangTable;

// Параметры для создания списка <select> "Участвует в сортировке"
const BOOLEAN_SELECT_PARAMS = [
    '1' => 'Да',
    '0' => 'Нет'
];

const SORTING_HB_NAME = 'SORTING';

$request = Bitrix\Main\Context::getCurrent()->getRequest();

if ($request->isAjaxRequest() && check_bitrix_sessid()) {
    if ($request->get('action') === 'update') {
        ajaxUpdate($request);
    } elseif ($request->get('action') === 'delete') {
        ajaxDelete($request);
    }

    exit();
}

function ajaxDelete(Bitrix\Main\HttpRequest $request)
{
    if ($id = $request->get('id')) {
        $obEntity = HL::getEntityClassByHLName(SORTING_HB_NAME);
        if ($obEntity && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();

            $sClass::delete($id);
        }
    }
}
function ajaxUpdate(Bitrix\Main\HttpRequest $request)
{
    $updatedItems = $request->get('data');

    if (!empty($updatedItems)) {
        $obEntity = HL::getEntityClassByHLName(SORTING_HB_NAME);
        if ($obEntity && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();

            $isMessageSended = false;

            foreach ($updatedItems as $item) {
                $data = [
                    'UF_HB_NAME' => $item['UF_HB_NAME'],
                    'UF_SORT' => $item['UF_SORT'],
                    'UF_ENABLED' => $item['UF_ENABLED'],
                ];

                // Если есть primary id, тогда обновляем запись
                if (!empty($item['id'])) {
                    $sClass::update($item['id'], $data);
                    // Посылаем сообщение пользователю
                    if (!$isMessageSended) {
                        echo '{"message_type":"update"}';
                        $isMessageSended = true;
                    }
                } else { // Если нет primary id, тогда создаем новую запись
                    // Ищем нет ли такого же HL-блока в Sorting
                    $sameHBCount = $sClass::getCount([
                        '=UF_HB_NAME' => $item['UF_HB_NAME']
                    ]);
                    // Если нет такого же HL-блока в Sorting, то добавляем
                    if (intval($sameHBCount) == 0) {
                        $HLBlocksList = getHLBlocksList();
                        $data['UF_NAME'] = $HLBlocksList[$item['UF_HB_NAME']];

                        $sClass::add($data);
                        // Посылаем сообщение пользователю
                        if (!$isMessageSended) {
                            echo '{"message_type":"add"}';
                            $isMessageSended = true;
                        }
                    } else {
                        // Посылаем сообщение пользователю
                        if (!$isMessageSended) {
                            echo '{"message_type":"hb_exist"}';
                            $isMessageSended = true;
                        }
                    }
                }
            }
        }
    }
}

function loadSortingHB()
{
    $properties = [];

    $obEntity = HL::getEntityClassByHLName(SORTING_HB_NAME);
    if ($obEntity && is_object($obEntity)) {
        $sClass = $obEntity->getDataClass();
        $rsData = $sClass::getList([
            'select' => ['ID', 'UF_NAME', 'UF_ENABLED', 'UF_SORT', 'UF_HB_NAME'],
            'order' => ['UF_SORT']
        ]);

        while ($entity = $rsData->fetch()) {
            array_push($properties, [
                'ID' => $entity['ID'],
                'UF_NAME' => $entity['UF_NAME'],
                'UF_ENABLED' => $entity['UF_ENABLED'],
                'UF_SORT' => $entity['UF_SORT'],
                'UF_HB_NAME' => $entity['UF_HB_NAME'],
            ]);
        }
    }

    return $properties;
}

function getHLBlocksList()
{
    $result = [];

    $HBBlocksList = HighloadBlockTable::getList(
        [
            'select' => ['*'],
        ]
    );

    while ($hlblock = $HBBlocksList->fetch()) {
        $HLBlockLang = HighloadBlockLangTable::getList([
            'filter' => ['ID' => $hlblock['ID']],
        ]);

        while ($item = $HLBlockLang->fetch()) {
            $hlblock['LANG'][$item['LID']] = [
                'NAME' => $item['NAME'],
            ];
        }

        $hlblock['NAME'] = strtoupper($hlblock['NAME']);
        if ($hlblock['NAME'] == 'COLLECTIONHB') {
            $hlblock['NAME'] = 'COLLECTION';
        }
        if (!empty($hlblock['LANG']['ru']['NAME'])) {
            $result[$hlblock['NAME']] = $hlblock['LANG']['ru']['NAME'];
        } else {
            $result[$hlblock['NAME']] = $hlblock['NAME'];
        }
    }

    return array_merge($result, [
        'SORT' => 'Товар'
    ]);
}

function filterHLBlocksList($HLBlocksList, $filterKeys)
{
    foreach ($filterKeys as $key) {
        unset($HLBlocksList[strtoupper($key)]);
    }
    return $HLBlocksList;
}

// Забираем из БД все сортировки сортировок
$sortingList = loadSortingHB();
// Берем список всех HL-блоков
$HLBlocksList = getHLBlocksList();
// Удаляем служебные HL-блоки из списка
$HLBlocksList = filterHLBlocksList(
    $HLBlocksList,
    [
        'SORTING', 'CURRENCY', 'COLORREFERENCE',
        'BRANDREFERENCE', 'EXPLANATIONROSTOVKA', 'ROSTOVKA',
        'UNIT', 'MLT', 'MRT', 'FEEDBACKSUBJECTS',
        'PRICESEGMENTID', 'MAXDISCBP', 'RESPECTDOMAINS',
        'GROUPPRODUCTS', 'FILIAL', 'PRICESHARE'
    ]
);

// Если есть сообщение, то офорляем его для показа
if (!empty($request->get('message_type'))) {
    if ($request->get('message_type') == 'add') {
        $message = 'Поле сортировки создано.';
        $messageType = 'OK';
    } elseif ($request->get('message_type') == 'update') {
        $message = 'Поле сортировки обновлено.';
        $messageType = 'OK';
    } elseif ($request->get('message_type') == 'hb_exist') {
        $message = 'Поле сортировки уже существует!';
        $messageType = 'ERROR';
    }
}

$APPLICATION->SetTitle('Сортировка сортировок');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?
if (!empty($message)) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => $message,
        "TYPE" => $messageType,
    ));
}
?>
<form method="post" id="discount_form">
    <?= bitrix_sessid_post() ?>
    <table>
        <thead>
            <tr>
                <td>Поле сортировки</td>
                <td>Участвует в сортировке</td>
                <td>Сортировка</td>
            </tr>
        </thead>
        <tbody class="js-rules-tbl">
            <? foreach ($sortingList as $prop) : ?>
                <tr class="js-data-row" data-sorting_id="<?= $prop['ID'] ?>">
                    <td>
                        <?= SelectBoxFromArray('UF_HB_NAME', ['REFERENCE' => array_values($HLBlocksList), 'REFERENCE_ID' => array_keys($HLBlocksList)], $prop['UF_HB_NAME']) ?>
                    </td>
                    <td>
                        <?= SelectBoxFromArray('UF_ENABLED', ['REFERENCE' => array_values(BOOLEAN_SELECT_PARAMS), 'REFERENCE_ID' => array_keys(BOOLEAN_SELECT_PARAMS)], intval($prop['UF_ENABLED'])) ?>
                    </td>
                    <td>
                        <input type="number" min="0" max="1000" id="UF_SORT" value="<?= $prop['UF_SORT'] ?>">
                    </td>
                    <td>
                        <input type="button" value="Удалить" class="adm-btn-delete" onclick="deleteRow(this)">
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>
    <input type="button" value="Еще" class="adm-btn-add" onclick="addRow()">
    <input type="submit" name="save" value="Сохранить" class="adm-btn-save">
</form>
<script>
    // Очищает URL от GET параметров
    window.history.pushState('', '', 'sorting_setup.php');

    $('#discount_form').on('submit', function(e) {
        e.preventDefault();
        processForm();
    });
    $('.js-data-row').find(':input').on('change', function(e) {
        if (!$(e.currentTarget).hasClass('js-process')) {
            $(e.currentTarget).closest('tr').addClass('js-row-changed');
            $(e.currentTarget).closest('tr').find('.js-process').prop('checked', true);
        } else {
            $(e.currentTarget).closest('tr').toggleClass('js-row-changed');
        }
    });

    function processForm() {
        var rows = $('.js-row-new, .js-row-changed');
        data = [];
        rows.each(function(i, row) {
            var rowData = {
                'UF_HB_NAME': $(row).find('#UF_HB_NAME').val(),
                'UF_SORT': $(row).find('#UF_SORT').val(),
                'UF_ENABLED': $(row).find('#UF_ENABLED').val(),
            };
            var id = $(row).data('sorting_id');
            if (id) {
                rowData.id = id;
            }

            data.push(rowData);
        });

        $.post(document.location.href, {
            action: 'update',
            data: data,
            sessid: $('#sessid').val()
        }).done(function(data) {
            data = JSON.parse(data);
            window.location.search = '?message_type=' + data.message_type;
        });

    }

    function getRow() {
        var row = `<tr class="js-data-row js-row-new">
            <td>
                <?= SelectBoxFromArray('UF_HB_NAME', ['REFERENCE' => array_values($HLBlocksList), 'REFERENCE_ID' => array_keys($HLBlocksList)]) ?>
            </td>
            <td>
                <?= SelectBoxFromArray('UF_ENABLED', ['REFERENCE' => array_values(BOOLEAN_SELECT_PARAMS), 'REFERENCE_ID' => array_keys(BOOLEAN_SELECT_PARAMS)], '0') ?>
            </td>
            <td>
                <input type="number" min="0" max="1000" id="UF_SORT" value="500">
            </td>
            <td>
                <input type="button" value="Удалить" class="adm-btn-delete" onclick="deleteRow(this)">
            </td>
        </tr>`;

        return row;
    }

    function addRow() {
        var row = getRow();
        $('.js-rules-tbl').append(row);
    }

    function deleteRow(elem) {
        var confirm = window.confirm('Удалить сортировку?');
        if (!confirm) {
            return;
        }
        var row = $(elem).closest('tr');
        if (row.data('sorting_id')) {
            $.post(document.location.href, {
                action: 'delete',
                id: row.data('sorting_id'),
                sessid: $('#sessid').val()
            });
        }

        row.remove();
    }
</script>
<style>
    table {
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    td {
        padding: 7px;
    }

    thead {
        font-weight: bold;
    }

    .js-row-changed {
        background-color: lightgreen;
    }
</style>
<? require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
