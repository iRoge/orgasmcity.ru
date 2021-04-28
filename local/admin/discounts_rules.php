<?php

use Likee\Site\Helpers\HL;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
\Bitrix\Main\Loader::includeModule('likee.site');
\Bitrix\Main\Loader::includeModule('highloadblock');

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
    global $DB;
    if ($id = $request->get('id')) {
        $DB->Query('DELETE FROM qsoft_discounts_rules WHERE  id = ' . $id);
    }
}
function ajaxUpdate(Bitrix\Main\HttpRequest $request)
{
    global $DB;

    $data = $request->get('data');
    $new = false;
    $update = false;
    $sqlN = 'INSERT INTO qsoft_discounts_rules (user_status, brand, branch, typeproduct, vid, discount, active) VALUES ';
    $sqlU = 'INSERT INTO qsoft_discounts_rules (id ,user_status, brand, branch, typeproduct, vid, discount, active) VALUES ';

    foreach ($data as $row) {
        if ($row['typeproduct'] !== 'All') {
            $row['vid'] = 'All';
        }
        if ($row['id']) {
            $update = true;
            $sqlU .= "({$row['id']},'{$row['user_status']}','{$row['brand']}','{$row['branch']}','{$row['typeproduct']}','{$row['vid']}',{$row['discount']}, {$row['active']}),";
        } else {
            $new = true;
            $sqlN .= "('{$row['user_status']}','{$row['brand']}','{$row['branch']}','{$row['typeproduct']}','{$row['vid']}',{$row['discount']}, {$row['active']}),";
        }
    }

    if ($new) {
        $sqlN = rtrim($sqlN, ',');
        $DB->Query($sqlN);
    }
    if ($update) {
        $sqlU = rtrim($sqlU, ',');
        $sqlU .= ' ON DUPLICATE KEY UPDATE user_status=VALUES(user_status), brand=VALUES(brand), branch=VALUES(branch), typeproduct=VALUES(typeproduct), vid=VALUES(vid), discount=VALUES(discount), active=VALUES(active)';
        $DB->Query($sqlU);
    }
}
function loadProperties($props, $table = null)
{
    if (!is_array($props) && !empty($table)) {
        $props = [$props => $table];
    }

    $properties = [];
    foreach ($props as $prop => $table) {
        $obEntity = HL::getEntityClassByTableName($table);
        if ($obEntity && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();
            $rsData = $sClass::getList(['select' => ['ID', 'UF_XML_ID', 'UF_NAME']]);

            $properties[$prop]['All'] = 'Все';
            while ($entry = $rsData->fetch()) {
                $properties[$prop][$entry['UF_XML_ID']] = $entry['UF_NAME'] . ' [' . $entry['ID'] . ']';
            }
        }
    }

    return $properties;
}

function sortBrand(array $brands)
{
    uasort($brands, function ($a, $b) {
        if ($a === 'Все') {
            return -1;
        } elseif ($a === 'Respect') {
            return -1;
        } elseif ($a === 'Респект') {
            return -1;
        }

        if ($b === 'Все') {
            return 1;
        } elseif ($b === 'Respect') {
            return 1;
        } elseif ($b === 'Респект') {
            return 1;
        }


        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    });

    return $brands;
}

function sortItems(array $items)
{
    uasort($items, function ($a, $b) {
        if ($a === 'Все') {
            return -1;
        }
        if ($b === 'Все') {
            return 1;
        }
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    });

    return $items;
}

function loadBranches()
{
    global $DB;
    $res = $DB->Query("SELECT id, name FROM b_respect_branch");

    $branches = [
        0 => 'Все',
    ];
    while ($branch = $res->Fetch()) {
        $branches[$branch['id']] = $branch['name'];
    }

    return $branches;
}

function getUserTypes()
{
    return [
        'Все',
        'Платиновый',
        'Золотой',
        'Серебряный',
        'Бронзовый'
    ];
}

function getRules()
{
    global $DB;
    $dbRes = $DB->Query('SELECT * FROM qsoft_discounts_rules');

    $rules = [];
    while ($rule = $dbRes->Fetch()) {
        $rules[$rule['id']] = $rule;
    }

    return $rules;
}

$props = loadProperties([
    'BRAND' => 'b_1c_dict_brand',
    'TYPEPRODUCT' => 'b_1c_dict_type_product',
    'VID' => 'b_1c_dict_vid',
]);


$props = array_merge([
    'BRANCH' => loadBranches(),
    'USER_STATUS' => getUserTypes(),
], $props);
$props['BRAND'] = sortBrand($props['BRAND']);
foreach (['VID', 'TYPEPRODUCT', 'BRANCH'] as $key) {
    $props[$key] = sortItems($props[$key]);
}

