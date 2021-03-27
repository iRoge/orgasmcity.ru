<?php
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
use Bitrix\Main\Application;

if (isset($_POST['save']) && check_bitrix_sessid()) {
    $branches = $_POST['branch'];

    $sqlHead = 'INSERT INTO `b_qsoft_location2branch` (`branch_id`, `location_code`) VALUES ';
    $sqlBody ='';

    foreach ($branches as $branch => $locations) {
        foreach ($locations as $location) {
            if (empty($location)|| strpos($sqlBody, $location) !== false) {
                continue;
            }

            $branch  = intval($branch);
            $location = $DB->ForSql($location);
            $sqlBody .= "({$branch}, '{$location}'),";
        }
    }

    $DB->Query("TRUNCATE TABLE `b_qsoft_location2branch`");

    if (!empty($sqlBody)) {
        $sqlBody = rtrim($sqlBody, ',');
        $sqlBody .= ';';

        $sql = $sqlHead . $sqlBody;

        $DB->Query($sql);
        Application::getInstance()->getTaggedCache()->clearByTag('branchless_locations');
        Application::getInstance()->getTaggedCache()->clearByTag('unique_showcases');
        Application::getInstance()->getTaggedCache()->clearByTag('unique_showcasesWO');
    }
}

//Получаем все филиалы
$rsData = $DB->Query("SELECT * FROM `b_respect_branch`");
while ($ar = $rsData->fetch()) {
    $arBranch[$ar['id']] = $ar;
}

//Получаем все регионы
$rsData = $DB->Query("
    SELECT  lb.`location_code`, lb.`branch_id`, lc.`NAME`
    FROM `b_qsoft_location2branch` AS lb
    JOIN (
        SELECT bsl.`CODE`, bsl.`ID`, ln.`NAME`
        FROM `b_sale_location` as bsl
        JOIN (SELECT `NAME`, `LOCATION_ID` FROM b_sale_loc_name WHERE `LANGUAGE_ID` = 'ru') as ln
        ON bsl.`ID` = ln.`LOCATION_ID`
    ) AS lc
    ON lb.`location_code` = lc.`CODE`
");
while ($location = $rsData->Fetch()) {
    $locations[$location['branch_id']][] = $location;
}

$tabControl = new CAdminTabControlDrag('tab_control', [["DIV" => "tab_branches", "TAB" => "Филиалы", "IS_DRAGGABLE" => "Y"]]);
$APPLICATION->SetTitle('Привязка филиалов к местоположениям');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (!empty($_REQUEST['success'])) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => 'Сброс сортировки произведен успешно.',
        "TYPE" => 'OK',
        "HTML" => true
    ));
}

