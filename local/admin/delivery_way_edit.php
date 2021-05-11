<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/lib/delivery/inputs.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Qsoft\DeliveryWays\WaysDeliveryTable;
use Qsoft\DeliveryWays\WaysByDeliveryServicesTable;
use Bitrix\Sale\Delivery\Services\Table as DeliveryTable;
use Bitrix\Sale\Delivery\Restrictions;
use Bitrix\Sale\Delivery\Services;
use Bitrix\Sale\Internals\Input;

$request = Application::getInstance()->getContext()->getRequest();
$way_id = intval($request->getQuery('WAY_ID'));
$page_title = ($way_id > 0) ? Loc::getMessage("EDIT_WAY") : Loc::getMessage("ADD_WAY");
$link_field = 'DELIVERIES';
$arErrors = [];

//Получаем поля
$arFields = WaysDeliveryTable::getEntity()->getFields();
$arValues = [];
if ($way_id > 0) {
    $oValues = WaysDeliveryTable::getById($way_id)->fetchObject();
    foreach ($arFields as $field) {
        if ($field->getName() != $link_field) {
            $arValues[$field->getName()] = $oValues->get($field->getName());
        } elseif ($field->getName() == $link_field) {
            $rsDeliveries = WaysByDeliveryServicesTable::getList([
                'select' => ['DELIVERY_ID', 'ID_1C'],
                'filter' => ['WAY_ID' => $way_id]
            ]);

            while ($delivery = $rsDeliveries->fetch()) {
                $arValues[$link_field][] = $delivery['DELIVERY_ID'];
                $arValues['ID_1C'][] = $delivery['ID_1C'];
            }

            //Получаем названия и описания служб доставки
            $rsDeliveries = DeliveryTable::getList([
                'select' => ['ID', 'NAME', 'DESCRIPTION'],
                'filter' => ['@ID' => $arValues[$link_field]]
            ]);

            while ($delivery = $rsDeliveries->fetch()) {
                $arValues['DELIVERIES_NAMES'][] = $delivery['NAME'];
                $arValues['DELIVERIES_DESCS'][] = $delivery['DESCRIPTION'];
            }
        }
    }
}

//Обработка сохранения
if ($request->getRequestMethod() == 'POST' && ($save != '' || $apply != '')) {
    if (intval($request->getPost('ID')) == 0) {
        $active = !empty($request->getPost('ACTIVE')) ? 'Y' : 'N';
        $local = !empty($request->getPost('LOCAL')) ? 'Y' : 'N';
        $type_ways = !empty($request->getPost('TYPEWAYS')) ? $request->getPost('TYPEWAYS') : 'N';
        $result = WaysDeliveryTable::add([
            'NAME' => $request->getPost('NAME'),
            'ACTIVE' => $active,
            'LOCAL' => $local,
            'TYPEWAYS' => $type_ways,
            'DESCRIPTION' => $request->getPost('DESCRIPTION'),
            'SORT' => $request->getPost('SORT'),
        ]);

        if ($result->isSuccess()) {
            $id = $result->getId();
            $arDeliveries = array_map('intval', $request->getPost('DELIVERY'));
            $ar1CIds = array_map('intval', $request->getPost('ID_1C'));

            foreach ($arDeliveries as $key => $delivery_id) {
                if ($delivery_id != 0) {
                    $rswaybydel = WaysByDeliveryServicesTable::add([
                        'WAY_ID' => $id,
                        'DELIVERY_ID' => $delivery_id,
                        'ID_1C' => $ar1CIds[$key]
                    ]);

                    if (!$rswaybydel->isSuccess()) {
                        $arErrors = array_merge($arErrors, $result->getErrorMessages());
                        break;
                    }
                }
            }

            if ($apply != '') {
                LocalRedirect('/bitrix/admin/delivery_way_edit.php?WAY_ID=' . $id . '&lang=ru');
            } elseif ($save != '') {
                LocalRedirect('/bitrix/admin/delivery_ways.php?lang=ru');
            }
        } else {
            $arErrors = array_merge($arErrors, $result->getErrorMessages());
        }
    } elseif (intval($request->getPost('ID')) > 0) {
        $id = intval($request->getPost('ID'));
        $active = !empty($request->getPost('ACTIVE')) ? 'Y' : 'N';
        $local = !empty($request->getPost('LOCAL')) ? 'Y' : 'N';
        $type_ways = !empty($request->getPost('TYPEWAYS')) ? $request->getPost('TYPEWAYS') : 'N';

        $result = WaysDeliveryTable::update($id, [
            'NAME' => $request->getPost('NAME'),
            'ACTIVE' => $active,
            'LOCAL' => $local,
            'TYPEWAYS' => $type_ways,
            'DESCRIPTION' => $request->getPost('DESCRIPTION'),
            'SORT' => $request->getPost('SORT'),
        ]);
        if ($result->isSuccess()) {
            $rsDeliveries = WaysByDeliveryServicesTable::getList([
                'select' => ['DELIVERY_ID', 'ID_1C'],
                'filter' => ['WAY_ID' => $id]
            ]);

            while ($arDeliveries = $rsDeliveries->fetch()) {
                WaysByDeliveryServicesTable::delete(['WAY_ID' => $id, 'DELIVERY_ID' => $arDeliveries['DELIVERY_ID']]);
            }

            $arDeliveries = array_map('intval', $request->getPost('DELIVERY'));
            $ar1CIds = array_map('intval', $request->getPost('ID_1C'));
            foreach ($arDeliveries as $key => $delivery_id) {
                if ($delivery_id != 0) {
                    $rswaybydel = WaysByDeliveryServicesTable::add([
                        'WAY_ID' => $id,
                        'DELIVERY_ID' => $delivery_id,
                        'ID_1C' => $ar1CIds[$key]
                    ]);

                    if (!$rswaybydel->isSuccess()) {
                        $arErrors = array_merge($arErrors, $result->getErrorMessages());
                        break;
                    }
                }
            }
            //Управление переходами с кнопок
            if ($apply != '') {
                LocalRedirect('/bitrix/admin/delivery_way_edit.php?WAY_ID=' . $id . '&lang=ru');
            } elseif ($save != '') {
                LocalRedirect('/bitrix/admin/delivery_ways.php?lang=ru');
            }
        } else {
            $arErrors = array_merge($arErrors, $result->getErrorMessages());
        }
    }
}