$rules = getRules();
$APPLICATION->SetTitle('Скидки по бонусной программе');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<? CAdminMessage::ShowMessage(array(
    "MESSAGE" => "Статус пользователя:
\"Платина\" - пользователь авторизован и имеет статус \"Платина\"
\"Золото\" - пользователь авторизован и имеет статус \"Золото\"
\"Серебро\" - пользователь авторизован и имеет статус \"Серебро\"
\"Бронза\" - пользователь не авторизован, пользователь авторизован и не имеет статуса, пользователь авторизован и имеет статус \"Бронза\"
\"Все\" - любой пункт из списка",
    "TYPE" => "OK",
)); ?>
     <form method="post" id="discount_form">
        <?= bitrix_sessid_post() ?>
        <table>
            <thead>
            <tr>
                <td>Филиал</td>
                <td>Статус пользователя</td>
                <td>Бренд</td>
                <td>Тип изделия</td>
                <td>Вид номенклатуры</td>
                <td>Скидка</td>
                <td>Активность</td>
                <td>Обновить</td>
            </tr>
            </thead>
            <tbody class="js-rules-tbl">
            <?foreach ($rules as $id => $rule) :?>
            <tr class="js-data-row" data-rule_id="<?=$id?>">
                <? foreach ($props as $prop => $values) : ?>
                    <?$key = strtolower($prop)?>
                    <td><?= SelectBoxFromArray($key, ['REFERENCE' => array_values($values), 'REFERENCE_ID' => array_keys($values)], $rule[$key]) ?></td>
                <? endforeach; ?>
                <td>
                    <input type="number" min="0" max="100" id="discount" value="<?=$rule['discount']?>">
                </td>
                <td>
                    <input type="checkbox" id="active" <?=$rule['active'] ? 'checked' : ''?>>
                </td>
                <td>
                    <input type="checkbox" class="js-process"">
                </td>
                <td>
                    <input type="button" value="Удалить" class="adm-btn-delete" onclick="deleteRow(this)">
                </td>
            </tr>
            <?endforeach;?>
            </tbody>
        </table>
        <input type="button" value="Еще" class="adm-btn-add" onclick="addRow()">
        <input type="submit" name="save" value="Сохранить" class="adm-btn-save">
    </form>
    <script>
        $('#discount_form').on('submit', function (e) {
           e.preventDefault();
           processForm();
        });
        $('.js-data-row').find(':input').on('change', function (e) {
            if(!$(e.currentTarget).hasClass('js-process')) {
                $(e.currentTarget).closest('tr').addClass('js-row-changed');
                $(e.currentTarget).closest('tr').find('.js-process').prop('checked', true);
            } else {
                $(e.currentTarget).closest('tr').toggleClass('js-row-changed');
            }
        });
        
        function updateNotify() {
            window.alert('Для того, что бы применить изменения, сбросьте кеш цен. Сервисы - Кеш каталога - Кеш филиальных цен');
        }

        function processForm() {
            var rows = $('.js-row-new, .js-row-changed');
            data = [];
            rows.each(function (i, row) {
                var rowData = {
                    'branch' : $(row).find('#branch').val(),
                    'user_status' : $(row).find('#user_status').val(),
                    'vid' : $(row).find('#vid').val(),
                    'typeproduct' : $(row).find('#typeproduct').val(),
                    'brand' : $(row).find('#brand').val(),
                    'discount' : $(row).find('#discount').val(),
                    'active' : $(row).find('#active').prop('checked'),
                };
                if (rowData.typeproduct !== 'All') {
                    rowData.vid = 'All';
                }
                var id =  $(row).data('rule_id');
                if (id) {
                    rowData.id = id;
                }

                data.push(rowData);
            });

            $.post(document.location.href, {
                action: 'update',
                data: data,
                sessid: $('#sessid').val()
            }).done(function () {
                updateNotify();
                window.location.reload();
            });

        }


        function getRow() {
            var row = `<tr class="js-data-row js-row-new">
            <? foreach ($props as $prop => $values) : ?>
                <td><?= SelectBoxFromArray(strtolower($prop), ['REFERENCE' => array_values($values), 'REFERENCE_ID' => array_keys($values)]) ?></td>
            <? endforeach; ?>
            <td>
                <input type="number" min="0" max="100" name="discount" id="discount">
            </td>
            <td>
                    <input type="checkbox" id="active" checked>
                </td>
            <td>
                <input type="checkbox" class="js-process" checked readonly>
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
            var confirm = window.confirm('Удалить правило?');
            if (!confirm) {
                return;
            }
            var row = $(elem).closest('tr');
            if (row.data('rule_id')) {
                $.post(document.location.href, {
                    action: 'delete',
                    id: row.data('rule_id'),
                    sessid: $('#sessid').val()
                });
            }

            row.remove();
            updateNotify();
        }
    </script>
<style>
    .js-row-changed {
        color: red;
    }
</style>
<? require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");