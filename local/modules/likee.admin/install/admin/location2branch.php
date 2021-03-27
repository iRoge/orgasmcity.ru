<?php
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;
global $CACHE_MANAGER;

//Получаем все филиалы
global $DB;
$rsData = $DB->Query("SELECT * FROM `b_respect_branch`");
while($ar = $rsData->fetch()){
    $arBranch[$ar['id']] = $ar;
}

$APPLICATION->SetTitle('Привязка филиалов к регионам');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (! empty($_REQUEST['success'])) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => 'Сброс сортировки произведен успешно.',
        "TYPE" => 'OK',
        "HTML" => true
    ));
}

?>
<script src="/local/templates/respect/lib/jquery.js"></script>
<style>
    .location2branch{
        height: 500px;
    }
    .location2branch__branch{
        float: left;
        width: 50%;
    }
    .location2branch__region{
        float: right;
        width: 50%;
    }
    .location2branch__select{
        margin-bottom: 10px;
    }
    .location2branch__btn{
        padding: 1px 13px 3px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        border: none;
        -webkit-box-shadow: 0 0 1px rgba(0,0,0,.11), 0 1px 1px rgba(0,0,0,.3), inset 0 1px #fff, inset 0 0 1px rgba(255,255,255,.5);
        box-shadow: 0 0 1px rgba(0,0,0,.3), 0 1px 1px rgba(0,0,0,.3), inset 0 1px 0 #fff, inset 0 0 1px rgba(255,255,255,.5);
        background-color: #e0e9ec;
        background-image: -webkit-linear-gradient(bottom, #d7e3e7, #fff)!important;
        background-image: -moz-linear-gradient(bottom, #d7e3e7, #fff)!important;
        background-image: -ms-linear-gradient(bottom, #d7e3e7, #fff)!important;
        background-image: -o-linear-gradient(bottom, #d7e3e7, #fff)!important;
        background-image: linear-gradient(bottom, #d7e3e7, #fff)!important;
        color: #3f4b54;
        cursor: pointer;
        display: inline-block;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-weight: bold;
        font-size: 13px;
        line-height: 29px;
        height: 29px;
        text-shadow: 0 1px rgba(255,255,255,0.7);
        text-decoration: none;
        position: relative;
        vertical-align: middle;
        -webkit-font-smoothing: antialiased;
    }
    .location2branch__btn:hover {
        text-decoration: none;
        background: #f3f6f7!important;
        background-image: -webkit-linear-gradient(top, #f8f8f9, #f2f6f8)!important;
        background-image: -moz-linear-gradient(top, #f8f8f9, #f2f6f8)!important;
        background-image: -ms-linear-gradient(top, #f8f8f9, #f2f6f8)!important;
        background-image: -o-linear-gradient(top, #f8f8f9, #f2f6f8)!important;
        background-image: linear-gradient(top, #f8f8f9, #f2f6f8)!important;
    }
    .location2branch__branch-select{
        width: 200px;
    }
    .clearfix:after{
        display: table;
        clear: both;
        content: '';
    }
    .location2branch__save{
        margin-top: 50px;
    }
</style>
<form method="POST" action="location2branch.php?lang=ru" enctype="multipart/form-data" name="editform">

<div class="adm-detail-content-item-block location2branch">
        <div class="adm-info-message-wrap">
            <div class="!adm-info-message">
                <p>Для начала работы вам необходимо выбрать филиал и установить связи с регионами.</p>
            </div>
        </div>
    <div class="clearfix">
        <div class="location2branch__branch">
            <?if($arBranch):?>
                <select name="branch" data-old="" id="" class="location2branch__branch-select">
                    <option value="" selected>Выбрать филиал</option>
                    <?foreach ($arBranch as $branch):?>
                        <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                        <?if(count($arBranch)==1){break;}?>
                    <?endforeach;?>
                </select>
            <?endif;?>
            <div class="location2branch__save">
                <input type="button" value="Сохранить все изменения" onclick="" class="location2branch__savebtn adm-btn-save">
            </div>
        </div>

        <div class="location2branch__region">

        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('select[name=branch]').on('change', function () {
                var val = $(this).val();
                var old_val = $(this).attr('data-old');
                $(this).attr('data-old',val);
                var arvalue = [];
                $('.regionbranch_'+old_val).each(function () {
                    if($(this).val()){
                        arvalue[arvalue.length] = $(this).val();
                    }
                });

                $.post(
                    "/local/ajax/location2branch.php",
                    {
                        VALUE: val,
                        ACTION: 'GETREGION',
                        OLDVALUE: old_val,
                        ACTION2: 'SAVE',
                        ARVALUE: arvalue,
                    },
                    function (data)
                    {
                        $('.location2branch__region').html(data);
                    }
                );
            });
            $('.location2branch__savebtn').on('click', function () {
                var old_val = $('select[name=branch]').val();
                var arvalues = [];
                $('.regionbranch_'+old_val).each(function () {
                    if($(this).val()){
                        arvalues[arvalues.length] = $(this).val();
                    }
                });
                $.post(
                    "/local/ajax/location2branch.php",
                    {
                        OLDVALUE: old_val,
                        ACTION2: 'SAVE',
                        ARVALUE: arvalues,
                    },
                    function (data)
                    {
                        // $('.location2branch__region').html(data);
                    }
                );
            });
        })
    </script>
</div>

<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?echo LANG?>">
</form>
<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>