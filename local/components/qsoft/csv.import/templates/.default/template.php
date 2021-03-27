<?
\Bitrix\Main\UI\Extension::load("ui.progressbar");
$obCFile = new CFile();
?>
<?if (!empty($arResult["ERRORS"])) :?>
    <div class="err-block">
        <font>
            <?=implode("<br>", $arResult["ERRORS"]);?>
        </font><br />
    </div>
<?endif;?>
<?if (empty($arResult['DATA'])) :?>
    <?if (empty($arResult['TOTAL'])) :?>
        Выберите файл для импорта:
        <form name="import_settings" method="post" enctype="multipart/form-data">
            <?=$obCFile->InputFile($arParams['IMPORT']['FILE_NAME'], 0, 0)?>
            <input type="hidden" name="config_import" value="Y" />
            <input type="submit" name="config" value="Далее" />
        </form>
    <?endif;?>
<?else :?>
    <?if (!empty($arResult["ERRORS"])) :?>
        <div class="err-message">ВНИМАНИЕ! Строки с ошибками не будут импортированы на сайт</div>
    <?endif;?>

    <div class="csv-import-process">
        <div id="csv-import-progress">
        </div>
        <form  name="import_start" method="post" enctype="multipart/form-data">
            <input type="hidden" name="max_value" id="max_value" value=<?=$arResult['ITEMS_COUNT']; ?> />
            <input type="hidden" name="tpm_file" id="tmp_file" value="<?=$arResult['TMP_FILE']?>" />
            <input type="hidden" name="file_hash" value="<?=$arResult['FILE_HASH']?>" />
            <input type="submit" id="csv-import-start" value="Начать загрузку" />
            <input type="button" id="csv-import-cancel" value="Отмена" onclick="canceled = true; window.location='<?=$APPLICATION->GetCurPage(false)?>?lang=ru';" />
        </form>
    </div>
    <script>
        var progressBar = new BX.UI.ProgressBar({
            color: BX.UI.ProgressBar.Color.PRIMARY,
            value: 0,
            maxValue:   parseInt($('#max_value').val()),
            statusType: BX.UI.ProgressBar.Status.COUNTER,
            textBefore: "Импортированно заказов",
            fill: true,
            column: true
        });
    </script>
    <div id="csv-import-preview" class="csv-import-preview">
        <br/><h2>Предварительный просмотр:</h2>
        Всего записей для импорта: <?=$arResult['ITEMS_COUNT']; ?><br/><br/>
        <table border=1>
            <tr>
                <th>ID</th>
                <th>Статус</th>
                <th>Деньги</th>
            </tr>
        <?foreach ($arResult["ITEMS"] as $arData) :?>
            <tr <?if ($arData['ERROR'] == 'Y') :
                ?> class="err-row" <?
                endif;?>>
                <td><?=$arData['ID']?></td>
                <td><?=$arData['STATUS']?></td>
                <td><?=$arData['REVENUE']?></td>
            </tr>
        <?endforeach;?>
        </table>
    </div>
<?endif;?>