?>
    <form method="POST" Action="<? echo $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
        <? echo bitrix_sessid_post(); ?>
        <input type="hidden" name="lang" value="<?= LANG ?>">
        <?
        // отобразим заголовки закладок
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        $tabControl->DraggableBlocksStart();

        foreach ($arBranch as $branch) {
            echo '<a id="' . $branch['id'] . '" class="adm-sale-fastnav-anchor"></a>';
            $tabControl->DraggableBlockBegin($branch['name'], $branch['id']);
            ?>
            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell"><b>Местоположение</b></div>
                    </div>
                    <?
                    $i = 0;
                    foreach ($locations[$branch['id']] as $location) { ?>
                        <div class="divTableRow <?= $branch['id'] ?>_row" data-num="<?= $i ?>">
                            <div class="divTableCell"
                                 id="<?= $branch['id'] ?>_view_<?= $i ?>"><?= $location['NAME'] ?></div>
                            <div class="divTableCell"><input class="tablebodybutton" type="button"
                                                             OnClick="open_win_location(<?= $branch['id'] ?>, <?= $i ?>)"
                                                             value="...">
                                <input class="locationInput" name="branch[<?=$branch['id']?>][]" type="hidden" id="<?= $branch['id'] ?>_loc_<?= $i ?>"
                                       value="<?= $location['location_code'] ?>">
                            </div>
                            <div class="divTableCell"><input type="button" value="Удалить"
                                                             onClick="$(this).parent('div').parent('div').remove()">
                            </div>
                        </div>
                        <?
                        $i++;
                    }

                    if (count($locations[$branch['id']]) === 0) {
                        ?>
                        <div class="divTableRow <?= $branch['id'] ?>_row" data-num="<?= $i ?>">
                            <div class="divTableCell"
                                 id="<?= $branch['id'] ?>_view_<?= $i ?>">Выберите местоположение:
                            </div>
                            <div class="divTableCell"><input class="tablebodybutton" type="button"
                                                             OnClick="open_win_location(<?= $branch['id'] ?>, <?= $i ?>)"
                                                             value="...">
                                <input class="locationInput" name="branch[<?=$branch['id']?>][]" type="hidden" id="<?= $branch['id'] ?>_loc_<?= $i ?>">
                            </div>
                            <div class="divTableCell"><input type="button" value="Удалить"
                                                             onClick="$(this).parent('div').parent('div').remove()">
                            </div>
                        </div>
                        <?
                    } ?>
                </div>
            </div>
            <input type="button" id="<?= $branch['id'] ?>_but" data-branch="<?= $branch['id'] ?>"
                   data-next="<?= $i + 1 ?>" value="Ещё" onClick="loc_link_next('<?= $branch['id'] ?>')">
            <?
            $tabControl->DraggableBlockEnd();
        }
        $tabControl->Buttons();
        ?>
        <input id="form-submit" type="submit" name="save"
               value="Cохранить изменения"
               title="Cохранить изменения"
               class="adm-btn-save"
        />
        <? $tabControl->End(); ?>
    </form>
    <script>
        function open_win_location(branch, num) {
            window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&field_name=' + branch + '&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top=' + Math.floor((screen.height - 560) / 2 - 14) + ',left=' + Math.floor((screen.width - 760) / 2 - 5));
        }

        function loc_link_next(name) {
            var $el = $("#" + name + "_but");
            var num = $el.data("next");
            var branch = $el.data("branch");
            $el.prev("div.divTable").append(`<div class="divTableRow" data-num="` + num + `">
                <div class="divTableCell" id="` + name + `_view_` + num + `">Выберите местоположение:</div>
                <div class="divTableCell" ><input class="tablebodybutton" type="button" onClick="open_win_location(${branch}, ${num})" value="...">
                    <input class="locationInput" name="branch[${branch}][]"type="hidden" id="` + name + `_loc_` + num + `"></div>
                <div class="divTableCell" ><input type="button" value="Удалить" onClick="$(this).parent('div').parent('div').remove()"></div>
            </tr>`);
            $el.data("next", num + 1);
        }

        $('#form-submit').on('click', function (e) {
           var $locations = $('.locationInput');
           var locationsValues = [];
           var locationsArray = [];
           var hasErrors = false;

           $.each($locations, function (key, $loc) {
               $($loc).parent('div').prev('div').removeClass('hasError');
               $('#error-message').remove();

               var value = $($loc).val();
               var index = locationsValues.indexOf(value);

               if(value !== '' && index !== -1) {
                   hasErrors = true;
                   $(locationsArray[index]).parent('div').prev('div').addClass('hasError');
                   $($loc).parent('div').prev('div').addClass('hasError');
               } else {
                   locationsValues.push(value);
                   locationsArray.push($loc);
               }
           });

           if(hasErrors) {
               e.preventDefault();
               e.stopImmediatePropagation();
               var $buttonsPanel = $('.adm-detail-content-btns');
               $buttonsPanel.append('<div id="error-message" class="hasError" style="display: inline">Не допустима привязка одного местоположения к неcкольким филиалам</div>');
               return false;
           }
        });
    </script>
    <style>
        .divTable {
            display: table;
            width: 30%;
        }

        .divTableRow {
            display: table-row;
        }

        .divTableCell {
            display: table-cell;
            padding: 3px 10px;
        }

        .divTableBody {
            display: table-row-group;
        }

        .hasError {
            color: red;
        }
    </style>
<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>