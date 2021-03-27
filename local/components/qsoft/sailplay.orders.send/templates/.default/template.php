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
        <p>Выберите файл с заказами для отправки (*.xlsx):</p>
        <form name="import_settings" method="post" enctype="multipart/form-data">
            <?=$obCFile->InputFile($arParams['IMPORT']['FILE_NAME'], 0, 0)?>
            <input type="hidden" name="config_import" value="Y" />
            <input type="submit" name="config" value="Далее" />
        </form>
    <?endif;?>
<?else :?>
    <div class="csv-import-process">
        <div id="csv-import-progress">
        </div>
        <form  name="import_start" method="post" enctype="multipart/form-data">
            <input type="hidden" name="max_value" id="max_value" value=<?=$arResult['ITEMS_COUNT']; ?> />
            <input type="hidden" name="tpm_file" id="tmp_file" value="<?=$arResult['TMP_FILE']?>" />
            <input type="hidden" name="file_hash" value="<?=$arResult['FILE_HASH']?>" />
            <input type="submit" id="csv-import-start" value="Начать отправку" />
            <input type="button" id="csv-import-cancel" value="Отмена" onclick="canceled = true; window.location='<?=$APPLICATION->GetCurPage(false)?>?lang=ru';" />
        </form>
    </div>
    <script>
        var progressBar = new BX.UI.ProgressBar({
            color: BX.UI.ProgressBar.Color.PRIMARY,
            value: 0,
            maxValue:   parseInt($('#max_value').val()),
            statusType: BX.UI.ProgressBar.Status.COUNTER,
            textBefore: "Отправлено заказов",
            fill: true,
            column: true
        });
    </script>
    <div id="csv-import-preview" class="csv-import-preview">
        <br/><h2>Предварительный просмотр:</h2>
        Всего заказов для отправки: <?=$arResult['ITEMS_COUNT']; ?><br/><br/>

        <?foreach ($arResult["ITEMS"] as $arData) :?>
            <span<?if ($arData['ERROR'] == 'Y') :
                ?> class="err-row" <?
                 endif;?> id="<?=$arData['ID']?>"><?=$arData['ID']?></span>
        <?endforeach;?>

    </div>
<?endif;?>