$aTabs = array(
    array(
        "DIV" => "delivery_way",
        "TAB" => Loc::getMessage("DELIVERY_WAY"),
        "ICON" => "main_user_edit",
        "TITLE" => Loc::getMessage("DELIVERY_WAY_TITLE")
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$APPLICATION->SetTitle($page_title);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php"); ?>

<style>
    .ways_table_td {
        padding: 0 10px;
    }
</style>

<form id="delivery_form" method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
    <? echo bitrix_sessid_post(); ?>
    <input type="hidden" name="ID" value="<?= $way_id ?>">
    <input type="hidden" id="check_typeways" value="">
    <? $tabControl->Begin(); ?>
    <? $tabControl->BeginNextTab(); ?>
    <tr>
        <td width="30%"><?= $arFields['ID']->getTitle() . ':' ?></td>
        <td width="70%"><?= $way_id ?></td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['NAME']->getTitle() . ':' ?></td>
        <td width="70%"><input type="text" name="NAME" value="<?= $arValues['NAME']; ?>" size="30" maxlength="255"/>
        </td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['ACTIVE']->getTitle() . ':' ?></td>
        <td width="70%"><input type="checkbox" name="ACTIVE"
                               value="Y"<? if ($arValues['ACTIVE'] == "Y") {
                                    echo " checked";
                                        } ?> /></td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['TYPEWAYS']->getTitle() . ':' ?></td>
        <td width="70%">
            <table>
                <tr>
                    <td width="10%"><input type="radio" name="TYPEWAYS" style="margin-left: -2px;"
                                           value="C"<? if ($arValues['TYPEWAYS'] == "C") {
                                                echo " checked";
                                                    } ?> /></td>
                    <td width="90%" style="padding-top: 4px;">Обычный блок</td>

                </tr>
                <tr>
                    <td width="10%"><input type="radio" name="TYPEWAYS" style="margin-left: -2px;"
                                           value="S"<? if ($arValues['TYPEWAYS'] == "S") {
                                                echo " checked";
                                                    } ?> /></td>
                    <td width="90%" style="padding-top: 4px;">С картой ПВЗ</td>

                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['DESCRIPTION']->getTitle() . ':' ?></td>
        <td width="70%"><input type="text" name="DESCRIPTION" value="<?= $arValues['DESCRIPTION']; ?>" size="30"
                               maxlength="255"/></td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['SORT']->getTitle() . ':' ?></td>
        <td width="70%"><input type="text" name="SORT" value="<?= $arValues['SORT']; ?>" size="30" maxlength="255"/>
        </td>
    </tr>

    <tr>
        <td width="30%"><?= $arFields['DELIVERIES']->getTitle() . ':' ?></td>
        <td width="70%">
            <table id="delivery_table">
                <tr>
                    <td>ID 1C</td>
                    <td style="display: none">ID службы доставки</td>
                    <td class="ways_table_td">ID</td>
                    <td class="ways_table_td">Название доставки</td>
                    <td class="ways_table_td">Описание</td>
                    <td></td>
                </tr>
                <? if (!empty($arValues['DELIVERIES'])) : ?>
                    <? foreach ($arValues['DELIVERIES'] as $key => $value) : ?>
                        <? $index = $key + 1; ?>
                        <tr id="str<?= $index ?>">
                            <td>
                                <input type="text" size="5" name="ID_1C[<?= $index ?>]" id="input_1c_<?= $index ?>"
                                       value="<?= $arValues['ID_1C'][$key] ?>">
                            </td>
                            <td style="display: none">
                                <input type="text" id="input_delivery_<?= $index ?>" size="5"
                                       name="DELIVERY[<?= $index ?>]" value="<?= $value ?>">
                            </td>
                            <td id="id<?= $index ?>" class="ways_table_td">
                                <?= $value ?>
                            </td>
                            <td id="name<?= $index ?>" class="ways_table_td">
                                <?= $arValues['DELIVERIES_NAMES'][$key] ?>
                            </td>
                            <td id="desc<?= $index ?>" class="ways_table_td">
                                <?= $arValues['DELIVERIES_DESCS'][$key] ?>
                            </td>
                            <td>
                                <input type="button" value="Выбрать" title="Выбрать службу доставки"
                                       onclick="chooseDeliveryService(this)">
                            </td>
                            <td>
                                <input type="button" value="Удалить" title="Удалить службу доставки из списка"
                                       onclick="deleteDeliveryService(this)">
                            </td>
                        </tr>
                    <? endforeach; ?>
                <? else : ?>
                    <tr id="str1">
                        <td>
                            <input type="text" size="5" name="ID_1C[1]" id="input_1c_1" value="">
                        </td>
                        <td style="display: none">
                            <input type="text" id="input_delivery_1" size="5" name="DELIVERY[1]" value="">
                        </td>
                        <td id="name1" class="ways_table_td">
                            <?= '' ?>
                        </td>
                        <td id="desc1" class="ways_table_td">
                            <?= '' ?>
                        </td>
                        <td>
                            <input type="button" value="Выбрать" title="Выбрать службу доставки"
                                   onclick="chooseDeliveryService(this)">
                        </td>
                        <td>
                            <input type="button" value="Удалить" title="Удалить службу доставки из списка"
                                   onclick="deleteDeliveryService(this)">
                        </td>
                    </tr>
                <? endif; ?>
            </table>

            <input type="button" value="Еще" width="100%" id="add_str">
        </td>
    </tr>

    <div id="modal_background">
        <div id="modal_container">
            <div class="modal_content">
                <h3>В выбранных службах доставки присутствуют службы, имеющие одинаковую территорию обслуживания</h3>
                <p class="modal_text"></p>
            </div>
            <button class="modal_btn modal_continue">Продолжить</button>
            <button class="modal_btn modal_btn_secondary modal_cancel">Отмена</button>
            <i class="modal_note">*Чтобы изменения вступили в силу, нажмите Сохранить/Применить еще раз после закрытия этого окна</i>
        </div>
    </div>

    <script type="application/javascript" src="<?='/local/templates/respect/js/jquery-3.3.1.min.js' ?>"></script>
    <script>
        $(window).on('load', function () {
            $('input[name*="apply"]').addClass("adm-btn-apply");
            $('#add_str').on('click', addStr);
            $('.adm-btn-save').on('click', startAjax);
            $('.adm-btn-apply').on('click', startAjax);
            $('.modal_cancel').on('click', function(e) {
                e.preventDefault();
                $('#modal_background').css('display', 'none');
            });
            $('.modal_continue').on('click', function(e) {
                e.preventDefault();
                $('#modal_background').css('display', 'none');
                $('#check_typeways').val('checked');
                $('.adm-btn-save').off('click');
                $('.adm-btn-apply').off('click');
            });
        });

        function chooseDeliveryService(item) {
            let number, arIds;

            $('#check_typeways').val('');
            $('.adm-btn-save').on('click', startAjax);
            $('.adm-btn-apply').on('click', startAjax);

            number = getNumStr(false, item);
            arIds = getTempIds();

            window.open('/local/php_interface/include/qsoft/tools/delivery_services_search.php?num=' + number + '&id=' + arIds,
                '',
                'scrollbars=yes,resizable=yes,width=760,height=500,top=' + Math.floor((screen.height - 560) / 2 - 14) + ',left=' + Math.floor((screen.width - 760) / 2 - 5)
            );
        }

        function deleteDeliveryService(item) {
            let number;
            number = getNumStr(false, item);

            $('#check_typeways').val('');
            $('.adm-btn-save').on('click', startAjax);
            $('.adm-btn-apply').on('click', startAjax);

            $('#input_delivery_' + number).val('');
            $('#id' + number).html('');
            $('#name' + number).html('');
            $('#desc' + number).html('');

        }

        function addStr() {
            let number, content, input_delivery, input_1c,
                td_delivery_name, td_delivery_desc;
            number = getNumStr(true);

            $('#check_typeways').val('');
            $('.adm-btn-save').on('click', startAjax);
            $('.adm-btn-apply').on('click', startAjax);

            content = $('#str' + number).clone();

            content.attr('id', 'str' + (number + 1));


            input_delivery = content.find('#input_delivery_' + number);
            input_delivery.attr('id', 'input_delivery_' + (number + 1));
            input_delivery.attr('name', 'DELIVERY[' + (number + 1) + ']');
            input_delivery.val('');

            td_delivery_id = content.find('#id' + number);
            td_delivery_id.attr('id', 'id' + (number + 1));
            td_delivery_id.html('');

            td_delivery_name = content.find('#name' + number);
            td_delivery_name.attr('id', 'name' + (number + 1));
            td_delivery_name.html('');

            td_delivery_desc = content.find('#desc' + number);
            td_delivery_desc.attr('id', 'desc' + (number + 1));
            td_delivery_desc.html('');

            input_1c = content.find('#input_1c_' + number);
            input_1c.attr('id', 'input_1c_' + (number + 1));
            input_1c.attr('name', 'ID_1C[' + (number + 1) + ']');
            input_1c.val('');

            $('#delivery_table').append(content);
        }

        function getNumStr(last = false, item) {
            let id, num;

            if (last) {
                id = $('#delivery_table').find('tr').last().attr('id');
            } else {
                id = $(item).closest('tr').attr('id');
            }

            num = id.replace('str', '');
            return parseInt(num);
        }

        function getTempIds() {
            let number, i, id, arIds = [];

            number = getNumStr(true);

            for (i = 1; i <= number; i++) {
                id = $('#input_delivery_' + i).val();
                arIds.push(id);
            }

            return arIds;
        }

        function startAjax(e) {
            e.preventDefault();
            if ($('#check_typeways').val() == 'checked') {
                return false;
            } else {
                let data = $('#delivery_form').serializeArray();
                let deliveriesID = [];
                let typeWays = '';

                $.each(data, function(){
                    if (this.name.includes('DELIVERY')) {
                        deliveriesID.push(this.value);
                    } else if (this.name == 'TYPEWAYS') {
                        typeWays = this.value;
                    }
                });
                
                $.ajax({
                    method: 'post',
                    url: '/local/ajax/check_type_ways.php',
                    data: {
                        'deliveries_id': deliveriesID,
                        'type_ways': typeWays,
                    },
                    success: function (response) {
                        if (response == 'OK') {
                            $('.adm-btn-save').off('click');
                            $('.adm-btn-apply').off('click');
                            $('.' + e.target.className).trigger('click');

                        } else {
                            $(".modal_text").html(response);
                            $('#modal_background').css('display', 'block');
                        }   
                    },
                });
            }
            return false;
        };

    </script>

    <? $tabControl->Buttons(
        array(
            "back_url" => "delivery_ways.php?lang=" . LANG,
        )
    ); ?>
    <? $tabControl->End(); ?>
</form>

<style>
#modal_background {
  background: rgba(102, 102, 102, 0.5);
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  display: none;
  z-index: 999;
}
#modal_container {
  font-family: sans-serif;
  line-height: 1.25;
  max-width: 500px;
  text-align: center;
  padding: 20px;
  border-radius: 5px;
  position: absolute;
  top: 25%;
  right: 0;
  left: 0;
  margin: auto;
  background: #fff;
}
.modal_content {
  max-height: 200px;
  overflow-x: hidden;
}

#modal_background:target {
  display: block;
}
.modal_btn {
  display: inline-block;
  margin: 10px 5px;
  text-decoration: none;
  color: #fff;
  background-color: #0275d8;
  border: 1px solid transparent;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  border-radius: 0.25rem;
  cursor: pointer;
}
.modal_btn:hover {
  background-color: #025aa5;
  text-decoration: none;
}
.modal_btn_secondary {
  color: #373a3c;
  background-color: #fff;
  border-color: #ccc;
}
.modal_btn_secondary:hover {
  background-color: #e6e6e6;
}
.modal_note {
    display:block;
    font-size: 12px;
}
</style>